<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CrmReferenceFactory extends Factory
{
    public function definition(): array
    {
        return ['kind' => 'university', 'code' => fake()->unique()->slug(2), 'name' => 'Example university', 'is_active' => true, 'version' => 1];
    }
}
