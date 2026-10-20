<?php

namespace App\Support;

use App\Models\CrmStudent;
use App\Models\User;

class CrmLeadBreakdown
{
    public const DIMENSIONS = ['lead_category' => 'Category', 'source_reference' => 'Reference', 'counsellor' => 'Counsellor',
        're' => 'RE', 'qualification' => 'Qualification', 'country' => 'Country'];

    public function forUser(User $user, string $dimension): array
    {
        if (! array_key_exists($dimension, self::DIMENSIONS)) {
            throw new \InvalidArgumentException('Unknown lead breakdown.');
        }
        $students = CrmStudent::visibleTo($user);
        $staffField = ['counsellor' => 'assigned_counsellor_id', 're' => 'assigned_re_id'][$dimension] ?? null;
        $field = $staffField ?? 'profile->'.$dimension;
        $missing = (clone $students)->where(fn ($query) => $query->whereNull($field)->when(! $staffField, fn ($q) => $q->orWhere($field, '')))->count();
        $groups = (clone $students)->whereNotNull($field)->when(! $staffField, fn ($q) => $q->where($field, '!=', ''))
            ->select($field.' as label')->selectRaw('COUNT(*) as total')->groupBy($field)->orderByDesc('total')->orderBy('label')->limit(200)->get();
        $names = $staffField ? User::whereIn('id', $groups->pluck('label'))->pluck('name', 'id') : collect();
        $rows = $groups->map(function ($group) use ($dimension, $staffField, $names): array {
            $filters = $staffField ? [$staffField => $group->label] : ['dimension' => $dimension, 'dimension_value' => (string) $group->label];

            return ['label' => $staffField ? ($names[$group->label] ?? 'Unavailable staff #'.$group->label) : (string) $group->label,
                'count' => (int) $group->total, 'url' => route('admin.crm.students.index', $filters)];
        })->all();
        if ($missing > 0) {
            $filters = $staffField ? ['unassigned_'.($dimension === 're' ? 're' : 'counsellor') => '1'] : ['dimension' => $dimension, 'dimension_missing' => '1'];
            $rows[] = ['label' => 'Not assigned', 'count' => $missing, 'url' => route('admin.crm.students.index', $filters)];
        }

        return ['dimension' => $dimension, 'label' => self::DIMENSIONS[$dimension], 'rows' => $rows,
            'other_count' => max(0, (clone $students)->count() - $missing - (int) $groups->sum('total'))];
    }
}
