<?php

namespace App\Http\Controllers;

use App\Models\CrmInboundForm;
use App\Rules\ValidEnquiryPhone;
use App\Support\CrmInboundSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class CrmInboundSubmissionController extends Controller
{
    public function __invoke(Request $request, string $formKey, CrmInboundSubmission $submissions): JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        $form = CrmInboundForm::where('form_key', $formKey)->first();
        $apiKey = (string) $request->header('X-API-KEY', '');
        abort_unless($form && $form->acceptsKey($apiKey), 401, 'Invalid API credentials.');
        abort_unless($request->isJson(), 415, 'Send the request body as application/json.');
        $rateKey = 'crm-inbound:'.$form->id.':'.hash('sha256', (string) $request->ip());
        abort_if(RateLimiter::tooManyAttempts($rateKey, $form->rate_limit_per_minute), 429, 'Too many submissions for this endpoint. Retry later.');
        RateLimiter::hit($rateKey, 60);
        if (filled($form->honeypot_field) && filled($request->input($form->honeypot_field))) {
            return response()->json(['message' => 'Submission accepted.'], 202, ['Cache-Control' => 'private, no-store']);
        }
        $idempotencyKey = (string) $request->header('Idempotency-Key', '');
        Validator::make(['idempotency_key' => $idempotencyKey], ['idempotency_key' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{8,120}$/D']])->validate();
        $mapped = $form->defaults ?? [];
        foreach ($form->field_map as $canonical => $source) {
            if ($request->exists($source)) {
                $mapped[$canonical] = $request->input($source);
            }
        }
        $fields = Validator::make($mapped, [
            'full_name' => ['required', 'string', 'max:120'], 'phone' => ['required', 'string', 'max:24', new ValidEnquiryPhone],
            'email' => ['nullable', 'email:rfc', 'max:160'], 'city' => ['nullable', 'string', 'max:100'], 'country' => ['nullable', 'string', 'max:80'],
            'course' => ['nullable', 'string', 'max:160'], 'study_level' => ['nullable', 'string', 'max:50'], 'intake' => ['nullable', 'string', 'max:50'],
            'qualification' => ['nullable', 'string', 'max:160'], 'passing_year' => ['nullable', 'integer', 'between:1950,2100'],
            'percentage' => ['nullable', 'string', 'max:50'], 'english_test' => ['nullable', 'string', 'max:50'],
            'lead_category' => ['nullable', 'string', 'max:160'], 'source_reference' => ['nullable', 'string', 'max:160'], 'message' => ['nullable', 'string', 'max:4000'],
        ])->validate();
        $receipt = $submissions->store($form, $apiKey, $idempotencyKey, $fields);

        return response()->json(['message' => $receipt['status'] === 202 ? 'Submission saved for duplicate review.' : 'Submission saved.',
            'data' => ['submission_id' => $receipt['id'], 'reference' => 'GEIC-'.$receipt['enquiry_id'], 'replayed' => $receipt['replayed']]], $receipt['status'], ['Cache-Control' => 'private, no-store']);
    }
}
