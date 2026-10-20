<?php

namespace Tests\Feature;

use App\Models\CrmInvoice;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmLogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_is_scoped_filtered_private_and_escaped(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'full_name' => '<script>private()</script>']);
        $other = CrmStudent::factory()->create();
        app(CrmAudit::class)->record($student, 'student.updated', $staff, ['email']);
        app(CrmAudit::class)->record($other, 'student.created');
        $this->actingAs($staff)->getJson('/admin/crm/logs')->assertOk()->assertJsonPath('total', 1)
            ->assertJsonMissingPath('data.0.changes')->assertJsonPath('data.0.student_id', $student->id);
        $this->get('/admin/crm/logs')->assertOk()->assertDontSee('<script>private()</script>', false)->assertSee('&lt;script&gt;', false);
        $this->getJson('/admin/crm/logs?action=student.created')->assertJsonPath('total', 0);
        $this->getJson('/admin/crm/logs?student='.$other->id)->assertNotFound();
        $this->getJson('/admin/crm/logs?from=invalid')->assertUnprocessable();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/logs')->assertNotFound();
    }

    public function test_log_access_requires_authentication_and_crm_permission(): void
    {
        config(['crm.enabled' => true]);
        $this->get('/admin/crm/logs')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => []]);
        $this->actingAs($staff)->get('/admin/crm/logs')->assertForbidden();
    }

    public function test_organization_log_excludes_secrets_and_requires_all_case_and_module_permissions(): void
    {
        config(['crm.enabled' => true]);
        $super = User::factory()->create(['is_admin' => true]);
        $reference = CrmReference::factory()->create();
        DB::table('crm_reference_events')->insert(['reference_id' => $reference->id, 'actor_id' => $super->id, 'action' => 'reference.updated', 'changes' => json_encode(['secret' => 'Never show this value']), 'created_at' => now()]);
        $this->actingAs($super)->getJson('/admin/crm/logs?scope=organization')->assertOk()->assertJsonPath('total', 1)->assertJsonMissingPath('data.0.changes')->assertJsonPath('data.0.module', 'Reference configuration');
        $this->get('/admin/crm/logs?scope=organization')->assertOk()->assertSee('Workspace audit trail')->assertDontSee('Never show this value');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.configure']]);
        $this->actingAs($staff)->get('/admin/crm/logs?scope=organization')->assertForbidden();
    }

    public function test_financial_activity_requires_finance_permission_and_current_student_visibility_in_both_log_views(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $visible = CrmInvoice::factory()->create(['student_id' => $student->id]);
        $outside = CrmInvoice::factory()->create();
        foreach ([$visible, $outside] as $invoice) {
            DB::table('crm_finance_events')->insert(['invoice_id' => $invoice->id, 'actor_id' => $staff->id, 'action' => 'payment.recorded', 'fields' => '{"secret":"never expose ledger details"}', 'created_at' => now()]);
        }
        $this->actingAs($staff)->getJson('/admin/crm/logs')->assertOk()->assertJsonPath('total', 0);
        $staff->update(['admin_permissions' => ['crm.view', 'crm.finance.view']]);
        $response = $this->getJson('/admin/crm/logs?action=payment.recorded')->assertOk()->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.student_id', $student->id)->assertJsonPath('data.0.module', 'Finance')->assertJsonMissingPath('data.0.fields');
        $this->assertStringNotContainsString('never expose ledger details', $response->getContent());
        $this->getJson('/admin/crm/logs?to='.now('Asia/Kolkata')->toDateString())->assertOk();
        $staff->update(['admin_permissions' => ['crm.view', 'crm.view_all']]);
        $this->getJson('/admin/crm/logs?scope=organization')->assertOk()->assertJsonPath('total', 0);
        $staff->update(['admin_permissions' => ['crm.view', 'crm.view_all', 'crm.finance.view']]);
        $this->getJson('/admin/crm/logs?scope=organization')->assertOk()->assertJsonPath('total', 2);
    }
}
