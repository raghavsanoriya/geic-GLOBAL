<?php

namespace Tests\Feature;

use App\Models\CrmInvoice;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmMoney;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmFinanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_invoice_creation_uses_exact_minor_units_and_tax_rounding_without_advancing_student_stage(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $data = $this->payload($student);
        $this->actingAs($super)->get('/admin/crm/invoices/create?student='.$student->id)->assertOk();
        $id = $this->postJson('/admin/crm/invoices', $data)->assertOk()->assertJsonPath('data.base_minor', 5)->assertJsonPath('data.tax_minor', 1)->assertJsonPath('data.total_minor', 6)->json('data.id');
        $this->postJson('/admin/crm/invoices', $data)->assertJsonPath('data.id', $id);
        $this->get('/admin/crm/invoices/'.$id)->assertOk()->assertSee('0.06');
        $this->get('/admin/crm/invoices/'.$id.'/print')->assertOk()->assertSee('Draft — not an issued invoice.')->assertSee('0.06');
        $this->get('/admin/crm/invoices')->assertOk()->assertSee('Commission partner');
        $this->assertDatabaseCount('crm_invoices', 1);
        $this->assertDatabaseCount('crm_finance_events', 1);
        $this->assertSame('lead', $student->fresh()->stage);
        $this->assertSame('0.05', CrmMoney::display(5));
        $this->assertSame('-0.05', CrmMoney::display(-5));
    }

    public function test_issued_invoice_amounts_cannot_be_rewritten_and_stale_status_changes_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $invoice = CrmInvoice::factory()->create();
        $this->actingAs($super)->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'issued'])->assertOk()->assertJsonPath('data.version', 2);
        $data = $this->payload($invoice->student);
        unset($data['creation_key'], $data['student_id']);
        $this->patchJson('/admin/crm/invoices/'.$invoice->id, [...$data, 'version' => 2])->assertConflict();
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'void', 'reason' => 'Stale correction'])->assertConflict();
        $this->assertDatabaseHas('crm_invoices', ['id' => $invoice->id, 'status' => 'issued', 'total_minor' => 118000]);
    }

    public function test_receipts_and_reversals_are_idempotent_preserve_originals_and_enforce_balance_caps(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $invoice = CrmInvoice::factory()->create(['status' => 'issued']);
        $receipt = ['creation_key' => (string) Str::uuid(), 'amount' => '500.00', 'reference' => 'Test bank receipt', 'paid_on' => '2026-10-20'];
        $id = $this->actingAs($super)->postJson('/admin/crm/invoices/'.$invoice->id.'/payments', $receipt)->assertOk()->json('data.id');
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/payments', $receipt)->assertJsonPath('data.id', $id);
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/payments', [...$receipt, 'creation_key' => (string) Str::uuid(), 'amount' => '700.00'])->assertUnprocessable()->assertJsonValidationErrors('amount');
        $this->getJson('/admin/crm/invoices/'.$invoice->id)->assertJsonPath('balance_minor', 68000);
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'void', 'reason' => 'Has receipts'])->assertConflict();
        $reverse = ['creation_key' => (string) Str::uuid(), 'reason' => 'Correct test receipt', 'paid_on' => '2026-10-20'];
        $this->postJson('/admin/crm/payments/'.$id.'/reversal', $reverse)->assertOk()->assertJsonPath('data.amount_minor', -50000);
        $this->postJson('/admin/crm/payments/'.$id.'/reversal', $reverse)->assertOk();
        $this->assertDatabaseCount('crm_payments', 2);
        $this->assertDatabaseHas('crm_payments', ['id' => $id, 'amount_minor' => 50000, 'reference' => 'Test bank receipt']);
        $this->getJson('/admin/crm/invoices/'.$invoice->id)->assertJsonPath('balance_minor', 118000);
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'void', 'reason' => 'All receipts reversed'])->assertOk();
        $this->get('/admin/crm/transactions')->assertOk()->assertSee('Reversal of receipt');
    }

    public function test_instalments_cannot_exceed_total_and_allocated_receipts_cannot_overpay_one_instalment(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $invoice = CrmInvoice::factory()->create(['status' => 'issued']);
        $data = ['creation_key' => (string) Str::uuid(), 'amount' => '600.00', 'due_on' => '2026-11-01'];
        $id = $this->actingAs($super)->postJson('/admin/crm/invoices/'.$invoice->id.'/instalments', $data)->assertOk()->json('data.id');
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/instalments', $data)->assertJsonPath('data.id', $id);
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/instalments', [...$data, 'creation_key' => (string) Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors('amount');
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/payments', ['creation_key' => (string) Str::uuid(), 'amount' => '650.00', 'instalment_id' => $id, 'reference' => 'Overallocated', 'paid_on' => '2026-10-20'])
            ->assertUnprocessable()->assertJsonValidationErrors('instalment_id');
        $this->assertDatabaseCount('crm_instalments', 1);
        $this->assertDatabaseCount('crm_payments', 0);
    }

    public function test_separate_finance_permissions_and_assigned_case_access_protect_all_financial_routes(): void
    {
        $invoice = CrmInvoice::factory()->create();
        $this->get('/admin/crm/invoices')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $this->actingAs($staff)->get('/admin/crm/invoices')->assertForbidden();
        $staff->update(['admin_permissions' => ['crm.view', 'crm.finance.view', 'crm.finance.manage']]);
        $this->actingAs($staff)->getJson('/admin/crm/invoices')->assertJsonPath('total', 0);
        $this->get('/admin/crm/invoices/'.$invoice->id)->assertNotFound();
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'issued'])->assertNotFound();
        $invoice->student->update(['assigned_re_id' => $staff->id]);
        $staff->update(['admin_permissions' => ['crm.view', 'crm.finance.view']]);
        $this->actingAs($staff)->get('/admin/crm/invoices/'.$invoice->id)->assertOk()->assertDontSee('Record receipt');
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/status', ['version' => 1, 'status' => 'issued'])->assertForbidden();
    }

    public function test_untrusted_totals_fractional_precision_and_draft_receipts_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($super)->postJson('/admin/crm/invoices', [...$this->payload($student), 'total_minor' => 1, 'base_amount' => '0.001'])->assertUnprocessable()->assertJsonValidationErrors(['total_minor', 'base_amount']);
        $invoice = CrmInvoice::factory()->create();
        $this->postJson('/admin/crm/invoices/'.$invoice->id.'/payments', ['creation_key' => (string) Str::uuid(), 'amount' => '1.00', 'reference' => 'Cannot record', 'paid_on' => '2026-10-20'])->assertConflict();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/invoices')->assertNotFound();
        $this->assertDatabaseCount('crm_payments', 0);
    }

    private function payload(CrmStudent $student): array
    {
        return ['student_id' => $student->id, 'creation_key' => (string) Str::uuid(), 'bill_to' => 'Commission partner', 'description' => 'Test commission invoice',
            'base_amount' => '0.05', 'tax_bps' => 1800, 'currency' => 'INR', 'due_on' => '2026-11-01'];
    }
}
