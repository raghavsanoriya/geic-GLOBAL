<?php

namespace App\Http\Requests;

use App\Models\CrmIntegrationSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmIntegrationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled') && array_key_exists($this->route('provider'), CrmIntegrationSetting::PROVIDERS), 404);

        return Gate::allows('crm.integrations.manage');
    }

    public function rules(): array
    {
        $rules = ['version' => ['required', 'integer', 'min:0'], 'secret' => ['nullable', 'string', 'max:4096'], 'clear_secret' => ['sometimes', 'boolean'], 'is_enabled' => ['required', 'boolean']];
        if ($this->route('provider') === 'email') {
            return [...$rules, 'configuration' => ['required', 'array:host,port,username,from_address,from_name,scheme'],
                'configuration.host' => ['required', 'string', 'max:253', 'regex:/^[A-Za-z0-9][A-Za-z0-9.-]*[A-Za-z0-9]$/'],
                'configuration.port' => ['required', 'integer', Rule::in([465, 587])], 'configuration.scheme' => ['required', Rule::in(['smtp', 'smtps'])],
                'configuration.username' => ['required', 'string', 'max:255'], 'configuration.from_address' => ['required', 'email:rfc', 'max:160'], 'configuration.from_name' => ['required', 'string', 'max:120']];
        }

        return [...$rules, 'configuration' => ['required', 'array:app_id'], 'configuration.app_id' => ['required', 'uuid']];
    }
}
