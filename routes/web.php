<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CounsellingEnquiryController;
use App\Http\Controllers\MirrorPageController;
use App\Http\Controllers\SiteAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::post('/site-events', [SiteAnalyticsController::class, 'store'])
    ->middleware('throttle:90,1')
    ->name('site-events.store');

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
        Route::get('/enquiries', [AdminController::class, 'enquiries'])->middleware('can:enquiries.view')->name('enquiries.index');
        Route::get('/enquiries/export', [AdminController::class, 'leadExport'])->middleware('can:enquiries.export')->name('enquiries.export');
        Route::get('/export', [AdminController::class, 'export'])->middleware('can:enquiries.export')->name('export');

        Route::middleware('can:content.manage')->group(function (): void {
            Route::get('/pages', [AdminController::class, 'pages'])->name('pages.index');
            Route::get('/pages/create', [AdminController::class, 'createPage'])->name('pages.create');
            Route::post('/pages', [AdminController::class, 'storePage'])->name('pages.store');
            Route::post('/pages/{pageKey}/duplicate', [AdminController::class, 'duplicatePage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.duplicate');
            Route::get('/pages/{pageKey}', [AdminController::class, 'editPage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.edit');
            Route::put('/pages/{pageKey}', [AdminController::class, 'updatePage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.update');
            Route::delete('/pages/{pageKey}/published', [AdminController::class, 'unpublishPage'])->where('pageKey', '[A-Za-z0-9._-]+')->name('pages.unpublish');
        });

        Route::middleware('can:media.manage')->group(function (): void {
            Route::get('/media', [AdminController::class, 'media'])->name('media.index');
            Route::post('/media', [AdminController::class, 'storeMedia'])->middleware('throttle:12,1')->name('media.store');
        });

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->middleware('throttle:6,1')->name('profile.password');
        Route::get('/settings', [AdminProfileController::class, 'settings'])->name('settings.edit');
        Route::put('/settings', [AdminProfileController::class, 'updateSettings'])->name('settings.update');

        Route::middleware('can:users.manage')->group(function (): void {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        });

        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

Route::post('/landing/form-handler.php', [CounsellingEnquiryController::class, 'storeLanding'])
    ->middleware('throttle:10,1')
    ->name('landing.enquire');
Route::post('/promotions/{promotion}/form-handler.php', [CounsellingEnquiryController::class, 'storePromotion'])
    ->where('promotion', '[a-z0-9-]+')
    ->middleware('throttle:10,1')
    ->name('promotions.enquire');
Route::get('/landing', [MirrorPageController::class, 'landing'])->name('landing');
Route::get('/landing/{asset}', [MirrorPageController::class, 'landingAsset'])
    ->where('asset', '.*')
    ->name('landing.asset');

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
