<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmContact;
use App\Models\CrmFollowUp;
use App\Models\CrmInvoice;
use App\Models\CrmLoyaltyMember;
use App\Models\CrmPayment;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\CrmVisa;
use App\Models\User;
use App\Support\CrmLoyalty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmWorkspaceExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_case_exports_apply_student_visibility_and_status_filters(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.export']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'full_name' => 'Accessible applicant']);
        $application = CrmApplication::factory()->create(['student_id' => $student->id, 'course' => 'Exported course']);
        CrmApplication::factory()->create(['course' => 'Hidden course']);
        $visa = CrmVisa::factory()->create(['student_id' => $student->id, 'application_id' => $application->id, 'visa_type' => 'Visible visa', 'ready_to_bill' => true]);
        CrmVisa::factory()->create(['visa_type' => 'Hidden visa']);
        $this->actingAs($staff);
        $csv = $this->get('/admin/crm/applications?format=csv&status_id='.$application->status_id)->assertOk()->streamedContent();
        $this->assertStringContainsString('Exported course', $csv);
        $this->assertStringNotContainsString('Hidden course', $csv);
        $csv = $this->get('/admin/crm/visas?format=csv&ready_to_bill=1')->assertOk()->streamedContent();
        $this->assertStringContainsString('Visible visa', $csv);
        $this->assertStringNotContainsString('Hidden visa', $csv);
        $this->get('/admin/crm/visas?format=print')->assertOk()->assertSee('Visible visa')->assertDontSee('Hidden visa');
        $staff->update(['admin_permissions' => ['crm.view']]);
        $this->get('/admin/crm/applications?format=csv')->assertForbidden();
        $this->get('/admin/crm/visas?format=print')->assertForbidden();
    }

    public function test_exports_follow_scope_filters_permissions_and_safe_spreadsheet_output(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.export']]);
        $other = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $outside = CrmStudent::factory()->create();
        CrmTask::factory()->create(['created_by' => $staff->id, 'title' => '=unsafe-title', 'status' => 'pending', 'priority' => 'high']);
        CrmTask::factory()->create(['created_by' => $staff->id, 'title' => 'Other priority', 'status' => 'pending', 'priority' => 'low']);
        CrmTask::factory()->create(['created_by' => $other->id, 'title' => 'Private outside task', 'priority' => 'high']);
        CrmContact::factory()->create(['created_by' => $staff->id, 'name' => '  =unsafe-contact', 'city' => 'Indore']);
        CrmContact::factory()->create(['created_by' => $other->id, 'name' => 'Private outside contact', 'city' => 'Indore']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $staff->id, 'subject' => 'Owned follow-up', 'status' => 'pending']);
        CrmFollowUp::factory()->create(['student_id' => $outside->id, 'owner_id' => $other->id, 'subject' => 'Private outside follow-up']);
        $this->actingAs($staff);
        $tasks = $this->get('/admin/crm/tasks?priority=high&format=csv')->assertOk()->assertDownload('geic-tasks.csv')->streamedContent();
        $this->assertStringContainsString("'=unsafe-title", $tasks);
        $this->assertStringNotContainsString('Other priority', $tasks);
        $this->assertStringNotContainsString('Private outside task', $tasks);
        $contacts = $this->get('/admin/crm/contacts?city=Indore&format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString("'  =unsafe-contact", $contacts);
        $this->assertStringNotContainsString('Private outside contact', $contacts);
        $followups = $this->get('/admin/crm/follow-ups?queue=pending&format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('Owned follow-up', $followups);
        $this->assertStringNotContainsString('Private outside follow-up', $followups);
        $this->get('/admin/crm/follow-ups?student='.$outside->id.'&format=csv')->assertNotFound();
        $staff->update(['admin_permissions' => ['crm.view']]);
        foreach (['tasks', 'contacts', 'follow-ups'] as $module) {
            $this->get('/admin/crm/'.$module.'?format=csv')->assertForbidden();
            $this->get('/admin/crm/'.$module.'?format=print')->assertForbidden();
        }
    }

    public function test_printed_records_are_escaped_and_converted_rocket_views_render_real_records(): void
    {
        $staff = User::factory()->create(['is_admin' => true]);
        $contact = CrmContact::factory()->create(['name' => '<script>Bad contact</script>', 'company' => 'Test university', 'city' => 'Indore']);
        CrmTask::factory()->create(['title' => 'Check admission checklist', 'status' => 'pending']);
        $this->actingAs($staff)->get('/admin/crm/contacts?format=print')->assertOk()->assertSee('GEIC · Indore')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>Bad contact</script>', false);
        $this->get('/admin/crm/contacts/'.$contact->id)->assertOk()->assertSee('profile-card-has-mask', false)->assertSee('CONTACT-'.$contact->id)->assertSee('Test university')->assertDontSee('Kate Williams')->assertDontSee('lms.rocket-soft.org');
        $this->get('/admin/crm/contacts')->assertOk()->assertSee('Matching contacts')->assertSee('Export CSV');
        $this->get('/admin/crm/tasks')->assertOk()->assertSee('Team worklist')->assertSee('Completed')->assertSee('Check admission checklist');
        $this->get('/admin/crm/tasks?format=print')->assertOk()->assertSee('Check admission checklist')->assertSee('Due (IST)');
        $this->get('/admin/crm/contacts?format=invalid')->assertSessionHasErrors('format');
    }

    public function test_finance_and_membership_exports_require_both_module_and_export_access_and_keep_exact_amounts(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor',
            'admin_permissions' => ['crm.view', 'crm.export', 'crm.finance.view', 'crm.loyalty.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'full_name' => 'Visible finance student']);
        $invoice = CrmInvoice::factory()->create(['student_id' => $student->id, 'number' => 'VISIBLE-INVOICE', 'total_minor' => 118001, 'status' => 'issued']);
        $hidden = CrmInvoice::factory()->create(['number' => 'HIDDEN-INVOICE']);
        foreach ([$invoice, $hidden] as $record) {
            CrmPayment::create(['invoice_id' => $record->id, 'creation_key' => (string) Str::uuid(), 'amount_minor' => 50001, 'reference' => '=receipt', 'paid_on' => '2026-10-20']);
        }
        $visibleMember = CrmLoyaltyMember::factory()->create(['student_id' => $student->id, 'name' => 'Visible member']);
        $hiddenMember = CrmLoyaltyMember::factory()->create(['name' => 'Hidden member']);
        $super = User::factory()->create(['is_admin' => true]);
        foreach ([$visibleMember, $hiddenMember] as $member) {
            app(CrmLoyalty::class)->adjust($super, $member->id, 25, (string) Str::uuid(), 'Test award');
        }
        $this->actingAs($staff);
        $csv = $this->get('/admin/crm/invoices?status=issued&format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('1180.01,500.01,680.00', $csv);
        $this->assertStringNotContainsString('HIDDEN-INVOICE', $csv);
        $csv = $this->get('/admin/crm/transactions?from=2026-10-20&to=2026-10-20&format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=receipt", $csv);
        $this->assertStringNotContainsString('HIDDEN-INVOICE', $csv);
        $this->get('/admin/crm/transactions?from=2026-10-21&format=print')->assertOk()->assertDontSee('VISIBLE-INVOICE');
        $this->get('/admin/crm/transactions?to=2026-10-20&format=print')->assertOk()->assertSee('VISIBLE-INVOICE');
        $this->getJson('/admin/crm/transactions?from=2026-10-21&to=2026-10-20')->assertUnprocessable()->assertJsonValidationErrors('to');
        $this->get('/admin/crm/transactions?invoice='.$hidden->id.'&format=csv')->assertNotFound();
        $csv = $this->get('/admin/crm/loyalty?format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('Visible member', $csv);
        $this->assertStringNotContainsString('Hidden member', $csv);
        $points = $this->get('/admin/crm/loyalty/points?format=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('Visible member', $points);
        $this->assertStringNotContainsString('Hidden member', $points);
        $this->get('/admin/crm/loyalty/points?member='.$hiddenMember->id.'&format=print')->assertNotFound();
        $staff->update(['admin_permissions' => ['crm.view', 'crm.finance.view', 'crm.loyalty.view']]);
        foreach (['invoices', 'transactions', 'loyalty'] as $module) {
            $this->get('/admin/crm/'.$module.'?format=csv')->assertForbidden();
            $this->get('/admin/crm/'.$module.'?format=print')->assertForbidden();
        }
        $staff->update(['admin_permissions' => ['crm.view', 'crm.export']]);
        $this->get('/admin/crm/invoices?format=csv')->assertForbidden();
        $this->get('/admin/crm/loyalty?format=csv')->assertForbidden();
    }
}
