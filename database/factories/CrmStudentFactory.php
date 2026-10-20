<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<CrmStudent>
 */
class CrmStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_enquiry_id' => fn () => DB::table('counselling_enquiries')->insertGetId([
                'full_name' => 'Test student', 'email' => fake()->unique()->safeEmail(),
                'phone' => '9876543210', 'destination' => 'Germany', 'city' => 'Indore',
                'study_level' => 'Postgraduate', 'preferred_intake' => 'Next available intake',
                'english_test' => 'Not sure yet', 'source_page' => '/contact', 'source' => 'website',
                'created_at' => now(), 'updated_at' => now(),
            ]),
            'full_name' => fake()->name(), 'email' => fake()->unique()->safeEmail(),
            'phone' => '+919876543210', 'stage' => 'lead', 'lead_status' => 'new',
            'is_active' => true, 'profile' => ['city' => 'Indore'],
            'version' => 1,
        ];
    }
}
