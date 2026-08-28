<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPageState;
use App\Support\DestinationCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CounsellingEnquiryController extends Controller
{
    /**
     * Store the standalone campaign landing-page profile request.
     */
    public function storeLanding(Request $request): JsonResponse
    {
        return $this->storePromotional($request, '/landing');
    }

    public function storePromotion(Request $request, string $promotion): JsonResponse
    {
        $page = CmsPage::query()
            ->where('group', 'promotions')
            ->where('slug', $promotion)
            ->firstOrFail();
        $state = CmsPageState::query()->where('page_key', $page->page_key)->first();
        abort_unless($state?->status === 'published', 404);

        return $this->storePromotional($request, '/'.$page->path);
    }

    private function storePromotional(Request $request, string $sourcePage): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you. Your enquiry has been received.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:24', 'regex:/^[0-9+()\-\s]{7,24}$/'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'qualification' => ['required', 'string', 'max:100'],
            'passing_year' => ['required', 'integer', 'between:1990,2035'],
            'score' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'website' => ['nullable', 'max:0'],
        ]);

        DB::table('counselling_enquiries')->insert([
            'destination' => $data['country'],
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => 'Not provided',
            'study_level' => $data['qualification'],
            'preferred_intake' => 'Passing year: '.$data['passing_year'],
            'preferred_course' => null,
            'english_test' => 'Not sure yet',
            'message' => 'Academic score: '.$data['score'],
            'source_page' => $sourcePage,
            ...$this->trackingAttributes($request),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->recordConversion($request, $sourcePage, $data['country']);

        return response()->json([
            'success' => true,
            'message' => 'Thank you. Your profile evaluation request has been received.',
        ], 201);
    }

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

    /**
     * Store a general enquiry from the dedicated contact page.
     */
    public function storeContact(Request $request): RedirectResponse
    {
        return $this->store($request, 'contact', 'General enquiry', '/contact#enquiry');
    }

    /**
     * Store the compact enquiry form displayed in detail-page heroes.
     */
    public function storeHero(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:24', 'regex:/^[0-9+()\-\s]{7,24}$/'],
            'source_context' => ['required', 'string', 'max:120'],
            'return_to' => ['required', 'regex:~^/(?:destinations|services|scholarships|tests)/[a-z\-]+\#overview$~'],
            'website' => ['nullable', 'max:0'],
        ], [
            'phone.regex' => 'Enter a valid phone number using digits and standard phone symbols.',
        ]);

        $returnUrl = $request->string('return_to')->toString() ?: '/contact#enquiry';

        if ($validator->fails()) {
            return redirect($returnUrl)->withErrors($validator)->withInput();
        }

        if ($request->filled('website')) {
            return redirect($returnUrl)
                ->with('enquiry_success', 'Thank you. Our Indore counselling team will contact you shortly.');
        }

        $data = $validator->validated();

        DB::table('counselling_enquiries')->insert([
            'destination' => $data['source_context'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => 'Not provided',
            'study_level' => 'Not sure yet',
            'preferred_intake' => 'Not sure yet',
            'preferred_course' => null,
            'english_test' => 'Not sure yet',
            'message' => 'Submitted through the detail-page hero form.',
            'source_page' => strtok($returnUrl, '#'),
            ...$this->trackingAttributes($request),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->recordConversion($request, strtok($returnUrl, '#'), $data['source_context']);

        return redirect($returnUrl)
            ->with('enquiry_success', 'Thank you. Our Indore counselling team will contact you shortly.');
    }

    private function store(Request $request, string $slug, string $destinationName, ?string $returnUrl = null): RedirectResponse
    {
        $returnUrl ??= "/destinations/{$slug}#contact";

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
            'source_page' => $slug === 'contact' ? '/contact' : "/destinations/{$slug}",
            ...$this->trackingAttributes($request),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->recordConversion($request, $slug === 'contact' ? '/contact' : "/destinations/{$slug}", $destinationName);

        return redirect($returnUrl)
            ->with('enquiry_success', 'Thank you. Our Indore counselling team will contact you shortly.');
    }

    /** @return array{source: string, utm_source: ?string, utm_medium: ?string, utm_campaign: ?string} */
    private function trackingAttributes(Request $request): array
    {
        return [
            'source' => 'website',
            'utm_source' => $this->limited($request->input('utm_source'), 120),
            'utm_medium' => $this->limited($request->input('utm_medium'), 120),
            'utm_campaign' => $this->limited($request->input('utm_campaign'), 180),
        ];
    }

    private function recordConversion(Request $request, string $path, string $label): void
    {
        DB::table('site_events')->insert([
            'event_type' => 'form_submit',
            'path' => $path,
            'label' => $label,
            'target' => null,
            'referrer_domain' => null,
            'utm_source' => $this->limited($request->input('utm_source'), 120),
            'utm_medium' => $this->limited($request->input('utm_medium'), 120),
            'utm_campaign' => $this->limited($request->input('utm_campaign'), 180),
            'visitor_hash' => hash_hmac('sha256', $request->ip().'|'.mb_substr((string) $request->userAgent(), 0, 180), (string) config('app.key')),
            'session_hash' => filled($request->input('analytics_session_id'))
                ? hash_hmac('sha256', (string) $request->input('analytics_session_id'), (string) config('app.key'))
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function limited(mixed $value, int $length): ?string
    {
        return is_string($value) && trim($value) !== '' ? mb_substr(trim($value), 0, $length) : null;
    }
}
