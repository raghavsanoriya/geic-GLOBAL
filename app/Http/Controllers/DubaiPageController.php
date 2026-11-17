<?php

namespace App\Http\Controllers;

use App\Support\DubaiPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DubaiPageController extends Controller
{
    /**
     * Send the release-bridge path back to /dubai, keeping campaign parameters.
     */
    public function canonical(Request $request): RedirectResponse
    {
        $query = $request->getQueryString();

        return redirect(DubaiPage::PUBLIC_PATH.($query ? '?'.$query : ''), 301);
    }

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
