<?php

namespace App\Http\Requests;

use App\Models\CrmInvoice;
use App\Models\CrmStudent;
use App\Support\CrmMoney;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.finance.manage') || ! Gate::allows('crm.finance.view') || ! Gate::allows('crm.view')) {
            return false;
        }
        if ($this->route('invoice')) {
            CrmInvoice::visibleTo($this->user())->findOrFail($this->route('invoice'));
        } elseif (filter_var($this->input('student_id'), FILTER_VALIDATE_INT)) {
            CrmStudent::visibleTo($this->user())->findOrFail($this->integer('student_id'));
        }

        return true;
    }

    public function rules(): array
    {
        $creating = ! $this->route('invoice');

        return [
            'student_id' => $creating ? ['required', 'integer', 'min:1'] : ['missing'],
            'application_id' => ['nullable', 'integer', 'min:1'],
            'creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'],
            'bill_to' => ['required', 'string', 'max:160'], 'description' => ['required', 'string', 'max:500'],
            'currency' => ['required', Rule::in(CrmMoney::CURRENCIES)],
            'base_amount' => ['required', 'string', 'regex:/^(0|[1-9][0-9]{0,8})(\.[0-9]{1,2})?$/D', function (string $key, mixed $value, \Closure $fail): void {
                if (preg_match('/^(0|[1-9][0-9]{0,8})(\.[0-9]{1,2})?$/D', $value) && CrmMoney::minor($value) < 1) {
                    $fail('The invoice amount must be greater than zero.');
                }
            }],
            'tax_bps' => ['required', 'integer', 'between:0,10000'], 'due_on' => ['required', 'date_format:Y-m-d'],
            'total_minor' => ['missing'], 'status' => ['missing'], 'number' => ['missing'], 'created_by' => ['missing'],
        ];
    }
}
