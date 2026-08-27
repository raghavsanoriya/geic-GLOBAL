<?php

namespace App\Http\Controllers;

use App\Support\DestinationCatalog;
use App\Models\SiteContent;
use App\Support\ScholarshipCatalog;
use App\Support\ServiceCatalog;
use App\Support\TestPrepCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MirrorPageController extends Controller
{
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
                'cms' => SiteContent::valuesForPage('home'),
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

        if (preg_match('#^destinations/([^/]+)$#', $page, $matches) && $matches[1] !== 'australia') {
            $destination = DestinationCatalog::find($matches[1]);

            abort_unless($destination, 404);

            return view('mirror.destinations.detail', [
                'mirrorPage' => 'destinations/'.$destination['slug'],
                'destination' => $destination,
                'cms' => SiteContent::valuesForPage('destination.'.$destination['slug']),
            ]);
        }

        if (preg_match('#^services/([a-z\-]+)$#', $page, $matches)) {
            $service = ServiceCatalog::find($matches[1]);

            abort_unless($service, 404);

            return view('mirror.services.detail', [
                'mirrorPage' => $page,
                'service' => $service,
                'cms' => SiteContent::valuesForPage('service.'.$service['slug']),
            ]);
        }

        if (preg_match('#^scholarships/([a-z\-]+)$#', $page, $matches)) {
            $scholarship = ScholarshipCatalog::find($matches[1]);

            abort_unless($scholarship, 404);

            return view('mirror.scholarships.detail', [
                'mirrorPage' => $page,
                'scholarship' => $scholarship,
                'cms' => SiteContent::valuesForPage('scholarship.'.$scholarship['slug']),
            ]);
        }

        if (preg_match('#^tests/([a-z\-]+)$#', $page, $matches)) {
            $test = TestPrepCatalog::find($matches[1]);

            abort_unless($test, 404);

            return view('mirror.tests.detail', [
                'mirrorPage' => $page,
                'test' => $test,
                'cms' => SiteContent::valuesForPage('test.'.$test['slug']),
            ]);
        }

        $viewPath = resource_path("views/mirror/{$page}.blade.php");

        abort_unless(is_file($viewPath), 404);

        return view()->file($viewPath, [
            'mirrorPage' => $page,
            'cms' => SiteContent::valuesForPage($page === 'index' ? 'home' : str_replace('/', '.', $page)),
        ]);
    }
}
