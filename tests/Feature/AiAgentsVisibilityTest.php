<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiAgentsVisibilityTest extends TestCase
{
    public function test_ai_agents_catalog_is_publicly_reachable(): void
    {
        $this->get('/ai-agents')
            ->assertOk()
            ->assertSee('AI Agents')
            ->assertSee('Web Research Agent')
            ->assertSee('Customer Support Agent');

        $this->get('/')
            ->assertOk()
            ->assertSee('/ai-agents', false)
            ->assertSee('AI Agents', false);
    }
}
