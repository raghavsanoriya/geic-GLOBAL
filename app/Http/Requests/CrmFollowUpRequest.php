<?php

namespace App\Http\Requests;

use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        if ($this->route('followUp')) {
            CrmFollowUp::visibleTo($this->user())->findOrFail($this->route('followUp'));
        } else {
            CrmStudent::visibleTo($this->user())->findOrFail($this->route('student'));
        }

        return ! $this->has('owner_id') || Gate::allows('crm.assign');
    }

    public function rules(): array
    {
        $creating = ! $this->route('followUp');

        return [
            'creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'],
            'subject' => ['required', 'string', 'max:160'],
            'channel' => ['required', Rule::in(array_keys(CrmFollowUp::CHANNELS))],
            'status' => ['required', Rule::in(array_keys(CrmFollowUp::STATUSES))],
            'due_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'remark' => ['nullable', 'string', 'max:4000'],
            'owner_id' => ['sometimes', 'required', 'integer', function (string $attribute, mixed $value, \Closure $fail): void {
                $owner = User::find($value);
                if (! $owner || ! $owner->hasAdminPermission('crm.view')) {
                    $fail('Choose an active staff member with CRM access.');
                }
            }],
            'student_id' => ['missing'], 'created_by' => ['missing'], 'ended_at' => ['missing'], 'branch_id' => ['missing'],
        ];
    }
}
