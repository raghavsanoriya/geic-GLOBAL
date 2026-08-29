<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudyPlannerPageTest extends TestCase
{
    public function test_study_planner_page_is_available_and_linked_from_the_footer(): void
    {
        $this->get('/study-planner')
            ->assertOk()
            ->assertSee('Build a study plan that moves with you.', false)
            ->assertSee('data-planner-form', false)
            ->assertSee('Build my study plan', false);

        $this->get('/')
            ->assertSee('/study-planner', false)
            ->assertSee('Study Planner', false);
    }
}
