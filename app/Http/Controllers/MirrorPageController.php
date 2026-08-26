<?php

namespace App\Http\Controllers;

use App\Support\DestinationCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MirrorPageController extends Controller
{
    /**
     * Render a page converted from the downloaded Rocket LMS HTML mirror.
     */
    public function show(?string $page = null): View|RedirectResponse
    {
        $page = rawurldecode($page ?? 'index');
        $page = trim(str_replace('\\', '/', $page), '/');

        if ($page === 'public') {
            return view('mirror.index', [
                'mirrorPage' => 'index',
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
            ]);
        }

        $viewPath = resource_path("views/mirror/{$page}.blade.php");

        abort_unless(is_file($viewPath), 404);

        return view()->file($viewPath, [
            'mirrorPage' => $page,
        ]);
    }
}
