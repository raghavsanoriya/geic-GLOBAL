<?php

namespace App\Http\Requests;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Rules\ValidEnquiryPhone;
use App\Support\CrmStudentFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);

        return Gate::allows('crm.configure') && (! $this->has('restricted') || Gate::allows('crm.sensitive'));
    }

    public function rules(): array
    {
        $record = $this->route('reference') ? CrmReference::findOrFail($this->route('reference')) : null;
        $providedKind = $record?->kind ?? $this->input('kind');
        $kind = is_string($providedKind) ? $providedKind : '';

        $rules = [
            'kind' => $record ? ['missing'] : ['required', Rule::in(array_keys(CrmReference::KINDS))],
            'code' => $record ? ['missing'] : ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]{0,59}$/', Rule::unique('crm_references')->where('kind', $kind),
                ...($kind === 'level' ? [Rule::in(array_keys(CrmStudentFields::STAGES))] : [])],
            'version' => $record ? ['required', 'integer', 'min:1'] : ['missing'],
            'name' => ['required', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:4000'],
            'is_active' => $kind === 'level' ? ['required', 'accepted'] : ['required', 'boolean'],
            'outcome' => in_array($kind, CrmReference::STATUS_KINDS, true) ? ['sometimes', Rule::in(['progress', 'success', 'failure'])] : ['missing'],
            'parent_id' => [
                in_array($kind, ['campus', 'level_status'], true) ? 'required' : 'nullable', 'integer',
                function (string $attribute, mixed $value, \Closure $fail) use ($kind): void {
                    $parentKind = match ($kind) {
                        'campus' => 'university','level_status' => 'level',default => null
                    };
                    if (! $parentKind || ! CrmReference::whereKey($value)->where('kind', $parentKind)->where('is_active', true)->exists()) {
                        $fail('Choose the matching active university or lifecycle level.');
                    }
                },
                function (string $attribute, mixed $value, \Closure $fail) use ($record, $kind): void {
                    if ($kind === 'level_status' && $record && $record->parent_id !== (int) $value && CrmStudent::where('stage_status_id', $record->id)->exists()) {
                        $fail('A status already used by students cannot be moved to another level. Create a new status instead.');
                    }
                },
            ],
            'transitions_present' => ['sometimes', 'accepted'],
            'transitions' => ['sometimes', 'array', 'max:100'],
            'transitions.*' => ['required', 'integer', 'distinct', function (string $attribute, mixed $value, \Closure $fail) use ($kind, $record): void {
                if (! in_array($kind, CrmReference::STATUS_KINDS, true) || (int) $value === $record?->id
                    || ! CrmReference::whereKey($value)->where('kind', $kind)->where('is_active', true)->exists()) {
                    $fail('Choose a different active status in this lifecycle.');
                }
            }],
            'branch_id' => ['missing'],
            'configuration' => $kind === 'level_status' ? ['sometimes', 'array:list_type,transfer_stage,is_default'] : (isset(CrmReference::DETAIL_FIELDS[$kind]) ? ['sometimes', 'array:'.implode(',', array_keys(CrmReference::DETAIL_FIELDS[$kind]))] : ['missing']),
            'configuration.list_type' => ['sometimes', Rule::in(['main', 'dead'])],
            'configuration.transfer_stage' => ['nullable', Rule::in(array_keys(CrmStudentFields::STAGES))],
            'configuration.is_default' => ['sometimes', 'boolean'],
            'linked_application_status_id' => $kind === 'visa_status' ? ['nullable', 'integer', Rule::exists('crm_references', 'id')->where('kind', 'application_status')->where('is_active', true)] : ['missing'],
            'restricted_details' => ['missing'],
            'restricted' => $kind === 'agent' ? ['sometimes', 'array:pan_number,portal_username,portal_password'] : ['missing'],
            'restricted.pan_number' => ['nullable', 'string', 'max:20'],
            'restricted.portal_username' => ['nullable', 'string', 'max:160'],
            'restricted.portal_password' => ['nullable', 'string', 'max:1000'],
        ];
        foreach (CrmReference::DETAIL_FIELDS[$kind] ?? [] as $key => $label) {
            $rules['configuration.'.$key] = ['nullable', 'string', 'max:'.($key === 'address' ? 1000 : ($key === 'portal_url' || $key === 'website' ? 500 : 160))];
            if (in_array($key, ['portal_url', 'website'], true)) {
                $rules['configuration.'.$key][] = 'url:https';
                $rules['configuration.'.$key][] = function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }
                    if (parse_url($value, PHP_URL_USER) !== null || parse_url($value, PHP_URL_PASS) !== null) {
                        $fail('Store portal credentials only in the protected credential fields.');
                    }
                };
            }
            if ($key === 'contact_phone') {
                $rules['configuration.'.$key][] = new ValidEnquiryPhone;
            }
        }

        return $rules;
    }
}
