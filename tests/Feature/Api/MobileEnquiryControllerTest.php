<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileEnquiryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_payload_creates_an_enquiry_and_returns_201(): void
    {
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'event',
            'fullName' => 'Mobile Student',
            'email' => 'mobile@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'destination' => 'Australia',
            'studyLevel' => 'Postgraduate',
            'preferredIntake' => 'Next available intake',
            'referenceId' => 'meet-eu-business-school-2026',
            'platform' => 'android',
            'consent' => true,
            'unexpected' => 'must not be stored',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'reference']);

        $this->assertDatabaseHas('counselling_enquiries', [
            'full_name' => 'Mobile Student',
            'source' => 'mobile',
            'source_form' => 'event',
            'source_page' => '/mobile/event',
        ]);

        $this->assertDatabaseHas('site_events', [
            'event_type' => 'form_submit',
            'path' => '/mobile/event',
            'label' => 'meet-eu-business-school-2026',
        ]);
    }

    public function test_returns_422_and_creates_nothing_without_contact_consent(): void
    {
        $this->postJson(route('api.mobile.enquiries.store'), [
            'kind' => 'counselling',
            'fullName' => 'Mobile Student',
            'email' => 'mobile@example.com',
            'phone' => '+91 98765 43210',
            'consent' => false,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['consent']);

        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('site_events', 0);
    }
}
