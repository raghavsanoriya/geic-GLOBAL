<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use stdClass;

class LeadDuplicateReview
{
    public const STATUSES = ['pending', 'confirmed_duplicate', 'distinct'];

    public function indexContact(stdClass $enquiry): void
    {
        $email = mb_strtolower(trim((string) $enquiry->email));
        $email = filter_var($email, FILTER_VALIDATE_EMAIL) && ! str_ends_with($email, '.invalid') ? $email : null;
        $phone = EnquiryPhoneNumber::normalize($enquiry->phone);
        DB::table('lead_contact_keys')->insertOrIgnore([
            'enquiry_id' => $enquiry->id,
            'email_key' => $email === null ? null : $this->key('email', $email),
            'phone_key' => $phone === null ? null : $this->key('phone', $phone),
        ]);
    }

    public function capture(int $enquiryId): void
    {
        $enquiry = DB::table('counselling_enquiries')->find($enquiryId);
        $this->indexContact($enquiry);
        $contact = DB::table('lead_contact_keys')->where('enquiry_id', $enquiryId)->first();
        if ($contact->email_key === null && $contact->phone_key === null) {
            return;
        }
        $matches = DB::table('lead_contact_keys')->where('enquiry_id', '<', $enquiryId)
            ->where(function ($query) use ($contact): void {
                if ($contact->email_key !== null) {
                    $query->where('email_key', $contact->email_key);
                }
                if ($contact->phone_key !== null) {
                    $query->orWhere('phone_key', $contact->phone_key);
                }
            })->orderBy('enquiry_id')->get()->map(function ($match) use ($contact): array {
                $reasons = [];
                if ($contact->email_key !== null && $match->email_key === $contact->email_key) {
                    $reasons[] = 'email';
                }
                if ($contact->phone_key !== null && $match->phone_key === $contact->phone_key) {
                    $reasons[] = 'phone';
                }

                return ['enquiry_id' => (int) $match->enquiry_id, 'reasons' => $reasons];
            });
        if ($matches->isEmpty()) {
            return;
        }
        DB::table('lead_duplicate_reviews')->insertOrIgnore([
            'enquiry_id' => $enquiryId,
            'matches' => $matches->toJson(),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function key(string $type, string $value): string
    {
        return hash_hmac('sha256', $type.':'.$value, (string) config('app.key'));
    }
}
