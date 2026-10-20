<?php

namespace Database\Factories;

use App\Models\CrmApplication;
use App\Models\CrmReference;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmVisaFactory extends Factory
{
    public function definition(): array
    {
        return ['application_id' => CrmApplication::factory(), 'student_id' => fn (array $attributes) => CrmApplication::findOrFail($attributes['application_id'])->student_id,
            'creation_key' => fake()->uuid(), 'status_id' => CrmReference::factory()->state(['kind' => 'visa_status']),
            'visa_type' => 'Student visa', 'ready_to_bill' => false, 'version' => 1];
    }
}
