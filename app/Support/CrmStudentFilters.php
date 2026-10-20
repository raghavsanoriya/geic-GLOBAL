<?php

namespace App\Support;

use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;

class CrmStudentFilters
{
    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:160'], 'stage' => ['nullable', Rule::in(array_keys(CrmStudentFields::STAGES))],
            'student_id' => ['nullable', 'integer', 'min:1'], 'enquiry_id' => ['nullable', 'integer', 'min:1'],
            'city' => ['nullable', 'string', 'max:160'], 'country' => ['nullable', 'string', 'max:160'],
            'qualification' => ['nullable', 'string', 'max:160'], 'study_level' => ['nullable', 'string', 'max:160'],
            'lead_category' => ['nullable', 'string', 'max:160'], 'source_reference' => ['nullable', 'string', 'max:160'],
            'created_from' => ['nullable', 'date_format:Y-m-d'], 'created_to' => ['nullable', 'date_format:Y-m-d'],
            'my_cases' => ['nullable', 'in:1'], 'application_status_id' => ['nullable', 'integer', 'min:1'],
            'lead_status' => ['nullable', Rule::in(array_keys(CrmStudentFields::STATUSES))],
            'active' => ['nullable', Rule::in(['1', '0'])], 'unassigned' => ['nullable', Rule::in(['1'])],
            'unassigned_counsellor' => ['nullable', 'in:1'], 'unassigned_re' => ['nullable', 'in:1'],
            'assigned_counsellor_id' => ['nullable', 'integer', 'min:1'], 'assigned_re_id' => ['nullable', 'integer', 'min:1'],
            'dimension' => ['nullable', Rule::in(['lead_category', 'source_reference', 'qualification', 'country'])],
            'dimension_value' => ['nullable', 'string', 'max:500'], 'dimension_missing' => ['nullable', 'in:1'],
            'list' => ['nullable', Rule::in(['all', 'main', 'dead'])], 'stage_status_id' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000']];
    }

    public function query(User $user, array $filters): Builder
    {
        $query = CrmStudent::visibleTo($user);
        foreach (['student_id' => 'id', 'enquiry_id' => 'original_enquiry_id'] as $filter => $column) {
            if (filled($filters[$filter] ?? null)) {
                $query->where($column, $filters[$filter]);
            }
        }
        foreach (['city', 'country', 'qualification', 'study_level', 'lead_category', 'source_reference'] as $key) {
            if (filled($filters[$key] ?? null)) {
                $query->whereNotNull('profile->'.$key)->where('profile->'.$key, trim($filters[$key]));
            }
        }
        if (($filters['my_cases'] ?? null) === '1') {
            $query->where(fn (Builder $q) => $q->where('assigned_counsellor_id', $user->id)->orWhere('assigned_re_id', $user->id));
        }
        foreach (['created_from' => '>=', 'created_to' => '<'] as $filter => $operator) {
            if (filled($filters[$filter] ?? null)) {
                $date = CarbonImmutable::parse($filters[$filter], 'Asia/Kolkata')->startOfDay();
                $query->where('created_at', $operator, ($filter === 'created_to' ? $date->addDay() : $date)->utc());
            }
        }
        if (filled($filters['application_status_id'] ?? null)) {
            $query->whereExists(fn (QueryBuilder $applications) => $applications->selectRaw('1')->from('crm_applications')
                ->whereColumn('crm_applications.student_id', 'crm_students.id')->where('crm_applications.status_id', $filters['application_status_id']));
        }
        if (filled($filters['q'] ?? null)) {
            $term = '%'.trim($filters['q']).'%';
            $query->where(fn (Builder $q) => $q->where('full_name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone', 'like', $term));
        }
        foreach (['stage', 'lead_status'] as $key) {
            if (filled($filters[$key] ?? null)) {
                $query->where($key, $filters[$key]);
            }
        }
        if (filled($filters['stage_status_id'] ?? null)) {
            $query->where('stage_status_id', $filters['stage_status_id']);
        }
        $dead = CrmReference::where('kind', 'level_status')->where('configuration->list_type', 'dead')->select('id');
        if (($filters['list'] ?? 'all') === 'dead') {
            $query->where(fn (Builder $q) => $q->whereIn('stage_status_id', $dead)->orWhere(fn (Builder $legacy) => $legacy->whereNull('stage_status_id')->where('lead_status', 'dead')));
        } elseif (($filters['list'] ?? 'all') === 'main') {
            $query->where(fn (Builder $q) => $q->whereNull('stage_status_id')->orWhereNotIn('stage_status_id', $dead))
                ->where(fn (Builder $q) => $q->whereNotNull('stage_status_id')->orWhere('lead_status', '!=', 'dead'));
        }
        if (filled($filters['active'] ?? null)) {
            $query->where('is_active', $filters['active'] === '1');
        }
        if (($filters['unassigned'] ?? null) === '1') {
            $query->whereNull('assigned_counsellor_id')->whereNull('assigned_re_id');
        }
        foreach (['counsellor', 're'] as $role) {
            if (($filters['unassigned_'.$role] ?? null) === '1') {
                $query->whereNull('assigned_'.$role.'_id');
            }
            if (filled($filters['assigned_'.$role.'_id'] ?? null)) {
                $query->where('assigned_'.$role.'_id', $filters['assigned_'.$role.'_id']);
            }
        }
        if (filled($filters['dimension'] ?? null)) {
            $field = 'profile->'.$filters['dimension'];
            if (($filters['dimension_missing'] ?? null) === '1' || ! filled($filters['dimension_value'] ?? null)) {
                $query->where(fn (Builder $q) => $q->whereNull($field)->orWhere($field, ''));
            } else {
                $query->whereNotNull($field)->where($field, $filters['dimension_value']);
            }
        }

        return $query;
    }
}
