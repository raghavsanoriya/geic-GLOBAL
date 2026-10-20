<?php

namespace App\Support;

use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveCrmTask
{
    public function save(User $actor, array $data, ?int $id = null): CrmTask
    {
        return DB::transaction(function () use ($actor, $data, $id): CrmTask {
            DB::table('users')->where('id', $actor->id)->lockForUpdate()->first();
            $task = $id ? CrmTask::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail() : null;
            if ($task) {
                abort_unless($task->version === (int) $data['version'], 409, 'This task has changed. Reload before saving.');
            } else {
                $existing = CrmTask::where('creation_key', $data['creation_key'])->first();
                if ($existing) {
                    abort_unless($existing->created_by === $actor->id && CrmTask::visibleTo($actor)->whereKey($existing->id)->exists(), 409, 'This submission key has already been used.');

                    return $existing;
                }
            }
            $creating = $task === null;
            $studentId = $task?->student_id ?? ($data['student_id'] ?? null);
            $student = $studentId ? CrmStudent::visibleTo($actor)->whereKey($studentId)->lockForUpdate()->firstOrFail() : null;
            $assignees = array_map('intval', $data['assignees'] ?? ($task ? $task->assignees()->pluck('users.id')->all() : [$actor->id]));
            $users = User::whereIn('id', $assignees)->get();
            if ($users->count() !== count($assignees) || $users->contains(fn (User $user) => ! $user->hasAdminPermission('crm.view')
                || ($student && ! CrmAccess::viewsAll($user) && ! in_array($user->id, [$student->assigned_counsellor_id, $student->assigned_re_id], true)))) {
                throw ValidationException::withMessages(['assignees' => 'Every assignee must be active, have CRM access and be allowed to access the linked student.']);
            }
            $task ??= new CrmTask(['student_id' => $student?->id, 'created_by' => $actor->id, 'creation_key' => $data['creation_key'], 'version' => 1]);
            $oldAssignees = $creating ? [] : $task->assignees()->pluck('users.id')->map(fn ($value) => (int) $value)->all();
            sort($oldAssignees);
            sort($assignees);
            $task->fill(['title' => $data['title'], 'description' => $data['description'] ?? null, 'status' => $data['status'], 'priority' => $data['priority'],
                'starts_at' => CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['starts_at'], 'Asia/Kolkata')->utc(),
                'due_at' => CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['due_at'], 'Asia/Kolkata')->utc()]);
            $task->completed_at = $data['status'] === 'completed' ? ($task->completed_at ?? now()) : null;
            $fields = array_keys($task->getDirty());
            if ($oldAssignees !== $assignees) {
                $fields[] = 'assignees';
            }
            $remark = trim($data['remark'] ?? '');
            if (! $creating && ($fields !== [] || $remark !== '')) {
                $task->version++;
            }
            $task->save();
            if ($oldAssignees !== $assignees) {
                $task->assignees()->sync($assignees);
            }
            if ($remark !== '') {
                DB::table('crm_task_notes')->insert(['task_id' => $task->id, 'actor_id' => $actor->id, 'remark' => $remark, 'created_at' => now()]);
                $fields[] = 'remark_added';
            }
            if ($creating || $fields !== []) {
                DB::table('crm_task_events')->insert(['task_id' => $task->id, 'actor_id' => $actor->id, 'action' => $creating ? 'task.created' : 'task.updated',
                    'changes' => json_encode(['version' => $task->version, 'fields' => $fields], JSON_THROW_ON_ERROR), 'created_at' => now()]);
            }

            return $task->load('assignees:id,name');
        });
    }
}
