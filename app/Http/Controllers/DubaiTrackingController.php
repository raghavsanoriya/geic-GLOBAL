<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\DubaiPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DubaiTrackingController extends Controller
{
    private const EXPO_TRACKING_KEY = 'integration.expo_tracking';

    public function edit(Request $request): View
    {
        Gate::authorize('ads.manage');

        $saved = SiteContent::valuesForPage(DubaiPage::TRACKING_KEY);
        $tracking = array_merge(DubaiPage::TRACKING_DEFAULTS, $saved);
        $prefilledFromExpo = false;

        // Until Dubai tracking is saved once, suggest the IDs already verified for
        // the Expo campaign. Nothing is enabled until an admin saves the form.
        if ($saved === []) {
            $expo = SiteContent::valuesForPage(self::EXPO_TRACKING_KEY);

            foreach (['meta_pixel_id', 'google_ads_id', 'google_ads_conversion_label'] as $key) {
                if (filled($expo[$key] ?? null)) {
                    $tracking[$key] = $expo[$key];
                    $prefilledFromExpo = true;
                }
            }
        }

        $canViewLeads = $request->user()?->can('enquiries.view') ?? false;
        $filters = $this->filters($request);

        return view('admin.tracking.dubai', [
            'tracking' => $tracking,
            'prefilledFromExpo' => $prefilledFromExpo,
            'canViewLeads' => $canViewLeads,
            'stats' => $canViewLeads ? $this->stats() : null,
            'leads' => $leads = $canViewLeads ? $this->filteredLeads($filters) : null,
            'repeatCounts' => $leads ? $this->repeatCounts(collect($leads->items())) : [],
            'filters' => $filters,
            'filterOptions' => [
                'levels' => ['Undergraduate', 'Postgraduate', 'Not sure yet'],
                'intakes' => ['September intake', 'January intake', 'May intake', 'Not sure yet'],
                'budgets' => DubaiPage::BUDGETS,
                'sources' => $canViewLeads ? $this->sources() : [],
                'periods' => ['today' => 'Today', '7' => 'Last 7 days', '30' => 'Last 30 days', 'all' => 'All time'],
                'duplicates' => ['' => 'All entries', 'duplicates' => 'Duplicates only', 'unique' => 'One per student'],
            ],
            'publicUrl' => url(DubaiPage::PUBLIC_PATH),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('ads.manage');

        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'meta_pixel_id' => ['nullable', 'regex:/^\d{5,20}$/'],
            'google_ads_id' => ['nullable', 'regex:/^AW-\d{5,20}$/i'],
            'google_ads_conversion_label' => ['nullable', 'regex:/^[A-Za-z0-9_-]{1,200}$/'],
        ], [
            'meta_pixel_id.regex' => 'Enter a Meta Pixel ID made up of 5–20 digits.',
            'google_ads_id.regex' => 'Enter a Google Ads tag ID in the format AW-123456789.',
            'google_ads_conversion_label.regex' => 'The Google Ads conversion label can contain only letters, numbers, hyphens, and underscores.',
        ]);

        $enabled = $request->boolean('enabled');
        $metaPixelId = trim((string) ($validated['meta_pixel_id'] ?? ''));
        $googleAdsId = strtoupper(trim((string) ($validated['google_ads_id'] ?? '')));
        $googleAdsConversionLabel = trim((string) ($validated['google_ads_conversion_label'] ?? ''));

        if ($enabled && $metaPixelId === '' && $googleAdsId === '') {
            throw ValidationException::withMessages([
                'enabled' => 'Add a Meta Pixel ID or Google Ads tag ID before enabling tracking.',
            ]);
        }

        $settings = [
            'enabled' => $enabled ? '1' : '0',
            'meta_pixel_id' => $metaPixelId,
            'google_ads_id' => $googleAdsId,
            'google_ads_conversion_label' => $googleAdsConversionLabel,
        ];

        foreach ($settings as $fieldKey => $value) {
            SiteContent::query()->updateOrCreate(
                ['page_key' => DubaiPage::TRACKING_KEY, 'field_key' => $fieldKey],
                [
                    'label' => str($fieldKey)->replace('_', ' ')->title()->toString(),
                    'type' => $fieldKey === 'enabled' ? 'toggle' : 'text',
                    'value' => $value,
                    'published_value' => $value,
                ],
            );
        }

        return back()->with('status', $enabled
            ? 'Dubai lead tracking is enabled. Page views and saved enquiries on /dubai are now sent to the selected provider(s).'
            : 'Dubai lead tracking is paused. The saved IDs remain available for a future re-enable.');
    }

    /**
     * Download the Dubai leads that match the current filters as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('enquiries.export');

        $filters = $this->filters($request);
        $budgetPrefix = 'Approx. yearly budget: ';
        $repeatCounts = [
            'email' => $this->valueCounts('email'),
            'phone' => $this->valueCounts('phone'),
        ];
        // Stop spreadsheet apps from treating a cell as a formula.
        $cell = fn (mixed $value): string => preg_match('/^[=+\-@\t\r]/', (string) $value) ? "'".$value : (string) $value;

        return response()->streamDownload(function () use ($filters, $budgetPrefix, $repeatCounts, $cell): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 marker so Excel shows ₹ correctly
            fputcsv($out, ['Received', 'Name', 'Email', 'Phone', 'City', 'Study level', 'Preferred intake', 'Study budget', 'Course of interest', 'Message', 'Source', 'Medium', 'Campaign', 'Times submitted']);

            $this->leadQuery($filters)
                ->select(['id', 'full_name', 'email', 'phone', 'city', 'study_level', 'preferred_intake', 'preferred_course', 'message', 'utm_source', 'utm_medium', 'utm_campaign', 'created_at'])
                ->chunk(500, function (Collection $leads) use ($out, $budgetPrefix, $repeatCounts, $cell): void {
                    foreach ($leads as $lead) {
                        $message = (string) $lead->message;
                        $firstLine = strtok($message, "\n") ?: '';
                        $budget = str_starts_with($firstLine, $budgetPrefix) ? mb_substr($firstLine, mb_strlen($budgetPrefix)) : '';
                        $note = $budget !== '' ? trim(mb_substr($message, mb_strlen($firstLine))) : $message;

                        fputcsv($out, array_map($cell, [
                            Carbon::parse($lead->created_at)->format('Y-m-d H:i'),
                            $lead->full_name, $lead->email, $lead->phone, $lead->city,
                            $lead->study_level, $lead->preferred_intake, $budget, $lead->preferred_course, $note,
                            $lead->utm_source ?: 'Direct', $lead->utm_medium, $lead->utm_campaign,
                            max((int) ($repeatCounts['email'][$lead->email] ?? 1), (int) ($repeatCounts['phone'][$lead->phone] ?? 1)),
                        ]));
                    }
                });

            fclose($out);
        }, 'dubai-leads-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array<string, int> */
    private function valueCounts(string $column): array
    {
        return DB::table('counselling_enquiries')
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->selectRaw($column.', count(*) as total')
            ->pluck('total', $column)
            ->map(fn ($total): int => (int) $total)
            ->all();
    }

    /** @return array{total: int, week: int, today: int} */
    private function stats(): array
    {
        $query = fn () => DB::table('counselling_enquiries')->where('source_page', DubaiPage::PUBLIC_PATH);

        return [
            'total' => $query()->count(),
            'week' => $query()->where('created_at', '>=', now()->subDays(7))->count(),
            'today' => $query()->where('created_at', '>=', now()->startOfDay())->count(),
        ];
    }

    /** @return array{q: string, level: string, intake: string, budget: string, source: string, period: string, dup: string} */
    private function filters(Request $request): array
    {
        $text = fn (string $key): string => is_string($request->query($key)) ? mb_substr(trim($request->query($key)), 0, 160) : '';

        return [
            'q' => $text('q'),
            'level' => $text('level'),
            'intake' => $text('intake'),
            'budget' => in_array($text('budget'), DubaiPage::BUDGETS, true) ? $text('budget') : '',
            'source' => $text('source'),
            'period' => in_array($text('period'), ['today', '7', '30', 'all'], true) ? $text('period') : 'all',
            'dup' => in_array($text('dup'), ['duplicates', 'unique'], true) ? $text('dup') : '',
        ];
    }

    /** @param array{q: string, level: string, intake: string, budget: string, source: string, period: string, dup: string} $filters */
    private function filteredLeads(array $filters): LengthAwarePaginator
    {
        return $this->leadQuery($filters)
            ->paginate(25, ['id', 'full_name', 'email', 'phone', 'city', 'study_level', 'preferred_intake', 'message', 'utm_source', 'utm_campaign', 'created_at'])
            ->withQueryString();
    }

    /** @param array{q: string, level: string, intake: string, budget: string, source: string, period: string, dup: string} $filters */
    private function leadQuery(array $filters): Builder
    {
        return DB::table('counselling_enquiries')
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->when($filters['q'] !== '', function ($query) use ($filters): void {
                $term = '%'.addcslashes($filters['q'], '%_\\').'%';
                $query->where(fn ($inner) => $inner->where('full_name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone', 'like', $term)->orWhere('city', 'like', $term));
            })
            ->when($filters['level'] !== '', fn ($query) => $query->where('study_level', $filters['level']))
            ->when($filters['intake'] !== '', fn ($query) => $query->where('preferred_intake', $filters['intake']))
            ->when($filters['budget'] !== '', fn ($query) => $query->where('message', 'like', 'Approx. yearly budget: '.$filters['budget'].'%'))
            ->when($filters['source'] === 'direct', fn ($query) => $query->where(fn ($inner) => $inner->whereNull('utm_source')->orWhere('utm_source', '')))
            ->when($filters['source'] !== '' && $filters['source'] !== 'direct', fn ($query) => $query->where('utm_source', $filters['source']))
            ->when($filters['dup'] === 'duplicates', fn ($query) => $query->where(fn ($inner) => $inner
                ->whereIn('email', $this->repeatedValues('email'))
                ->orWhereIn('phone', $this->repeatedValues('phone'))))
            ->when($filters['dup'] === 'unique', fn ($query) => $query->whereIn('id', DB::table('counselling_enquiries')
                ->selectRaw('max(id)')
                ->where('source_page', DubaiPage::PUBLIC_PATH)
                ->groupBy('email', 'phone')))
            ->when($filters['period'] === 'today', fn ($query) => $query->where('created_at', '>=', now()->startOfDay()))
            ->when(in_array($filters['period'], ['7', '30'], true), fn ($query) => $query->where('created_at', '>=', now()->subDays((int) $filters['period'])))
            ->latest('created_at')
            ->orderByDesc('id');
    }

    /** Emails or phones that appear on more than one Dubai enquiry. */
    private function repeatedValues(string $column): Builder
    {
        return DB::table('counselling_enquiries')
            ->select($column)
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->havingRaw('count(*) > 1');
    }

    /**
     * How many Dubai enquiries share each listed lead's email or phone.
     *
     * @param  Collection<int, object>  $leads
     * @return array<int, int>
     */
    private function repeatCounts(Collection $leads): array
    {
        if ($leads->isEmpty()) {
            return [];
        }

        $count = fn (string $column) => DB::table('counselling_enquiries')
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->whereIn($column, $leads->pluck($column)->filter()->unique()->values())
            ->groupBy($column)
            ->selectRaw($column.', count(*) as total')
            ->pluck('total', $column);
        $byEmail = $count('email');
        $byPhone = $count('phone');

        return $leads->mapWithKeys(fn (object $lead): array => [
            $lead->id => max((int) ($byEmail[$lead->email] ?? 1), (int) ($byPhone[$lead->phone] ?? 1)),
        ])->all();
    }

    /** @return list<string> */
    private function sources(): array
    {
        return DB::table('counselling_enquiries')
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->distinct()
            ->orderBy('utm_source')
            ->limit(40)
            ->pluck('utm_source')
            ->all();
    }
}
