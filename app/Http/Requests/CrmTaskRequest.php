<?php

namespace App\Http\Requests;

use App\Models\CrmStudent;
use App\Models\CrmTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        if ($this->route('task')) {
            CrmTask::visibleTo($this->user())->findOrFail($this->route('task'));
        }

        return ! $this->hasAny(['assignees', 'assignees_present']) || Gate::allows('crm.assign');
    }

    public function rules(): array
    {
        $creating = ! $this->route('task');

        return [
            'creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'],
            'student_id' => $creating ? ['nullable', 'integer', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! CrmStudent::visibleTo($this->user())->whereKey($value)->exists()) {
                    $fail('Choose a student you can access.');
                }
            }] : ['missing'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:12000'],
            'status' => ['required', Rule::in(array_keys(CrmTask::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(CrmTask::PRIORITIES))],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'due_at' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:starts_at'],
            'assignees_present' => ['sometimes', 'accepted'],
            'assignees' => ['required_with:assignees_present', 'array', 'min:1', 'max:20'],
            'assignees.*' => ['required', 'integer', 'distinct'],
            'remark' => ['nullable', 'string', 'max:4000'],
            'created_by' => ['missing'], 'completed_at' => ['missing'], 'branch_id' => ['missing'],
        ];
    }
}
