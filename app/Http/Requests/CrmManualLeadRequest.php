<?php

namespace App\Http\Requests;

use App\Models\CrmReference;
use App\Rules\ValidEnquiryPhone;
use App\Support\CrmStudentFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CrmManualLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);

        return Gate::allows('crm.view') && Gate::allows('crm.manage');
    }

    public function rules(): array
    {
        $rules = [
            'creation_key' => ['required', 'uuid'],
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:24', new ValidEnquiryPhone],
            'email' => ['nullable', 'email:rfc', 'max:160'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:80'],
            'course' => ['nullable', 'string', 'max:160'],
            'study_level' => ['nullable', 'string', 'max:50'],
            'intake' => ['nullable', 'string', 'max:50'],
            'english_test' => ['nullable', 'string', 'max:50'],
            'qualification' => ['nullable', 'string', 'max:160'],
            'passing_year' => ['nullable', 'integer', 'between:1950,2100'],
            'percentage' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:4000'],
            'lead_category' => ['nullable', 'string', 'max:160'], 'source_reference' => ['nullable', 'string', 'max:160'],
            'assigned_counsellor_id' => ['missing'], 'assigned_re_id' => ['missing'],
            'stage' => ['missing'], 'created_by' => ['missing'], 'sensitive_profile' => ['missing'],
        ];
        foreach (CrmStudentFields::DICTIONARIES as $key => $kind) {
            $rules[$key][] = function (string $attribute, mixed $value, \Closure $fail) use ($kind): void {
                if (! is_string($value)) {
                    return;
                }
                $values = CrmReference::where('kind', $kind)->where('is_active', true);
                if ((clone $values)->exists() && ! (clone $values)->where('name', $value)->exists()) {
                    $fail('Choose an active configured value.');
                }
            };
        }

        return $rules;
    }
}
