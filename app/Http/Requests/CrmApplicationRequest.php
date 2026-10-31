<?php

namespace App\Http\Requests;

use App\Models\CrmApplication;
use App\Models\CrmStudent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CrmApplicationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_array($this->input('tags'))) {
            $this->merge(['tags' => array_values(array_filter($this->input('tags'), fn ($value) => $value !== null && $value !== ''))]);
        }
    }

    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        if ($this->route('application')) {
            CrmApplication::visibleTo($this->user())->findOrFail($this->route('application'));
        } elseif (filter_var($this->input('student_id'), FILTER_VALIDATE_INT)) {
            CrmStudent::visibleTo($this->user())->findOrFail($this->integer('student_id'));
        }

        return ! $this->has('owner_id') || Gate::allows('crm.assign');
    }

    public function rules(): array
    {
        $creating = ! $this->route('application');

        return [
            'creation_key' => $creating ? ['required', 'uuid'] : ['missing'],
            'student_id' => $creating ? ['required', 'integer', 'min:1'] : ['missing'],
            'version' => $creating ? ['missing'] : ['required', 'integer', 'min:1'],
            'university_id' => ['required', 'integer', 'min:1'], 'campus_id' => ['nullable', 'integer', 'min:1'],
            'program_id' => ['nullable', 'integer', 'min:1'],
            'agent_id' => ['nullable', 'integer', 'min:1'], 'status_id' => ['required', 'integer', 'min:1'],
            'owner_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'course' => ['required', 'string', 'max:1000'], 'intake' => ['required', 'string', 'max:80'],
            'submitted_on' => ['nullable', 'date_format:Y-m-d'],
            'tags' => ['sometimes', 'array', 'max:10'], 'tags.*' => ['required', 'string', 'max:50', 'distinct'],
            'remark' => ['nullable', 'string', 'max:4000'],
            'created_by' => ['missing'], 'branch_id' => ['missing'], 'application_id' => ['missing'],
        ];
    }
}
