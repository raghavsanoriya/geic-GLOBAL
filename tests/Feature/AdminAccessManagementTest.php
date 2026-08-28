<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_administrator_can_create_a_team_member_with_a_role_preset(): void
    {
        $superAdmin = $this->admin();

        $this->actingAs($superAdmin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Team access')
            ->assertSee('Create user');

        $this->actingAs($superAdmin)
            ->post('/admin/users', [
                'name' => 'Indore Counsellor',
                'email' => 'counsellor@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
                'admin_role' => 'counsellor',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $member = User::query()->where('email', 'counsellor@example.com')->firstOrFail();

        $this->assertSame('counsellor', $member->admin_role);
        $this->assertSame(['enquiries.view', 'enquiries.export'], $member->admin_permissions);
        $this->assertTrue($member->is_active);
    }

    public function test_counsellor_can_review_leads_but_cannot_manage_content_media_or_users(): void
    {
        $counsellor = $this->admin([
            'admin_role' => 'counsellor',
            'admin_permissions' => ['enquiries.view', 'enquiries.export'],
        ]);

        $this->actingAs($counsellor)->get('/admin')->assertOk()->assertSee('Latest student enquiries');
        $this->actingAs($counsellor)
            ->get('/admin/enquiries')
            ->assertOk()
            ->assertSee('Student enquiries')
            ->assertSee('All student enquiries');
        $this->actingAs($counsellor)
            ->get('/admin/enquiries/export')
            ->assertOk()
            ->assertSee('Prepare your export')
            ->assertSee('Download CSV');
        $this->actingAs($counsellor)->get('/admin/export')->assertOk();
        $this->actingAs($counsellor)->get('/admin/pages')->assertForbidden();
        $this->actingAs($counsellor)->get('/admin/media')->assertForbidden();
        $this->actingAs($counsellor)->get('/admin/users')->assertForbidden();
    }

    public function test_content_editor_dashboard_does_not_expose_student_enquiry_data(): void
    {
        DB::table('counselling_enquiries')->insert([
            'destination' => 'Australia',
            'full_name' => 'Private Student Name',
            'email' => 'private-student@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'February 2027',
            'preferred_course' => 'Data Science',
            'english_test' => 'IELTS',
            'source_page' => '/contact',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $editor = $this->admin([
            'admin_role' => 'content_editor',
            'admin_permissions' => ['content.manage', 'media.manage'],
        ]);

        $this->actingAs($editor)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Your workspace is ready')
            ->assertDontSee('Private Student Name')
            ->assertDontSee('private-student@example.com');

        $this->actingAs($editor)->get('/admin/pages')->assertOk();
        $this->actingAs($editor)->get('/admin/media')->assertOk();
        $this->actingAs($editor)->get('/admin/export')->assertForbidden();
    }

    public function test_profile_password_and_dashboard_preferences_can_be_updated(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/profile', [
                'name' => 'Updated Administrator',
                'email' => 'updated-admin@example.com',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->put('/admin/profile/password', [
                'current_password' => 'password',
                'password' => 'NewSecurePass123',
                'password_confirmation' => 'NewSecurePass123',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->put('/admin/settings', [
                'default_screen' => 'pages',
                'sidebar_collapsed' => '1',
                'email_notifications' => '1',
            ])
            ->assertRedirect();

        $admin->refresh();
        $this->assertSame('Updated Administrator', $admin->name);
        $this->assertTrue(Hash::check('NewSecurePass123', $admin->password));
        $this->assertSame([
            'default_screen' => 'pages',
            'sidebar_collapsed' => true,
            'email_notifications' => true,
        ], $admin->admin_preferences);
    }

    public function test_last_active_super_administrator_cannot_remove_their_own_access(): void
    {
        $superAdmin = $this->admin();

        $this->actingAs($superAdmin)
            ->from('/admin/users/'.$superAdmin->id.'/edit')
            ->put('/admin/users/'.$superAdmin->id, [
                'name' => $superAdmin->name,
                'email' => $superAdmin->email,
                'password' => '',
                'password_confirmation' => '',
                'admin_role' => 'administrator',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/users/'.$superAdmin->id.'/edit')
            ->assertSessionHasErrors('admin_role');

        $this->actingAs($superAdmin)
            ->from('/admin/users/'.$superAdmin->id.'/edit')
            ->put('/admin/users/'.$superAdmin->id, [
                'name' => $superAdmin->name,
                'email' => $superAdmin->email,
                'password' => '',
                'password_confirmation' => '',
                'admin_role' => 'super_admin',
            ])
            ->assertSessionHasErrors('is_active');
    }

    public function test_inactive_administrator_cannot_sign_in_or_open_the_dashboard(): void
    {
        $inactive = $this->admin(['is_active' => false]);

        $this->post('/admin/login', [
            'email' => $inactive->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($inactive)->get('/admin')->assertForbidden();
    }

    public function test_page_editor_has_a_predictable_top_back_button(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/pages/home')
            ->assertOk()
            ->assertSee('topbar-back', false)
            ->assertSee('Back to pages');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function admin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
            'admin_role' => 'super_admin',
            'admin_permissions' => [],
        ], $overrides));
    }
}
