<?php

use App\Http\Controllers\Api\MobileCatalogController;
use App\Http\Controllers\Api\MobileEnquiryController;
use App\Http\Controllers\Api\MobileProfileEvaluationController;
use App\Http\Controllers\StudyAssistantController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->name('api.mobile.')->group(function (): void {
    Route::get('/catalog', MobileCatalogController::class)->middleware('throttle:60,1')->name('catalog');
    Route::post('/study-assistant/chat', [StudyAssistantController::class, 'chat'])->middleware('throttle:30,1')->name('study-assistant.chat');
    Route::post('/profile-evaluations', MobileProfileEvaluationController::class)->middleware('throttle:20,1')->name('profile-evaluations.store');
    Route::post('/enquiries', MobileEnquiryController::class)->middleware('throttle:10,1')->name('enquiries.store');
});
