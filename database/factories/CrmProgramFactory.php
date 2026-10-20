<?php

namespace Database\Factories;

use App\Models\CrmReference;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmProgramFactory extends Factory
{
    public function definition(): array
    {
        return ['code' => fake()->unique()->slug(2), 'name' => 'Example postgraduate program',
            'university_id' => CrmReference::factory(), 'country' => 'Germany', 'study_level' => 'Postgraduate',
            'subject' => 'Business', 'is_active' => true, 'version' => 1];
    }
}
