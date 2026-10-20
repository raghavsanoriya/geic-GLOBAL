<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmProfileEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['creation_key' => fake()->uuid(), 'student_id' => CrmStudent::factory(), 'kind' => 'education', 'details' => ['institution' => 'Example university', 'qualification' => 'Bachelor'], 'version' => 1];
    }
}
