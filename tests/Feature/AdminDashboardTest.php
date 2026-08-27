<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_admin_sign_in_page(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_administrator_can_sign_in_and_view_enquiries(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->post('/contact/enquire', [
            'full_name' => 'Dashboard Student',
            'email' => 'student@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'Next available intake',
            'preferred_course' => 'Business Analytics',
            'english_test' => 'Planning to take a test',
            'consent' => '1',
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'a-safe-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Trans Globe Indore LMS')
            ->assertSee('Dashboard Student');
    }

    public function test_non_administrators_cannot_sign_in_to_the_dashboard(): void
    {
        User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('a-safe-password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'student@example.com',
            'password' => 'a-safe-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_administrator_can_edit_geic_homepage_content(): void
    {
        $admin = User::create([
            'name' => 'Trans Globe Indore Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-safe-password'),
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pages')
            ->assertOk()
            ->assertSee('Manage website content');

        $this->actingAs($admin)
            ->put('/admin/pages/home', ['content' => [
                'hero_eyebrow' => 'Trusted since 1992',
                'hero_title' => 'A practical international future',
                'hero_copy' => 'Content managed from the GEIC dashboard.',
                'hero_image' => 'assets/transglobe/services/services-team.avif',
            ]])
            ->assertRedirect('/admin/pages/home');

        $this->assertDatabaseHas('site_contents', [
            'page_key' => 'home',
            'field_key' => 'hero_title',
            'value' => 'A practical international future',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('A practical international future');
    }
}
