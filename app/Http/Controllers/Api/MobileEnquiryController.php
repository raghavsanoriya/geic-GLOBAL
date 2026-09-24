<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MobileEnquiryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you. Your enquiry has been received.',
            ], 201);
        }

        $data = $request->validate([
            'kind' => ['required', Rule::in(['counselling', 'event', 'service', 'expo'])],
            'fullName' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:24', 'regex:/^[0-9+()\-\s]{7,24}$/'],
            'city' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:80'],
            'studyLevel' => ['nullable', 'string', 'max:50'],
            'preferredIntake' => ['nullable', 'string', 'max:50'],
            'preferredCourse' => ['nullable', 'string', 'max:160'],
            'englishTest' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:1200'],
            'referenceId' => ['nullable', 'string', 'max:120'],
            'platform' => ['nullable', Rule::in(['android', 'ios', 'web'])],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ], [
            'phone.regex' => 'Enter a valid phone number using digits and standard phone symbols.',
            'consent.accepted' => 'Please allow our counsellor to contact you about this enquiry.',
        ]);

        $sourcePage = '/mobile/'.$data['kind'];
        $id = DB::table('counselling_enquiries')->insertGetId([
            'destination' => $data['destination'] ?? 'General enquiry',
            'full_name' => $data['fullName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'] ?? 'Not provided',
            'study_level' => $data['studyLevel'] ?? 'Not sure yet',
            'preferred_intake' => $data['preferredIntake'] ?? 'Not sure yet',
            'preferred_course' => $data['preferredCourse'] ?? null,
            'english_test' => $data['englishTest'] ?? 'Not sure yet',
            'message' => $data['message'] ?? null,
            'source_page' => $sourcePage,
            'source' => 'mobile',
            'source_form' => $data['kind'],
            'metadata' => json_encode([
                'reference_id' => $data['referenceId'] ?? null,
                'platform' => $data['platform'] ?? null,
                'consent' => true,
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('site_events')->insert([
            'event_type' => 'form_submit',
            'path' => $sourcePage,
            'label' => $data['referenceId'] ?? $data['kind'],
            'target' => null,
            'referrer_domain' => null,
            'visitor_hash' => hash_hmac('sha256', $request->ip().'|'.mb_substr((string) $request->userAgent(), 0, 180), (string) config('app.key')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you. A GEIC counsellor will contact you shortly.',
            'reference' => 'GEIC-'.$id,
        ], 201);
    }
}
