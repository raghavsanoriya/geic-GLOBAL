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
            ->assertOk()
            ->assertSee('Login', false);
    }

    public function test_unknown_pages_return_not_found(): void
    {
        $this->get('/this-page-does-not-exist.html')->assertNotFound();
    }
}
