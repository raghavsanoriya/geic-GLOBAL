<?php

namespace App\Http\Requests;

use App\Models\CrmInboundForm;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmInboundFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);

        return Gate::allows('crm.inbound.manage');
    }

    protected function prepareForValidation(): void
    {
        if (is_array($this->input('allowed_origins'))) {
            $this->merge(['allowed_origins' => array_values(array_filter($this->input('allowed_origins'), fn ($value) => $value !== null && $value !== ''))]);
        }
        if (is_array($this->input('field_map'))) {
            $this->merge(['field_map' => array_filter($this->input('field_map'), fn ($value) => $value !== null && $value !== '')]);
        }
    }

    public function rules(): array
    {
        $rules = ['version' => $this->route('form') ? ['required', 'integer', 'min:1'] : ['missing'], 'name' => ['required', 'string', 'max:160'],
            'field_map' => ['required', 'array:'.implode(',', array_keys(CrmInboundForm::FIELDS))],
            'defaults' => ['sometimes', 'array:country,city,course,study_level,intake,lead_category,source_reference,message'], 'defaults.*' => ['nullable', 'string', 'max:160'],
            'defaults.study_level' => ['nullable', 'string', 'max:50'], 'defaults.intake' => ['nullable', 'string', 'max:50'],
            'defaults.message' => ['nullable', 'string', 'max:4000'],
            'allowed_origins' => ['sometimes', 'array', 'max:20'],
            'allowed_origins.*' => ['required', 'string', 'max:255', 'distinct', function (string $attribute, mixed $value, \Closure $fail): void {
                $parts = parse_url($value);
                if (! $parts || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || isset($parts['path']) || str_contains($parts['host'], '*')) {
                    $fail('Use an exact HTTPS origin with no path, credentials or wildcard.');
                }
            }],
            'rate_limit_per_minute' => ['sometimes', 'integer', 'between:1,300'],
            'honeypot_field' => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z][A-Za-z0-9_]{0,59}$/', function (string $attribute, mixed $value, \Closure $fail): void {
                if (filled($value) && in_array($value, array_values($this->input('field_map', [])), true)) {
                    $fail('The honeypot key must not also be a mapped field.');
                }
            }],
            'duplicate_policy' => ['required', Rule::in(['review', 'reject'])], 'forward_to_foris' => ['required', 'boolean'], 'is_active' => ['required', 'boolean'],
            'key_expires_at' => ['nullable', 'date_format:Y-m-d', 'after:'.CarbonImmutable::now('Asia/Kolkata')->toDateString()], 'form_key' => ['missing'], 'api_key_hash' => ['missing'], 'created_by' => ['missing']];
        foreach (CrmInboundForm::FIELDS as $key => $label) {
            $rules['field_map.'.$key] = [in_array($key, ['full_name', 'phone'], true) ? 'required' : 'nullable', 'string', 'max:60', 'regex:/^[A-Za-z][A-Za-z0-9_]{0,59}$/'];
        }

        return $rules;
    }
}
