<?php

namespace App\Support;

use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CrmAudit
{
    /** @param list<string> $fields */
    public function record(CrmStudent $student, string $action, ?User $actor = null, array $fields = []): void
    {
        DB::table('crm_audit_events')->insert([
            'student_id' => $student->id, 'actor_id' => $actor?->id, 'action' => $action,
            'changes' => json_encode(['version' => $student->version, 'fields' => array_values($fields)], JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }
}
