<?php

namespace App\Support;

use App\Models\CrmCampaign;
use App\Models\CrmStudent;
use App\Models\User;
use App\Rules\ValidEnquiryPhone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CrmCampaigns
{
    public function audience(CrmCampaign $campaign, User $user): array
    {
        $filter = $campaign->audience;
        $candidates = [];
        if ($filter['mode'] === 'students') {
            $query = CrmStudent::visibleTo($user)->where('is_active', true);
            foreach (['stage', 'lead_status'] as $field) {
                if (filled($filter[$field] ?? null)) {
                    $query->where($field, $filter[$field]);
                }
            }
            foreach ($query->orderBy('id')->limit(1001)->get(['id', 'full_name', 'email', 'phone']) as $student) {
                $candidates[] = ['student_id' => $student->id, 'name' => $student->full_name, 'address' => $student->{$campaign->channel === 'email' ? 'email' : 'phone'}];
            }
        } else {
            foreach (preg_split('/\R/', trim($filter['manual'] ?? '')) as $line) {
                if (trim($line) === '') {
                    continue;
                }
                $row = str_getcsv($line, ',', '"', '');
                $candidates[] = ['student_id' => null, 'name' => mb_substr(trim($row[1] ?? ''), 0, 160), 'address' => trim($row[0] ?? '')];
            }
        }
        if (count($candidates) > 1000) {
            throw ValidationException::withMessages(['audience' => 'Limit each campaign to 1,000 recipients. Narrow the filters or split the CSV list.']);
        }
        $rows = [];
        $invalid = 0;
        $duplicates = 0;
        $suppressed = 0;
        $excluded = DB::table('crm_marketing_suppressions')->where('channel', $campaign->channel)->pluck('address')->flip();
        foreach ($candidates as $row) {
            $address = $campaign->channel === 'email' ? mb_strtolower(trim($row['address'] ?? '')) : EnquiryPhoneNumber::normalize((string) ($row['address'] ?? ''));
            $validator = Validator::make(['address' => $address], ['address' => $campaign->channel === 'email' ? ['required', 'email:rfc', 'max:254'] : ['required', new ValidEnquiryPhone]]);
            if ($validator->fails()) {
                $invalid++;

                continue;
            }
            if ($excluded->has($address)) {
                $suppressed++;

                continue;
            }
            if (isset($rows[$address])) {
                $duplicates++;

                continue;
            }
            $rows[$address] = [...$row, 'address' => $address];
        }

        return ['recipients' => array_values($rows), 'valid' => count($rows), 'invalid' => $invalid, 'duplicates' => $duplicates, 'suppressed' => $suppressed];
    }

    public function record(CrmCampaign $campaign, string $action, User $user): void
    {
        DB::table('crm_campaign_events')->insert(['campaign_id' => $campaign->id, 'actor_id' => $user->id, 'action' => $action, 'created_at' => now()]);
    }
}
