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

    public function test_destination_pages_are_available(): void
    {
        foreach (['australia', 'new-zealand', 'uk', 'ireland', 'germany', 'europe', 'usa', 'canada', 'singapore', 'dubai', 'malaysia', 'switzerland'] as $destination) {
            $this->get('/destinations/'.$destination)->assertOk();
        }
    }
}
