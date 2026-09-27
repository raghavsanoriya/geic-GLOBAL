<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpoTrackingController extends Controller
{
    private const PAGE_KEY = 'integration.expo_tracking';

    /** @var array<string, string> */
    private const DEFAULTS = [
        'enabled' => '0',
        'meta_pixel_id' => '',
        'google_ads_id' => '',
        'google_ads_conversion_label' => '',
    ];

    public function edit(): View
    {
        Gate::authorize('ads.manage');

        return view('admin.tracking.expo', [
            'tracking' => array_merge(self::DEFAULTS, SiteContent::valuesForPage(self::PAGE_KEY)),
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
                ['page_key' => self::PAGE_KEY, 'field_key' => $fieldKey],
                [
                    'label' => str($fieldKey)->replace('_', ' ')->title()->toString(),
                    'type' => $fieldKey === 'enabled' ? 'toggle' : 'text',
                    'value' => $value,
                    'published_value' => $value,
                ],
            );
        }

        return back()->with('status', $enabled
            ? 'Expo advertising tracking is enabled. New page views and confirmed registrations will now be sent to the selected provider(s).'
            : 'Expo advertising tracking is paused. The saved IDs remain available for a future re-enable.');
    }

    public function config(): Response
    {
        $tracking = array_merge(self::DEFAULTS, SiteContent::valuesForPage(self::PAGE_KEY));

        $config = [
            'appsScriptUrl' => 'https://script.google.com/macros/s/AKfycbyHn9J57MrkRPLzUF4WCPh8eogwPP4RrU6-tCo4z1p6LTrfLZuerDgW12x2TGeF8I7F/exec',
            'laravelEndpoint' => '',
            'googleMapsApiKey' => '',
            'geicSearchQuery' => 'GEIC Indore study abroad consultants',
            'tracking' => [
                'enabled' => $tracking['enabled'] === '1',
                'metaPixelId' => $tracking['meta_pixel_id'],
                'googleAdsId' => $tracking['google_ads_id'],
                'googleAdsConversionLabel' => $tracking['google_ads_conversion_label'],
            ],
        ];

        return response('window.EXPO_CONFIG = '.json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT).";\n", 200, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'no-store, max-age=0',
        ]);
    }
}