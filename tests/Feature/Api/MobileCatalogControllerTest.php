<?php

namespace Tests\Feature\Api;

use App\Models\BlogPost;
use App\Models\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_the_verified_mobile_catalog(): void
    {
        $this->getJson(route('api.mobile.catalog'))
            ->assertOk()
            ->assertJsonPath('version', '2026-09-04')
            ->assertJsonPath('data.destinations.0.id', 'australia')
            ->assertJsonPath('data.services.0.id', 'expert-counselling')
            ->assertJsonStructure([
                'version',
                'updatedAt',
                'data' => [
                    'destinations' => [['id', 'name', 'tagline', 'detail', 'imageUrl', 'stat', 'highlights', 'facts', 'costs', 'intakes', 'universities']],
                    'services',
                    'events',
                    'tests',
                    'scholarships',
                    'testimonials',
                    'contact',
                ],
            ]);
    }

    public function test_studio_uses_laravel_lists_and_keeps_unknown_university_fields_empty(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 25));
        $response = $this->getJson(route('api.mobile.catalog'))->assertOk()
            ->assertJsonPath('studio.source', 'laravel')
            ->assertJsonPath('studio.STUDY_DESTINATIONS.0.id', 'australia')
            ->assertJsonCount(7, 'studio.STUDENT_REVIEWS')
            ->assertJsonPath('studio.STUDENT_REVIEWS.0.source', 'Google Reviews')
            ->assertJsonPath('studio.STUDY_DESTINATIONS.0.postStudyWorkVisa', '2–3 years')
            ->assertJsonPath('studio.GLOBAL_UNIVERSITIES.0.globalRankNumber', null)
            ->assertJsonPath('studio.GLOBAL_UNIVERSITIES.0.annualTuition', 'Not provided')
            ->assertJsonPath('studio.UPCOMING_EXPO.registrationOpen', true)
            ->assertJsonPath('studio.UPCOMING_EXPO.date', '24 October 2026')
            ->assertJsonPath('studio.UPCOMING_EXPO.venue', 'Hotel Sayaji, Scheme No. 54, Vijay Nagar, Indore')
            ->assertJsonPath('studio.NOTIFICATIONS.0.id', 'global-uni-expo-dubai-europe-2026');
        $this->assertCount(count($response->json('data.services')), $response->json('studio.GEIC_SERVICES'));
        $this->assertCount(count($response->json('data.events')), $response->json('studio.UPCOMING_EVENTS_LIST'));
        $this->assertNotEmpty($response->json('studio.VISA_ROADMAP_DATA.Australia.steps'));
        $this->assertNotSame('Not provided', $response->json('studio.GEIC_SERVICES.0.whoNeedsThis'));
        $this->assertNotEmpty($response->json('studio.GEIC_SERVICES.0.popularDestinations'));
        $this->assertNotEmpty($response->json('studio.UPCOMING_EVENTS_LIST.0.agenda'));
        $this->assertNotEmpty($response->json('studio.UPCOMING_EVENTS_LIST.0.registrationPerks'));
        $this->assertSame('Trans Globe Indore', $response->json('studio.ENGLISH_TESTS.0.testCenterIndore'));
        $this->assertSame(4.8, $response->json('studio.GEIC_BRAND.googleRating'));
        $this->assertSame(597, $response->json('studio.GEIC_BRAND.googleReviewCount'));
        $this->assertTrue($response->json('studio.UPCOMING_EVENTS_LIST.0.registrationOpen'));
    }

    public function test_studio_reads_published_blogs_and_excludes_drafts_and_future_posts(): void
    {
        $this->freezeTime();
        foreach (['published', 'draft', 'scheduled'] as $status) {
            BlogPost::create([
                'slug' => 'mobile-'.$status, 'title' => 'Mobile '.$status, 'category' => 'Admissions',
                'excerpt' => 'Catalogue test', 'image' => 'assets/example.jpg', 'read_time' => '2 min',
                'author' => 'GEIC', 'intro' => 'Current content', 'sections' => [['title' => 'Real takeaway', 'copy' => 'Published section']], 'tags' => [],
                'status' => $status === 'draft' ? 'draft' : 'published',
                'published_at' => $status === 'scheduled' ? now()->addDay() : now()->subDay(),
            ]);
        }
        $response = $this->getJson(route('api.mobile.catalog'))->assertOk();
        $this->assertSame(['Real takeaway'], $response->json('studio.GEIC_BLOGS.0.keyTakeaways'));
        $slugs = array_column($response->json('studio.GEIC_BLOGS'), 'slug');
        $this->assertContains('mobile-published', $slugs);
        $this->assertNotContains('mobile-draft', $slugs);
        $this->assertNotContains('mobile-scheduled', $slugs);
    }

    public function test_contact_changes_are_visible_in_the_mobile_catalogue(): void
    {
        SiteContent::create(['page_key' => 'home', 'field_key' => 'contact_phone',
            'label' => 'Phone', 'type' => 'text', 'value' => '+91 90000 00000', 'published_value' => '+91 90000 00000']);
        $this->getJson(route('api.mobile.catalog'))->assertOk()
            ->assertJsonPath('studio.GEIC_BRAND.phone', '+91 90000 00000');
    }
}
