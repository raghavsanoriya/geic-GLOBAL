<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CrmContactFactory extends Factory
{
    public function definition(): array
    {
        return ['creation_key' => fake()->uuid(), 'name' => 'Example business contact', 'company' => 'Example education partner',
            'contact_type' => 'University partner', 'email' => fake()->safeEmail(), 'country' => 'India', 'city' => 'Indore', 'version' => 1, 'is_active' => true];
    }
}
