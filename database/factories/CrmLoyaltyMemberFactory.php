<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmLoyaltyMemberFactory extends Factory
{
    public function definition(): array
    {
        return ['student_id' => CrmStudent::factory(), 'name' => 'Example loyalty member', 'is_active' => true, 'version' => 1];
    }
}
