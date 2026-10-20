<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmCampaignFactory extends Factory
{
    public function definition(): array
    {
        return ['creation_key' => fake()->uuid(), 'created_by' => User::factory(), 'name' => 'Example campaign', 'channel' => 'email', 'subject' => 'Study guidance', 'body' => 'An update from GEIC Indore.', 'audience' => ['mode' => 'students'], 'version' => 1];
    }
}
