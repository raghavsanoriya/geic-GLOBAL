<?php

namespace Tests\Feature;

use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_multiple_assignees_and_repeated_create_are_audited_once(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $one = $this->staff();
        $two = $this->staff();
        $data = [...$this->payload(), 'creation_key' => (string) Str::uuid(), 'assignees' => [$one->id, $two->id], 'remark' => 'Prepare checklist'];
        $this->actingAs($super)->get('/admin/crm/tasks/create')->assertOk();
        $id = $this->postJson('/admin/crm/tasks', $data)->assertOk()->assertJsonPath('data.version', 1)->json('data.id');
        $this->postJson('/admin/crm/tasks', $data)->assertOk()->assertJsonPath('data.id', $id);
        $this->assertDatabaseCount('crm_tasks', 1);
        $this->assertDatabaseCount('crm_task_assignees', 2);
        $this->assertDatabaseCount('crm_task_notes', 1);
        $this->assertDatabaseCount('crm_task_events', 1);
        $this->assertDatabaseHas('crm_tasks', ['due_at' => '2026-10-21 10:30:00']);
        $this->assertStringNotContainsString('Prepare checklist', DB::table('crm_task_events')->value('changes'));
        $this->actingAs($one)->getJson('/admin/crm/tasks')->assertJsonPath('total', 1);
        $this->actingAs($two)->get('/admin/crm/tasks/'.$id)->assertOk()->assertSee('Prepare checklist')->assertSee('profile-card-has-mask', false)->assertSee('TASK-'.$id);
    }

    public function test_task_access_requires_assignment_or_creation_and_linked_student_access(): void
    {
        $one = $this->staff();
        $two = $this->staff();
        $task = CrmTask::factory()->create(['created_by' => $one->id]);
        $this->actingAs($two)->getJson('/admin/crm/tasks')->assertJsonPath('total', 0);
        $this->getJson('/admin/crm/tasks/'.$task->id)->assertNotFound();
        $this->patchJson('/admin/crm/tasks/'.$task->id, [...$this->payload(), 'version' => 1])->assertNotFound();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $two->id]);
        $task->update(['student_id' => $student->id]);
        $task->assignees()->attach($two->id);
        $this->getJson('/admin/crm/tasks/'.$task->id)->assertOk();
        $this->actingAs($one)->getJson('/admin/crm/tasks/'.$task->id)->assertNotFound();
        $student->update(['assigned_re_id' => null]);
        $this->actingAs($two)->getJson('/admin/crm/tasks/'.$task->id)->assertNotFound();
    }

    public function test_only_staff_with_linked_case_access_may_be_assignees(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $one = $this->staff();
        $student = CrmStudent::factory()->create();
        $data = [...$this->payload(), 'creation_key' => (string) Str::uuid(), 'student_id' => $student->id, 'assignees' => [$one->id]];
        $this->actingAs($super)->postJson('/admin/crm/tasks', $data)->assertUnprocessable()->assertJsonValidationErrors('assignees');
        $student->update(['assigned_counsellor_id' => $one->id]);
        $this->postJson('/admin/crm/tasks', $data)->assertOk();
        $this->actingAs($one)->postJson('/admin/crm/tasks', [...$data, 'creation_key' => (string) Str::uuid()])->assertForbidden();
        $this->assertDatabaseCount('crm_tasks', 1);
    }

    public function test_completion_reopen_and_stale_writes_preserve_remark_history(): void
    {
        $one = $this->staff();
        $task = CrmTask::factory()->create(['created_by' => $one->id]);
        $task->assignees()->attach($one->id);
        $this->actingAs($one)->patchJson('/admin/crm/tasks/'.$task->id, [...$this->payload(), 'version' => 1, 'status' => 'completed', 'remark' => 'Checklist complete'])
            ->assertOk()->assertJsonPath('data.version', 2);
        $this->assertNotNull($task->fresh()->completed_at);
        $this->patchJson('/admin/crm/tasks/'.$task->id, [...$this->payload(), 'version' => 1])->assertConflict();
        $this->patchJson('/admin/crm/tasks/'.$task->id, [...$this->payload(), 'version' => 2, 'status' => 'in_progress', 'remark' => 'Additional document required'])
            ->assertOk()->assertJsonPath('data.version', 3)->assertJsonPath('data.completed_at', null);
        $this->assertDatabaseCount('crm_task_notes', 2);
        $this->assertDatabaseHas('crm_task_notes', ['remark' => 'Checklist complete']);
    }

    public function test_invalid_dates_assignees_and_immutable_fields_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/tasks', [...$this->payload(), 'creation_key' => (string) Str::uuid(),
            'due_at' => '2026-10-20T08:00', 'assignees_present' => 1, 'created_by' => null])
            ->assertUnprocessable()->assertJsonValidationErrors(['due_at', 'assignees', 'created_by']);
        $this->postJson('/admin/crm/tasks', [...$this->payload(), 'creation_key' => (string) Str::uuid(), 'assignees' => [99999]])
            ->assertUnprocessable()->assertJsonValidationErrors('assignees');
        $this->assertDatabaseCount('crm_tasks', 0);
    }

    public function test_filters_escape_text_and_do_not_expose_restricted_student_details(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 22)->setTime(10, 0));
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['sensitive_profile' => ['guardian_name' => 'Restricted guardian']]);
        $task = CrmTask::factory()->create(['student_id' => $student->id, 'due_at' => '2026-10-21 10:00:00', 'priority' => 'urgent', 'title' => '<script>alert(1)</script>']);
        CrmTask::factory()->create(['status' => 'completed', 'due_at' => '2026-10-21 10:00:00']);
        $this->actingAs($super)->getJson('/admin/crm/tasks?overdue=1&priority=urgent')->assertJsonPath('total', 1)->assertDontSee('Restricted guardian');
        $this->get('/admin/crm/tasks')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
        $this->get('/admin/crm/tasks/'.$task->id)->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_authentication_read_only_permissions_and_feature_flag_protect_tasks(): void
    {
        $this->get('/admin/crm/tasks')->assertRedirect('/admin/login');
        $viewer = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($viewer)->postJson('/admin/crm/tasks', [...$this->payload(), 'creation_key' => (string) Str::uuid()])->assertForbidden();
        config(['crm.enabled' => false]);
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->get('/admin/crm/tasks')->assertNotFound();
    }

    private function payload(): array
    {
        return ['title' => 'Review next steps', 'description' => 'Review study documents.', 'status' => 'new', 'priority' => 'normal',
            'starts_at' => '2026-10-21T10:00', 'due_at' => '2026-10-21T16:00'];
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }
}
