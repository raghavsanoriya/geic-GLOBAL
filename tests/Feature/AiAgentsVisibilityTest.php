<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiAgentsVisibilityTest extends TestCase
{
    public function test_ai_agents_catalog_is_publicly_reachable(): void
    {
        $this->get('/ai-agents')
            ->assertRedirect('/admin/login');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('/ai-agents', false);
    }
}
