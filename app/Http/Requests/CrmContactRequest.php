<?php

namespace App\Http\Requests;

use App\Models\CrmContact;
use App\Rules\ValidEnquiryPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CrmContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        if ($this->route('contact')) {
            CrmContact::visibleTo($this->user())->findOrFail($this->route('contact'));
        }

        return true;
    }

    public function rules(): array
    {
        $creating = ! $this->route('contact');
        $rules = ['creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'], 'is_active' => ['required', 'boolean'], 'created_by' => ['missing']];

        return [...$rules, ...self::fieldRules()];
    }

    public static function fieldRules(): array
    {
        $rules = [];
        foreach (CrmContact::FIELDS as $key => [$label,$maximum]) {
            $rules[$key] = [in_array($key, ['name', 'contact_type'], true) ? 'required' : 'nullable', 'string', 'max:'.$maximum];
        }
        $rules['email'][] = 'email:rfc';
        $rules['phone'][] = new ValidEnquiryPhone;

        return $rules;
    }
}
