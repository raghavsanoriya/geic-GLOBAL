<?php

namespace Tests\Feature;

use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmFollowUpControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_scheduling_saves_utc_and_repeated_submission_does_not_duplicate_history(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $data = [...$this->payload(), 'creation_key' => (string) Str::uuid(), 'remark' => 'Arrange documents'];
        $this->actingAs($staff)->postJson("/admin/crm/students/{$student->id}/follow-ups", $data)
            ->assertOk()->assertJsonPath('data.version', 1)->assertJsonPath('data.student_id', $student->id);
        $this->postJson("/admin/crm/students/{$student->id}/follow-ups", $data)->assertOk();
        $this->assertDatabaseCount('crm_follow_ups', 1);
        $this->assertDatabaseCount('crm_follow_up_notes', 1);
        $this->assertDatabaseCount('crm_audit_events', 1);
        $this->assertDatabaseHas('crm_follow_ups', ['due_at' => '2026-10-21 04:30:00', 'owner_id' => $staff->id]);
        $this->assertStringNotContainsString('Arrange documents', DB::table('crm_audit_events')->value('changes'));
    }

    public function test_other_staff_cannot_list_read_update_or_schedule_an_unassigned_case(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create();
        $followUp = CrmFollowUp::factory()->create(['student_id' => $student->id]);
        $this->actingAs($staff)->getJson('/admin/crm/follow-ups?queue=all')->assertJsonPath('total', 0);
        $this->getJson("/admin/crm/follow-ups/{$followUp->id}")->assertNotFound();
        $this->patchJson("/admin/crm/follow-ups/{$followUp->id}", [...$this->payload(), 'version' => 1])->assertNotFound();
        $this->postJson("/admin/crm/students/{$student->id}/follow-ups", [...$this->payload(), 'creation_key' => (string) Str::uuid()])->assertNotFound();
        $this->get("/admin/crm/students/{$student->id}/follow-ups/create")->assertNotFound();
        $this->assertDatabaseCount('crm_follow_ups', 1);
    }

    public function test_today_and_missed_queues_use_india_day_boundaries_and_exclude_ended_cases(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 20)->setTime(1, 0));
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-19 18:29:00']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-19 18:30:00']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-20 18:29:00']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-20 18:30:00']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-19 18:30:00', 'status' => 'ended']);
        $this->actingAs($super)->getJson('/admin/crm/follow-ups?queue=today')->assertOk()->assertJsonPath('total', 2);
        $this->getJson('/admin/crm/follow-ups?queue=missed')->assertOk()->assertJsonPath('total', 2);
        $this->getJson('/admin/crm/follow-ups?queue=ended')->assertJsonPath('total', 1);
        $this->get('/admin/crm/follow-ups?queue=all')->assertOk()->assertSee('IST');
    }

    public function test_end_reopen_and_remarks_preserve_history_and_reject_stale_updates(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $item = CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $staff->id]);
        $this->actingAs($staff)->patchJson("/admin/crm/follow-ups/{$item->id}", [...$this->payload(), 'version' => 1, 'status' => 'ended', 'remark' => 'Called student'])
            ->assertOk()->assertJsonPath('data.version', 2);
        $this->assertNotNull($item->fresh()->ended_at);
        $this->patchJson("/admin/crm/follow-ups/{$item->id}", [...$this->payload(), 'version' => 1])->assertConflict();
        $this->patchJson("/admin/crm/follow-ups/{$item->id}", [...$this->payload(), 'version' => 2, 'status' => 'pending', 'remark' => 'Call again'])
            ->assertJsonPath('data.version', 3)->assertJsonPath('data.ended_at', null);
        $this->assertDatabaseCount('crm_follow_up_notes', 2);
        $this->assertDatabaseHas('crm_follow_up_notes', ['remark' => 'Called student']);
        $this->assertDatabaseHas('crm_follow_up_notes', ['remark' => 'Call again']);
    }

    public function test_owner_needs_case_access_and_ordinary_staff_cannot_change_ownership(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $staff = $this->staff();
        $student = CrmStudent::factory()->create();
        $path = "/admin/crm/students/{$student->id}/follow-ups";
        $data = [...$this->payload(), 'creation_key' => (string) Str::uuid(), 'owner_id' => $staff->id];
        $this->actingAs($super)->postJson($path, $data)->assertUnprocessable()->assertJsonValidationErrors('owner_id');
        $student->update(['assigned_re_id' => $staff->id]);
        $this->postJson($path, $data)->assertOk();
        $this->actingAs($staff)->postJson($path, [...$data, 'creation_key' => (string) Str::uuid()])->assertForbidden();
    }

    public function test_invalid_data_immutable_fields_and_unauthorized_roles_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $path = "/admin/crm/students/{$student->id}/follow-ups";
        $this->post($path)->assertRedirect('/admin/login');
        $this->actingAs($super)->postJson($path, [])->assertUnprocessable()->assertJsonValidationErrors(['creation_key', 'subject', 'due_at', 'channel', 'status']);
        $this->postJson($path, [...$this->payload(), 'creation_key' => 'bad', 'student_id' => null, 'due_at' => 'not a date', 'status' => 'visa-approved'])
            ->assertUnprocessable()->assertJsonValidationErrors(['creation_key', 'student_id', 'due_at', 'status']);
        $viewer = User::factory()->create(['is_admin' => true, 'admin_permissions' => ['crm.view'], 'admin_role' => 'content_editor']);
        $this->actingAs($viewer)->postJson($path, [...$this->payload(), 'creation_key' => (string) Str::uuid()])->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs($super)->get('/admin/crm/follow-ups')->assertNotFound();
        $this->assertDatabaseCount('crm_follow_ups', 0);
    }

    public function test_follow_up_screen_escapes_subject_and_notes_and_never_leaks_restricted_profile(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['sensitive_profile' => ['guardian_name' => 'Restricted guardian']]);
        $item = CrmFollowUp::factory()->create(['student_id' => $student->id, 'subject' => '<script>alert(1)</script>']);
        DB::table('crm_follow_up_notes')->insert(['follow_up_id' => $item->id, 'actor_id' => $super->id, 'remark' => '<script>alert(2)</script>', 'created_at' => now()]);
        $this->actingAs($super)->get("/admin/crm/follow-ups/{$item->id}")->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<script>alert(2)</script>', false)->assertSee('&lt;script&gt;', false);
        $this->getJson("/admin/crm/follow-ups/{$item->id}")->assertJsonMissingPath('data.student.sensitive_profile')->assertDontSee('Restricted guardian');
        $this->get("/admin/crm/students/{$student->id}/follow-ups/create")->assertOk()->assertSee('Schedule follow-up');
    }

    public function test_list_has_detail_popup_links_and_authorized_popup_returns_only_the_requested_editor(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id, 'full_name' => 'Popup test student']);
        $item = CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $staff->id]);
        $this->actingAs($staff)->get('/admin/crm/follow-ups?queue=all')->assertOk()
            ->assertSee('Student / subject')->assertSee('data-follow-up-open', false)->assertSee('data-follow-up-dialog', false);
        $this->get('/admin/crm/follow-ups/'.$item->id.'?panel=1')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')->assertSee('data-follow-up-panel', false)
            ->assertSee('Popup test student')->assertSee('Save follow-up')->assertDontSee('<html', false);
        $other = $this->staff();
        $this->actingAs($other)->get('/admin/crm/follow-ups/'.$item->id.'?panel=1')->assertNotFound();
        $this->actingAs($staff)->get('/admin/crm/follow-ups/'.$item->id.'?panel=2')->assertSessionHasErrors('panel');
    }

    private function payload(): array
    {
        return ['subject' => 'Discuss course choices', 'channel' => 'phone', 'status' => 'new', 'due_at' => '2026-10-21T10:00'];
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }
}
