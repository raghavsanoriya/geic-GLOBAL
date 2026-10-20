<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CrmTaskFactory extends Factory
{
    public function definition(): array
    {
        return ['creation_key' => fake()->uuid(), 'title' => 'Review student checklist', 'description' => 'Prepare the next counselling steps.',
            'status' => 'new', 'priority' => 'normal', 'starts_at' => now(), 'due_at' => now()->addDay(), 'version' => 1];
    }
}
