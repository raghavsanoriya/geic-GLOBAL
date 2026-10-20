<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmStudentOperationsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_csv_and_print_exports_use_current_access_filters_and_escape_spreadsheet_formulas(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.export']]);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'full_name' => '=FORMULA', 'stage' => 'visa', 'sensitive_profile' => ['guardian_name' => 'Restricted family']]);
        CrmStudent::factory()->create(['assigned_re_id' => $staff->id, 'full_name' => '  =WHITESPACE', 'stage' => 'visa']);
        CrmStudent::factory()->create(['full_name' => 'Outside student']);
        $csv = $this->actingAs($staff)->get('/admin/crm/student-export?stage=visa')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=FORMULA", $csv);
        $this->assertStringContainsString("'  =WHITESPACE", $csv);
        $this->assertStringNotContainsString('Outside student', $csv);
        $this->assertStringNotContainsString('Restricted family', $csv);
        $this->get('/admin/crm/student-export?format=print')->assertOk()->assertSee('=FORMULA')->assertDontSee('Outside student');
    }

    public function test_saved_searches_are_personal_allowlisted_and_do_not_grant_access(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($staff)->postJson('/admin/crm/student-views', ['name' => 'My visa list', 'filters' => ['stage' => 'visa']])->assertOk();
        $this->get('/admin/crm/students')->assertOk()->assertSee('My visa list');
        $this->postJson('/admin/crm/student-views', ['name' => 'Unsafe', 'filters' => ['redirect' => 'https://evil.example']])->assertUnprocessable();
        $other = User::factory()->create(['is_admin' => true]);
        $this->actingAs($other)->get('/admin/crm/students')->assertOk()->assertDontSee('My visa list');
        $this->assertDatabaseCount('crm_saved_views', 1);
    }

    public function test_bulk_actions_are_atomic_and_refuse_stale_or_inaccessible_profiles(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $first = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $second = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'version' => 2]);
        $outside = CrmStudent::factory()->create();
        $this->actingAs($staff)->postJson('/admin/crm/student-bulk', ['students' => [$first->id, $second->id], 'versions' => [$first->id => 1, $second->id => 1], 'action' => 'deactivate'])->assertConflict();
        $this->assertDatabaseHas('crm_students', ['id' => $first->id, 'version' => 1, 'is_active' => true]);
        $this->postJson('/admin/crm/student-bulk', ['students' => [$outside->id], 'versions' => [$outside->id => 1], 'action' => 'deactivate'])->assertNotFound();
        $this->postJson('/admin/crm/student-bulk', ['students' => [$first->id], 'versions' => [$first->id => 1], 'action' => 'deactivate'])->assertOk()->assertJsonPath('count', 1);
        $this->assertDatabaseHas('crm_students', ['id' => $first->id, 'is_active' => false, 'version' => 2]);
        $this->assertDatabaseHas('crm_audit_events', ['student_id' => $first->id, 'action' => 'student.bulk_deactivate']);
    }

    public function test_only_the_owner_can_remove_a_saved_search_without_changing_students(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $other = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $this->actingAs($staff)->postJson('/admin/crm/student-views', ['name' => 'Temporary search', 'filters' => ['stage' => 'lead']])->assertOk();
        $id = DB::table('crm_saved_views')->where('user_id', $staff->id)->value('id');
        $this->get('/admin/crm/students')->assertOk()->assertSee('Remove saved search: Temporary search');
        $this->actingAs($other)->deleteJson('/admin/crm/student-views/'.$id)->assertNotFound();
        $this->assertDatabaseCount('crm_saved_views', 1);
        $this->actingAs($staff)->deleteJson('/admin/crm/student-views/'.$id)->assertOk()->assertJsonPath('message', 'Saved search removed. Student records are unchanged.');
        $this->assertDatabaseCount('crm_saved_views', 0);
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'version' => 1]);
        $this->deleteJson('/admin/crm/student-views/'.$id)->assertNotFound();
        config(['crm.enabled' => false]);
        $this->deleteJson('/admin/crm/student-views/'.$id)->assertNotFound();
    }

    public function test_transfers_require_assignment_permission_and_validate_staff(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $data = ['students' => [$student->id], 'versions' => [$student->id => 1], 'action' => 'assign', 'assigned_counsellor_id' => $staff->id];
        $this->actingAs($staff)->postJson('/admin/crm/student-bulk', $data)->assertForbidden();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->postJson('/admin/crm/student-bulk', [...$data, 'assigned_counsellor_id' => 999999])->assertUnprocessable();
        $this->postJson('/admin/crm/student-bulk', [...$data, 'assigned_counsellor_id' => $admin->id])->assertOk();
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'assigned_counsellor_id' => $admin->id]);
    }

    public function test_bulk_re_assignment_checks_permission_versions_and_staff_without_changing_counsellor(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $inactive = User::factory()->create(['is_admin' => true, 'is_active' => false]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $data = ['action' => 'assign_re', 'students' => [$student->id], 'versions' => [$student->id => 1], 'assigned_re_id' => $admin->id];
        $this->actingAs($staff)->postJson('/admin/crm/student-bulk', $data)->assertForbidden();
        $this->actingAs($admin)->postJson('/admin/crm/student-bulk', [...$data, 'assigned_re_id' => $inactive->id])->assertUnprocessable();
        $this->postJson('/admin/crm/student-bulk', $data)->assertOk()->assertJsonPath('count', 1);
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'assigned_re_id' => $admin->id, 'assigned_counsellor_id' => $staff->id, 'version' => 2]);
        $this->assertDatabaseHas('crm_audit_events', ['student_id' => $student->id, 'action' => 'student.bulk_assign_re']);
        $this->postJson('/admin/crm/student-bulk', $data)->assertConflict();
        $this->postJson('/admin/crm/student-bulk', [...$data, 'versions' => [$student->id => 2], 'assigned_re_id' => null])->assertOk();
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'assigned_re_id' => null, 'assigned_counsellor_id' => $staff->id, 'version' => 3]);
    }

    public function test_dedicated_lead_filters_combine_and_survive_saved_searches_and_exports_without_expanding_access(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.export']]);
        $profile = ['city' => 'Indore', 'country' => 'Germany', 'qualification' => 'Bachelor', 'study_level' => 'Masters', 'lead_category' => 'Website', 'source_reference' => 'Event'];
        $student = CrmStudent::factory()->create(['full_name' => 'Filter target', 'assigned_re_id' => $staff->id, 'profile' => $profile]);
        CrmStudent::factory()->create(['full_name' => 'Outside access', 'profile' => $profile]);
        CrmStudent::factory()->create(['assigned_re_id' => $staff->id, 'profile' => [...$profile, 'country' => 'Canada']]);
        $application = CrmApplication::factory()->create(['student_id' => $student->id]);
        $filters = [...$profile, 'my_cases' => '1', 'application_status_id' => $application->status_id];
        $url = '/admin/crm/students?'.http_build_query($filters);
        $this->actingAs($staff)->getJson($url)->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.id', $student->id);
        $this->get($url)->assertOk()->assertSee('More search filters')->assertSee('value="Germany"', false);
        $this->postJson('/admin/crm/student-views', ['name' => 'Detailed search', 'filters' => $filters])->assertOk();
        $this->assertDatabaseHas('crm_saved_views', ['user_id' => $staff->id, 'name' => 'Detailed search']);
        $csv = $this->get('/admin/crm/student-export?'.http_build_query($filters))->assertOk()->streamedContent();
        $this->assertStringContainsString('Filter target', $csv);
        $this->assertStringNotContainsString('Outside access', $csv);
        $this->getJson('/admin/crm/students?student_id='.$student->id.'&enquiry_id='.$student->original_enquiry_id)->assertOk()->assertJsonPath('total', 1);
        $this->getJson('/admin/crm/students?student_id='.$student->id.'&enquiry_id=999999')->assertOk()->assertJsonPath('total', 0);
        $this->getJson('/admin/crm/students?student_id=0')->assertUnprocessable()->assertJsonValidationErrors('student_id');
        $this->postJson('/admin/crm/student-views', ['name' => 'Private data search', 'filters' => ['passport_status' => 'yes']])->assertUnprocessable();
    }

    public function test_profile_creation_date_filters_use_inclusive_india_days_and_my_cases_restricts_even_an_administrator(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $start = CrmStudent::factory()->create(['assigned_counsellor_id' => $admin->id, 'created_at' => '2026-10-19 18:30:00']);
        $end = CrmStudent::factory()->create(['assigned_re_id' => $admin->id, 'created_at' => '2026-10-20 18:29:59']);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $admin->id, 'created_at' => '2026-10-20 18:30:00']);
        CrmStudent::factory()->create(['created_at' => '2026-10-19 18:30:00']);
        $result = $this->actingAs($admin)->getJson('/admin/crm/students?created_from=2026-10-20&created_to=2026-10-20&my_cases=1')
            ->assertOk()->assertJsonPath('total', 2);
        $this->assertSame([$end->id, $start->id], array_column($result->json('data'), 'id'));
        $this->getJson('/admin/crm/students?created_to=2026-10-20')->assertOk()->assertJsonPath('total', 3);
        $this->getJson('/admin/crm/students?created_from=tomorrow')->assertUnprocessable()->assertJsonValidationErrors('created_from');
        $this->getJson('/admin/crm/students?country=%27%20OR%201%3D1')->assertOk()->assertJsonPath('total', 0);
    }
}
