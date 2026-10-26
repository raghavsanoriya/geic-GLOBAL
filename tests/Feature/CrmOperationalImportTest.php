<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmContact;
use App\Models\CrmFollowUp;
use App\Models\CrmImport;
use App\Models\CrmInvoice;
use App\Models\CrmLoyaltyMember;
use App\Models\CrmPayment;
use App\Models\CrmProfileEntry;
use App\Models\CrmProgram;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\CrmVisa;
use App\Models\User;
use App\Support\CrmAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmOperationalImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        Http::preventStrayRequests();
    }

    private function upload(string $type, string $csv): int
    {
        return $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'entity_type' => $type,
            'source' => 'Foris historical test', 'file' => UploadedFile::fake()->createWithContent('records.csv', $csv)])
            ->assertOk()->json('data.id');
    }

    private function process(int $id): void
    {
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
    }

    public function test_task_imports_preserve_completion_history_explicit_assignees_and_unassigned_office_tasks(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $this->actingAs($admin);
        $csv = "source_id,student_geic_id,title,description,status,priority,starts_at,due_at,completed_at,assignee_geic_ids,remark,submitted_at\n"
            ."task-old-1,{$student->id},Original completed task,Original description,completed,high,2025-01-01T10:00:00+05:30,2025-01-02T10:00:00+05:30,2025-01-02T09:00:00+05:30,{$staff->id};{$admin->id},Original remark,2025-01-01T09:00:00+05:30\n"
            ."office-old-1,,Office archive,Office task,pending,normal,2025-01-01,2025-02-01,,,,2025-01-01\n";
        $batch = $this->upload('task', $csv);
        $this->process($batch);
        $task = CrmTask::where('title', 'Original completed task')->firstOrFail();
        $this->assertSame($student->id, $task->student_id);
        $this->assertSame('2025-01-01 03:30:00', $task->created_at->format('Y-m-d H:i:s'));
        $this->assertSame('2025-01-02 03:30:00', $task->completed_at->format('Y-m-d H:i:s'));
        $this->assertSame(2, $task->assignees()->count());
        $this->assertDatabaseHas('crm_task_notes', ['task_id' => $task->id, 'remark' => 'Original remark']);
        $office = CrmTask::where('title', 'Office archive')->firstOrFail();
        $this->assertNull($office->student_id);
        $this->assertSame(0, $office->assignees()->count());
        $this->assertDatabaseHas('crm_task_events', ['task_id' => $office->id, 'action' => 'task.imported']);
        $repeat = $this->upload('task', $csv);
        $this->process($repeat);
        $this->getJson('/admin/crm/imports/'.$repeat)->assertOk()->assertJsonPath('counts.existing', 2);
        $this->postJson('/admin/crm/imports/'.$batch.'/reverse', ['version' => CrmImport::findOrFail($batch)->version])->assertOk();
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.reversal_review', 2);
        $this->assertDatabaseCount('crm_tasks', 2);
        Http::assertNothingSent();
    }

    public function test_task_imports_reject_unresolved_students_unauthorized_staff_mapping_and_invalid_completion_dates(): void
    {
        $actor = User::factory()->create(['is_admin' => true, 'admin_role' => 'administrator', 'admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.manage']]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($actor);
        $header = "source_id,student_geic_id,title,status,priority,starts_at,due_at,completed_at,assignee_geic_ids,submitted_at\n";
        $csv = $header."unknown,999999,Unknown student,new,normal,2025-01-01,2025-01-02,,,2025-01-01\n"
            ."staff,{$student->id},Unauthorized assignment,new,normal,2025-01-01,2025-01-02,,{$staff->id},2025-01-01\n"
            ."completion,,Missing completion,completed,normal,2025-01-01,2025-01-02,,,2025-01-01\n"
            ."time,,Wrong chronology,new,normal,2025-01-03,2025-01-02,,,2025-01-01\n";
        $batch = $this->upload('task', $csv);
        $this->process($batch);
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.invalid', 4);
        $this->assertDatabaseCount('crm_tasks', 0);
        $actor->update(['admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.manage', 'crm.assign']]);
        $this->actingAs($actor->fresh());
        $batch = $this->upload('task', $header."still-denied,{$student->id},Inaccessible assignee,new,normal,2025-01-01,2025-01-02,,{$staff->id},2025-01-01\n");
        $this->process($batch);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $batch, 'status' => 'invalid']);
        $this->assertDatabaseCount('crm_task_assignees', 0);
    }

    public function test_dictionary_imports_resolve_parent_codes_preserve_source_dates_and_leave_status_rules_explicit(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $level = CrmReference::factory()->create(['kind' => 'level', 'code' => 'lead']);
        $csv = "source_id,kind,code,name,parent_code,country,description,outcome,is_active,submitted_at\n"
            ."uni-old-1,university,source_uni,Original university,,Germany,Original description,,1,2025-01-01T10:30:00+05:30\n"
            ."campus-old-1,campus,source_campus,Original campus,source_uni,Germany,,,1,2025-01-02\n"
            ."app-old-1,application_status,source_offer,Original offer,,,,success,0,2025-01-03\n"
            ."level-old-1,level_status,source_lead_status,Original lead status,lead,,,,1,2025-01-04\n";
        $batch = $this->upload('reference', $csv);
        $this->process($batch);
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.imported', 4);
        $university = CrmReference::where('code', 'source_uni')->firstOrFail();
        $campus = CrmReference::where('code', 'source_campus')->firstOrFail();
        $this->assertSame($university->id, $campus->parent_id);
        $this->assertSame('2025-01-01 05:00:00', $university->created_at->format('Y-m-d H:i:s'));
        $status = CrmReference::where('code', 'source_lead_status')->firstOrFail();
        $this->assertSame($level->id, $status->parent_id);
        $this->assertFalse($status->configuration['is_default']);
        $this->assertNull($status->configuration['transfer_stage']);
        $this->assertDatabaseHas('crm_references', ['code' => 'source_offer', 'outcome' => 'success', 'is_active' => false]);
        $this->assertDatabaseCount('crm_status_transitions', 0);
        $this->assertDatabaseHas('crm_reference_events', ['reference_id' => $campus->id, 'action' => 'reference.imported']);
        $university->update(['name' => 'GEIC edited university', 'version' => 2]);
        $repeat = $this->upload('reference', $csv);
        $this->process($repeat);
        $this->getJson('/admin/crm/imports/'.$repeat)->assertOk()->assertJsonPath('counts.existing', 4);
        $this->assertSame('GEIC edited university', $university->fresh()->name);
        $this->postJson('/admin/crm/imports/'.$batch.'/reverse', ['version' => CrmImport::findOrFail($batch)->version])->assertOk();
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.reversal_review', 4);
        $this->assertDatabaseCount('crm_references', 5);
        Http::assertNothingSent();
    }

    public function test_dictionary_import_rejects_code_collisions_missing_parents_unknown_outcomes_and_new_lifecycle_levels(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        CrmReference::factory()->create(['kind' => 'country', 'code' => 'germany', 'name' => 'GEIC Germany']);
        $csv = "source_id,kind,code,name,parent_code,outcome,is_active,submitted_at\n"
            ."collision,country,germany,Overwrite attempt,,,1,2025-01-01\n"
            ."unknown-parent,campus,missing_parent_campus,Campus,not_configured,,1,2025-01-01\n"
            ."new-level,level,another_branch,Not allowed,,,1,2025-01-01\n"
            ."no-outcome,visa_status,original_status,Unknown outcome,,,1,2025-01-01\n";
        $batch = $this->upload('reference', $csv);
        $this->process($batch);
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.invalid', 4);
        $this->assertDatabaseCount('crm_references', 1);
        $this->assertDatabaseHas('crm_references', ['code' => 'germany', 'name' => 'GEIC Germany', 'version' => 1]);
    }

    public function test_dictionary_import_requires_configure_permission_even_when_import_permission_is_present(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'administrator', 'admin_permissions' => ['crm.view', 'crm.imports.manage']]);
        $this->actingAs($staff)->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'entity_type' => 'reference', 'source' => 'Foris dictionaries',
            'file' => UploadedFile::fake()->createWithContent('references.csv', "source_id,kind,code,name,is_active,submitted_at\ncountry-1,country,germany,Germany,1,2025-01-01\n")])->assertForbidden();
        $this->assertDatabaseCount('crm_imports', 0);
        $this->assertDatabaseCount('crm_references', 0);
    }

    public function test_restricted_imports_are_hidden_from_organization_and_student_activity_without_module_permission(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = CrmLoyaltyMember::factory()->create();
        $this->actingAs($admin);
        $batch = $this->upload('loyalty_points', "source_id,member_geic_id,points,reason,submitted_at\ncredit-audit,{$member->id},100,Private original ledger,2025-01-01\n");
        $this->process($batch);
        $admin->update(['admin_role' => 'administrator', 'admin_permissions' => ['crm.view', 'crm.imports.manage']]);
        $this->actingAs($admin->fresh())->getJson('/admin/crm/logs?scope=organization')->assertOk()->assertJsonPath('total', 0);
        $this->getJson('/admin/crm/logs?student='.$member->student_id)->assertOk()->assertJsonPath('total', 0);
        $admin->update(['admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.loyalty.view']]);
        $this->actingAs($admin->fresh())->getJson('/admin/crm/logs?scope=organization&action=loyalty_points.imported')
            ->assertOk()->assertJsonPath('total', 2)->assertJsonMissingPath('data.0.fields')->assertJsonMissingPath('data.0.changes');
        $this->getJson('/admin/crm/logs?student='.$member->student_id.'&action=loyalty_points.imported')->assertOk()->assertJsonPath('total', 2);
    }

    public function test_loyalty_history_preserves_members_referrals_and_signed_points_without_running_award_rules(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $referred = CrmStudent::factory()->create();
        DB::table('crm_loyalty_rules')->insert(['code' => 'enabled-test-rule', 'name' => 'Existing award rule', 'milestone' => 'application_created', 'points' => 999, 'is_enabled' => true, 'version' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $members = "source_id,student_geic_id,name,is_active,submitted_at\nmember-old-1,{$student->id},Original member,0,2025-01-01T10:30:00+05:30\n";
        $this->process($this->upload('loyalty_member', $members));
        $member = CrmLoyaltyMember::firstOrFail();
        $this->assertFalse($member->is_active);
        $this->assertSame('2025-01-01 05:00:00', $member->created_at->format('Y-m-d H:i:s'));
        $referrals = "source_id,student_geic_id,member_source_id,submitted_at\nreferral-old-1,{$referred->id},member-old-1,2025-02-01\n";
        $this->process($this->upload('loyalty_referral', $referrals));
        $points = "source_id,member_source_id,points,reason,submitted_at\ncredit-old-1,member-old-1,100,Original credit,2025-02-01\ndebit-old-1,member-old-1,-25,Original redemption,2025-03-01\n";
        $batch = $this->upload('loyalty_points', $points);
        $this->process($batch);
        $this->assertSame(75, (int) DB::table('crm_loyalty_points')->where('member_id', $member->id)->sum('points'));
        $this->assertDatabaseHas('crm_loyalty_referrals', ['member_id' => $member->id, 'student_id' => $referred->id, 'created_at' => '2025-01-31 18:30:00']);
        $this->assertDatabaseHas('crm_loyalty_points', ['points' => -25, 'reason' => 'Original redemption', 'created_at' => '2025-02-28 18:30:00']);
        $member->update(['name' => 'GEIC edited member', 'version' => 2]);
        $this->travel(61)->seconds();
        foreach (['loyalty_member' => $members, 'loyalty_referral' => $referrals, 'loyalty_points' => $points] as $type => $csv) {
            $repeat = $this->upload($type, $csv);
            $this->process($repeat);
            $this->assertDatabaseHas('crm_import_rows', ['import_id' => $repeat, 'status' => 'existing']);
        }
        $this->assertSame('GEIC edited member', $member->fresh()->name);
        $this->assertDatabaseCount('crm_loyalty_members', 1);
        $this->assertDatabaseCount('crm_loyalty_referrals', 1);
        $this->assertDatabaseCount('crm_loyalty_points', 2);
        $this->postJson('/admin/crm/imports/'.$batch.'/reverse', ['version' => CrmImport::findOrFail($batch)->version])->assertOk();
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.reversal_review', 2);
        $this->assertDatabaseCount('crm_loyalty_points', 2);
        $this->assertSame('lead', $student->fresh()->stage);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_loyalty_imports_refuse_self_referrals_member_replacement_unresolved_parents_and_negative_balances(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $member = CrmLoyaltyMember::factory()->create();
        $replacement = $this->upload('loyalty_member', "source_id,student_geic_id,name,is_active,submitted_at\nother-member,{$member->student_id},Replacement,1,2025-01-01\n");
        $this->process($replacement);
        $referrals = $this->upload('loyalty_referral', "source_id,student_geic_id,member_geic_id,submitted_at\nself,{$member->student_id},{$member->id},2025-01-01\n");
        $this->process($referrals);
        $points = $this->upload('loyalty_points', "source_id,member_geic_id,points,reason,submitted_at\nnegative,{$member->id},-10,Missing original credit,2025-01-01\nunknown,999999,100,Missing member,2025-01-01\nzero,{$member->id},0,Empty adjustment,2025-01-01\nfraction,{$member->id},1.5,Invalid fractional points,2025-01-01\n");
        $this->process($points);
        foreach ([$replacement, $referrals] as $batch) {
            $this->assertDatabaseHas('crm_import_rows', ['import_id' => $batch, 'status' => 'invalid']);
        }
        $this->getJson('/admin/crm/imports/'.$points)->assertOk()->assertJsonPath('counts.invalid', 4);
        $this->assertDatabaseCount('crm_loyalty_members', 1);
        $this->assertDatabaseCount('crm_loyalty_referrals', 0);
        $this->assertDatabaseCount('crm_loyalty_points', 0);
    }

    public function test_loyalty_import_visibility_and_worker_processing_require_current_loyalty_permissions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = CrmLoyaltyMember::factory()->create();
        $this->actingAs($admin);
        $batch = $this->upload('loyalty_points', "source_id,member_geic_id,points,reason,submitted_at\ncredit-1,{$member->id},100,Original source credit,2025-01-01\n");
        $this->postJson('/admin/crm/imports/'.$batch.'/confirm', ['version' => 1])->assertOk();
        $admin->update(['admin_role' => 'administrator', 'admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.loyalty.manage']]);
        $this->actingAs($admin->fresh())->getJson('/admin/crm/imports/'.$batch)->assertNotFound();
        $this->getJson('/admin/crm/imports')->assertOk()->assertJsonPath('total', 0);
        $this->get('/admin/crm/imports')->assertOk()->assertDontSee('Historical loyalty points');
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_imports', ['id' => $batch, 'status' => 'paused_permissions']);
        $this->assertDatabaseCount('crm_loyalty_points', 0);
        $admin->update(['admin_permissions' => ['crm.view', 'crm.imports.manage', 'crm.loyalty.manage', 'crm.loyalty.view']]);
        $this->actingAs($admin->fresh())->postJson('/admin/crm/imports/'.$batch.'/confirm', ['version' => CrmImport::findOrFail($batch)->version])->assertOk();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseCount('crm_loyalty_points', 1);
    }

    public function test_invoice_import_preserves_exact_source_values_without_receipts_stage_changes_or_loyalty_awards(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $csv = "source_id,student_geic_id,number,bill_to,description,currency,base_amount,tax_bps,tax_amount,total_amount,status,due_on,submitted_at,issued_at\ninvoice-old-1,{$student->id},FORIS-2025-001,Example university,Historical commission,INR,10.05,1800,1.81,11.86,issued,2025-02-01,2025-01-01T10:30:00+05:30,2025-01-02T10:30:00+05:30\n";
        $id = $this->upload('invoice', $csv);
        $this->get('/admin/crm/imports/'.$id)->assertOk()->assertSee('Historical invoice snapshot only.');
        $this->process($id);
        $invoice = CrmInvoice::firstOrFail();
        $this->assertSame('FORIS-2025-001', $invoice->number);
        $this->assertSame(1005, $invoice->base_minor);
        $this->assertSame(181, $invoice->tax_minor);
        $this->assertSame(1186, $invoice->total_minor);
        $this->assertSame('2025-01-01 05:00:00', $invoice->created_at->format('Y-m-d H:i:s'));
        $this->assertSame('2025-01-02 05:00:00', $invoice->issued_at->format('Y-m-d H:i:s'));
        $this->assertSame('lead', $student->fresh()->stage);
        $repeat = $this->upload('invoice', $csv);
        $this->process($repeat);
        $this->assertDatabaseCount('crm_invoices', 1);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $repeat, 'status' => 'existing']);
        $this->assertDatabaseHas('crm_finance_events', ['invoice_id' => $invoice->id, 'action' => 'invoice.imported']);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
        $this->assertSame('issued', $invoice->fresh()->status);
        $this->assertDatabaseCount('crm_payments', 0);
        $this->assertDatabaseCount('crm_instalments', 0);
        $this->assertDatabaseCount('crm_loyalty_points', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_invalid_invoice_amounts_dates_and_parent_relationships_remain_visible_without_creating_financial_records(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $otherApplication = CrmApplication::factory()->create();
        $header = "source_id,student_geic_id,number,bill_to,description,currency,base_amount,tax_bps,tax_amount,total_amount,status,due_on,submitted_at,issued_at,application_geic_id\n";
        $rows = "wrong-total,{$student->id},OLD-1,University,Commission,INR,10.05,1800,1.81,11.85,issued,2025-02-01,2025-01-01,2025-01-02,\n"
            ."missing-date,{$student->id},OLD-2,University,Commission,INR,10.05,1800,1.81,11.86,issued,2025-02-01,2025-01-01,,\n"
            ."wrong-parent,{$student->id},OLD-3,University,Commission,INR,10.05,1800,1.81,11.86,issued,2025-02-01,2025-01-01,2025-01-02,{$otherApplication->id}\n"
            ."floating-format,{$student->id},OLD-4,University,Commission,INR,1e2,0,0,100,draft,2025-02-01,2025-01-01,,\n";
        $id = $this->upload('invoice', $header.$rows);
        $this->process($id);
        $this->getJson('/admin/crm/imports/'.$id)->assertOk()->assertJsonPath('counts.invalid', 4);
        $this->assertDatabaseCount('crm_invoices', 0);
        $this->assertDatabaseCount('crm_payments', 0);
    }

    public function test_historical_instalments_receipts_and_reversals_reconcile_without_awarding_points_and_repeat_safely(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $invoice = CrmInvoice::factory()->create(['status' => 'issued', 'base_minor' => 10000, 'tax_bps' => 0, 'tax_minor' => 0, 'total_minor' => 10000]);
        $instalments = "source_id,invoice_geic_id,currency,amount,due_on,submitted_at\npart-1,{$invoice->id},INR,50,2025-02-01,2025-01-01\npart-2,{$invoice->id},INR,50,2025-03-01,2025-01-01\n";
        $this->process($this->upload('instalment', $instalments));
        $receipts = "source_id,invoice_geic_id,currency,amount,paid_on,reference,submitted_at,instalment_source_id,reversal_source_id,reason\n"
            ."receipt-1,{$invoice->id},INR,50,2025-02-01,Original receipt 1,2025-02-01,part-1,,\n"
            ."receipt-2,{$invoice->id},INR,50,2025-03-01,Original receipt 2,2025-03-01,part-2,,\n"
            ."reversal-1,{$invoice->id},INR,-50,2025-03-02,Original reversal,2025-03-02,,receipt-2,Cancelled second receipt\n";
        $batch = $this->upload('receipt', $receipts);
        $this->process($batch);
        $this->assertSame([], DB::table('crm_import_rows')->where('import_id', $batch)->where('status', 'invalid')->pluck('reason')->all());
        $original = CrmPayment::where('reference', 'Original receipt 2')->firstOrFail();
        $reversal = CrmPayment::where('reference', 'Original reversal')->firstOrFail();
        $this->assertSame($original->id, $reversal->reversal_of_id);
        $this->assertSame(-5000, $reversal->amount_minor);
        $this->assertSame((int) $original->instalment_id, (int) $reversal->instalment_id);
        $this->assertSame(5000, (int) $invoice->payments()->sum('amount_minor'));
        $this->assertSame('2025-03-01 18:30:00', $reversal->created_at->format('Y-m-d H:i:s'));
        $this->process($this->upload('instalment', $instalments));
        $repeat = $this->upload('receipt', $receipts);
        $this->process($repeat);
        $this->getJson('/admin/crm/imports/'.$repeat)->assertOk()->assertJsonPath('counts.existing', 3);
        $this->assertDatabaseCount('crm_instalments', 2);
        $this->assertDatabaseCount('crm_payments', 3);
        $this->assertDatabaseCount('crm_loyalty_points', 0);
        $this->assertDatabaseHas('crm_finance_events', ['invoice_id' => $invoice->id, 'action' => 'receipt.imported']);
        $this->postJson('/admin/crm/imports/'.$batch.'/reverse', ['version' => CrmImport::findOrFail($batch)->version])->assertOk();
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.reversal_review', 3);
        $this->assertDatabaseCount('crm_payments', 3);
        Http::assertNothingSent();
    }

    public function test_financial_child_imports_reject_overpayment_currency_conflicts_or_untraceable_adjustments_and_recheck_after_confirmation(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $invoice = CrmInvoice::factory()->create(['status' => 'issued', 'base_minor' => 10000, 'tax_bps' => 0, 'tax_minor' => 0, 'total_minor' => 10000]);
        $header = "source_id,invoice_geic_id,currency,amount,paid_on,reference,submitted_at,reversal_geic_id,reason\n";
        $pending = $this->upload('receipt', $header."pending-1,{$invoice->id},INR,100,2025-02-01,Pending source receipt,2025-02-01,,\n");
        $this->postJson('/admin/crm/imports/'.$pending.'/confirm', ['version' => 1])->assertOk();
        $recorded = CrmPayment::create(['invoice_id' => $invoice->id, 'creation_key' => (string) Str::uuid(), 'amount_minor' => 5000, 'reference' => 'Already recorded', 'paid_on' => '2025-01-01']);
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $pending, 'status' => 'invalid']);
        $invalid = $header."wrong-currency,{$invoice->id},USD,10,2025-02-01,Wrong currency,2025-02-01,,\n"
            ."orphan-credit,{$invoice->id},INR,-50,2025-02-01,Unlinked credit,2025-02-01,,\n"
            ."partial-credit,{$invoice->id},INR,-25,2025-02-01,Partial credit,2025-02-01,{$recorded->id},Original adjustment\n";
        $batch = $this->upload('receipt', $invalid);
        $this->process($batch);
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.invalid', 3);
        $this->assertDatabaseCount('crm_payments', 1);
        $schedule = $this->upload('instalment', "source_id,invoice_geic_id,currency,amount,due_on,submitted_at\ntoo-large,{$invoice->id},INR,101,2025-02-01,2025-01-01\n");
        $this->process($schedule);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $schedule, 'status' => 'invalid']);
        $this->assertDatabaseCount('crm_instalments', 0);
    }

    public function test_original_voided_invoice_requires_traceable_dates_and_reason_and_cannot_reuse_an_invoice_number(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $header = "source_id,student_geic_id,number,bill_to,description,currency,base_amount,tax_bps,tax_amount,total_amount,status,due_on,submitted_at,issued_at,voided_at,void_reason\n";
        $valid = "void-1,{$student->id},ORIGINAL-VOID,University,Commission,EUR,100,0,0,100,void,2025-02-01,2025-01-01,2025-01-02,2025-01-03,Original cancellation\n";
        $this->process($this->upload('invoice', $header.$valid));
        $invoice = CrmInvoice::firstOrFail();
        $this->assertSame('void', $invoice->status);
        $this->assertSame('Original cancellation', $invoice->void_reason);
        $this->assertSame('2025-01-02 18:30:00', $invoice->voided_at->format('Y-m-d H:i:s'));
        $invalid = "void-2,{$student->id},OTHER-VOID,University,Commission,EUR,100,0,0,100,void,2025-02-01,2025-01-01,2025-01-03,2025-01-02,Original cancellation\n"
            ."void-3,{$student->id},ORIGINAL-VOID,University,Commission,EUR,100,0,0,100,void,2025-02-01,2025-01-01,2025-01-02,2025-01-03,Original cancellation\n";
        $batch = $this->upload('invoice', $header.$invalid);
        $this->process($batch);
        $this->getJson('/admin/crm/imports/'.$batch)->assertOk()->assertJsonPath('counts.invalid', 2);
        $this->assertDatabaseCount('crm_invoices', 1);
    }

    public function test_invoice_import_details_are_finance_restricted_and_workers_pause_when_finance_access_is_revoked(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($admin);
        $id = $this->upload('invoice', "source_id,student_geic_id,number,bill_to,description,currency,base_amount,tax_bps,tax_amount,total_amount,status,due_on,submitted_at\ninv-1,{$student->id},PRIVATE-INV,Private bill-to,Commission,INR,100,0,0,100,draft,2025-02-01,2025-01-01\n");
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 1])->assertOk();
        $permissions = ['crm.view', 'crm.imports.manage', 'crm.finance.manage'];
        $admin->update(['admin_role' => 'administrator', 'admin_permissions' => $permissions]);
        $this->actingAs($admin->fresh())->getJson('/admin/crm/imports/'.$id)->assertNotFound();
        $this->getJson('/admin/crm/imports')->assertOk()->assertJsonPath('total', 0);
        $this->patchJson('/admin/crm/imports/'.$id.'/mapping', ['version' => 2, 'mapping' => []])->assertNotFound();
        $this->postJson('/admin/crm/imports/'.$id.'/confirm', ['version' => 2])->assertNotFound();
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => 2])->assertNotFound();
        $this->artisan('crm:process-imports')->assertSuccessful();
        $this->assertDatabaseHas('crm_imports', ['id' => $id, 'status' => 'paused_permissions']);
        $this->assertDatabaseCount('crm_invoices', 0);
    }

    public function test_contact_imports_preserve_dates_are_repeatable_and_never_replace_edited_values(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $csv = "source_id,name,contact_type,email,submitted_at\ncontact-1,Historical partner,University,partner@example.com,2025-01-01T10:30:00+05:30\ncontact-2,Other partner,Agent,other@example.com,\n";
        $id = $this->upload('contact', $csv);
        $this->get('/admin/crm/imports/'.$id)->assertOk()->assertSee('Business contacts')->assertSee('Field mapping');
        $this->process($id);
        $contact = CrmContact::where('name', 'Historical partner')->firstOrFail();
        $this->assertSame('2025-01-01 05:00:00', $contact->created_at->format('Y-m-d H:i:s'));
        $contact->update(['name' => 'GEIC edited partner', 'version' => 2]);
        $repeat = $this->upload('contact', $csv);
        $this->process($repeat);
        $this->assertDatabaseCount('crm_contacts', 2);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $repeat, 'status' => 'existing']);
        $this->assertSame('GEIC edited partner', $contact->fresh()->name);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertTrue($contact->fresh()->is_active);
        $this->assertFalse(CrmContact::where('name', 'Other partner')->firstOrFail()->is_active);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_follow_up_and_profile_imports_link_only_to_resolved_stable_parent_ids_and_preserve_history(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $lead = $this->upload('lead', "source_id,full_name,phone\nparent-1,Historical student,9876543210\n");
        $this->process($lead);
        $student = CrmStudent::firstOrFail();
        $followup = $this->upload('follow_up', "source_id,student_source_id,subject,channel,status,due_at,ended_at,remark\nfollow-1,parent-1,Historical call,phone,ended,2025-02-01T10:00:00+05:30,2025-02-01T10:30:00+05:30,Original call notes\n");
        $this->process($followup);
        $record = CrmFollowUp::firstOrFail();
        $this->assertSame($student->id, $record->student_id);
        $this->assertNull($record->owner_id);
        $this->assertSame('2025-02-01 04:30:00', $record->due_at->format('Y-m-d H:i:s'));
        $this->assertSame('2025-02-01 05:00:00', $record->ended_at->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('crm_follow_up_notes', ['follow_up_id' => $record->id, 'remark' => 'Original call notes']);
        $education = $this->upload('education', "source_id,student_source_id,institution,qualification,start_year,passing_year\nedu-1,parent-1,Example university,Bachelor,2020,2023\n");
        $this->process($education);
        $entry = CrmProfileEntry::firstOrFail();
        $this->assertSame($student->id, (int) $entry->student_id);
        $this->assertSame('Example university', $entry->details['institution']);
        $this->postJson('/admin/crm/imports/'.$education.'/reverse', ['version' => CrmImport::findOrFail($education)->version])->assertOk();
        $this->assertFalse($entry->fresh()->is_active);
        $this->postJson('/admin/crm/imports/'.$followup.'/reverse', ['version' => CrmImport::findOrFail($followup)->version])->assertOk();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $followup, 'status' => 'reversal_review']);
        $this->assertDatabaseCount('crm_follow_ups', 1);
        $this->assertSame('lead', $student->fresh()->stage);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_missing_parents_invalid_scores_and_conflicting_identifiers_remain_invalid_without_invented_profiles(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $id = $this->upload('education', "source_id,student_source_id,institution,qualification\nmissing-1,unknown-parent,University,Bachelor\n");
        $preview = $this->getJson('/admin/crm/imports/'.$id)->assertOk();
        $this->assertStringContainsString('student relationship', implode(' ', $preview->json('rows.0.errors')));
        $this->process($id);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'invalid']);
        $student = CrmStudent::factory()->create();
        $score = $this->upload('test_score', "source_id,student_geic_id,test,overall\nscore-1,{$student->id},IELTS,9.5\n");
        $this->process($score);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $score, 'status' => 'invalid']);
        $this->assertDatabaseCount('crm_profile_entries', 0);
        $this->assertDatabaseCount('crm_students', 1);
        $parent = $this->upload('lead', "source_id,full_name,phone\nparent-2,Other historical student,8791234560\n");
        $this->process($parent);
        $conflict = $this->upload('education', "source_id,student_source_id,student_geic_id,institution,qualification\nconflict-1,parent-2,{$student->id},University,Bachelor\n");
        $this->process($conflict);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $conflict, 'status' => 'invalid']);
        $this->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris test', 'entity_type' => 'users',
            'file' => UploadedFile::fake()->createWithContent('records.csv', "source_id,name\none,Test\n")])->assertUnprocessable()->assertJsonValidationErrors('entity_type');
    }

    public function test_operational_imports_require_manage_access_in_addition_to_import_access(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'manager', 'admin_permissions' => ['crm.view', 'crm.view_all', 'crm.imports.manage']]);
        $this->actingAs($staff)->postJson('/admin/crm/imports', ['creation_key' => (string) Str::uuid(), 'source' => 'Foris test', 'entity_type' => 'contact',
            'file' => UploadedFile::fake()->createWithContent('contacts.csv', "source_id,name,contact_type\none,Partner,University\n")])->assertForbidden();
        $this->assertDatabaseCount('crm_imports', 0);
    }

    public function test_each_profile_family_uses_the_shared_validation_and_keeps_inactive_source_records(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $rows = [
            'employment' => "source_id,student_geic_id,employer,designation,ended_on,is_active\njob-1,{$student->id},Example employer,Assistant,2024-12-31,0\n",
            'test_score' => "source_id,student_geic_id,test,overall,is_active\nscore-1,{$student->id},PTE,65,1\n",
            'preference' => "source_id,student_geic_id,country,course,is_active\npref-1,{$student->id},Germany,Data science,1\n",
        ];
        foreach ($rows as $type => $csv) {
            $id = $this->upload($type, $csv);
            $this->process($id);
            $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'imported']);
        }
        $this->assertDatabaseCount('crm_profile_entries', 3);
        $this->assertFalse(CrmProfileEntry::where('kind', 'employment')->firstOrFail()->is_active);
        $this->assertSame('65', CrmProfileEntry::where('kind', 'test_score')->firstOrFail()->details['overall']);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_owner_mapping_does_not_create_accounts_or_grant_student_access(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $outside = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $id = $this->upload('follow_up', "source_id,student_geic_id,subject,channel,status,due_at,owner_geic_id\nfollow-1,{$student->id},Call,phone,pending,2026-10-20T10:00:00+05:30,{$outside->id}\n");
        $this->process($id);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'invalid']);
        $this->assertDatabaseCount('crm_follow_ups', 0);
        $this->assertNull($student->fresh()->assigned_counsellor_id);
        $this->assertSame(['crm.view'], $outside->fresh()->admin_permissions);
    }

    public function test_subsequent_student_activity_blocks_reversal_even_when_the_imported_profile_entry_is_unchanged(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $student = CrmStudent::factory()->create();
        $id = $this->upload('education', "source_id,student_geic_id,institution,qualification\nedu-1,{$student->id},University,Bachelor\n");
        $this->process($id);
        app(CrmAudit::class)->record($student, 'application.created', $admin, ['application:123']);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
        $this->assertTrue(CrmProfileEntry::firstOrFail()->is_active);
    }

    public function test_rows_from_the_same_batch_do_not_falsely_block_each_others_controlled_reversal(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $student = CrmStudent::factory()->create();
        $id = $this->upload('education', "source_id,student_geic_id,institution,qualification\nedu-1,{$student->id},School,Secondary\nedu-2,{$student->id},University,Bachelor\n");
        $this->process($id);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertSame(0, CrmProfileEntry::where('is_active', true)->count());
        $this->assertSame(2, DB::table('crm_import_rows')->where('import_id', $id)->where('status', 'reversed')->count());
    }

    public function test_program_import_requires_explicit_reference_codes_is_repeatable_and_blocks_reversal_when_in_use(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $university = CrmReference::factory()->create(['kind' => 'university', 'code' => 'historical_uni']);
        $csv = "source_id,code,name,country,university_code,study_level,subject,ielts,is_active\nprogram-1,historical-course,Historical course,Germany,historical_uni,Masters,Computing,6.5,1\n";
        $id = $this->upload('program', $csv);
        $this->process($id);
        $program = CrmProgram::firstOrFail();
        $this->assertSame($university->id, (int) $program->university_id);
        $this->assertSame('6.5', $program->ielts);
        $repeat = $this->upload('program', $csv);
        $this->process($repeat);
        $this->assertDatabaseCount('crm_programs', 1);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $repeat, 'status' => 'existing']);
        CrmApplication::factory()->create(['program_id' => $program->id, 'university_id' => $university->id]);
        $this->postJson('/admin/crm/imports/'.$id.'/reverse', ['version' => CrmImport::findOrFail($id)->version])->assertOk();
        $this->assertTrue($program->fresh()->is_active);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'reversal_review']);
        $bad = $this->upload('program', "source_id,name,country,university_code,study_level,subject\nmissing,Missing university,Germany,not_configured,Masters,Computing\n");
        $this->process($bad);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $bad, 'status' => 'invalid']);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_application_and_visa_history_preserve_links_without_awards_stage_changes_or_billing_side_effects(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $student = CrmStudent::factory()->create(['stage' => 'lead']);
        CrmReference::factory()->create(['kind' => 'university', 'code' => 'snapshot_uni']);
        $appStatus = CrmReference::factory()->create(['kind' => 'application_status', 'code' => 'snapshot_app', 'outcome' => 'success']);
        $otherStatus = CrmReference::factory()->create(['kind' => 'application_status', 'code' => 'should_not_apply']);
        CrmReference::factory()->create(['kind' => 'visa_status', 'code' => 'snapshot_visa', 'outcome' => 'success', 'linked_application_status_id' => $otherStatus->id]);
        $member = CrmLoyaltyMember::factory()->create();
        DB::table('crm_loyalty_referrals')->insert(['member_id' => $member->id, 'student_id' => $student->id, 'created_by' => $admin->id, 'created_at' => now()]);
        foreach (['application_created', 'visa_approved'] as $milestone) {
            DB::table('crm_loyalty_rules')->insert(['code' => $milestone, 'name' => 'Active historical safety test', 'milestone' => $milestone, 'points' => 100, 'is_enabled' => true, 'version' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }
        $csv = "source_id,student_geic_id,university_code,status_code,course,intake,tags,source_data,submitted_at\n"
            ."app-1,{$student->id},snapshot_uni,snapshot_app,Historical course,January 2025,Priority;Archive,\"{\"\"Foris ID\"\":\"\"1699\"\",\"\"Course link\"\":\"\"https://example.test/course\"\"}\",2024-12-01T10:00:00+05:30\n";
        $batch = $this->upload('application', $csv);
        $this->process($batch);
        $application = CrmApplication::firstOrFail();
        $this->assertSame('2024-12-01 04:30:00', $application->created_at->format('Y-m-d H:i:s'));
        $this->assertNull($application->owner_id);
        $this->assertSame(['Priority', 'Archive'], $application->tags);
        $this->assertSame('1699', $application->source_data['Foris ID']);
        $this->get('/admin/crm/applications/'.$application->id)->assertOk()->assertSee('Original Foris application data')->assertSee('https://example.test/course');
        $unsafe = "source_id,student_geic_id,university_code,status_code,course,intake,source_data,submitted_at\n"
            .sprintf('app-unsafe,%d,snapshot_uni,snapshot_app,Unsafe application,January 2025,"{""API key"":""must-not-import""}",2024-12-01T10:00:00+05:30'."\n", $student->id);
        $unsafeBatch = $this->upload('application', $unsafe);
        $this->process($unsafeBatch);
        $this->assertSame('invalid', DB::table('crm_import_rows')->where('import_id', $unsafeBatch)->value('status'));
        $application->update(['course' => 'GEIC edited course', 'version' => 2]);
        $again = $this->upload('application', $csv);
        $this->process($again);
        $this->assertSame('GEIC edited course', $application->fresh()->course);
        $visaBatch = $this->upload('visa', "source_id,application_source_id,status_code,visa_type,decision_on,ready_to_bill,remark\nvisa-1,app-1,snapshot_visa,Student,2025-01-10,1,Original outcome note\n");
        $this->process($visaBatch);
        $visa = CrmVisa::firstOrFail();
        $this->assertSame($application->id, $visa->application_id);
        $this->assertSame($student->id, $visa->student_id);
        $this->assertTrue($visa->ready_to_bill);
        $this->assertSame($appStatus->id, $application->fresh()->status_id);
        $this->assertSame('lead', $student->fresh()->stage);
        $this->assertDatabaseCount('crm_loyalty_points', 0);
        $this->assertDatabaseCount('crm_invoices', 0);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $this->postJson('/admin/crm/imports/'.$visaBatch.'/reverse', ['version' => CrmImport::findOrFail($visaBatch)->version])->assertOk();
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $visaBatch, 'status' => 'reversal_review']);
        $this->assertDatabaseCount('crm_visas', 1);
        Http::assertNothingSent();
    }

    public function test_unknown_case_parents_and_invalid_outcome_dates_remain_in_correction_review(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        CrmReference::factory()->create(['kind' => 'visa_status', 'code' => 'historical_visa']);
        $id = $this->upload('visa', "source_id,application_source_id,status_code,visa_type,ready_to_bill\nvisa-1,missing-app,historical_visa,Student,0\n");
        $this->process($id);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $id, 'status' => 'invalid']);
        $application = CrmApplication::factory()->create();
        $date = $this->upload('visa', "source_id,application_geic_id,status_code,visa_type,lodged_on,decision_on,ready_to_bill\nvisa-2,{$application->id},historical_visa,Student,2025-02-01,2025-01-01,0\n");
        $this->process($date);
        $this->assertDatabaseHas('crm_import_rows', ['import_id' => $date, 'status' => 'invalid']);
        $this->assertDatabaseCount('crm_visas', 0);
    }
}
