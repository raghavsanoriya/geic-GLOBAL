<?php

namespace Database\Factories;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmApplicationFactory extends Factory
{
    public function definition(): array
    {
        return ['student_id' => CrmStudent::factory(), 'creation_key' => fake()->uuid(),
            'university_id' => CrmReference::factory(), 'status_id' => CrmReference::factory()->state(['kind' => 'application_status']),
            'course' => 'Example study program', 'intake' => 'September 2027', 'tags' => [], 'version' => 1];
    }
}
