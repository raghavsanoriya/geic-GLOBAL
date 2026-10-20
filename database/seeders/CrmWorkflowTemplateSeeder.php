<?php

namespace Database\Seeders;

use App\Models\CrmReference;
use App\Support\CrmStudentFields;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class CrmWorkflowTemplateSeeder extends Seeder
{
    public ?int $actorId = null;

    private function recordCreation(CrmReference $reference): void
    {
        if ($reference->wasRecentlyCreated) {
            DB::table('crm_reference_events')->insert(['reference_id' => $reference->id, 'actor_id' => $this->actorId, 'action' => 'reference.created',
                'changes' => json_encode(['version' => 1, 'fields' => ['workflow_template']], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! config('crm.enabled')) {
            throw new LogicException('Explicitly enable CRM before installing the audited workflow template.');
        }
        DB::transaction(function (): void {
            $levels = [];
            foreach (CrmStudentFields::STAGES as $code => $name) {
                $levels[$code] = CrmReference::firstOrCreate(['kind' => 'level', 'code' => $code], ['name' => $name, 'is_active' => true, 'version' => 1]);
                $this->recordCreation($levels[$code]);
            }
            $definitions = [
                'lead' => [['New/Raw'], ['High'], ['Moderate'], ['Low'], ['Dead/Unqualified', 'dead'], ['Joined Academy'], ['Application Process', 'main', 'application']],
                'application' => [['New'], ['Working'], ['Applied To University'], ['Offer Phase'], ['In Payment Phase'], ['Drop', 'dead'], ['Fess Paid (Uni / College Tuition)', 'main', 'visa'], ['Back to Lead', 'main', 'lead']],
                'visa' => [['Fees Paid'], ['Working'], ['Apply For Refund'], ['Refund Transfered', 'dead'], ['VISA Recived', 'main', 'billing'], ['Application Process', 'main', 'application'], ['Old Done', 'dead'], ['Applied for VISA'], ['VISA Rejected'], ['Cast Requested'], ['Cast Approved']],
                'billing' => [['New'], ['Due'], ['Partial Paid'], ['Paid'], ['Cancelled', 'dead'], ['Application Process', 'main', 'application'], ['VISA Process', 'main', 'visa']],
            ];
            foreach ($definitions as $stage => $statuses) {
                CrmReference::whereKey($levels[$stage]->id)->lockForUpdate()->firstOrFail();
                $hasDefault = CrmReference::where('kind', 'level_status')->where('parent_id', $levels[$stage]->id)->where('configuration->is_default', true)->exists();
                foreach ($statuses as $index => $definition) {
                    $status = CrmReference::firstOrCreate(['kind' => 'level_status', 'code' => $stage.'_'.($index + 1)], [
                        'name' => $definition[0], 'parent_id' => $levels[$stage]->id, 'is_active' => true, 'version' => 1,
                        'configuration' => ['list_type' => $definition[1] ?? 'main', 'transfer_stage' => $definition[2] ?? null, 'is_default' => $index === 0 && ! $hasDefault],
                        'description' => 'Audited lifecycle template. Transfers require explicit staff confirmation.',
                    ]);
                    $this->recordCreation($status);
                }
            }
            $titles = [-1 => 'Drop', 1 => 'Document Pending', 2 => 'Document Completed', 3 => 'Sent To Agent', 4 => 'Rejection From Agent', 5 => 'Pending Queries', 6 => 'Queries Fullfilled', 7 => 'Submitted To Uni', 8 => 'Rejection From Uni', 9 => 'Conditional Offer', 10 => 'Uncondittional Offer', 11 => 'Pending Conditions', 12 => 'Conditions Fullfilled', 13 => 'GTE Process Started', 14 => 'GTE Approved', 15 => 'GTE Rejected', 16 => 'Pending Fee', 17 => 'Tuition Fee Paid', 18 => 'Acceptance Doc. Received', 19 => 'Applied For VISA', 20 => 'VISA Received', 21 => 'Rejection From VISA', 22 => 'Pending Refund', 23 => 'Refunded', 24 => 'Defer', 25 => 'Intake Not Available', 26 => 'Intake Closed', 27 => 'Intake Not Open Yet', 28 => 'Course Not Available', 29 => 'Ready To Pay'];
            $matrix = [
                -1 => [1, 3, 24, 17, 29], 1 => [2, 3, -1], 2 => [1, 3, -1, 26, 25, 27], 3 => [2, 4, 7, -1, 26, 25, 27], 4 => [3, 7, -1, 26, 25, 27],
                5 => [3, 6, -1, 26, 25, 27], 6 => [5, -1, 26, 25, 27], 7 => [6, 3, 8, 9, 10, -1, 1, 5], 8 => [7, -1],
                9 => [7, 10, 24, 11, 17, 22, -1, 29, 14, 13, 15], 10 => [7, 9, 24, 11, 17, 22, -1, 14, 13, 15, 29], 11 => [9, 12, -1], 12 => [11, 13, -1],
                13 => [12, 10, 14, 15, 17, -1], 14 => [13, 17, -1], 15 => [14, -1], 16 => [14, 10, 12, -1], 17 => [16, 18, 22, -1, 20, 19, 21],
                18 => [17, 19, 22, -1], 19 => [18, 20, 22, -1], 20 => [19, 21, 22, -1], 21 => [19, 20, 22, -1], 22 => [21, 15, 17, 18, 19, -1, 23],
                23 => [22], 24 => [10, 12, -1, 23], 25 => [2, 3, 5, 6, -1], 26 => [2, 3, 5, 6, -1], 27 => [2, 3, 5, 6, -1], 28 => [2, 3, 5, 6, -1], 29 => [16, 18, 22, -1],
            ];
            $applications = [];
            $new = [];
            foreach ($titles as $sourceId => $name) {
                $reference = CrmReference::firstOrCreate(['kind' => 'application_status', 'code' => 'workflow_app_'.($sourceId === -1 ? 'drop' : $sourceId)], [
                    'name' => $name, 'is_active' => true, 'version' => 1, 'outcome' => $sourceId === 20 ? 'success' : (in_array($sourceId, [-1, 4, 8, 15, 21, 23], true) ? 'failure' : 'progress'),
                    'description' => 'Audited application workflow template. Existing custom configuration is never overwritten.',
                ]);
                $applications[$sourceId] = $reference->id;
                if ($reference->wasRecentlyCreated) {
                    $new[] = $sourceId;
                }
                $this->recordCreation($reference);
            }
            foreach ($new as $sourceId) {
                foreach (array_unique($matrix[$sourceId]) as $target) {
                    DB::table('crm_status_transitions')->insertOrIgnore(['from_id' => $applications[$sourceId], 'to_id' => $applications[$target]]);
                }
            }
            $visaIds = [];
            $newVisaIds = [];
            foreach ([1 => 'New', 2 => 'Acceptance Doc. Received', 3 => 'Applied For VISA', 4 => 'VISA Received', 5 => 'Rejection From VISA', 6 => 'Applied For Refund', 7 => 'Refunded', 8 => 'Drop'] as $sourceId => $name) {
                $visa = CrmReference::firstOrCreate(['kind' => 'visa_status', 'code' => 'workflow_visa_'.$sourceId], [
                    'name' => $name, 'is_active' => true, 'version' => 1, 'outcome' => $sourceId === 4 ? 'success' : (in_array($sourceId, [5, 7, 8], true) ? 'failure' : 'progress'),
                    'linked_application_status_id' => $sourceId === 4 ? $applications[20] : null,
                    'description' => 'Audited visa workflow template. Configure allowed next statuses before operational use.',
                ]);
                $visaIds[] = $visa->id;
                if ($visa->wasRecentlyCreated) {
                    $newVisaIds[] = $visa->id;
                }
                $this->recordCreation($visa);
            }
            foreach ($newVisaIds as $from) {
                foreach ($visaIds as $to) {
                    if ($from !== $to) {
                        DB::table('crm_status_transitions')->insertOrIgnore(['from_id' => $from, 'to_id' => $to]);
                    }
                }
            }
        });
    }
}
