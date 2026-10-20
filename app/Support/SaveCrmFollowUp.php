<?php

namespace App\Support;

use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveCrmFollowUp
{
    public function __construct(private CrmAudit $audit) {}

    public function save(User $actor, array $data, ?int $studentId = null, ?int $followUpId = null): CrmFollowUp
    {
        return DB::transaction(function () use ($actor, $data, $studentId, $followUpId): CrmFollowUp {
            $record = $followUpId ? CrmFollowUp::visibleTo($actor)->findOrFail($followUpId) : null;
            $student = CrmStudent::visibleTo($actor)->whereKey($record?->student_id ?? $studentId)->lockForUpdate()->firstOrFail();
            if ($record) {
                $record = CrmFollowUp::visibleTo($actor)->whereKey($followUpId)->lockForUpdate()->firstOrFail();
                abort_unless($record->version === (int) $data['version'], 409, 'This follow-up has changed. Reload before saving.');
            } else {
                $existing = CrmFollowUp::where('creation_key', $data['creation_key'])->first();
                if ($existing) {
                    abort_unless($existing->student_id === $student->id && $existing->created_by === $actor->id, 409, 'This submission key has already been used.');

                    return $existing;
                }
            }
            $owner = User::find($data['owner_id'] ?? $record?->owner_id ?? $actor->id);
            if (! $owner || ! $owner->hasAdminPermission('crm.view') || ! CrmStudent::visibleTo($owner)->whereKey($student->id)->exists()) {
                throw ValidationException::withMessages(['owner_id' => 'The follow-up owner must have access to this assigned student.']);
            }
            $creating = $record === null;
            $record ??= new CrmFollowUp(['student_id' => $student->id, 'created_by' => $actor->id, 'creation_key' => $data['creation_key'], 'version' => 1]);
            $record->fill([
                'owner_id' => $owner->id, 'subject' => $data['subject'], 'channel' => $data['channel'],
                'due_at' => CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['due_at'], 'Asia/Kolkata')->utc(),
                'status' => $data['status'],
            ]);
            $record->ended_at = $data['status'] === 'ended' ? ($record->ended_at ?? now()) : null;
            $changed = array_keys($record->getDirty());
            $remark = trim($data['remark'] ?? '');
            if (! $creating && ($changed !== [] || $remark !== '')) {
                $record->version++;
            }
            $record->save();
            if ($remark !== '') {
                DB::table('crm_follow_up_notes')->insert(['follow_up_id' => $record->id, 'actor_id' => $actor->id, 'remark' => $remark, 'created_at' => now()]);
            }
            if ($creating || $changed !== [] || $remark !== '') {
                $this->audit->record($student, $creating ? 'follow_up.created' : 'follow_up.updated', $actor, [...$changed, ...($remark !== '' ? ['remark_added'] : [])]);
            }

            return $record;
        });
    }
}
