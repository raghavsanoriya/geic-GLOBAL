<?php

namespace App\Support;

use App\Models\CrmReference;

class CrmStudentFields
{
    public static function stages(): array
    {
        return array_replace(self::STAGES, CrmReference::where('kind', 'level')->whereIn('code', array_keys(self::STAGES))->pluck('name', 'code')->all());
    }

    public const DICTIONARIES = ['country' => 'country', 'qualification' => 'qualification', 'study_level' => 'qualification', 'lead_category' => 'category', 'source_reference' => 'reference'];

    public const STAGES = ['lead' => 'Lead', 'application' => 'Application', 'visa' => 'VISA', 'billing' => 'Billing'];

    public const STATUSES = ['new' => 'New/Raw', 'high' => 'High', 'moderate' => 'Moderate', 'low' => 'Low', 'dead' => 'Dead/Unqualified', 'academy' => 'Joined Academy', 'application_process' => 'Application Process'];

    public const PROFILE = [
        'city' => 'Current city', 'country' => 'Preferred country', 'course' => 'Preferred course',
        'study_level' => 'Intended qualification level', 'intake' => 'Preferred intake',
        'qualification' => 'Current qualification', 'passing_year' => 'Passing year',
        'percentage' => 'Percentage / CGPA', 'english_test' => 'English-test status',
        'first_name' => 'First name', 'middle_name' => 'Middle name', 'last_name' => 'Last name',
        'secondary_phone' => 'Secondary contact', 'whatsapp_number' => 'WhatsApp number',
        'preferred_city' => 'Preferred study city', 'departure_year' => 'Intended departure year',
        'interest_area' => 'Area of interest', 'source_reference' => 'Enquiry reference',
        'lead_category' => 'Lead category',
    ];

    public const SENSITIVE = [
        'date_of_birth' => 'Date of birth', 'gender' => 'Gender', 'address' => 'Home address',
        'guardian_name' => 'Guardian name', 'guardian_occupation' => 'Guardian occupation',
        'relationship_status' => 'Relationship status', 'study_finance' => 'Study funding',
        'reservation' => 'Reservation (optional)', 'passport_status' => 'Passport status',
    ];
}
