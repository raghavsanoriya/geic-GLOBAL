<?php

namespace Tests\Feature;

use App\Models\CrmReference;
use App\Models\User;
use Database\Seeders\CrmReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmReferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_only_configuration_permission_allows_dictionary_access_and_changes(): void
    {
        $this->get('/admin/crm/references')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $this->actingAs($staff)->getJson('/admin/crm/references')->assertForbidden();
        $this->postJson('/admin/crm/references', $this->payload())->assertForbidden();
        $super = User::factory()->create(['is_admin' => true]);
        $id = $this->actingAs($super)->postJson('/admin/crm/references', $this->payload())->assertOk()->json('data.id');
        $this->get('/admin/crm/references')->assertOk()->assertSee('Example university');
        $this->get('/admin/crm/references/'.$id)->assertOk()->assertSee('Stable code: example');
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/references')->assertNotFound();
    }

    public function test_campuses_require_a_verified_active_university_and_reference_codes_are_unique(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/references', [...$this->payload(), 'kind' => 'campus'])->assertUnprocessable()->assertJsonValidationErrors('parent_id');
        $parent = CrmReference::factory()->create();
        $this->postJson('/admin/crm/references', [...$this->payload(), 'kind' => 'campus', 'parent_id' => $parent->id])->assertOk();
        $this->postJson('/admin/crm/references', [...$this->payload(), 'kind' => 'campus', 'parent_id' => $parent->id])->assertUnprocessable()->assertJsonValidationErrors('code');
        $this->assertDatabaseCount('crm_references', 2);
    }

    public function test_status_transition_matrix_is_explicit_and_separate_between_application_and_visa(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $from = CrmReference::factory()->create(['kind' => 'application_status']);
        $to = CrmReference::factory()->create(['kind' => 'application_status']);
        $visa = CrmReference::factory()->create(['kind' => 'visa_status']);
        $this->actingAs($super)->patchJson('/admin/crm/references/'.$from->id, ['name' => $from->name, 'version' => 1, 'is_active' => 1, 'transitions' => [$visa->id]])
            ->assertUnprocessable()->assertJsonValidationErrors('transitions.0');
        $this->patchJson('/admin/crm/references/'.$from->id, ['name' => $from->name, 'version' => 1, 'is_active' => 1, 'transitions_present' => 1, 'transitions' => [$to->id]])->assertOk()->assertJsonPath('data.version', 2);
        $this->assertDatabaseHas('crm_status_transitions', ['from_id' => $from->id, 'to_id' => $to->id]);
        $this->patchJson('/admin/crm/references/'.$from->id, ['name' => $from->name, 'version' => 2, 'is_active' => 1, 'transitions_present' => 1])->assertOk();
        $this->assertDatabaseCount('crm_status_transitions', 0);
        $this->assertDatabaseCount('crm_reference_events', 2);
    }

    public function test_deactivation_preserves_rows_and_stale_edits_and_kind_changes_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $record = CrmReference::factory()->create();
        $this->actingAs($super)->patchJson('/admin/crm/references/'.$record->id, ['name' => 'Inactive university', 'version' => 1, 'is_active' => 0])->assertOk()->assertJsonPath('data.version', 2);
        $this->patchJson('/admin/crm/references/'.$record->id, ['name' => 'Stale name', 'version' => 1, 'is_active' => 1])->assertConflict();
        $this->patchJson('/admin/crm/references/'.$record->id, ['name' => 'Wrong kind', 'version' => 2, 'is_active' => 1, 'kind' => 'agent'])->assertUnprocessable()->assertJsonValidationErrors('kind');
        $this->assertDatabaseHas('crm_references', ['id' => $record->id, 'name' => 'Inactive university', 'is_active' => false]);
    }

    public function test_escapes_reference_text_and_starter_seeder_is_repeatable_without_permitted_transitions(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        CrmReference::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->actingAs($super)->get('/admin/crm/references')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $this->seed(CrmReferenceSeeder::class);
        $this->seed(CrmReferenceSeeder::class);
        $this->assertSame(10, DB::table('crm_references')->whereIn('kind', CrmReference::STATUS_KINDS)->count());
        $this->assertDatabaseCount('crm_status_transitions', 0);
    }

    private function payload(): array
    {
        return ['kind' => 'university', 'code' => 'example', 'name' => 'Example university', 'country' => 'Germany', 'is_active' => 1];
    }
}
