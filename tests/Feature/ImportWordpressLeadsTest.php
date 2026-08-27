<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ImportWordpressLeadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_wordpress_leads_are_mapped_and_imported_idempotently(): void
    {
        $legacyDatabase = database_path('legacy-wordpress-test.sqlite');
        touch($legacyDatabase);
        config(['database.connections.legacy_wordpress' => [
            'driver' => 'sqlite',
            'database' => $legacyDatabase,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);
        DB::purge('legacy_wordpress');

        Schema::connection('legacy_wordpress')->create('wplb_cf7anyapi_entries', function (Blueprint $table): void {
            $table->id();
            $table->integer('form_id');
            $table->integer('data_id');
            $table->string('field_name');
            $table->text('field_value')->nullable();
            $table->timestamp('date');
        });

        $date = '2024-03-02 09:15:00';
        foreach ([
            'text-136' => 'Historical Student',
            'email-171' => 'history@example.com',
            'text-138' => 'Indore',
            'text-139' => '+91 90000 00000',
            'menu-510' => 'Australia',
            'text-140' => 'Please share course options.',
            'submitted_from' => 'https://geic.in/contact/',
            'User_IP' => '192.0.2.10',
        ] as $field => $value) {
            DB::connection('legacy_wordpress')->table('wplb_cf7anyapi_entries')->insert([
                'form_id' => 2174,
                'data_id' => 8,
                'field_name' => $field,
                'field_value' => $value,
                'date' => $date,
            ]);
        }

        $this->artisan('legacy:import-wordpress-leads')->assertSuccessful();
        $this->artisan('legacy:import-wordpress-leads')->assertSuccessful();

        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseHas('counselling_enquiries', [
            'source' => 'wordpress',
            'external_id' => '2174:8',
            'full_name' => 'Historical Student',
            'destination' => 'Australia',
            'source_form' => 'Contact Form Home',
        ]);

        $metadata = json_decode((string) DB::table('counselling_enquiries')->value('metadata'), true);
        $this->assertArrayHasKey('legacy_ip_hash', $metadata);
        $this->assertArrayNotHasKey('User_IP', $metadata);

        Schema::connection('legacy_wordpress')->drop('wplb_cf7anyapi_entries');
        @unlink($legacyDatabase);
    }
}
