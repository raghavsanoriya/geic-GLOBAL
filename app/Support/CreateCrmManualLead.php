<?php

namespace App\Support;

use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateCrmManualLead
{
    public function __construct(private LeadDuplicateReview $duplicates, private CaptureCrmStudent $capture, private CrmAudit $audit) {}

    /** @return array{enquiry_id: int, student_id: ?int, review_pending: bool} */
    public function create(User $actor, array $data): array
    {
        return DB::transaction(function () use ($actor, $data): array {
            DB::table('users')->where('id', $actor->id)->lockForUpdate()->first();
            $receipt = DB::table('crm_manual_submissions')->where('creation_key', $data['creation_key'])->first();
            if ($receipt) {
                abort_unless((int) $receipt->actor_id === $actor->id, 409, 'This submission key has already been used.');
                $id = (int) $receipt->enquiry_id;
            } else {
                $id = DB::table('counselling_enquiries')->insertGetId([
                    'full_name' => $data['full_name'], 'email' => mb_strtolower(trim($data['email'] ?? '')),
                    'phone' => EnquiryPhoneNumber::normalize($data['phone']),
                    'city' => $data['city'] ?? '', 'destination' => $data['country'] ?? '',
                    'preferred_course' => $data['course'] ?? null, 'study_level' => $data['study_level'] ?? '',
                    'preferred_intake' => $data['intake'] ?? '', 'english_test' => $data['english_test'] ?? '',
                    'message' => $data['message'] ?? null, 'source_page' => '/admin/crm/leads/create',
                    'source_form' => 'Staff-created GEIC lead',
                    'metadata' => json_encode(['form_fields' => Arr::except($data, ['creation_key'])], JSON_THROW_ON_ERROR),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $this->duplicates->capture($id);
                DB::table('crm_manual_submissions')->insert(['creation_key' => $data['creation_key'], 'actor_id' => $actor->id, 'enquiry_id' => $id, 'created_at' => now()]);
            }
            $student = $this->capture->capture($id, $actor);
            if (! $receipt && $student) {
                $student->created_by = $actor->id;
                $student->assigned_counsellor_id = $actor->id;
                $student->save();
                $this->audit->record($student, 'student.manually_created', $actor, ['created_by', 'assigned_counsellor_id']);
            }
            $visible = $student && CrmStudent::visibleTo($actor)->whereKey($student->id)->exists();

            return ['enquiry_id' => $id, 'student_id' => $visible ? $student->id : null, 'review_pending' => $student === null];
        });
    }
}
