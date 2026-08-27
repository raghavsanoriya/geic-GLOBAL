<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CounsellingEnquiryController;
use App\Http\Controllers\MirrorPageController;
use Illuminate\Support\Facades\Route;

Route::post('/destinations/australia/enquire', [CounsellingEnquiryController::class, 'storeAustralia'])
    ->middleware('throttle:10,1')
    ->name('destinations.australia.enquire');

Route::post('/destinations/{destination}/enquire', [CounsellingEnquiryController::class, 'storeDestination'])
    ->where('destination', '[a-z\-]+')
    ->middleware('throttle:10,1')
    ->name('destinations.enquire');

Route::post('/contact/enquire', [CounsellingEnquiryController::class, 'storeContact'])
    ->middleware('throttle:10,1')
    ->name('contact.enquire');

Route::post('/hero-enquire', [CounsellingEnquiryController::class, 'storeHero'])
    ->middleware('throttle:10,1')
    ->name('hero.enquire');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:8,1')->name('login.store');
    Route::get('/preview', [AdminController::class, 'preview'])->name('preview');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/export', [AdminController::class, 'export'])->name('export');
        Route::get('/pages', [AdminController::class, 'pages'])->name('pages.index');
        Route::get('/pages/{pageKey}', [AdminController::class, 'editPage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.edit');
        Route::put('/pages/{pageKey}', [AdminController::class, 'updatePage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.update');
        Route::delete('/pages/{pageKey}/published', [AdminController::class, 'unpublishPage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.unpublish');
        Route::get('/media', [AdminController::class, 'media'])->name('media.index');
        Route::post('/media', [AdminController::class, 'storeMedia'])->middleware('throttle:12,1')->name('media.store');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
