<?php

namespace Database\Seeders;

use App\Models\CrmReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class CrmReferenceSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('crm.enabled')) {
            throw new LogicException('Enable the verified CRM module before explicitly installing starter statuses.');
        }
        DB::transaction(function (): void {
            foreach ([
                'application_status' => ['draft' => 'Draft', 'submitted' => 'Submitted', 'offer_received' => 'Offer received', 'accepted' => 'Accepted', 'withdrawn' => 'Withdrawn'],
                'visa_status' => ['preparing' => 'Preparing', 'lodged' => 'Lodged', 'approved' => 'Approved', 'refused' => 'Refused', 'withdrawn' => 'Withdrawn'],
            ] as $kind => $values) {
                foreach ($values as $code => $name) {
                    CrmReference::firstOrCreate(['kind' => $kind, 'code' => $code], ['name' => $name, 'is_active' => true, 'version' => 1,
                        'outcome' => in_array($code, ['accepted', 'approved'], true) ? 'success' : (in_array($code, ['withdrawn', 'refused'], true) ? 'failure' : 'progress'),
                        'description' => 'GEIC starter status. Configure permitted transitions before operational use.']);
                }
            }
        });
    }
}
