<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StudyAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_include_the_study_assistant_widget(): void
    {
        $this->get('/')->assertOk()->assertSee('Trans Globe Study Assistant')->assertSee('tgAssistantLauncher');
    }

    public function test_assistant_returns_a_guided_reply_without_an_api_key(): void
    {
        Config::set('services.study_assistant.api_key', null);

        $this->postJson(route('study-assistant.chat'), [
            'message' => 'Which English test should I take?',
        ])->assertOk()
            ->assertJsonStructure(['reply', 'source'])
            ->assertJsonPath('source', 'guided');
    }

    public function test_guided_replies_use_catalogue_facts_for_destination_questions(): void
    {
        Config::set('services.study_assistant.api_key', null);

        $response = $this->postJson(route('study-assistant.chat'), [
            'message' => 'What does Australia cost?',
        ])->assertOk();

        $this->assertStringContainsString('AUD 24K–40K', (string) $response->json('reply'));
    }
}
