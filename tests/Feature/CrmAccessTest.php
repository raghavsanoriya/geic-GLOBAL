<?php

namespace Tests\Feature;

use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrmAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_requires_authentication_and_crm_view_permission(): void
    {
        $this->getJson('/admin/crm/students')->assertUnauthorized();
        $this->get('/admin/crm/students')->assertRedirect(route('admin.login'));
        $editor = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['content.manage']]);

        $this->actingAs($editor)->getJson('/admin/crm/students')->assertForbidden();
    }

    #[DataProvider('visibilityRoles')]
    public function test_all_case_access_depends_on_role_permission_and_account_status(string $role, array $permissions, bool $active, bool $admin, bool $expected): void
    {
        $user = User::factory()->create(['admin_role' => $role, 'admin_permissions' => $permissions, 'is_active' => $active, 'is_admin' => $admin]);

        $this->assertSame($expected, CrmAccess::viewsAll($user));
    }

    public static function visibilityRoles(): array
    {
        return [
            'super administrator' => ['super_admin', [], true, true, true],
            'administrator' => ['administrator', ['crm.view'], true, true, true],
            'explicit all-case permission' => ['custom', ['crm.view', 'crm.view_all'], true, true, true],
            'assigned counsellor' => ['counsellor', ['crm.view'], true, true, false],
            'assigned RE' => ['relationship_executive', ['crm.view'], true, true, false],
            'inactive administrator' => ['super_admin', [], false, true, false],
            'ordinary user' => ['super_admin', [], true, false, false],
        ];
    }

    public function test_counsellors_and_res_see_only_their_assignments_even_when_searching(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $counsellorCase = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $reCase = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $other = CrmStudent::factory()->create(['full_name' => 'Not accessible student']);

        $response = $this->actingAs($staff)->getJson('/admin/crm/students');

        $response->assertJsonCount(2, 'data')->assertJsonPath('data.0.id', $reCase->id)->assertJsonPath('data.1.id', $counsellorCase->id);
        $response->assertJsonMissingPath('data.0.sensitive_profile');
        $this->getJson('/admin/crm/students?q=Not%20accessible')->assertJsonCount(0, 'data');
        $this->getJson('/admin/crm/students/'.$other->id)->assertNotFound();
        $this->patchJson('/admin/crm/students/'.$other->id, [])->assertNotFound();
        $this->assertDatabaseCount('crm_audit_events', 0);
    }

    public function test_legacy_enquiry_routes_cannot_bypass_assigned_case_access_when_crm_is_enabled(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor',
            'admin_permissions' => ['crm.view', 'enquiries.view', 'enquiries.review', 'enquiries.export']]);

        $this->actingAs($staff)->get('/admin/enquiries')->assertForbidden();
        $this->get('/admin/landing-page-leads')->assertForbidden();
        $this->get('/admin/duplicate-review')->assertForbidden();
        $this->get('/admin/enquiries/export')->assertForbidden();
        $this->get('/admin/export')->assertForbidden();
    }

    public function test_the_disabled_crm_does_not_change_existing_enquiry_access(): void
    {
        config(['crm.enabled' => false]);
        $super = User::factory()->create(['is_admin' => true]);

        $this->actingAs($super)->get('/admin/crm/students')->assertNotFound();
        $this->get('/admin/enquiries')->assertOk()->assertDontSee('title="Student CRM"', false);
        $this->artisan('crm:backfill-students')->assertFailed();
        $this->assertDatabaseCount('crm_students', 0);
    }

    public function test_crm_role_presets_and_staff_landing_page_are_enabled_only_with_the_crm(): void
    {
        $super = User::factory()->create(['is_admin' => true]);

        $this->actingAs($super)->post('/admin/users', [
            'name' => 'Test Indore RE', 'email' => 're@example.com', 'admin_role' => 'relationship_executive',
            'password' => 'TestingOnlyPass123', 'password_confirmation' => 'TestingOnlyPass123', 'is_active' => '1',
        ])->assertRedirect();

        $staff = User::where('email', 're@example.com')->firstOrFail();
        $this->assertSame(['crm.view', 'crm.manage'], $staff->admin_permissions);
        $this->actingAs($staff)->get('/admin/login')->assertRedirect(route('admin.crm.students.index'));
    }
}
