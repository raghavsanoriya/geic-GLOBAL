<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class MirrorPageController extends Controller
{
    /**
     * Render a page converted from the downloaded Rocket LMS HTML mirror.
     */
    public function show(?string $page = null): View
    {
        $page = rawurldecode($page ?? 'index');
        $page = trim(str_replace('\\', '/', $page), '/');
        $page = preg_replace('/\.html$/i', '', $page) ?: 'index';

        if (str_contains($page, '..') || ! preg_match('/^[\pL\pN\-_. +&!]+(?:\/[\pL\pN\-_. +&!]+)*$/u', $page)) {
            abort(404);
        }

        $viewPath = resource_path("views/mirror/{$page}.blade.php");

        abort_unless(is_file($viewPath), 404);

        return view()->file($viewPath, [
            'mirrorPage' => $page,
        ]);
    }
}
