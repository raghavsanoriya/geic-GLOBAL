<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class MobileCatalogControllerTest extends TestCase
{
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
}
