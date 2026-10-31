<?php

namespace Tests\Feature;

use App\Models\CrmImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditRetainedForisDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reports_only_structural_metadata_and_never_row_values(): void
    {
        config(['crm.enabled' => true]);
        $user = User::factory()->create(['is_admin' => true]);
        $import = CrmImport::create([
            'creation_key' => (string) Str::uuid(),
            'created_by' => $user->id,
            'source' => 'Foris historical records',
            'entity_type' => 'lead',
            'status' => 'completed',
            'mapping' => ['source_id' => 'Lead ID', 'full_name' => 'Student Name'],
            'headers' => ['Lead ID', 'Student Name', "Application\nStatus", 'API key'],
            'row_count' => 2,
            'version' => 1,
        ]);
        foreach ([
            ['Lead ID' => 'secret-lead-1', 'Student Name' => 'Private Person', 'Application Status' => 'Offer', 'API key' => 'must-not-leak'],
            ['Lead ID' => 'secret-lead-2', 'Student Name' => '', 'Application Status' => 'Visa'],
        ] as $index => $payload) {
            DB::table('crm_import_rows')->insert([
                'import_id' => $import->id,
                'row_number' => $index + 2,
                'payload' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
                'status' => 'imported',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->assertSame(0, Artisan::call('crm:audit-retained-foris', ['--json' => true]));
        $output = Artisan::output();
        $this->assertStringNotContainsString('secret-lead-1', $output);
        $this->assertStringNotContainsString('Private Person', $output);
        $this->assertStringNotContainsString('must-not-leak', $output);
        $report = json_decode(trim($output), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($import->id, $report['batches'][0]['batch_id']);
        $columns = collect($report['batches'][0]['columns'])->keyBy('header');
        $this->assertSame(['header' => 'Lead ID', 'mapped_to' => 'source_id', 'non_empty' => 2, 'sensitive_label' => false], $columns['Lead ID']);
        $this->assertSame(['header' => 'Application Status', 'mapped_to' => null, 'non_empty' => 0, 'sensitive_label' => false], $columns['Application Status']);
        $this->assertSame(['header' => 'API key', 'mapped_to' => null, 'non_empty' => 1, 'sensitive_label' => true], $columns['API key']);
    }
}
