<?php

namespace Tests\Feature;

use App\Models\CrmImport;
use App\Models\CrmLoyaltyMember;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\ForisLeadSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmImportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        Http::preventStrayRequests();
    }

    public function test_confirmed_foris_reference_links_existing_geic_profile_without_replacing_edits_or_forwarding_history(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create(['full_name' => 'GEIC corrected name', 'phone' => '+918765432190', 'version' => 3]);
        DB::table('foris_lead_deliveries')->insert(['enquiry_id' => $student->original_enquiry_id, 'remote_lead_id' => 'foris_123',
            'status' => 'delivered', 'created_at' => now(), 'updated_at' => now()]);
        $csv = "source_id,foris_lead_id,full_name,phone\nhistorical-123,foris_123,Old export name,9876543210\n";
        $batch = $this->upload($csv);
        $this->postJson('/admin/crm/imports/'.$batch.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $batch, 'status' => 'existing', 'student_id' => $student->id]);
        $this->assertSame('GEIC corrected name', $student->fresh()->full_name);
        $this->assertSame(3, $student->fresh()->version);
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 1);
        $this->assertDatabaseHas('foris_lead_deliveries', ['remote_lead_id' => 'foris_123', 'status' => 'delivered']);
        $again = $this->upload($csv);
        $this->postJson('/admin/crm/imports/'.$again.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('crm_import_identities', 1);
        $child = $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris test', 'entity_type' => 'education',
            'file' => UploadedFile::fake()->createWithContent('education.csv', "source_id,student_source_id,institution,qualification\neducation-old,historical-123,Original university,Bachelor\n")])->assertOk()->json('data.id');
        $this->postJson('/admin/crm/imports/'.$child.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_profile_entries', ['student_id' => $student->id, 'kind' => 'education']);
        Http::assertNothingSent();
    }

    public function test_unknown_or_ambiguous_foris_references_require_correction_and_leave_failed_deliveries_untouched(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        foreach (['delivered', 'delivered', 'failed'] as $index => $status) {
            $student = CrmStudent::factory()->create();
            DB::table('foris_lead_deliveries')->insert(['enquiry_id' => $student->original_enquiry_id, 'remote_lead_id' => $index === 2 ? 'failed_123' : 'ambiguous_123',
                'status' => $status, 'attempts' => $index === 2 ? 4 : 1, 'created_at' => now(), 'updated_at' => now()]);
        }
        $batch = $this->upload("source_id,foris_lead_id,full_name,phone\nunknown,unknown_123,Unknown,9876543210\nambiguous,ambiguous_123,Ambiguous,9876543210\nfailed,failed_123,Failed,9876543210\n");
        $this->postJson('/admin/crm/imports/'.$batch.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.invalid', 3);
        $this->assertDatabaseCount('crm_import_identities', 0);
        $this->assertDatabaseCount('crm_students', 3);
        $this->assertDatabaseCount('counselling_enquiries', 3);
        $this->assertDatabaseHas('foris_lead_deliveries', ['remote_lead_id' => 'failed_123', 'status' => 'failed', 'attempts' => 4]);
        Http::assertNothingSent();
    }

    public function test_preview_confirmation_worker_replay_and_invalid_rows_preserve_source_data_without_external_delivery(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super);
        $csv = "source_id,full_name,email,phone,submitted_at\nold-1,Imported student,imported@example.com,9876543210,2025-12-31T20:00:00Z\nold-2,Missing contact,,,\n";
        $id = $this->upload($csv);
        $preview = $this->getJson('/admin/crm/imports/'.$id)->assertOk()->assertJsonPath('rows.0.fields.source_id', 'old-1');
        $this->assertNotEmpty($preview->json('rows.1.errors'));
        $this->get('/admin/crm/imports/'.$id)->assertOk()->assertSee('Field mapping');
        $payload = DB::table('crm_import_rows')->where('import_id', $id)->value('payload');
        $this->assertStringNotContainsString('imported@example.com', $payload);
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'row_number' => 2, 'status' => 'imported']);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'row_number' => 3, 'status' => 'invalid']);
        $this->assertDatabaseHas('counselling_enquiries', ['email' => 'imported@example.com', 'created_at' => '2025-12-31 20:00:00', 'source' => 'crm_import']);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $importedId = (int) DB::table('counselling_enquiries')->where('email', 'imported@example.com')->value('id');
        $this->assertFalse(app(ForisLeadSync::class)->register($importedId, true));
        $again = $this->upload($csv);
        $this->postJson('/admin/crm/imports/'.$again.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $again, 'status' => 'existing']);
        Http::assertNothingSent();
    }

    public function test_contact_matches_enter_review_and_reversal_only_deactivates_unchanged_created_profiles(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone\none,First student,9876543210\ntwo,Matching student,9876543210\n");
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'duplicate_review']);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertDatabaseHas('crm_students', ['is_active' => false]);
        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseCount('crm_import_identities', 2);
    }

    public function test_changed_profiles_are_not_reversed_and_mapping_is_validated(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone\none,Student,9876543210\n");
        $this->patchJson('/admin/crm/imports/'.$id.'/mapping', ['version' => 1, 'mapping' => ['source_id' => 'not-a-column', 'full_name' => 'full_name', 'phone' => 'phone']])->assertUnprocessable();
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        CrmStudent::query()->update(['version' => 2]);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
        $this->assertDatabaseHas('crm_students', ['is_active' => true]);
    }

    public function test_import_access_is_separate_and_credential_columns_are_refused(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->get('/admin/crm/imports')->assertOk();
        $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris test', 'file' => UploadedFile::fake()->createWithContent('leads.csv', "source_id,password\nx,secret")])
            ->assertUnprocessable()->assertJsonValidationErrors('file');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.imports.manage']]);
        $this->actingAs($staff)->get('/admin/crm/imports')->assertForbidden();
        $this->assertDatabaseCount('crm_imports', 0);
    }

    public function test_templates_are_header_only_and_restricted_by_record_type_permissions(): void
    {
        $this->get('/admin/crm/imports/template?entity_type=lead')->assertRedirect('/admin/login');
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super);
        foreach (['lead', 'task', 'reference', 'invoice', 'loyalty_points'] as $type) {
            $csv = $this->get('/admin/crm/imports/template?entity_type='.$type)
                ->assertDownload('geic-'.$type.'-import-template.csv')->assertHeader('X-Content-Type-Options', 'nosniff')->streamedContent();
            $this->assertCount(1, explode("\n", trim($csv)));
            $this->assertStringStartsWith('source_id,', $csv);
        }
        $this->getJson('/admin/crm/imports/template?entity_type=branch')->assertUnprocessable()->assertJsonValidationErrors('entity_type');
        $this->getJson('/admin/crm/imports/template')->assertUnprocessable()->assertJsonValidationErrors('entity_type');
        $restricted = User::factory()->create(['is_admin' => true, 'admin_role' => 'manager',
            'admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.view_all']]);
        $this->actingAs($restricted)->get('/admin/crm/imports/template?entity_type=invoice')->assertForbidden();
        $this->get('/admin/crm/imports/template?entity_type=reference')->assertForbidden();
        $this->get('/admin/crm/imports/template?entity_type=loyalty_points')->assertForbidden();
        $this->assertDatabaseCount('crm_imports', 0);
    }

    public function test_reconciliation_csv_contains_only_mapped_rows_with_safe_cells_and_no_import_side_effects(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone,unused_column\nvalid,Valid student,9876543210,not-exported\ninvalid,=HYPERLINK(),bad,also-private\n");
        $csv = $this->get('/admin/crm/imports/'.$id.'/export')
            ->assertDownload('geic-import-'.$id.'-corrections.csv')->assertHeader('X-Content-Type-Options', 'nosniff')->streamedContent();
        $this->assertStringContainsString('invalid', $csv);
        $this->assertStringContainsString("'=HYPERLINK()", $csv);
        $this->assertStringNotContainsString('Valid student', $csv);
        $this->assertStringNotContainsString('also-private', $csv);
        $this->assertStringNotContainsString('unused_column', $csv);
        $all = $this->get('/admin/crm/imports/'.$id.'/export?scope=all')->assertOk()->streamedContent();
        $this->assertStringContainsString('Valid student', $all);
        $this->getJson('/admin/crm/imports/'.$id.'/export?scope=unknown')->assertUnprocessable()->assertJsonValidationErrors('scope');
        $this->assertDatabaseHas('crm_import_events', ['import_id' => $id, 'action' => 'reconciliation.exported']);
        $this->assertDatabaseHas('crm_imports', ['id' => $id, 'status' => 'preview', 'version' => 1]);
        $this->assertDatabaseCount('crm_students', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_reconciliation_exports_recheck_module_and_organization_access(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone\none,Test student,9876543210\n");
        CrmImport::whereKey($id)->update(['entity_type' => 'invoice']);
        $manager = User::factory()->create(['is_admin' => true, 'admin_role' => 'manager',
            'admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.view_all']]);
        $this->actingAs($manager)->get('/admin/crm/imports/'.$id.'/export')->assertNotFound();
        $assigned = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor',
            'admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.finance.view']]);
        $this->actingAs($assigned)->get('/admin/crm/imports/'.$id.'/export')->assertForbidden();
        $this->assertDatabaseMissing('crm_import_events', ['import_id' => $id, 'action' => 'reconciliation.exported']);
    }

    private function upload(string $csv): int
    {
        return $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris test', 'file' => UploadedFile::fake()->createWithContent('leads.csv', $csv)])->assertOk()->json('data.id');
    }

    public function test_loyalty_relationships_prevent_reversal_of_otherwise_unchanged_imported_students(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone\nloyalty-1,Imported member,9876543210\n");
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $student = CrmStudent::firstOrFail();
        CrmLoyaltyMember::factory()->create(['student_id' => $student->id]);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertTrue($student->fresh()->is_active);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
    }

    public function test_revoked_worker_authority_pauses_pending_rows_and_a_new_admin_can_resume_without_reimporting(): void
    {
        $creator = User::factory()->create(['is_admin' => true]);
        $this->actingAs($creator);
        $id = $this->upload("source_id,full_name,phone\nfirst,First student,9876543210\nsecond,Second student,8765432190\n");
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports', ['--limit' => 1])->assertSuccessful();
        $creator->update(['is_active' => false]);
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $import = CrmImport::findOrFail($id);
        $this->assertSame('paused_permissions', $import->status);
        $replacement = User::factory()->create(['is_admin' => true]);
        $this->actingAs($replacement)->get('/admin/crm/imports/'.$id)->assertOk()->assertSee('Resume pending rows under my access');
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => $import->version])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseHas('crm_imports', ['id' => $id, 'status' => 'completed', 'created_by' => $creator->id, 'approved_by' => $replacement->id]);
        $this->assertDatabaseHas('crm_import_events', ['import_id' => $id, 'action' => 'paused.permissions']);
        $this->assertDatabaseHas('crm_import_events', ['import_id' => $id, 'action' => 'resumed', 'actor_id' => $replacement->id]);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_lead_import_keeps_category_and_reference_for_dashboard_breakdowns_and_rejects_oversized_storage_values(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload("source_id,full_name,phone,lead_category,source_reference,country\none,Tagged student,9876543210,Study abroad,Expo,Germany\n");
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $student = CrmStudent::firstOrFail();
        $this->assertSame('Study abroad', $student->profile['lead_category']);
        $this->assertSame('Expo', $student->profile['source_reference']);
        $this->getJson('/admin/crm?breakdown=lead_category')->assertOk()->assertJsonPath('breakdown.rows.0.label', 'Study abroad');
        $bad = $this->upload("source_id,full_name,phone,country\noversized,Invalid country,8765432190,".str_repeat('x', 81)."\n");
        $this->postJson('/admin/crm/imports/'.$bad.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $bad, 'status' => 'invalid']);
        $this->assertDatabaseCount('counselling_enquiries', 1);
    }
}
