<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return config('crm.enabled') && Gate::allows('crm.marketing.manage') && Gate::allows('crm.view');
    }

    public function rules(): array
    {
        return [
            'creation_key' => [$this->isMethod('POST') ? 'required' : 'missing', 'uuid'],
            'version' => [$this->isMethod('POST') ? 'missing' : 'required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:160'],
            'channel' => ['required', Rule::in(['email', 'sms'])],
            'subject' => ['required_if:channel,email', 'nullable', 'string', 'max:200', 'not_regex:/[\r\n]/'],
            'body' => ['required', 'string', 'max:20000'],
            'audience' => ['required', 'array:mode,stage,lead_status,manual'],
            'audience.mode' => ['required', Rule::in(['students', 'manual'])],
            'audience.stage' => ['nullable', Rule::in(['lead', 'application', 'visa', 'billing'])],
            'audience.lead_status' => ['nullable', 'string', 'max:24'],
            'audience.manual' => ['nullable', 'string', 'max:100000'],
            'consent_confirmed' => ['required', 'boolean'],
        ];
    }
}
