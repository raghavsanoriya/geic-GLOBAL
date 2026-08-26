<?php

namespace App\Http\Controllers;

use App\Support\DestinationCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CounsellingEnquiryController extends Controller
{
    /**
     * Store a study-abroad counselling enquiry from a destination page.
     */
    public function storeAustralia(Request $request): RedirectResponse
    {
        return $this->store($request, 'australia', 'Australia');
    }

    public function storeDestination(Request $request, string $destination): RedirectResponse
    {
        $details = DestinationCatalog::find($destination);

        abort_unless($details, 404);

        return $this->store($request, $details['slug'], $details['name']);
    }

    private function store(Request $request, string $slug, string $destinationName): RedirectResponse
    {
        $returnUrl = "/destinations/{$slug}#contact";

        if ($request->filled('website')) {
            return redirect($returnUrl)
                ->with('enquiry_success', 'Thank you. Our Indore counselling team will contact you shortly.');
        }

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:24', 'regex:/^[0-9+()\-\s]{7,24}$/'],
            'city' => ['required', 'string', 'max:100'],
            'study_level' => ['required', Rule::in(['Undergraduate', 'Postgraduate', 'Diploma or pathway', 'Research', 'Not sure yet'])],
            'preferred_intake' => ['required', Rule::in(['Next available intake', 'February intake', 'July intake', 'October intake', 'Not sure yet'])],
            'preferred_course' => ['nullable', 'string', 'max:160'],
            'english_test' => ['required', Rule::in(['IELTS', 'PTE', 'TOEFL', 'Planning to take a test', 'Not sure yet'])],
            'message' => ['nullable', 'string', 'max:1200'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ], [
            'phone.regex' => 'Enter a valid phone number using digits and standard phone symbols.',
            'consent.accepted' => 'Please allow our counsellor to contact you about this enquiry.',
        ]);

        if ($validator->fails()) {
            return redirect($returnUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        DB::table('counselling_enquiries')->insert([
            'destination' => $destinationName,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'study_level' => $data['study_level'],
            'preferred_intake' => $data['preferred_intake'],
            'preferred_course' => $data['preferred_course'] ?? null,
            'english_test' => $data['english_test'],
            'message' => $data['message'] ?? null,
            'source_page' => "/destinations/{$slug}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect($returnUrl)
            ->with('enquiry_success', 'Thank you. Our Indore counselling team will contact you shortly.');
    }
}
