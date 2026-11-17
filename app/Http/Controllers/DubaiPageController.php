<?php

namespace App\Http\Controllers;

use App\Support\DubaiPage;
use Illuminate\View\View;

class DubaiPageController extends Controller
{
    /**
     * Render the CMS-driven Study in Dubai landing page.
     */
    public function show(): View
    {
        $tracking = DubaiPage::tracking();

        return view('dubai.show', [
            'page' => DubaiPage::content(),
            'tracking' => [
                'enabled' => $tracking['enabled'] === '1',
                'metaPixelId' => $tracking['meta_pixel_id'],
                'googleAdsId' => $tracking['google_ads_id'],
                'googleAdsConversionLabel' => $tracking['google_ads_conversion_label'],
            ],
        ]);
    }
}
