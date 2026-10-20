<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmReference;
use App\Models\CrmVisa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmVisaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_linked_visa_save_is_repeatable_and_does_not_advance_student_or_create_billing(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $application = CrmApplication::factory()->create();
        $data = $this->payload($application);
        $this->actingAs($super)->get('/admin/crm/visas/create?application='.$application->id)->assertOk();
        $id = $this->postJson('/admin/crm/visas', $data)->assertOk()->assertJsonPath('data.student_id', $application->student_id)->json('data.id');
        $this->postJson('/admin/crm/visas', $data)->assertJsonPath('data.id', $id);
        $this->assertDatabaseCount('crm_visas', 1);
        $this->assertSame('lead', $application->student->fresh()->stage);
        $this->get('/admin/crm/visas/'.$id)->assertOk()->assertSee('Student visa');
        $this->get('/admin/crm/visas')->assertOk()->assertSee('Student visa');
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
    }

    public function test_transition_matrix_dates_and_stale_versions_protect_visa_updates(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $application = CrmApplication::factory()->create();
        $data = $this->payload($application);
        $id = $this->actingAs($super)->postJson('/admin/crm/visas', $data)->json('data.id');
        $next = CrmReference::factory()->create(['kind' => 'visa_status', 'name' => 'Lodged']);
        $update = collect($data)->except(['creation_key', 'application_id'])->all();
        $this->patchJson('/admin/crm/visas/'.$id, [...$update, 'version' => 1, 'status_id' => $next->id])->assertUnprocessable()->assertJsonValidationErrors('status_id');
        DB::table('crm_status_transitions')->insert(['from_id' => $data['status_id'], 'to_id' => $next->id]);
        $this->patchJson('/admin/crm/visas/'.$id, [...$update, 'version' => 1, 'status_id' => $next->id])->assertOk()->assertJsonPath('data.version', 2);
        $this->patchJson('/admin/crm/visas/'.$id, [...$update, 'version' => 1])->assertConflict();
        $this->patchJson('/admin/crm/visas/'.$id, [...$update, 'version' => 2, 'lodged_on' => '2026-10-21', 'decision_on' => '2026-10-20'])->assertUnprocessable()->assertJsonValidationErrors('decision_on');
        $this->assertDatabaseHas('crm_case_events', ['visa_id' => $id, 'from_status_id' => $data['status_id'], 'to_status_id' => $next->id]);
    }

    public function test_student_assignment_scopes_protect_all_visa_reads_and_writes(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $visa = CrmVisa::factory()->create();
        $this->actingAs($staff)->getJson('/admin/crm/visas')->assertJsonPath('total', 0);
        $this->get('/admin/crm/visas/'.$visa->id)->assertNotFound();
        $this->get('/admin/crm/visas/create?application='.$visa->application_id)->assertNotFound();
        $this->postJson('/admin/crm/visas', $this->payload($visa->application))->assertNotFound();
        $this->patchJson('/admin/crm/visas/'.$visa->id, [])->assertNotFound();
    }

    public function test_invalid_input_wrong_status_family_and_parent_reassignment_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $application = CrmApplication::factory()->create();
        $this->get('/admin/crm/visas')->assertRedirect('/admin/login');
        $this->actingAs($super)->postJson('/admin/crm/visas', [])->assertUnprocessable()->assertJsonValidationErrors(['application_id', 'creation_key', 'status_id', 'ready_to_bill']);
        $this->postJson('/admin/crm/visas', [...$this->payload($application), 'status_id' => $application->status_id])->assertUnprocessable()->assertJsonValidationErrors('status_id');
        $visa = CrmVisa::factory()->create();
        $this->patchJson('/admin/crm/visas/'.$visa->id, ['version' => 1, 'application_id' => $application->id, 'status_id' => $visa->status_id, 'visa_type' => 'Student visa', 'ready_to_bill' => 0])
            ->assertUnprocessable()->assertJsonValidationErrors('application_id');
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/visas')->assertNotFound();
    }

    private function payload(CrmApplication $application): array
    {
        return ['application_id' => $application->id, 'creation_key' => (string) Str::uuid(),
            'status_id' => CrmReference::factory()->create(['kind' => 'visa_status'])->id,
            'visa_type' => 'Student visa', 'ready_to_bill' => 0, 'remark' => 'Prepare visa checklist'];
    }

    public function test_mapped_application_status_requires_explicit_action_allowed_transition_and_current_version(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $application = CrmApplication::factory()->create();
        $target = CrmReference::factory()->create(['kind' => 'application_status', 'name' => 'Visa confirmation']);
        $status = CrmReference::factory()->create(['kind' => 'visa_status', 'linked_application_status_id' => $target->id]);
        $data = [...$this->payload($application), 'status_id' => $status->id];
        $id = $this->actingAs($admin)->postJson('/admin/crm/visas', $data)->assertOk()->json('data.id');
        $this->assertNotSame($target->id, $application->fresh()->status_id);
        $update = ['version' => 1, 'status_id' => $status->id, 'visa_type' => 'Student visa', 'ready_to_bill' => false, 'application_version' => 1, 'apply_linked_application_status' => true];
        $this->patchJson('/admin/crm/visas/'.$id, $update)->assertUnprocessable()->assertJsonValidationErrors('apply_linked_application_status');
        DB::table('crm_status_transitions')->insert(['from_id' => $application->status_id, 'to_id' => $target->id]);
        $this->patchJson('/admin/crm/visas/'.$id, [...$update, 'application_version' => 99])->assertConflict();
        $this->assertSame(1, $application->fresh()->version);
        $this->patchJson('/admin/crm/visas/'.$id, $update)->assertOk()->assertJsonPath('data.linked_application_version', 2);
        $this->assertSame($target->id, $application->fresh()->status_id);
        $this->assertDatabaseHas('crm_case_events', ['application_id' => $application->id, 'action' => 'application.updated', 'to_status_id' => $target->id]);
        $this->assertSame('lead', $application->student->fresh()->stage);
        $this->assertDatabaseCount('crm_invoices', 0);
    }
}
