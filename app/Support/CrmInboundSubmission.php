<?php

namespace App\Support;

use App\Models\CrmInboundForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CrmInboundSubmission
{
    public function __construct(private LeadDuplicateReview $duplicates, private CaptureCrmStudent $capture, private ForisLeadSync $foris) {}

    public function store(CrmInboundForm $form, string $key, string $idempotencyKey, array $fields): array
    {
        ksort($fields);
        $payloadHash = hash('sha256', json_encode($fields, JSON_THROW_ON_ERROR));

        return DB::transaction(function () use ($form, $key, $idempotencyKey, $fields, $payloadHash): array {
            $form = CrmInboundForm::whereKey($form->id)->lockForUpdate()->firstOrFail();
            abort_unless($form->acceptsKey($key), 401, 'Invalid API credentials.');
            $idempotencyHash = hash('sha256', $idempotencyKey);
            $existing = DB::table('crm_inbound_submissions')->where('form_id', $form->id)->where('idempotency_hash', $idempotencyHash)->first();
            if ($existing) {
                abort_unless(hash_equals($existing->payload_hash, $payloadHash), 409, 'This idempotency key was used for different form details.');

                return ['id' => (int) $existing->id, 'enquiry_id' => (int) $existing->enquiry_id, 'status' => (int) $existing->response_status, 'replayed' => true];
            }
            $id = DB::table('counselling_enquiries')->insertGetId([
                'full_name' => $fields['full_name'], 'phone' => EnquiryPhoneNumber::normalize($fields['phone']), 'email' => filled($fields['email'] ?? null) ? mb_strtolower(trim($fields['email'])) : '',
                'destination' => $fields['country'] ?? '', 'city' => $fields['city'] ?? '', 'preferred_course' => $fields['course'] ?? null,
                'study_level' => $fields['study_level'] ?? '', 'preferred_intake' => $fields['intake'] ?? '', 'english_test' => $fields['english_test'] ?? '',
                'message' => $fields['message'] ?? null, 'source' => 'inbound_api', 'source_page' => '/api/v1/inbound-forms/'.$form->form_key.'/submissions',
                'source_form' => $form->name, 'metadata' => json_encode(['form_fields' => $fields], JSON_THROW_ON_ERROR), 'created_at' => now(), 'updated_at' => now()]);
            $this->duplicates->capture($id);
            $review = DB::table('lead_duplicate_reviews')->where('enquiry_id', $id)->where('status', 'pending')->exists();
            if ($review && $form->duplicate_policy === 'reject') {
                throw ValidationException::withMessages(['phone' => 'A matching contact is already registered. Ask the administrator to review the existing lead.']);
            }
            $this->capture->capture($id);
            if ($form->forward_to_foris) {
                $this->foris->register($id);
            }
            $status = $review ? 202 : 201;
            $receipt = DB::table('crm_inbound_submissions')->insertGetId(['form_id' => $form->id, 'idempotency_hash' => $idempotencyHash, 'payload_hash' => $payloadHash, 'enquiry_id' => $id, 'response_status' => $status, 'created_at' => now()]);

            return ['id' => $receipt, 'enquiry_id' => $id, 'status' => $status, 'replayed' => false];
        });
    }
}
