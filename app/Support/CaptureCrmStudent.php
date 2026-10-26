<?php

namespace App\Support;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class CaptureCrmStudent
{
    public function __construct(private CrmAudit $audit) {}

    public function capture(int $enquiryId, ?User $actor = null): ?CrmStudent
    {
        if (! config('crm.enabled')) {
            return null;
        }

        return DB::transaction(function () use ($enquiryId, $actor): ?CrmStudent {
            $linked = DB::table('crm_student_enquiries')->where('enquiry_id', $enquiryId)->value('student_id');
            if ($linked !== null) {
                return CrmStudent::findOrFail($linked);
            }
            $enquiry = DB::table('counselling_enquiries')->where('id', $enquiryId)->lockForUpdate()->first();
            if ($enquiry === null) {
                return null;
            }
            $review = DB::table('lead_duplicate_reviews')->where('enquiry_id', $enquiryId)->first();
            if ($review?->status === 'pending') {
                return null;
            }
            if ($review?->status === 'confirmed_duplicate') {
                if (! $review->linked_enquiry_id || $review->linked_enquiry_id >= $enquiryId) {
                    return null;
                }
                $student = $this->capture((int) $review->linked_enquiry_id, $actor);
                if ($student === null) {
                    return null;
                }
            } else {
                $student = CrmStudent::where('original_enquiry_id', $enquiryId)->first();
                if ($student === null) {
                    $state = $this->initialState($enquiry);
                    $phone = EnquiryPhoneNumber::normalize($enquiry->phone);
                    $email = filter_var($enquiry->email, FILTER_VALIDATE_EMAIL)
                        && ! str_ends_with(mb_strtolower($enquiry->email), '.invalid')
                            ? mb_strtolower(trim($enquiry->email)) : null;
                    $student = CrmStudent::create([
                        'original_enquiry_id' => $enquiryId, 'full_name' => $enquiry->full_name,
                        'email' => $email, 'phone' => $phone, 'stage' => $state['stage'],
                        'lead_status' => 'new', 'stage_status_id' => $state['stage'] === 'lead' ? CrmReference::defaultStatusId('lead') : null,
                        'is_active' => $state['is_active'], 'version' => 1,
                        'profile' => $this->profile($enquiry),
                        'correction_reason' => $phone === null ? 'The original submission has no valid phone number. Original details are preserved.' : null,
                    ]);
                    $this->audit->record($student, 'student.captured', $actor);
                    if ($state['stage'] === 'lead') {
                        DB::table('crm_student_stage_events')->insert(['student_id' => $student->id, 'actor_id' => $actor?->id, 'from_stage' => null, 'to_stage' => 'lead', 'occurred_at' => $enquiry->created_at ?? now(), 'created_at' => now()]);
                    } else {
                        $this->audit->record($student, 'student.imported_stage_snapshot', $actor, ['stage', 'transition_date_unavailable']);
                    }
                }
            }
            $inserted = DB::table('crm_student_enquiries')->insertOrIgnore([
                'student_id' => $student->id, 'enquiry_id' => $enquiryId, 'created_at' => now(),
            ]);
            if ($inserted && $student->original_enquiry_id !== $enquiryId) {
                $this->audit->record($student, 'submission.linked', $actor);
            }

            return $student;
        });
    }

    /** @return array{stage: string, is_active: bool} */
    private function initialState(stdClass $enquiry): array
    {
        if ($enquiry->source !== 'crm_import') {
            return ['stage' => 'lead', 'is_active' => true];
        }
        $metadata = json_decode((string) $enquiry->metadata, true);
        $fields = is_array($metadata['form_fields'] ?? null) ? $metadata['form_fields'] : [];
        $state = Validator::make($fields, [
            'stage' => ['nullable', 'string', 'in:'.implode(',', array_keys(CrmStudentFields::STAGES))],
            'is_active' => ['nullable', 'boolean'],
        ])->validate();

        return ['stage' => filled($state['stage'] ?? null) ? $state['stage'] : 'lead',
            'is_active' => filled($state['is_active'] ?? null) ? (bool) $state['is_active'] : true];
    }

    /** @return array<string, string> */
    private function profile(stdClass $enquiry): array
    {
        $metadata = json_decode((string) $enquiry->metadata, true);
        $fields = is_array($metadata['form_fields'] ?? null) ? $metadata['form_fields'] : [];
        $profile = [
            'city' => $enquiry->city, 'country' => $enquiry->destination,
            'course' => $enquiry->preferred_course, 'study_level' => $enquiry->study_level,
            'intake' => $fields['preferred_intake'] ?? $fields['intake']
                ?? (isset($fields['passing_year']) || isset($fields['event_date']) ? null : $enquiry->preferred_intake),
            'english_test' => $enquiry->english_test,
            'qualification' => $fields['qualification'] ?? null,
            'passing_year' => $fields['passing_year'] ?? null,
            'percentage' => $fields['percentage'] ?? $fields['score'] ?? null,
            'lead_category' => $fields['lead_category'] ?? null, 'source_reference' => $fields['source_reference'] ?? null,
        ];

        return array_map(fn ($value) => mb_substr((string) $value, 0, 500),
            array_filter($profile, fn ($value) => is_scalar($value) && (string) $value !== ''));
    }
}
