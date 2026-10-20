<?php

use App\Http\Controllers\Api\MobileCatalogController;
use App\Http\Controllers\Api\MobileEnquiryController;
use App\Http\Controllers\Api\MobileProfileEvaluationController;
use App\Http\Controllers\CrmInboundSubmissionController;
use App\Http\Controllers\CrmMarketingUnsubscribeController;
use App\Http\Controllers\StudyAssistantController;
use App\Http\Middleware\CrmInboundAudit;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/v1/marketing/unsubscribe/{recipient}/{token}', [CrmMarketingUnsubscribeController::class, 'handle'])
    ->whereNumber('recipient')->where('token', '[a-f0-9]{64}')->middleware('throttle:30,1')->name('crm.marketing.unsubscribe');

Route::post('/v1/inbound-forms/{formKey}/submissions', CrmInboundSubmissionController::class)
    ->where('formKey', '[A-Za-z0-9]{24}')->middleware(['throttle:60,1', CrmInboundAudit::class])->name('api.inbound-forms.submissions');

Route::prefix('mobile')->name('api.mobile.')->group(function (): void {
    Route::get('/catalog', MobileCatalogController::class)->middleware('throttle:60,1')->name('catalog');
    Route::post('/study-assistant/chat', [StudyAssistantController::class, 'chat'])->middleware('throttle:30,1')->name('study-assistant.chat');
    Route::post('/profile-evaluations', MobileProfileEvaluationController::class)->middleware('throttle:20,1')->name('profile-evaluations.store');
    Route::post('/enquiries', MobileEnquiryController::class)->middleware('throttle:10,1')->name('enquiries.store');
});
