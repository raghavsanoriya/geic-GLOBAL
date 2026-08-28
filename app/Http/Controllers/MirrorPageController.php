<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPageState;
use App\Models\SiteContent;
use App\Support\CmsPageCatalog;
use App\Support\DestinationCatalog;
use App\Support\EventCatalog;
use App\Support\PromotionPageRenderer;
use App\Support\ScholarshipCatalog;
use App\Support\ServiceCatalog;
use App\Support\TestPrepCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MirrorPageController extends Controller
{
    /**
     * Serve the standalone campaign landing page from its committed source.
     */
    public function landing(): View
    {
        $page = CmsPageCatalog::find('promotion.landing');
        abort_unless($page, 404);

        return view('mirror.promotions.show', [
            'promotionHtml' => PromotionPageRenderer::render(
                $page,
                SiteContent::publicValuesForPage('promotion.landing'),
                route('landing.enquire')
            ),
        ]);
    }

    /**
     * Serve landing-page assets without duplicating them inside public/.
     */
    public function landingAsset(string $asset): BinaryFileResponse
    {
        $landingRoot = realpath(base_path('landing-page'));
        $assetPath = realpath(base_path('landing-page/'.str_replace('\\', '/', $asset)));
        $contentTypes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];
        $extension = strtolower(pathinfo($asset, PATHINFO_EXTENSION));

        abort_unless(
            $landingRoot !== false
            && $assetPath !== false
            && is_file($assetPath)
            && str_starts_with($assetPath, $landingRoot.DIRECTORY_SEPARATOR)
            && isset($contentTypes[$extension]),
            404
        );

        return response()->file($assetPath, ['Content-Type' => $contentTypes[$extension]]);
    }

    /**
     * Render a page converted from the downloaded Trans Globe Indore LMS HTML mirror.
     */
    public function show(?string $page = null): View|RedirectResponse
    {
        $page = rawurldecode($page ?? 'index');
        $page = trim(str_replace('\\', '/', $page), '/');

        if ($page === 'public') {
            return view('mirror.index', [
                'mirrorPage' => 'index',
                'cms' => SiteContent::publicValuesForPage('home'),
            ]);
        }

        if (preg_match('/\.html$/i', $page)) {
            $canonicalPage = preg_replace('/\.html$/i', '', $page) ?: 'index';
            $canonicalUrl = $canonicalPage === 'index' ? url('/') : url('/'.$canonicalPage);

            return redirect()->to($canonicalUrl, 301);
        }

        if ($page === 'index' && request()->path() !== '/') {
            return redirect()->to(url('/'), 301);
        }

        $page = preg_replace('/\.html$/i', '', $page) ?: 'index';

        if (str_contains($page, '..') || ! preg_match('/^[\pL\pN\-_. +&!]+(?:\/[\pL\pN\-_. +&!]+)*$/u', $page)) {
            abort(404);
        }

        $customPage = null;

        try {
            if (Schema::hasTable('cms_pages')) {
                $customPage = CmsPage::query()->where('path', $page)->first();
            }
        } catch (QueryException) {
            // Public fixed pages must remain available during setup or migration.
        }

        if ($customPage) {
            $pageState = CmsPageState::query()->where('page_key', $customPage->page_key)->first();
            abort_unless($pageState?->status === 'published', 404);

            if ($customPage->group === 'promotions') {
                $catalogPage = CmsPageCatalog::find($customPage->page_key);

                return view('mirror.promotions.show', [
                    'promotionHtml' => PromotionPageRenderer::render(
                        $catalogPage,
                        SiteContent::publicValuesForPage($customPage->page_key),
                        route('promotions.enquire', $customPage->slug)
                    ),
                ]);
            }

            return view('mirror.dynamic', [
                'mirrorPage' => $page,
                'customPage' => $customPage,
                'cms' => SiteContent::publicValuesForPage($customPage->page_key),
            ]);
        }

        if (preg_match('#^destinations/([^/]+)$#', $page, $matches)) {
            $destination = DestinationCatalog::find($matches[1]);

            abort_unless($destination, 404);

            return view('mirror.destinations.detail', [
                'mirrorPage' => 'destinations/'.$destination['slug'],
                'destination' => $destination,
                'cms' => SiteContent::publicValuesForPage('destination.'.$destination['slug']),
            ]);
        }

        if (preg_match('#^services/([a-z\-]+)$#', $page, $matches)) {
            $service = ServiceCatalog::find($matches[1]);

            abort_unless($service, 404);

            return view('mirror.services.detail', [
                'mirrorPage' => $page,
                'service' => $service,
                'cms' => SiteContent::publicValuesForPage('service.'.$service['slug']),
            ]);
        }

        if (preg_match('#^events/([a-z0-9\-]+)$#', $page, $matches)) {
            $event = EventCatalog::find($matches[1]);

            abort_unless($event, 404);

            return view('mirror.events.detail', [
                'mirrorPage' => $page,
                'event' => $event,
                'cms' => SiteContent::publicValuesForPage('event.'.$event['slug']),
            ]);
        }

        if (preg_match('#^scholarships/([a-z\-]+)$#', $page, $matches)) {
            $scholarship = ScholarshipCatalog::find($matches[1]);

            abort_unless($scholarship, 404);

            return view('mirror.scholarships.detail', [
                'mirrorPage' => $page,
                'scholarship' => $scholarship,
                'cms' => SiteContent::publicValuesForPage('scholarship.'.$scholarship['slug']),
            ]);
        }

        if (preg_match('#^tests/([a-z\-]+)$#', $page, $matches)) {
            $test = TestPrepCatalog::find($matches[1]);

            abort_unless($test, 404);

            return view('mirror.tests.detail', [
                'mirrorPage' => $page,
                'test' => $test,
                'cms' => SiteContent::publicValuesForPage('test.'.$test['slug']),
            ]);
        }

        $viewPath = resource_path("views/mirror/{$page}.blade.php");

        abort_unless(is_file($viewPath), 404);

        return view()->file($viewPath, [
            'mirrorPage' => $page,
            'cms' => SiteContent::publicValuesForPage($page === 'index' ? 'home' : str_replace('/', '.', $page)),
            'events' => $page === 'events' ? EventCatalog::all() : [],
        ]);
    }
}
