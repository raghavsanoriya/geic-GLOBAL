<?php

namespace App\Support;

use App\Models\CrmStudent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CrmReports
{
    private const AGGREGATE = 'COUNT(*) as leads, COALESCE(SUM(m.application),0) as applications, COALESCE(SUM(m.visa),0) as visas, COALESCE(SUM(m.billing),0) as billing';

    public function build(User $user, int $fromYear, int $toYear): array
    {
        $now = CarbonImmutable::now('Asia/Kolkata');
        $last12 = $now->startOfMonth()->subMonths(11);
        $base = $this->base($user);
        $overall = $this->totals(clone $base);
        $recent = $this->totals((clone $base)->where('original.created_at', '>=', $last12->utc())->where('original.created_at', '<=', $now->utc()));
        $yearExpr = $this->dateExpression('%Y');
        $monthExpr = $this->dateExpression('%Y-%m');
        $years = (clone $base)->where('original.created_at', '>=', CarbonImmutable::create($fromYear, 1, 1, 0, 0, 0, 'Asia/Kolkata')->utc())
            ->where('original.created_at', '<', CarbonImmutable::create($toYear + 1, 1, 1, 0, 0, 0, 'Asia/Kolkata')->utc())
            ->selectRaw($yearExpr.' as period, '.self::AGGREGATE)->groupByRaw($yearExpr)->orderBy('period')->get()->keyBy('period');
        $yearRows = [];
        for ($year = $fromYear; $year <= $toYear; $year++) {
            $row = $this->normalize($years->get((string) $year));
            $yearRows[] = ['label' => (string) $year, ...$row, 'conversion' => $this->percent($row['billing'], $row['leads'])];
        }
        $months = (clone $base)->where('original.created_at', '>=', $last12->utc())->where('original.created_at', '<=', $now->utc())
            ->selectRaw($monthExpr.' as period, COUNT(*) as leads')->groupByRaw($monthExpr)->orderBy('period')->get()->keyBy('period');
        $monthRows = [];
        for ($index = 0; $index < 12; $index++) {
            $label = $last12->addMonths($index)->format('Y-m');
            $monthRows[] = ['label' => $label, 'leads' => (int) ($months->get($label)?->leads ?? 0)];
        }
        $counsellors = (clone $base)->leftJoin('users as owner', 'owner.id', '=', 'crm_students.assigned_counsellor_id')
            ->selectRaw('crm_students.assigned_counsellor_id as owner_id, owner.name as owner_name, '.self::AGGREGATE)
            ->groupBy('crm_students.assigned_counsellor_id', 'owner.name')->orderByDesc('leads')->limit(1000)->get()
            ->map(fn ($row) => ['label' => $row->owner_name ?: 'Unassigned / former staff', ...$this->normalize($row)])->all();
        $countryApplications = $this->countries($user, false);
        $countryVisas = $this->countries($user, true);

        return [
            'overall' => $overall, 'last12' => $recent, 'years' => $yearRows, 'conversions' => array_map(fn (array $row) => ['label' => $row['label'], 'percent' => $row['conversion']], $yearRows), 'months' => $monthRows, 'counsellors' => $counsellors,
            'applications_by_country' => $countryApplications, 'visas_by_country' => $countryVisas,
            'history_gaps' => (clone $base)->whereNull('m.student_id')->count(),
            'submission_count' => DB::table('crm_student_enquiries')->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'))->count(),
            'from_year' => $fromYear, 'to_year' => $toYear, 'generated_at' => $now->toIso8601String(),
        ];
    }

    public function percent(int $numerator, int $denominator): ?float
    {
        return $denominator > 0 ? round($numerator * 100 / $denominator, 2) : null;
    }

    private function base(User $user): Builder
    {
        $milestones = DB::table('crm_student_stage_events')->selectRaw("student_id, MAX(CASE WHEN to_stage='application' THEN 1 ELSE 0 END) as application, MAX(CASE WHEN to_stage='visa' THEN 1 ELSE 0 END) as visa, MAX(CASE WHEN to_stage='billing' THEN 1 ELSE 0 END) as billing")->groupBy('student_id');

        return CrmStudent::visibleTo($user)->toBase()->join('counselling_enquiries as original', 'original.id', '=', 'crm_students.original_enquiry_id')
            ->leftJoinSub($milestones, 'm', 'm.student_id', '=', 'crm_students.id');
    }

    private function totals(Builder $query): array
    {
        return $this->normalize($query->selectRaw(self::AGGREGATE)->first());
    }

    private function normalize(?object $row): array
    {
        return ['leads' => (int) ($row?->leads ?? 0), 'applications' => (int) ($row?->applications ?? 0), 'visas' => (int) ($row?->visas ?? 0), 'billing' => (int) ($row?->billing ?? 0)];
    }

    private function dateExpression(string $format): string
    {
        return DB::connection()->getDriverName() === 'sqlite' ? "strftime('".$format."',datetime(original.created_at,'+330 minutes'))" : "DATE_FORMAT(DATE_ADD(original.created_at,INTERVAL 330 MINUTE),'".$format."')";
    }

    private function countries(User $user, bool $visa): array
    {
        $table = $visa ? 'crm_visas' : 'crm_applications';
        $query = DB::table($table.' as c')->whereIn('c.student_id', CrmStudent::visibleTo($user)->select('id'))
            ->join('crm_references as status', 'status.id', '=', 'c.status_id');
        if ($visa) {
            $query->join('crm_applications as app', 'app.id', '=', 'c.application_id')->join('crm_references as university', 'university.id', '=', 'app.university_id');
        } else {
            $query->join('crm_references as university', 'university.id', '=', 'c.university_id');
        }
        $country = "COALESCE(NULLIF(university.country,''),'Not recorded')";

        $normalized = $query->selectRaw($country." as reporting_country, CASE WHEN status.outcome='success' THEN 1 ELSE 0 END as succeeded");

        return DB::query()->fromSub($normalized, 'country_rows')
            ->selectRaw('reporting_country as country, COUNT(*) as registered, SUM(succeeded) as successful')
            ->groupBy('reporting_country')->orderByDesc('registered')->orderBy('reporting_country')->limit(1000)->get()
            ->map(fn ($row) => ['label' => $row->country, 'registered' => (int) $row->registered, 'successful' => (int) $row->successful])->all();
    }
}
