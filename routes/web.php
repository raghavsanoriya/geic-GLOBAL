<?php

use App\Http\Controllers\MirrorPageController;
use Illuminate\Support\Facades\Route;

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
