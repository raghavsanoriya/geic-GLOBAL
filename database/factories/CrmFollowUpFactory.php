<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmFollowUpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => CrmStudent::factory(), 'creation_key' => fake()->uuid(),
            'subject' => 'Review study plans', 'channel' => 'phone', 'status' => 'new',
            'due_at' => now()->addDay(), 'version' => 1,
        ];
    }
}
