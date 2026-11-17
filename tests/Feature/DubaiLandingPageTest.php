<?php

namespace Tests\Feature;

use App\Models\CmsPageState;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\DubaiPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DubaiLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_dubai_page_renders_default_content_and_form(): void
    {
        $this->get('/dubai')
            ->assertOk()
            ->assertSee('Study In Dubai With')
            ->assertSee('From First Call To Landing In Dubai')
            ->assertSee('action="'.route('dubai.enquire').'"', false)
            ->assertDontSee('fbevents.js');
    }

    public function test_trailing_slash_bridge_path_redirects_to_the_page(): void
    {
        $this->get('/_geic_release/dubai')->assertRedirect('/dubai');
    }

    public function test_published_cms_values_override_text_and_control_sections(): void
    {
        $this->publish([
            'hero_title' => 'Your Dubai Degree Starts With',
            'sections' => "hero\nfaq\ncontact",
            'faqs' => 'Is there a new intake? | Yes, ask our counsellor.',
        ]);

        $this->get('/dubai')
            ->assertOk()
            ->assertSee('Your Dubai Degree Starts With')
            ->assertSee('Is there a new intake?')
            ->assertDontSee('From First Call To Landing In Dubai')
            ->assertDontSee('Real Students. Real Google Reviews.');
    }

    public function test_dubai_page_is_listed_in_the_cms_editor(): void
    {
        $editor = $this->admin(['admin_permissions' => ['content.manage', 'media.manage']]);

        $this->actingAs($editor)
            ->get(route('admin.pages.edit', DubaiPage::PAGE_KEY))
            ->assertOk()
            ->assertSee('Sections shown, in order');
    }

    public function test_dubai_enquiry_is_stored_for_the_crm_with_campaign_values(): void
    {
        $this->post(route('dubai.enquire'), [
            'full_name' => 'Dubai Student',
            'email' => 'dubai.student@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Indore',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'September intake',
            'preferred_course' => 'MBA',
            'message' => 'Please call me.',
            'consent' => '1',
            'utm_source' => 'facebook',
            'utm_campaign' => 'dubai-leads',
        ])
            ->assertRedirect('/dubai#contact')
            ->assertSessionHas('enquiry_success')
            ->assertSessionHas('dubai_lead', true);

        $this->assertDatabaseHas('counselling_enquiries', [
            'destination' => 'Dubai',
            'email' => 'dubai.student@example.com',
            'source_page' => '/dubai',
            'source_form' => 'Dubai landing page enquiry',
            'utm_source' => 'facebook',
            'utm_campaign' => 'dubai-leads',
        ]);
        $this->assertDatabaseHas('site_events', ['event_type' => 'form_submit', 'path' => '/dubai', 'label' => 'Dubai']);
    }

    public function test_dubai_enquiry_requires_contact_details_and_consent(): void
    {
        $this->post(route('dubai.enquire'), [])
            ->assertRedirect('/dubai#contact')
            ->assertSessionHasErrors(['full_name', 'email', 'phone', 'study_level', 'preferred_intake', 'consent']);

        $this->assertDatabaseMissing('counselling_enquiries', ['source_page' => '/dubai']);
    }

    public function test_honeypot_submissions_are_not_stored_or_tracked(): void
    {
        $this->post(route('dubai.enquire'), [
            'full_name' => 'Bot',
            'email' => 'bot@example.com',
            'phone' => '+91 98765 43210',
            'study_level' => 'Postgraduate',
            'preferred_intake' => 'September intake',
            'consent' => '1',
            'website' => 'https://spam.example',
        ])->assertRedirect('/dubai#contact')->assertSessionMissing('dubai_lead');

        $this->assertDatabaseMissing('counselling_enquiries', ['source_page' => '/dubai']);
    }

    public function test_tracking_page_requires_ads_manage_permission(): void
    {
        $counsellor = $this->admin(['admin_role' => 'counsellor', 'admin_permissions' => ['enquiries.view']]);
        $adsManager = $this->admin(['admin_permissions' => ['ads.view', 'ads.manage', 'enquiries.view']]);

        $this->actingAs($counsellor)->get(route('admin.dubai-tracking.edit'))->assertForbidden();
        $this->actingAs($adsManager)->get(route('admin.dubai-tracking.edit'))
            ->assertOk()
            ->assertSee('Dubai lead tracking')
            ->assertSee('Latest Dubai enquiries');
    }

    public function test_enabled_tracking_renders_pixel_and_lead_event_only_after_saved_enquiry(): void
    {
        $adsManager = $this->admin(['admin_permissions' => ['ads.view', 'ads.manage']]);

        $this->actingAs($adsManager)->put(route('admin.dubai-tracking.update'), [
            'enabled' => '1',
            'meta_pixel_id' => '123456789012345',
            'google_ads_id' => 'aw-987654321',
            'google_ads_conversion_label' => 'AbC-123_x',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('1', SiteContent::valuesForPage(DubaiPage::TRACKING_KEY)['enabled']);
        $this->assertSame('AW-987654321', SiteContent::valuesForPage(DubaiPage::TRACKING_KEY)['google_ads_id']);

        $this->get('/dubai')
            ->assertOk()
            ->assertSee('fbevents.js')
            ->assertSee('"123456789012345"', false)
            ->assertSee('gtag/js?id=AW-987654321', false)
            ->assertDontSee("fbq('track', 'Lead'", false);

        $this->withSession(['dubai_lead' => true])->get('/dubai')->assertSee("fbq('track', 'Lead'", false);
    }

    public function test_tracking_cannot_be_enabled_without_a_provider_id(): void
    {
        $adsManager = $this->admin(['admin_permissions' => ['ads.view', 'ads.manage']]);

        $this->actingAs($adsManager)
            ->put(route('admin.dubai-tracking.update'), ['enabled' => '1'])
            ->assertSessionHasErrors('enabled');

        $this->get('/dubai')->assertDontSee('fbevents.js');
    }

    public function test_tracking_form_suggests_expo_ids_until_dubai_is_saved(): void
    {
        SiteContent::query()->create(['page_key' => 'integration.expo_tracking', 'field_key' => 'meta_pixel_id', 'label' => 'Meta Pixel Id', 'type' => 'text', 'value' => '555555555555', 'published_value' => '555555555555']);
        $adsManager = $this->admin(['admin_permissions' => ['ads.view', 'ads.manage']]);

        $this->actingAs($adsManager)->get(route('admin.dubai-tracking.edit'))
            ->assertOk()
            ->assertSee('555555555555')
            ->assertSee('pre-filled from');

        $this->get('/dubai')->assertDontSee('fbevents.js');
    }

    /** @param array<string, string> $values */
    private function publish(array $values): void
    {
        foreach ($values as $key => $value) {
            SiteContent::query()->create([
                'page_key' => DubaiPage::PAGE_KEY,
                'field_key' => $key,
                'label' => $key,
                'type' => 'textarea',
                'value' => $value,
                'published_value' => $value,
            ]);
        }

        CmsPageState::query()->create(['page_key' => DubaiPage::PAGE_KEY, 'status' => 'published', 'published_at' => now()]);
    }

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
