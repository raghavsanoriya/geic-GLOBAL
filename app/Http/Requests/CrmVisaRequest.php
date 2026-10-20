<?php

namespace App\Http\Requests;

use App\Models\CrmApplication;
use App\Models\CrmVisa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CrmVisaRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        if ($this->route('visa')) {
            CrmVisa::visibleTo($this->user())->findOrFail($this->route('visa'));
        } elseif (filter_var($this->input('application_id'), FILTER_VALIDATE_INT)) {
            CrmApplication::visibleTo($this->user())->findOrFail($this->integer('application_id'));
        }

        return ! $this->has('owner_id') || Gate::allows('crm.assign');
    }

    public function rules(): array
    {
        $creating = ! $this->route('visa');

        return [
            'creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'application_id' => $creating ? ['required', 'integer', 'min:1'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'],
            'status_id' => ['required', 'integer', 'min:1'], 'owner_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'visa_type' => ['required', 'string', 'max:100'],
            'lodged_on' => ['nullable', 'date_format:Y-m-d'],
            'decision_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:lodged_on'],
            'admission_on' => ['nullable', 'date_format:Y-m-d'],
            'census_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:admission_on'],
            'ready_to_bill' => ['required', 'boolean'],
            'apply_linked_application_status' => ['sometimes', 'boolean'],
            'application_version' => ['required_if:apply_linked_application_status,1', 'nullable', 'integer', 'min:1'],
            'remark' => ['nullable', 'string', 'max:4000'],
            'student_id' => ['missing'], 'created_by' => ['missing'], 'branch_id' => ['missing'],
        ];
    }
}
