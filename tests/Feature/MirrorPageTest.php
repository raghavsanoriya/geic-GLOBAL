<?php

namespace Tests\Feature;

use Tests\TestCase;

class MirrorPageTest extends TestCase
{
    public function test_homepage_is_served_by_laravel(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Trans Globe', false);
        $response->assertSee('assets/design_1/css/app.min.css', false);
        $response->assertSee('id="tg-home-offer"', false);
        $response->assertSee('Book Free Counselling', false);
    }

    public function test_downloaded_html_urls_are_supported(): void
    {
        $this->get('/login.html')
            ->assertMovedPermanently()
            ->assertRedirect('/login');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Login', false);
    }

    public function test_unknown_pages_return_not_found(): void
    {
        $this->get('/this-page-does-not-exist.html')
            ->assertMovedPermanently()
            ->assertRedirect('/this-page-does-not-exist');

        $this->get('/this-page-does-not-exist')->assertNotFound();
    }

    public function test_cached_public_path_serves_the_homepage(): void
    {
        $this->get('/public')
            ->assertOk()
            ->assertSee('Shape Your Ambition', false);
    }

    public function test_standalone_landing_page_and_assets_are_available(): void
    {
        $this->get('/landing')
            ->assertOk()
            ->assertSee('Study Abroad with the Right Guidance', false)
            ->assertSee('styles.css', false);

        $this->get('/landing/styles.css')
            ->assertOk()
            ->assertHeader('content-type', 'text/css; charset=UTF-8');

        $this->get('/landing/assets/tg-logo.svg')
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml');

        $this->get('/landing/form-handler.php')->assertNotFound();
        $this->get('/landing/../.env')->assertNotFound();
    }

    public function test_destination_pages_are_available(): void
    {
        foreach (['australia', 'new-zealand', 'uk', 'ireland', 'germany', 'europe', 'usa', 'canada', 'singapore', 'dubai', 'malaysia', 'switzerland'] as $destination) {
            $this->get('/destinations/'.$destination)->assertOk();
        }
    }

    public function test_primary_public_pages_are_available(): void
    {
        foreach (['/', '/pages/about', '/pages/terms', '/destinations', '/destinations/australia', '/services', '/events', '/scholarships', '/tests', '/contact'] as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_terms_page_uses_the_shared_trans_globe_layout(): void
    {
        $this->get('/pages/terms')
            ->assertOk()
            ->assertSee('Terms &amp; Conditions', false)
            ->assertSee('Trans Globe Indore', false)
            ->assertSee('No guarantee of outcomes', false)
            ->assertSee('Educational institutions and immigration authorities', false)
            ->assertSee('theme-footer-1__newsletter', false)
            ->assertDontSee('Rocket LMS', false)
            ->assertDontSee('Login', false)
            ->assertDontSee('Register', false);
    }

    public function test_events_listing_and_details_are_available(): void
    {
        $this->get('/events')
            ->assertSee('Meet EU Business School', false)
            ->assertSee('Indore Global Uni Expo 2026', false)
            ->assertSee('/events/meet-eu-business-school-2026', false)
            ->assertSee('assets/transglobe/events/meet-eu-business-school-2026.jpg', false);

        $this->get('/events/meet-eu-business-school-2026')
            ->assertSee('27 August 2026', false)
            ->assertSee('Four simple steps from interest to action.', false)
            ->assertSee('Register my interest', false);

        $this->get('/events/not-a-real-event')->assertNotFound();
    }

    public function test_about_page_preserves_the_complete_rocket_page_structure(): void
    {
        $this->get('/pages/about')
            ->assertOk()
            ->assertSee('About Trans Globe Indore', false)
            ->assertSee('Who we are', false)
            ->assertSee('Our mission', false)
            ->assertSee('Meet our expert education consultants.', false)
            ->assertSee('data-team-slider', false)
            ->assertSee('Pause team slider', false)
            ->assertSee('Johar Ali', false)
            ->assertSee('Student counsellor', false)
            ->assertSee('assets/transglobe/about/johar-ali.webp', false)
            ->assertSee('assets/transglobe/about/international-business-award-2023.jpeg', false)
            ->assertSee('assets/transglobe/about/student-guidance-session-2023.jpg', false)
            ->assertSee('Guidance in action', false)
            ->assertSee('assets/transglobe/about/gallery/counselling-event-01.jpg', false)
            ->assertSee('assets/transglobe/about/gallery/award-event-04.jpg', false)
            ->assertSee("font-family:'Plus Jakarta Sans'", false)
            ->assertSee('What we do', false)
            ->assertSee('Experience you can measure', false)
            ->assertSee('How we work', false)
            ->assertSee('Questions students ask', false)
            ->assertSee('theme-footer-1__newsletter', false);
    }
}
