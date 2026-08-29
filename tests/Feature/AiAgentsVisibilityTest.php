<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiAgentsVisibilityTest extends TestCase
{
    public function test_internal_ai_agents_page_is_not_publicly_reachable(): void
    {
        $this->get('/ai-agents')->assertNotFound();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('/ai-agents', false)
            ->assertDontSee('>AI Agents<', false);
    }
}
