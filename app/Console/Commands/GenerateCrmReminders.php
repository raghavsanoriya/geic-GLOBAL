<?php

namespace App\Console\Commands;

use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateCrmReminders extends Command
{
    protected $signature = 'crm:generate-reminders {--limit=100}';

    protected $description = 'Generate private, idempotent in-app reminders for due tasks and follow-ups';

    public function handle(): int
    {
        if (! config('crm.enabled')) {
            $this->info('CRM is disabled.');

            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 500]]);
        if ($limit === false) {
            $this->error('Use --limit=1..500.');

            return self::FAILURE;
        }
        $lock = Cache::lock('crm-reminder-generator', 90);
        if (! $lock->get()) {
            return self::SUCCESS;
        }
        $count = 0;
        $eligibleUsers = User::where('is_admin', true)->where('is_active', true)->get()
            ->filter(fn (User $user) => $user->hasAdminPermission('crm.view') && ($user->admin_preferences['crm_reminders'] ?? true) !== false)->pluck('id')->all();
        $deadline = microtime(true) + 35;
        try {
            foreach (['follow_up', 'task'] as $kind) {
                $query = $kind === 'follow_up' ? CrmFollowUp::whereIn('status', ['new', 'pending']) : CrmTask::whereNotIn('status', ['completed', 'cancelled'])->with('assignees');
                $query->where('due_at', '<=', now()->addMinutes(30));
                $query->whereExists(function ($users) use ($kind, $eligibleUsers): void {
                    $users->selectRaw('1')->from($kind === 'follow_up' ? 'users' : 'crm_task_assignees');
                    if ($kind === 'follow_up') {
                        $users->whereColumn('users.id', 'crm_follow_ups.owner_id');
                    } else {
                        $users->whereColumn('crm_task_assignees.task_id', 'crm_tasks.id');
                    }
                    $users->whereIn($kind === 'follow_up' ? 'users.id' : 'crm_task_assignees.user_id', $eligibleUsers);
                    $users->whereNotExists(function ($reminders) use ($kind): void {
                        $reminders->selectRaw('1')->from('crm_reminders')->where('kind', $kind);
                        $table = $kind === 'follow_up' ? 'crm_follow_ups' : 'crm_tasks';
                        $reminders->whereColumn('record_id', $table.'.id')->whereColumn('record_version', $table.'.version')
                            ->whereColumn('user_id', $kind === 'follow_up' ? 'users.id' : 'crm_task_assignees.user_id');
                    });
                });
                foreach ($query->lazyById(100) as $record) {
                    if (microtime(true) >= $deadline || $count >= $limit) {
                        break;
                    }
                    $users = $kind === 'follow_up' ? User::whereKey($record->owner_id)->get() : $record->assignees;
                    foreach ($users as $user) {
                        if ($count >= $limit || microtime(true) >= $deadline) {
                            break;
                        }
                        if (! $user->hasAdminPermission('crm.view') || ($user->admin_preferences['crm_reminders'] ?? true) === false) {
                            continue;
                        }
                        $visible = $kind === 'follow_up' ? CrmFollowUp::visibleTo($user) : CrmTask::visibleTo($user);
                        if (! $visible->whereKey($record->id)->exists()) {
                            continue;
                        }
                        $count += DB::table('crm_reminders')->insertOrIgnore(['user_id' => $user->id, 'kind' => $kind, 'record_id' => $record->id,
                            'record_version' => $record->version, 'due_at' => $record->due_at, 'created_at' => now(), 'updated_at' => now()]);
                    }
                }
            }
        } finally {
            $lock->release();
        }
        $this->info("Generated {$count} in-app reminders. No email or push messages were sent.");

        return self::SUCCESS;
    }
}
