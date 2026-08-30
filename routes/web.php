<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\CounsellingEnquiryController;
use App\Http\Controllers\MirrorPageController;
use App\Http\Controllers\SiteAnalyticsController;
use App\Http\Controllers\StudyAssistantController;
use Illuminate\Support\Facades\Route;

Route::post('/site-events', [SiteAnalyticsController::class, 'store'])
    ->middleware('throttle:90,1')
    ->name('site-events.store');

Route::post('/study-assistant/chat', [StudyAssistantController::class, 'chat'])
    ->middleware('throttle:30,1')
    ->name('study-assistant.chat');

Route::post('/ai-agents/run', [AiAgentController::class, 'run'])
    ->middleware(['auth', 'admin', 'throttle:20,1'])
    ->name('ai-agents.run');

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
        Route::get('/ads', [AdminController::class, 'ads'])->middleware('can:ads.view')->name('ads.index');
        Route::post('/ads/accounts', [AdminController::class, 'storeAdAccount'])->middleware('can:ads.manage')->name('ads.accounts.store');
        Route::post('/ads/campaigns', [AdminController::class, 'storeAdCampaign'])->middleware('can:ads.manage')->name('ads.campaigns.store');
        Route::post('/ads/performance', [AdminController::class, 'storeAdPerformance'])->middleware('can:ads.manage')->name('ads.performance.store');
        Route::get('/export', [AdminController::class, 'export'])->middleware('can:enquiries.export')->name('export');

        Route::middleware('can:content.manage')->group(function (): void {
            Route::get('/blogs', [AdminController::class, 'blogs'])->name('blogs.index');
            Route::get('/blogs/create', [AdminController::class, 'blogsCreate'])->name('blogs.create');
            Route::post('/blogs', [AdminController::class, 'blogsStore'])->name('blogs.store');
            Route::get('/blogs/{blog}/edit', [AdminController::class, 'blogsEdit'])->name('blogs.edit');
            Route::put('/blogs/{blog}', [AdminController::class, 'blogsUpdate'])->name('blogs.update');
            Route::post('/blogs/{blog}/publish', [AdminController::class, 'blogsPublish'])->name('blogs.publish');
            Route::post('/blogs/{blog}/unpublish', [AdminController::class, 'blogsUnpublish'])->name('blogs.unpublish');
            Route::delete('/blogs/{blog}', [AdminController::class, 'blogsDestroy'])->name('blogs.destroy');

            Route::get('/forms', [AdminController::class, 'forms'])->name('forms.index');
            Route::get('/forms/create', [AdminController::class, 'formsCreate'])->name('forms.create');
            Route::post('/forms', [AdminController::class, 'formsStore'])->name('forms.store');
            Route::get('/forms/{form}/edit', [AdminController::class, 'formsEdit'])->name('forms.edit');
            Route::put('/forms/{form}', [AdminController::class, 'formsUpdate'])->name('forms.update');
            Route::post('/forms/{form}/publish', [AdminController::class, 'formsPublish'])->name('forms.publish');
            Route::post('/forms/{form}/unpublish', [AdminController::class, 'formsUnpublish'])->name('forms.unpublish');
            Route::delete('/forms/{form}', [AdminController::class, 'formsDestroy'])->name('forms.destroy');
        });

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
            Route::post('/media/folders', [AdminController::class, 'createMediaFolder'])->name('media.folders.store');
            Route::patch('/media/folders/{folder}', [AdminController::class, 'renameMediaFolder'])->where('folder', '[^/]+')->name('media.folders.update');
            Route::delete('/media/folders/{folder}', [AdminController::class, 'deleteMediaFolder'])->where('folder', '[^/]+')->name('media.folders.destroy');
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

// The agent workspace is an internal tool and is not part of the public site.
Route::get('/ai-agents', [MirrorPageController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('ai-agents.private');

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
