<?php

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

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
