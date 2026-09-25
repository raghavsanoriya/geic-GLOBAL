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
            ->assertJsonPath('studio.STUDENT_REVIEWS', [])
            ->assertJsonPath('studio.STUDY_DESTINATIONS.0.postStudyWorkVisa', '2–3 years')
            ->assertJsonPath('studio.GLOBAL_UNIVERSITIES.0.globalRankNumber', null)
            ->assertJsonPath('studio.GLOBAL_UNIVERSITIES.0.annualTuition', 'Not provided')
            ->assertJsonPath('studio.UPCOMING_EXPO.registrationOpen', false)
            ->assertJsonPath('studio.NOTIFICATIONS', []);
        $this->assertCount(count($response->json('data.services')), $response->json('studio.GEIC_SERVICES'));
        $this->assertCount(count($response->json('data.events')), $response->json('studio.UPCOMING_EVENTS_LIST'));
        $this->assertNotEmpty($response->json('studio.VISA_ROADMAP_DATA.Australia.steps'));
    }

    public function test_studio_reads_published_blogs_and_excludes_drafts_and_future_posts(): void
    {
        $this->freezeTime();
        foreach (['published', 'draft', 'scheduled'] as $status) {
            BlogPost::create([
                'slug' => 'mobile-'.$status, 'title' => 'Mobile '.$status, 'category' => 'Admissions',
                'excerpt' => 'Catalogue test', 'image' => 'assets/example.jpg', 'read_time' => '2 min',
                'author' => 'GEIC', 'intro' => 'Current content', 'sections' => [], 'tags' => [],
                'status' => $status === 'draft' ? 'draft' : 'published',
                'published_at' => $status === 'scheduled' ? now()->addDay() : now()->subDay(),
            ]);
        }
        $response = $this->getJson(route('api.mobile.catalog'))->assertOk();
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
