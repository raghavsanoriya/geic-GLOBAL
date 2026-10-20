<?php

namespace Database\Factories;

use App\Models\CrmStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmInvoiceFactory extends Factory
{
    public function definition(): array
    {
        return ['student_id' => CrmStudent::factory(), 'creation_key' => fake()->uuid(), 'number' => 'TEST-'.fake()->unique()->numerify('########'),
            'bill_to' => 'Example commission partner', 'description' => 'Commission invoice', 'currency' => 'INR',
            'base_minor' => 100000, 'tax_bps' => 1800, 'tax_minor' => 18000, 'total_minor' => 118000,
            'status' => 'draft', 'due_on' => now()->addMonth()->toDateString(), 'version' => 1];
    }
}
