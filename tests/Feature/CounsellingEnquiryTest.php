<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounsellingEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_enquiry_requires_valid_contact_details(): void
    {
        $this->from('/contact#enquiry')
            ->post('/contact/enquire', [])
            ->assertRedirect('/contact#enquiry')
            ->assertSessionHasErrors(['full_name', 'email', 'phone', 'city', 'consent']);
    }

    public function test_contact_enquiry_is_stored(): void
    {
        $this->post('/contact/enquire', [
            'full_name' => 'Test Student',
            'email' => 'student@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'Next available intake',
            'preferred_course' => 'Business Analytics',
            'english_test' => 'Planning to take a test',
            'message' => 'Please contact me.',
            'consent' => '1',
        ])->assertRedirect('/contact#enquiry');

        $this->assertDatabaseHas('counselling_enquiries', [
            'destination' => 'General enquiry',
            'email' => 'student@example.com',
            'source_page' => '/contact',
        ]);
    }

    public function test_landing_page_profile_request_is_stored(): void
    {
        $this->postJson('/landing/form-handler.php', [
            'name' => 'Landing Student',
            'phone' => '+91 98765 43210',
            'email' => 'landing@example.com',
            'qualification' => 'Bachelor’s degree',
            'passing_year' => 2026,
            'score' => '8.1 CGPA',
            'country' => 'Germany',
        ])->assertCreated()->assertJson(['success' => true]);

        $this->assertDatabaseHas('counselling_enquiries', [
            'destination' => 'Germany',
            'email' => 'landing@example.com',
            'source_page' => '/landing',
        ]);

        $this->assertDatabaseHas('site_events', [
            'event_type' => 'form_submit',
            'path' => '/landing',
        ]);
    }
}
