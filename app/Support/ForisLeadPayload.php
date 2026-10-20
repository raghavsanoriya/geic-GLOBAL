<?php

namespace App\Support;

use InvalidArgumentException;
use stdClass;

class ForisLeadPayload
{
    /** @return array<string, string> */
    public function make(stdClass $lead): array
    {
        $phone = preg_replace('/\D/', '', EnquiryPhoneNumber::normalize($lead->phone) ?? (string) $lead->phone);
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) < 7 || strlen($phone) > 15 || preg_match('/^0+$/', $phone)) {
            throw new InvalidArgumentException('A valid phone number is required by Foris. Correct this lead before retrying.');
        }
        if (! $this->provided($lead->full_name) || $lead->full_name === 'Name not provided') {
            throw new InvalidArgumentException('The student name is missing. Correct this lead before retrying.');
        }

        $metadata = json_decode((string) ($lead->metadata ?? ''), true) ?: [];
        $form = is_array($metadata['form_fields'] ?? null) ? $metadata['form_fields'] : [];
        $payload = ['name' => $lead->full_name, 'contact_number' => $phone, '_hp_check' => ''];
        if (strlen($phone) === 10) {
            $payload['phonecode'] = '91';
        }
        if (filter_var($lead->email, FILTER_VALIDATE_EMAIL) && ! str_ends_with($lead->email, '.invalid')) {
            $payload['email'] = $lead->email;
        }
        if ($this->provided($lead->city)) {
            $payload['city'] = $lead->city;
        }

        $country = match ($lead->destination) {
            'Dubai', 'Dubai & UAE' => 'United Arab Emirates',
            'UK' => 'United Kingdom',
            'USA' => 'United States',
            default => $lead->destination,
        };
        if ($this->provided($country) && ! in_array($country, ['Europe', 'General enquiry', 'Newsletter', 'Not decided yet'], true)
            && ! str_starts_with($lead->source_page, '/services/')
            && ! str_starts_with($lead->source_page, '/tests/')
            && ! str_starts_with($lead->source_page, '/scholarships/')) {
            $payload['pref_country'] = $country;
        }
        if ($this->provided($lead->preferred_course)) {
            $payload['interest_area'] = $lead->preferred_course;
        }
        if (! isset($form['qualification'])) {
            $level = match ($lead->study_level) {
                'Postgraduate' => '1',
                'Undergraduate' => '3',
                'Research' => '4',
                default => null,
            };
            if ($level !== null) {
                $payload['intended_level_qua'] = $level;
            }
        }

        $intake = match (strtolower(trim((string) $lead->preferred_intake))) {
            'february intake' => '2',
            'july intake' => '7',
            'october intake' => '10',
            default => null,
        };
        if ($intake !== null && ! isset($form['passing_year'])) {
            $payload['intake'] = $intake;
        }

        return $payload;
    }

    private function provided(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '' && ! in_array($value, ['Not provided', 'Not specified', 'Not sure yet'], true);
    }
}
