<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_site_activity_is_stored_without_raw_ip_addresses(): void
    {
        $this->postJson('/site-events', [
            'event_type' => 'page_view',
            'path' => '/destinations/australia',
            'referrer' => 'https://www.google.com/search?q=study+in+australia',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'australia-intake',
            'session_id' => 'browser-session-1',
        ])->assertAccepted();

        $this->assertDatabaseHas('site_events', [
            'event_type' => 'page_view',
            'path' => '/destinations/australia',
            'referrer_domain' => 'www.google.com',
            'utm_campaign' => 'australia-intake',
        ]);

        $event = \DB::table('site_events')->first();
        $this->assertNotSame('127.0.0.1', $event->visitor_hash);
        $this->assertSame(64, strlen($event->visitor_hash));
    }

    public function test_enquiry_records_campaign_attribution_and_a_conversion_event(): void
    {
        $this->post('/contact/enquire', [
            'full_name' => 'Campaign Student',
            'email' => 'campaign@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'Next available intake',
            'english_test' => 'Not sure yet',
            'consent' => '1',
            'utm_source' => 'instagram',
            'utm_medium' => 'paid-social',
            'utm_campaign' => 'august-counselling',
            'analytics_session_id' => 'browser-session-2',
        ])->assertRedirect('/contact#enquiry');

        $this->assertDatabaseHas('counselling_enquiries', [
            'email' => 'campaign@example.com',
            'source' => 'website',
            'utm_campaign' => 'august-counselling',
        ]);
        $this->assertDatabaseHas('site_events', [
            'event_type' => 'form_submit',
            'path' => '/contact',
            'utm_source' => 'instagram',
        ]);
    }
}
