<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\CreateCrmManualLead;
use App\Support\SaveCrmFollowUp;
use App\Support\SaveCrmTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;

class CrmPreviewSeeder extends Seeder
{
    public function run(CreateCrmManualLead $leads, SaveCrmFollowUp $followUps, SaveCrmTask $tasks): void
    {
        if (! app()->environment('local') || config('database.default') !== 'sqlite'
            || basename((string) config('database.connections.sqlite.database')) !== 'geic-crm-indore-preview-20261020.sqlite'
            || ! config('crm.enabled')) {
            throw new LogicException('Preview data is restricted to the named isolated local CRM preview database.');
        }
        $actor = User::firstOrCreate(['email' => 'local-preview@geic.test'], [
            'name' => 'Local Preview Administrator', 'password' => Hash::make(Str::random(64)),
            'is_admin' => true, 'is_active' => true, 'admin_role' => 'super_admin', 'admin_permissions' => [],
        ]);
        for ($index = 1; $index <= 3; $index++) {
            $result = $leads->create($actor, [
                'creation_key' => '61b1dba7-e04c-4438-938f-8af284af000'.$index,
                'full_name' => 'Preview student '.$index, 'phone' => '876543219'.$index,
                'email' => 'crm-preview-'.$index.'@example.com', 'city' => 'Indore',
                'country' => ['Germany', 'United Kingdom', 'Dubai'][$index - 1],
                'qualification' => 'Bachelor degree', 'passing_year' => 2026,
            ]);
            if (! $result['student_id']) {
                continue;
            }
            $due = now('Asia/Kolkata')->addDays($index - 2)->setTime(10, 30)->format('Y-m-d\TH:i');
            $followUps->save($actor, [
                'creation_key' => '5dfdbdb0-89dd-4875-a1b8-137889ac000'.$index,
                'subject' => 'Discuss study plans', 'channel' => 'phone', 'status' => 'new', 'due_at' => $due,
                'remark' => 'Synthetic preview record; not a real enquiry.',
            ], studentId: $result['student_id']);
        }
        $tasks->save($actor, [
            'creation_key' => 'f7f699bf-3f5d-4196-9917-a0b193f65b83',
            'title' => 'Prepare counselling checklist', 'description' => 'Synthetic office task for the Indore CRM preview.',
            'status' => 'in_progress', 'priority' => 'high',
            'starts_at' => now('Asia/Kolkata')->startOfDay()->format('Y-m-d\TH:i'),
            'due_at' => now('Asia/Kolkata')->addDay()->setTime(16, 0)->format('Y-m-d\TH:i'),
        ]);
    }
}
