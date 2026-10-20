<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_creates_multiple_applications_without_changing_the_student_or_external_deliveries(): void
    {
        Http::preventStrayRequests();
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $data = $this->payload($student);
        $this->actingAs($staff)->get('/admin/crm/applications/create?student='.$student->id)->assertOk();
        $id = $this->postJson('/admin/crm/applications', $data)->assertOk()->json('data.id');
        $this->postJson('/admin/crm/applications', $data)->assertOk()->assertJsonPath('data.id', $id);
        $this->postJson('/admin/crm/applications', [...$data, 'creation_key' => (string) Str::uuid(), 'course' => 'Second course'])->assertOk();
        $this->get('/admin/crm/applications/'.$id)->assertOk()->assertSee('Test course')->assertSee('profile-card-has-mask', false)->assertSee('APPLICATION-'.$id);
        $this->get('/admin/crm/applications')->assertOk()->assertSee('Second course');
        $this->assertDatabaseCount('crm_applications', 2);
        $this->assertDatabaseCount('crm_case_notes', 2);
        $this->assertSame('lead', $student->fresh()->stage);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_status_transitions_are_explicit_recorded_and_reject_stale_updates(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $data = $this->payload($student);
        $id = $this->actingAs($staff)->postJson('/admin/crm/applications', $data)->json('data.id');
        $next = CrmReference::factory()->create(['kind' => 'application_status', 'name' => 'Submitted']);
        $update = collect($data)->except(['creation_key', 'student_id'])->all();
        $this->patchJson('/admin/crm/applications/'.$id, [...$update, 'version' => 1, 'status_id' => $next->id])
            ->assertUnprocessable()->assertJsonValidationErrors('status_id');
        DB::table('crm_status_transitions')->insert(['from_id' => $data['status_id'], 'to_id' => $next->id]);
        $this->patchJson('/admin/crm/applications/'.$id, [...$update, 'version' => 1, 'status_id' => $next->id])
            ->assertOk()->assertJsonPath('data.version', 2);
        $this->patchJson('/admin/crm/applications/'.$id, [...$update, 'version' => 1])->assertConflict();
        $this->assertDatabaseHas('crm_case_events', ['application_id' => $id, 'from_status_id' => $data['status_id'], 'to_status_id' => $next->id]);
    }

    public function test_case_assignment_scopes_protect_lists_and_direct_read_and_write_endpoints(): void
    {
        $staff = $this->staff();
        $record = CrmApplication::factory()->create();
        $this->actingAs($staff)->getJson('/admin/crm/applications')->assertJsonPath('total', 0);
        $this->get('/admin/crm/applications/'.$record->id)->assertNotFound();
        $this->get('/admin/crm/applications/create?student='.$record->student_id)->assertNotFound();
        $this->patchJson('/admin/crm/applications/'.$record->id, [])->assertNotFound();
        $this->postJson('/admin/crm/applications', $this->payload($record->student))->assertNotFound();
    }

    public function test_campus_must_match_university_and_status_and_owner_must_match_the_case(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $data = $this->payload($student);
        $campus = CrmReference::factory()->create(['kind' => 'campus', 'parent_id' => CrmReference::factory()->create()->id]);
        $this->actingAs($super)->postJson('/admin/crm/applications', [...$data, 'campus_id' => $campus->id])->assertUnprocessable()->assertJsonValidationErrors('campus_id');
        $visaStatus = CrmReference::factory()->create(['kind' => 'visa_status']);
        $this->postJson('/admin/crm/applications', [...$data, 'status_id' => $visaStatus->id])->assertUnprocessable()->assertJsonValidationErrors('status_id');
        $staff = $this->staff();
        $this->postJson('/admin/crm/applications', [...$data, 'owner_id' => $staff->id])->assertUnprocessable()->assertJsonValidationErrors('owner_id');
        $this->assertDatabaseCount('crm_applications', 0);
    }

    public function test_invalid_fields_read_only_roles_and_feature_flag_are_enforced(): void
    {
        $student = CrmStudent::factory()->create();
        $this->get('/admin/crm/applications')->assertRedirect('/admin/login');
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/applications', [])->assertUnprocessable()->assertJsonValidationErrors(['student_id', 'creation_key', 'course', 'intake', 'status_id']);
        $this->postJson('/admin/crm/applications', [...$this->payload($student), 'created_by' => null, 'tags' => [str_repeat('x', 51)]])->assertUnprocessable()->assertJsonValidationErrors(['created_by', 'tags.0']);
        $viewer = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($viewer)->postJson('/admin/crm/applications', [])->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs($super)->get('/admin/crm/applications')->assertNotFound();
    }

    public function test_case_pages_escape_user_text_and_never_serialize_restricted_student_fields(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['sensitive_profile' => ['guardian_name' => 'Restricted guardian']]);
        $record = CrmApplication::factory()->create(['student_id' => $student->id, 'course' => '<script>alert(1)</script>']);
        $this->actingAs($super)->get('/admin/crm/applications/'.$record->id)->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $this->getJson('/admin/crm/applications/'.$record->id)->assertDontSee('Restricted guardian')->assertJsonMissingPath('data.student.sensitive_profile');
    }

    private function payload(CrmStudent $student): array
    {
        return ['student_id' => $student->id, 'creation_key' => (string) Str::uuid(),
            'university_id' => CrmReference::factory()->create()->id,
            'status_id' => CrmReference::factory()->create(['kind' => 'application_status'])->id,
            'course' => 'Test course', 'intake' => 'September 2027', 'tags' => ['Expo', ''], 'remark' => 'Test application remark'];
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }
}
