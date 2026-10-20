<?php

namespace App\Http\Requests;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use App\Rules\ValidEnquiryPhone;
use App\Support\CrmStudentFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.manage') || ! Gate::allows('crm.view')) {
            return false;
        }
        CrmStudent::visibleTo($this->user())->findOrFail($this->route('student'));
        if ($this->hasAny(['assigned_counsellor_id', 'assigned_re_id']) && ! Gate::allows('crm.assign')) {
            return false;
        }

        return ! $this->has('sensitive_profile') || Gate::allows('crm.sensitive');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'version' => ['required', 'integer', 'min:1'],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:24', new ValidEnquiryPhone],
            'stage' => ['required', Rule::in(array_keys(CrmStudentFields::STAGES))],
            'lead_status' => ['required', Rule::in(array_keys(CrmStudentFields::STATUSES))],
            'stage_status_id' => ['sometimes', 'nullable', 'integer'],
            'apply_status_transfer' => ['sometimes', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'profile' => ['sometimes', 'array:'.implode(',', array_keys(CrmStudentFields::PROFILE))],
            'sensitive_profile' => ['sometimes', 'array:'.implode(',', array_keys(CrmStudentFields::SENSITIVE))],
            'original_enquiry_id' => ['missing'],
            'created_by' => ['missing'],
            'correction_reason' => ['missing'],
            'branch_id' => ['missing'],
        ];
        foreach (CrmStudentFields::PROFILE as $key => $label) {
            $rules['profile.'.$key] = ['nullable', 'string', 'max:500'];
        }
        $student = CrmStudent::visibleTo($this->user())->findOrFail($this->route('student'));
        foreach (CrmStudentFields::DICTIONARIES as $key => $kind) {
            $rules['profile.'.$key][] = function (string $attribute, mixed $value, \Closure $fail) use ($student, $key, $kind): void {
                if (! is_string($value) || ($student->profile[$key] ?? null) === $value) {
                    return;
                }
                $values = CrmReference::where('kind', $kind)->where('is_active', true);
                if ((clone $values)->exists() && ! (clone $values)->where('name', $value)->exists()) {
                    $fail('Choose an active configured value. Existing historical values can be retained unchanged.');
                }
            };
        }
        foreach (CrmStudentFields::SENSITIVE as $key => $label) {
            $rules['sensitive_profile.'.$key] = ['nullable', 'string', 'max:500'];
        }
        foreach (['assigned_counsellor_id', 'assigned_re_id'] as $key) {
            $rules[$key] = ['sometimes', 'nullable', 'integer', function (string $attribute, mixed $value, \Closure $fail): void {
                $assignee = User::find($value);
                if (! $assignee || ! $assignee->hasAdminPermission('crm.view')) {
                    $fail('Choose an active staff member with CRM access.');
                }
            }];
        }

        return $rules;
    }
}
