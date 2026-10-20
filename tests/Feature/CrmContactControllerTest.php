<?php

namespace Tests\Feature;

use App\Models\CrmContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_contact_creation_normalizes_details_is_repeatable_and_supports_geographic_search(): void
    {
        $staff = $this->staff();
        $data = $this->payload();
        $this->actingAs($staff)->get('/admin/crm/contacts/create')->assertOk();
        $id = $this->postJson('/admin/crm/contacts', $data)->assertOk()->assertJsonPath('data.phone', '+918765432190')->assertJsonPath('data.email', 'partner@example.com')->json('data.id');
        $this->postJson('/admin/crm/contacts', $data)->assertJsonPath('data.id', $id);
        $this->getJson('/admin/crm/contacts?country=India&city=Indore&contact_type=University')->assertJsonPath('total', 1);
        $this->get('/admin/crm/contacts/'.$id)->assertOk()->assertSee('Example partner');
        $this->assertDatabaseCount('crm_contacts', 1);
        $this->assertDatabaseCount('crm_contact_events', 1);
    }

    public function test_other_staff_cannot_read_or_change_contacts_and_stale_edits_are_rejected(): void
    {
        $one = $this->staff();
        $two = $this->staff();
        $contact = CrmContact::factory()->create(['created_by' => $one->id]);
        $this->actingAs($two)->getJson('/admin/crm/contacts')->assertJsonPath('total', 0);
        $this->get('/admin/crm/contacts/'.$contact->id)->assertNotFound();
        $this->patchJson('/admin/crm/contacts/'.$contact->id, [])->assertNotFound();
        $data = $this->payload();
        unset($data['creation_key']);
        $this->actingAs($one)->patchJson('/admin/crm/contacts/'.$contact->id, [...$data, 'version' => 1, 'name' => 'Updated partner'])->assertJsonPath('data.version', 2);
        $this->patchJson('/admin/crm/contacts/'.$contact->id, [...$data, 'version' => 1])->assertConflict();
    }

    public function test_validation_read_only_permissions_escaping_and_feature_flag_protect_contacts(): void
    {
        $this->get('/admin/crm/contacts')->assertRedirect('/admin/login');
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/contacts', [...$this->payload(), 'phone' => 'invalid', 'created_by' => null])->assertUnprocessable()->assertJsonValidationErrors(['phone', 'created_by']);
        $contact = CrmContact::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->get('/admin/crm/contacts')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $viewer = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($viewer)->postJson('/admin/crm/contacts', $this->payload())->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs($super)->get('/admin/crm/contacts')->assertNotFound();
    }

    private function payload(): array
    {
        return ['creation_key' => (string) Str::uuid(), 'name' => 'Example partner', 'company' => 'Example company', 'contact_type' => 'University', 'email' => 'PARTNER@example.com', 'phone' => '8765432190', 'country' => 'India', 'city' => 'Indore', 'is_active' => true];
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }
}
