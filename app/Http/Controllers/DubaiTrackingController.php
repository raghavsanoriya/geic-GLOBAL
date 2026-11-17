<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\DubaiPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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

        return view('admin.tracking.dubai', [
            'tracking' => $tracking,
            'prefilledFromExpo' => $prefilledFromExpo,
            'canViewLeads' => $canViewLeads,
            'stats' => $canViewLeads ? $this->stats() : null,
            'leads' => $canViewLeads ? $this->latestLeads() : collect(),
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

    private function latestLeads(): Collection
    {
        return DB::table('counselling_enquiries')
            ->where('source_page', DubaiPage::PUBLIC_PATH)
            ->latest('created_at')
            ->limit(15)
            ->get(['id', 'full_name', 'email', 'phone', 'city', 'study_level', 'preferred_intake', 'utm_source', 'utm_campaign', 'created_at']);
    }
}
