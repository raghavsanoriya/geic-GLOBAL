<?php

namespace App\Http\Requests;

use App\Models\CrmProfileEntry;
use App\Models\CrmStudent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CrmProfileEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless(config('crm.enabled'), 404);
        if (! Gate::allows('crm.view') || ! Gate::allows('crm.manage')) {
            return false;
        }
        CrmStudent::visibleTo($this->user())->findOrFail($this->route('student'));

        return true;
    }

    public function rules(): array
    {
        $kind = $this->route('kind');
        $details = is_array($this->input('details')) ? $this->input('details') : [];

        return ['creation_key' => [$this->isMethod('POST') ? 'required' : 'missing', 'uuid'],
            'version' => [$this->isMethod('POST') ? 'missing' : 'required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'], ...self::detailRules($kind, $details)];
    }

    public static function detailRules(string $kind, array $details): array
    {
        $fields = CrmProfileEntry::FIELDS[$kind] ?? [];
        $rules = ['details' => ['required', 'array:'.implode(',', array_keys($fields))]];
        $required = ['education' => ['institution', 'qualification'], 'employment' => ['employer', 'designation'], 'test_score' => ['test', 'overall'], 'preference' => ['country', 'course']][$kind] ?? [];
        foreach ($fields as $key => $label) {
            $rules['details.'.$key] = [in_array($key, $required, true) ? 'required' : 'nullable', 'string', 'max:1000'];
        }
        foreach (['start_year', 'passing_year'] as $key) {
            if (isset($fields[$key])) {
                $rules['details.'.$key] = ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 15)];
            }
        }
        foreach (['started_on', 'ended_on', 'taken_on', 'expires_on'] as $key) {
            if (isset($fields[$key])) {
                $rules['details.'.$key] = ['nullable', 'date_format:Y-m-d'];
            }
        }
        if ($kind === 'employment' && filled($details['started_on'] ?? null)) {
            $rules['details.ended_on'][] = 'after_or_equal:details.started_on';
        }
        if ($kind === 'test_score') {
            if (filled($details['taken_on'] ?? null)) {
                $rules['details.expires_on'][] = 'after_or_equal:details.taken_on';
            }
            $test = mb_strtoupper(trim(is_string($details['test'] ?? null) ? $details['test'] : ''));
            $ielts = in_array($test, ['IELTS', 'IELTS ACADEMIC', 'IELTS GENERAL', 'IELTS UKVI'], true);
            $pte = in_array($test, ['PTE', 'PTE ACADEMIC', 'PTE CORE', 'PTE UKVI'], true);
            foreach (['overall', 'listening', 'reading', 'writing', 'speaking'] as $key) {
                $rules['details.'.$key] = [$key === 'overall' ? 'required' : 'nullable', 'numeric', $pte ? 'min:10' : 'min:0', 'max:'.($ielts ? 9 : ($pte ? 90 : 1000))];
                if ($ielts) {
                    $rules['details.'.$key][] = 'multiple_of:0.5';
                } elseif ($pte) {
                    $rules['details.'.$key][] = 'integer';
                }
            }
        }
        if ($kind === 'education') {
            $rules['details.backlogs'] = ['nullable', 'integer', 'min:0', 'max:100'];
            if (filled($details['start_year'] ?? null)) {
                $rules['details.passing_year'][] = 'gte:details.start_year';
            }
        }

        return $rules;
    }
}
