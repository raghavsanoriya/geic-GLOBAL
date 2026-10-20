<?php

namespace Tests\Feature;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Database\Seeders\CrmWorkflowTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmLifecycleConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        Http::preventStrayRequests();
    }

    public function test_workflow_template_is_repeatable_preserves_edits_and_installs_verified_sections(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->assertSame(4, CrmReference::where('kind', 'level')->count());
        $this->assertSame(33, CrmReference::where('kind', 'level_status')->count());
        $this->assertSame(30, CrmReference::where('kind', 'application_status')->count());
        $this->assertSame(8, CrmReference::where('kind', 'visa_status')->count());
        $pending = CrmReference::where('code', 'workflow_app_1')->firstOrFail();
        $next = CrmReference::where('code', 'workflow_app_2')->firstOrFail();
        $this->assertDatabaseHas('crm_status_transitions', ['from_id' => $pending->id, 'to_id' => $next->id]);
        $count = DB::table('crm_status_transitions')->count();
        $pending->update(['name' => 'GEIC custom pending', 'is_active' => false]);
        DB::table('crm_status_transitions')->where('from_id', $pending->id)->delete();
        $remaining = DB::table('crm_status_transitions')->count();
        $this->assertLessThan($count, $remaining);
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->assertSame($remaining, DB::table('crm_status_transitions')->count());
        $this->assertSame('GEIC custom pending', $pending->fresh()->name);
        $this->assertFalse($pending->fresh()->is_active);
        $this->assertDatabaseCount('crm_students', 0);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get('/admin/crm/references?kind=level')->assertOk()->assertSee('Manage level statuses');
        $this->get('/admin/crm/references/'.$pending->id)->assertOk()->assertSee('Allowed next statuses');
        $this->get('/admin/crm/students')->assertOk()->assertSee('Application · New')->assertSee('Application · Working')->assertSee('VISA · Working');
        Http::assertNothingSent();
    }

    public function test_level_status_is_scoped_and_transfer_requires_explicit_confirmation_without_child_creation(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $transfer = CrmReference::where('kind', 'level_status')->where('code', 'lead_7')->firstOrFail();
        $wrong = CrmReference::where('kind', 'level_status')->where('code', 'visa_1')->firstOrFail();
        $url = '/admin/crm/students/'.$student->id;
        $payload = ['version' => 1, 'full_name' => $student->full_name, 'email' => $student->email, 'phone' => $student->phone, 'stage' => 'lead', 'lead_status' => 'new', 'is_active' => true];
        $this->patchJson($url, [...$payload, 'stage_status_id' => $wrong->id])->assertUnprocessable()->assertJsonValidationErrors('stage_status_id');
        $this->patchJson($url, [...$payload, 'stage_status_id' => $transfer->id])->assertOk()->assertJsonPath('data.stage', 'lead');
        $this->patchJson($url, [...$payload, 'version' => 2, 'stage_status_id' => $transfer->id, 'apply_status_transfer' => true])->assertOk()->assertJsonPath('data.stage', 'application')->assertJsonPath('data.stage_status_id', CrmReference::defaultStatusId('application'));
        $this->assertDatabaseHas('crm_student_stage_events', ['student_id' => $student->id, 'from_stage' => 'lead', 'to_stage' => 'application']);
        $this->assertDatabaseCount('crm_applications', 0);
        $this->assertDatabaseCount('crm_visas', 0);
        $this->assertDatabaseCount('crm_invoices', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $this->patchJson($url, [...$payload, 'stage' => 'application', 'version' => 2])->assertConflict();
        Http::assertNothingSent();
    }

    public function test_agent_business_details_and_protected_values_are_validated_encrypted_and_never_echoed(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $payload = ['kind' => 'agent', 'code' => 'test_agent', 'name' => 'Test Agent', 'is_active' => true,
            'configuration' => ['organisation_name' => 'Test company', 'state' => 'Madhya Pradesh', 'contact_phone' => '9876543210', 'portal_url' => 'https://portal.example.test'],
            'restricted' => ['portal_username' => 'test-user', 'portal_password' => 'test-only-protected-value', 'pan_number' => 'TESTPAN123']];
        $id = $this->postJson('/admin/crm/references', $payload)->assertOk()->assertJsonMissingPath('data.restricted_details')->json('data.id');
        $record = CrmReference::findOrFail($id);
        $this->assertSame('test-only-protected-value', $record->restricted_details['portal_password']);
        $this->assertStringNotContainsString('test-only-protected-value', DB::table('crm_references')->where('id', $id)->value('restricted_details'));
        $this->get('/admin/crm/references/'.$id)->assertOk()->assertDontSee('test-only-protected-value')->assertSee('Protected agent details');
        $this->getJson('/admin/crm/references?kind=agent')->assertJsonMissingPath('data.0.restricted_details');
        $this->assertStringNotContainsString('test-only-protected-value', DB::table('crm_reference_events')->value('changes'));
        $this->patchJson('/admin/crm/references/'.$id, ['version' => 1, 'name' => $record->name, 'is_active' => true, 'restricted' => ['portal_password' => null]])->assertOk();
        $this->assertSame('test-only-protected-value', $record->fresh()->restricted_details['portal_password']);
        $limited = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['crm.view', 'crm.configure']]);
        $this->actingAs($limited)->postJson('/admin/crm/references', $payload)->assertForbidden();
        $this->actingAs($admin)->postJson('/admin/crm/references', [...$payload, 'code' => 'other', 'configuration' => ['portal_url' => 'https://user:secret@example.test']])->assertUnprocessable();
    }

    public function test_configured_names_and_dictionaries_drive_forms_without_erasing_legacy_values(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $level = CrmReference::where('kind', 'level')->where('code', 'application')->firstOrFail();
        $this->patchJson('/admin/crm/references/'.$level->id, ['version' => 1, 'name' => 'University applications', 'is_active' => true])->assertOk();
        $student = CrmStudent::factory()->create(['profile' => ['country' => 'Legacy country']]);
        CrmReference::factory()->create(['kind' => 'country', 'name' => 'Germany']);
        CrmReference::factory()->create(['kind' => 'qualification', 'name' => 'Masters']);
        CrmReference::factory()->create(['kind' => 'category', 'name' => 'Website']);
        CrmReference::factory()->create(['kind' => 'reference', 'name' => 'Education fair']);
        $this->get('/admin/crm/students/'.$student->id)->assertOk()->assertSee('University applications')->assertSee('Legacy country (existing value)')->assertSee('Education fair')->assertSee('Website');
        $payload = ['version' => 1, 'full_name' => $student->full_name, 'phone' => $student->phone, 'stage' => 'lead', 'lead_status' => 'new', 'is_active' => true];
        $this->patchJson('/admin/crm/students/'.$student->id, [...$payload, 'profile' => ['country' => 'Unconfigured country']])->assertUnprocessable()->assertJsonValidationErrors('profile.country');
        $this->patchJson('/admin/crm/students/'.$student->id, [...$payload, 'profile' => ['country' => 'Legacy country']])->assertOk();
        $this->postJson('/admin/crm/references', ['kind' => ['bad'], 'name' => 'Invalid', 'code' => 'invalid', 'is_active' => true])->assertUnprocessable();
        $this->postJson('/admin/crm/references', ['kind' => 'agent', 'code' => 'bad_url', 'name' => 'Invalid URL', 'is_active' => true, 'configuration' => ['portal_url' => ['bad']]])->assertUnprocessable();
    }

    public function test_used_level_status_cannot_be_reparented_and_status_can_be_explicitly_cleared(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $status = CrmReference::where('kind', 'level_status')->where('code', 'lead_1')->firstOrFail();
        $student = CrmStudent::factory()->create(['stage_status_id' => $status->id]);
        $level = CrmReference::where('kind', 'level')->where('code', 'visa')->firstOrFail();
        $this->patchJson('/admin/crm/references/'.$status->id, ['version' => 1, 'name' => $status->name, 'is_active' => true, 'parent_id' => $level->id])->assertUnprocessable()->assertJsonValidationErrors('parent_id');
        $this->patchJson('/admin/crm/students/'.$student->id, ['version' => 1, 'full_name' => $student->full_name, 'phone' => $student->phone, 'stage' => 'lead', 'lead_status' => 'new', 'is_active' => true, 'stage_status_id' => null])->assertOk()->assertJsonPath('data.stage_status_id', null);
    }

    public function test_template_installation_requires_permission_and_confirmation_and_list_rules_apply_to_exports(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $this->postJson('/admin/crm/references/install-workflow-template', [])->assertUnprocessable();
        $this->postJson('/admin/crm/references/install-workflow-template', ['confirm' => true])->assertOk();
        $this->assertSame(75, CrmReference::count());
        $this->assertDatabaseHas('crm_reference_events', ['actor_id' => $admin->id, 'action' => 'reference.created']);
        $dead = CrmReference::where('code', 'lead_5')->firstOrFail();
        $profile = CrmStudent::factory()->create(['full_name' => 'Configured dead profile', 'stage_status_id' => $dead->id]);
        CrmStudent::factory()->create(['full_name' => 'Main profile']);
        CrmStudent::factory()->create(['full_name' => 'Legacy dead profile', 'lead_status' => 'dead']);
        $this->getJson('/admin/crm/students?list=dead')->assertOk()->assertJsonPath('total', 2);
        $this->getJson('/admin/crm/students?list=main')->assertJsonPath('total', 1);
        $this->getJson('/admin/crm/students?stage_status_id='.$dead->id)->assertJsonPath('total', 1)->assertJsonPath('data.0.id', $profile->id);
        $this->postJson(route('admin.crm.students.views'), ['name' => 'Dead review', 'filters' => ['list' => 'dead', 'stage_status_id' => $dead->id]])->assertOk();
        $csv = $this->get(route('admin.crm.students.export', ['list' => 'dead']))->assertOk()->streamedContent();
        $this->assertStringContainsString('Configured dead profile', $csv);
        $this->assertStringNotContainsString('Main profile', $csv);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($staff)->postJson('/admin/crm/references/install-workflow-template', ['confirm' => true])->assertForbidden();
    }

    public function test_manual_leads_use_configured_values_and_default_status_without_forwarding_to_foris(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        foreach (['country' => 'Germany', 'qualification' => 'Masters', 'category' => 'Website', 'reference' => 'Education fair'] as $kind => $name) {
            CrmReference::factory()->create(['kind' => $kind, 'name' => $name]);
        }
        $this->get('/admin/crm/leads/create')->assertOk()->assertSee('Education fair')->assertSee('Masters');
        $data = ['creation_key' => (string) Str::uuid(), 'full_name' => 'Manual configured student', 'phone' => '9876543210', 'country' => 'Germany', 'study_level' => 'Masters', 'lead_category' => 'Website', 'source_reference' => 'Education fair'];
        $this->postJson('/admin/crm/leads', [...$data, 'country' => 'Unknown country'])->assertUnprocessable()->assertJsonValidationErrors('country');
        $id = $this->postJson('/admin/crm/leads', $data)->assertCreated()->json('data.student_id');
        $student = CrmStudent::findOrFail($id);
        $this->assertSame(CrmReference::defaultStatusId('lead'), $student->stage_status_id);
        $this->assertSame('Website', $student->profile['lead_category']);
        $this->assertSame('Education fair', $student->profile['source_reference']);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_default_changes_are_unique_audited_and_do_not_rewrite_existing_students(): void
    {
        $this->seed(CrmWorkflowTemplateSeeder::class);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $old = CrmReference::where('code', 'lead_1')->firstOrFail();
        $next = CrmReference::where('code', 'lead_2')->firstOrFail();
        $student = CrmStudent::factory()->create(['stage_status_id' => $old->id]);
        $this->patchJson('/admin/crm/references/'.$next->id, ['version' => 1, 'name' => $next->name, 'is_active' => true, 'parent_id' => $next->parent_id, 'configuration' => ['is_default' => true]])->assertOk();
        $this->assertSame($next->id, CrmReference::defaultStatusId('lead'));
        $this->assertFalse($old->fresh()->configuration['is_default']);
        $this->assertSame($old->id, $student->fresh()->stage_status_id);
        $this->assertDatabaseHas('crm_reference_events', ['reference_id' => $old->id, 'action' => 'reference.updated']);
        $this->patchJson('/admin/crm/references/'.$next->id, ['version' => 2, 'name' => $next->name, 'is_active' => false, 'parent_id' => $next->parent_id])->assertUnprocessable()->assertJsonValidationErrors('is_active');
    }
}
