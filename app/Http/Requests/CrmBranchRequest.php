<?php

namespace App\Http\Requests;

use App\Models\CrmBranch;
use App\Rules\ValidEnquiryPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);

        return Gate::allows('crm.configure') && Gate::allows('crm.sensitive');
    }

    public function rules(): array
    {
        $branch = $this->route('branch') ? CrmBranch::findOrFail($this->route('branch')) : null;

        return [
            'version' => $branch ? ['required', 'integer', 'min:1'] : ['missing'],
            'name' => ['required', 'string', 'max:160', Rule::unique('crm_branches', 'name')->ignore($branch)],
            'address' => ['nullable', 'string', 'max:2000'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:160'],
            'contact_phone' => ['nullable', 'string', 'max:24', new ValidEnquiryPhone],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'tax' => ['sometimes', 'array:pan_number,gst_number'],
            'tax.pan_number' => ['nullable', 'string', 'max:20'],
            'tax.gst_number' => ['nullable', 'string', 'max:30'],
            'banking' => ['sometimes', 'array:account_holder,bank_name,account_number,ifsc_code'],
            'banking.account_holder' => ['nullable', 'string', 'max:160'],
            'banking.bank_name' => ['nullable', 'string', 'max:160'],
            'banking.account_number' => ['nullable', 'string', 'max:40'],
            'banking.ifsc_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
