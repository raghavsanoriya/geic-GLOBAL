<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmDocumentFactory extends Factory
{
    public function definition(): array
    {
        return ['creation_key' => fake()->uuid(), 'student_id' => CrmStudent::factory(), 'category' => 'education', 'label' => 'Test transcript', 'path' => fake()->uuid().'.pdf', 'mime' => 'application/pdf', 'bytes' => 30, 'sha256' => hash('sha256', 'test'), 'version' => 1];
    }
}
