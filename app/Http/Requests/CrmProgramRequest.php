<?php

namespace App\Http\Requests;

use App\Models\CrmProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);

        return Gate::allows('crm.configure');
    }

    public function rules(): array
    {
        $record = $this->route('program') ? CrmProgram::findOrFail($this->route('program')) : null;

        return [
            'code' => $record ? ['missing'] : ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]{0,59}$/', Rule::unique('crm_programs')],
            'version' => $record ? ['required', 'integer', 'min:1'] : ['missing'],
            'name' => ['required', 'string', 'max:160'], 'country' => ['required', 'string', 'max:100'],
            'university_id' => ['required', 'integer', 'min:1'], 'campus_id' => ['nullable', 'integer', 'min:1'],
            'qualification_id' => ['nullable', 'integer', 'min:1'],
            'study_level' => ['required', 'string', 'max:80'], 'subject' => ['required', 'string', 'max:100'],
            'ranking' => ['nullable', 'integer', 'between:1,100000'],
            'ielts' => ['nullable', 'numeric', 'between:0,9', 'decimal:0,1'],
            'toefl' => ['nullable', 'integer', 'between:0,120'], 'pte' => ['nullable', 'integer', 'between:0,90'],
            'description' => ['nullable', 'string', 'max:12000'], 'is_active' => ['required', 'boolean'],
            'branch_id' => ['missing'],
        ];
    }
}
