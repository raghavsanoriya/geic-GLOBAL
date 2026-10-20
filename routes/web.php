<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\CounsellingEnquiryController;
use App\Http\Controllers\CrmApplicationController;
use App\Http\Controllers\CrmCampaignAttachmentController;
use App\Http\Controllers\CrmCampaignController;
use App\Http\Controllers\CrmCampaignReportController;
use App\Http\Controllers\CrmContactController;
use App\Http\Controllers\CrmDashboardController;
use App\Http\Controllers\CrmDocumentController;
use App\Http\Controllers\CrmFinanceController;
use App\Http\Controllers\CrmFollowUpController;
use App\Http\Controllers\CrmImportController;
use App\Http\Controllers\CrmInboundFormController;
use App\Http\Controllers\CrmIntegrationSettingController;
use App\Http\Controllers\CrmLogController;
use App\Http\Controllers\CrmLoyaltyController;
use App\Http\Controllers\CrmManualLeadController;
use App\Http\Controllers\CrmProfileEntryController;
use App\Http\Controllers\CrmProgramController;
use App\Http\Controllers\CrmReferenceController;
use App\Http\Controllers\CrmReminderController;
use App\Http\Controllers\CrmReportController;
use App\Http\Controllers\CrmSearchController;
use App\Http\Controllers\CrmStudentController;
use App\Http\Controllers\CrmStudentOperationsController;
use App\Http\Controllers\CrmTaskController;
use App\Http\Controllers\CrmVisaController;
use App\Http\Controllers\ExpoTrackingController;
use App\Http\Controllers\ForisSyncController;
use App\Http\Controllers\LeadDuplicateReviewController;
use App\Http\Controllers\MirrorPageController;
use App\Http\Controllers\SiteAnalyticsController;
use App\Http\Controllers\StudyAssistantController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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

Route::post('/newsletters', [CounsellingEnquiryController::class, 'storeNewsletter'])->middleware('throttle:10,1')->name('newsletter.store');

Route::post('/contact/enquire', [CounsellingEnquiryController::class, 'storeContact'])
    ->middleware('throttle:10,1')
    ->name('contact.enquire');

Route::post('/hero-enquire', [CounsellingEnquiryController::class, 'storeHero'])
    ->middleware('throttle:10,1')
    ->name('hero.enquire');

if (env('GEIC_PUBLIC_WEBSITE_ONLY', false)) {
    Route::any('/admin/{any?}', fn () => abort(404))->where('any', '.*');
    Route::any('/dashboard/{any?}', fn () => abort(404))->where('any', '.*');
} else {
    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/login', [AdminController::class, 'loginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:8,1')->name('login.store');
        Route::get('/preview', [AdminController::class, 'preview'])->name('preview');

        Route::middleware(['auth', 'admin'])->group(function (): void {
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/crm/students', [CrmStudentController::class, 'index'])->middleware('can:crm.view')->name('crm.students.index');
            Route::get('/crm/student-export', [CrmStudentOperationsController::class, 'export'])->name('crm.students.export');
            Route::post('/crm/student-views', [CrmStudentOperationsController::class, 'saveView'])->name('crm.students.views');
            Route::delete('/crm/student-views/{view}', [CrmStudentOperationsController::class, 'removeView'])->whereNumber('view')->name('crm.students.views.remove');
            Route::post('/crm/student-bulk', [CrmStudentOperationsController::class, 'bulk'])->name('crm.students.bulk');
            Route::get('/crm/reports', [CrmReportController::class, 'index'])->middleware('can:crm.view')->name('crm.reports.index');
            Route::get('/crm/search', [CrmSearchController::class, 'index'])->middleware('throttle:60,1')->name('crm.search');
            Route::get('/crm/reminders', [CrmReminderController::class, 'index'])->name('crm.reminders.index');
            Route::post('/crm/reminders/preferences', [CrmReminderController::class, 'preferences'])->name('crm.reminders.preferences');
            Route::post('/crm/reminders/{reminder}/read', [CrmReminderController::class, 'read'])->whereNumber('reminder')->name('crm.reminders.read');
            Route::get('/crm/logs', [CrmLogController::class, 'index'])->middleware('can:crm.view')->name('crm.logs.index');
            Route::get('/crm', [CrmDashboardController::class, 'index'])->middleware('can:crm.view')->name('crm.dashboard');
            Route::get('/crm/imports', [CrmImportController::class, 'index'])->middleware('can:crm.imports.manage')->name('crm.imports.index');
            Route::get('/crm/imports/template', [CrmImportController::class, 'template'])->middleware('can:crm.imports.manage')->name('crm.imports.template');
            Route::get('/crm/imports/{import}/export', [CrmImportController::class, 'export'])->whereNumber('import')->middleware('can:crm.imports.manage')->name('crm.imports.export');
            Route::post('/crm/imports', [CrmImportController::class, 'store'])->middleware(['can:crm.imports.manage', 'throttle:5,1'])->name('crm.imports.store');
            Route::get('/crm/imports/{import}', [CrmImportController::class, 'show'])->whereNumber('import')->middleware('can:crm.imports.manage')->name('crm.imports.show');
            Route::patch('/crm/imports/{import}/mapping', [CrmImportController::class, 'map'])->whereNumber('import')->middleware('can:crm.imports.manage')->name('crm.imports.map');
            Route::post('/crm/imports/{import}/confirm', [CrmImportController::class, 'confirm'])->whereNumber('import')->middleware('can:crm.imports.manage')->name('crm.imports.confirm');
            Route::post('/crm/imports/{import}/reverse', [CrmImportController::class, 'reverse'])->whereNumber('import')->middleware('can:crm.imports.manage')->name('crm.imports.reverse');
            Route::get('/crm/students/{student}/records/{kind}', [CrmProfileEntryController::class, 'index'])->whereNumber('student')->whereIn('kind', ['education', 'employment', 'test_score', 'preference'])->name('crm.profile-entries.index');
            Route::post('/crm/students/{student}/records/{kind}', [CrmProfileEntryController::class, 'save'])->whereNumber('student')->whereIn('kind', ['education', 'employment', 'test_score', 'preference'])->name('crm.profile-entries.store');
            Route::patch('/crm/students/{student}/records/{kind}/{entry}', [CrmProfileEntryController::class, 'save'])->whereNumber(['student', 'entry'])->whereIn('kind', ['education', 'employment', 'test_score', 'preference'])->name('crm.profile-entries.update');
            Route::get('/crm/students/{student}/documents', [CrmDocumentController::class, 'index'])->whereNumber('student')->name('crm.documents.index');
            Route::post('/crm/students/{student}/documents', [CrmDocumentController::class, 'store'])->whereNumber('student')->middleware('throttle:10,1')->name('crm.documents.store');
            Route::get('/crm/students/{student}/documents/{document}/download', [CrmDocumentController::class, 'download'])->whereNumber(['student', 'document'])->name('crm.documents.download');
            Route::post('/crm/students/{student}/documents/{document}/retire', [CrmDocumentController::class, 'retire'])->whereNumber(['student', 'document'])->name('crm.documents.retire');
            Route::post('/crm/references/install-workflow-template', [CrmReferenceController::class, 'installWorkflow'])->middleware('throttle:3,1')->name('crm.references.install-workflow');
            Route::get('/crm/campaigns', [CrmCampaignController::class, 'index'])->middleware('can:crm.marketing.manage')->name('crm.campaigns.index');
            Route::get('/crm/campaign-reports', [CrmCampaignReportController::class, 'index'])->name('crm.campaign-reports');
            Route::get('/crm/campaigns/create', [CrmCampaignController::class, 'create'])->middleware('can:crm.marketing.manage')->name('crm.campaigns.create');
            Route::post('/crm/campaigns/{campaign}/attachments', [CrmCampaignAttachmentController::class, 'store'])->whereNumber('campaign')->middleware('throttle:10,1')->name('crm.campaigns.attachments.store');
            Route::get('/crm/campaigns/{campaign}/attachments/{attachment}', [CrmCampaignAttachmentController::class, 'download'])->whereNumber(['campaign', 'attachment'])->name('crm.campaigns.attachments.download');
            Route::post('/crm/campaigns/{campaign}/attachments/{attachment}/retire', [CrmCampaignAttachmentController::class, 'retire'])->whereNumber(['campaign', 'attachment'])->name('crm.campaigns.attachments.retire');
            Route::get('/crm/campaign-suppressions', [CrmCampaignController::class, 'suppressions'])->name('crm.campaigns.suppressions');
            Route::post('/crm/campaign-suppressions', [CrmCampaignController::class, 'suppress'])->name('crm.campaigns.suppressions.store');
            Route::post('/crm/campaigns/{campaign}/retry', [CrmCampaignController::class, 'retryBlocked'])->whereNumber('campaign')->name('crm.campaigns.retry');
            Route::post('/crm/campaigns/{campaign}/recipients/{recipient}/reconcile', [CrmCampaignController::class, 'reconcile'])->whereNumber(['campaign', 'recipient'])->middleware('throttle:20,1')->name('crm.campaigns.reconcile');
            Route::post('/crm/campaigns', [CrmCampaignController::class, 'store'])->middleware('can:crm.marketing.manage')->name('crm.campaigns.store');
            Route::get('/crm/campaigns/{campaign}', [CrmCampaignController::class, 'show'])->whereNumber('campaign')->middleware('can:crm.marketing.manage')->name('crm.campaigns.show');
            Route::patch('/crm/campaigns/{campaign}', [CrmCampaignController::class, 'update'])->whereNumber('campaign')->middleware('can:crm.marketing.manage')->name('crm.campaigns.update');
            Route::get('/crm/campaigns/{campaign}/preview', [CrmCampaignController::class, 'preview'])->whereNumber('campaign')->middleware('can:crm.marketing.manage')->name('crm.campaigns.preview');
            Route::post('/crm/campaigns/{campaign}/action', [CrmCampaignController::class, 'action'])->whereNumber('campaign')->middleware('can:crm.marketing.manage')->name('crm.campaigns.action');
            Route::get('/crm/inbound-forms', [CrmInboundFormController::class, 'index'])->middleware('can:crm.inbound.manage')->name('crm.inbound.index');
            Route::get('/crm/inbound-forms/create', [CrmInboundFormController::class, 'create'])->middleware('can:crm.inbound.manage')->name('crm.inbound.create');
            Route::post('/crm/inbound-forms', [CrmInboundFormController::class, 'store'])->middleware('can:crm.inbound.manage')->name('crm.inbound.store');
            Route::get('/crm/inbound-forms/{form}', [CrmInboundFormController::class, 'show'])->whereNumber('form')->middleware('can:crm.inbound.manage')->name('crm.inbound.show');
            Route::patch('/crm/inbound-forms/{form}', [CrmInboundFormController::class, 'update'])->whereNumber('form')->middleware('can:crm.inbound.manage')->name('crm.inbound.update');
            Route::post('/crm/inbound-forms/{form}/rotate', [CrmInboundFormController::class, 'rotate'])->whereNumber('form')->middleware('can:crm.inbound.manage')->name('crm.inbound.rotate');
            Route::get('/crm/inbound-forms/{form}/logs', [CrmInboundFormController::class, 'logs'])->whereNumber('form')->middleware('can:crm.inbound.manage')->name('crm.inbound.logs');
            Route::get('/crm/settings/{provider}', [CrmIntegrationSettingController::class, 'show'])->whereIn('provider', ['email', 'onesignal'])->middleware('can:crm.integrations.manage')->name('crm.integrations.show');
            Route::put('/crm/settings/{provider}', [CrmIntegrationSettingController::class, 'update'])->whereIn('provider', ['email', 'onesignal'])->middleware('can:crm.integrations.manage')->name('crm.integrations.update');
            Route::post('/crm/settings/{provider}/test', [CrmIntegrationSettingController::class, 'test'])->whereIn('provider', ['email', 'onesignal'])->middleware(['can:crm.integrations.manage', 'throttle:5,1'])->name('crm.integrations.test');
            Route::get('/crm/loyalty', [CrmLoyaltyController::class, 'index'])->middleware('can:crm.loyalty.view')->name('crm.loyalty.index');
            Route::get('/crm/loyalty/create', [CrmLoyaltyController::class, 'create'])->middleware('can:crm.loyalty.manage')->name('crm.loyalty.create');
            Route::post('/crm/loyalty', [CrmLoyaltyController::class, 'store'])->middleware('can:crm.loyalty.manage')->name('crm.loyalty.store');
            Route::get('/crm/loyalty/points', [CrmLoyaltyController::class, 'points'])->middleware('can:crm.loyalty.view')->name('crm.loyalty.points');
            Route::get('/crm/loyalty/rules', [CrmLoyaltyController::class, 'rules'])->middleware('can:crm.configure')->name('crm.loyalty.rules');
            Route::post('/crm/loyalty/rules', [CrmLoyaltyController::class, 'saveRule'])->middleware('can:crm.configure')->name('crm.loyalty.rules.store');
            Route::patch('/crm/loyalty/rules/{rule}', [CrmLoyaltyController::class, 'saveRule'])->whereNumber('rule')->middleware('can:crm.configure')->name('crm.loyalty.rules.update');
            Route::get('/crm/loyalty/{member}', [CrmLoyaltyController::class, 'show'])->whereNumber('member')->middleware('can:crm.loyalty.view')->name('crm.loyalty.show');
            Route::patch('/crm/loyalty/{member}', [CrmLoyaltyController::class, 'update'])->whereNumber('member')->middleware('can:crm.loyalty.manage')->name('crm.loyalty.update');
            Route::post('/crm/loyalty/{member}/referrals', [CrmLoyaltyController::class, 'referral'])->whereNumber('member')->middleware('can:crm.loyalty.manage')->name('crm.loyalty.referral');
            Route::post('/crm/loyalty/{member}/points', [CrmLoyaltyController::class, 'adjust'])->whereNumber('member')->middleware('can:crm.loyalty.manage')->name('crm.loyalty.adjust');
            Route::get('/crm/contacts', [CrmContactController::class, 'index'])->middleware('can:crm.view')->name('crm.contacts.index');
            Route::get('/crm/contacts/create', [CrmContactController::class, 'create'])->middleware('can:crm.manage')->name('crm.contacts.create');
            Route::post('/crm/contacts', [CrmContactController::class, 'store'])->middleware('can:crm.manage')->name('crm.contacts.store');
            Route::get('/crm/contacts/{contact}', [CrmContactController::class, 'show'])->whereNumber('contact')->middleware('can:crm.view')->name('crm.contacts.show');
            Route::patch('/crm/contacts/{contact}', [CrmContactController::class, 'update'])->whereNumber('contact')->middleware('can:crm.manage')->name('crm.contacts.update');
            Route::get('/crm/invoices', [CrmFinanceController::class, 'index'])->middleware('can:crm.finance.view')->name('crm.invoices.index');
            Route::get('/crm/transactions', [CrmFinanceController::class, 'transactions'])->middleware('can:crm.finance.view')->name('crm.transactions.index');
            Route::get('/crm/invoices/create', [CrmFinanceController::class, 'create'])->middleware('can:crm.finance.manage')->name('crm.invoices.create');
            Route::post('/crm/invoices', [CrmFinanceController::class, 'store'])->middleware('can:crm.finance.manage')->name('crm.invoices.store');
            Route::get('/crm/invoices/{invoice}', [CrmFinanceController::class, 'show'])->whereNumber('invoice')->middleware('can:crm.finance.view')->name('crm.invoices.show');
            Route::get('/crm/invoices/{invoice}/print', [CrmFinanceController::class, 'print'])->whereNumber('invoice')->middleware('can:crm.finance.view')->name('crm.invoices.print');
            Route::patch('/crm/invoices/{invoice}', [CrmFinanceController::class, 'update'])->whereNumber('invoice')->middleware('can:crm.finance.manage')->name('crm.invoices.update');
            Route::post('/crm/invoices/{invoice}/status', [CrmFinanceController::class, 'status'])->whereNumber('invoice')->middleware('can:crm.finance.manage')->name('crm.invoices.status');
            Route::post('/crm/invoices/{invoice}/payments', [CrmFinanceController::class, 'payment'])->whereNumber('invoice')->middleware('can:crm.finance.manage')->name('crm.invoices.payments');
            Route::post('/crm/invoices/{invoice}/instalments', [CrmFinanceController::class, 'instalment'])->whereNumber('invoice')->middleware('can:crm.finance.manage')->name('crm.invoices.instalments');
            Route::post('/crm/payments/{payment}/reversal', [CrmFinanceController::class, 'reverse'])->whereNumber('payment')->middleware('can:crm.finance.manage')->name('crm.payments.reverse');
            Route::get('/crm/programs', [CrmProgramController::class, 'index'])->middleware('can:crm.view')->name('crm.programs.index');
            Route::get('/crm/programs/create', [CrmProgramController::class, 'create'])->middleware('can:crm.configure')->name('crm.programs.create');
            Route::post('/crm/programs', [CrmProgramController::class, 'store'])->middleware('can:crm.configure')->name('crm.programs.store');
            Route::get('/crm/programs/{program}', [CrmProgramController::class, 'show'])->whereNumber('program')->middleware('can:crm.configure')->name('crm.programs.show');
            Route::patch('/crm/programs/{program}', [CrmProgramController::class, 'update'])->whereNumber('program')->middleware('can:crm.configure')->name('crm.programs.update');
            Route::get('/crm/applications', [CrmApplicationController::class, 'index'])->middleware('can:crm.view')->name('crm.applications.index');
            Route::get('/crm/applications/create', [CrmApplicationController::class, 'create'])->middleware('can:crm.manage')->name('crm.applications.create');
            Route::post('/crm/applications', [CrmApplicationController::class, 'store'])->middleware('can:crm.manage')->name('crm.applications.store');
            Route::get('/crm/applications/{application}', [CrmApplicationController::class, 'show'])->whereNumber('application')->middleware('can:crm.view')->name('crm.applications.show');
            Route::patch('/crm/applications/{application}', [CrmApplicationController::class, 'update'])->whereNumber('application')->middleware('can:crm.manage')->name('crm.applications.update');
            Route::get('/crm/visas', [CrmVisaController::class, 'index'])->middleware('can:crm.view')->name('crm.visas.index');
            Route::get('/crm/visas/create', [CrmVisaController::class, 'create'])->middleware('can:crm.manage')->name('crm.visas.create');
            Route::post('/crm/visas', [CrmVisaController::class, 'store'])->middleware('can:crm.manage')->name('crm.visas.store');
            Route::get('/crm/visas/{visa}', [CrmVisaController::class, 'show'])->whereNumber('visa')->middleware('can:crm.view')->name('crm.visas.show');
            Route::patch('/crm/visas/{visa}', [CrmVisaController::class, 'update'])->whereNumber('visa')->middleware('can:crm.manage')->name('crm.visas.update');
            Route::get('/crm/references', [CrmReferenceController::class, 'index'])->middleware('can:crm.configure')->name('crm.references.index');
            Route::get('/crm/references/create', [CrmReferenceController::class, 'create'])->middleware('can:crm.configure')->name('crm.references.create');
            Route::post('/crm/references', [CrmReferenceController::class, 'store'])->middleware('can:crm.configure')->name('crm.references.store');
            Route::get('/crm/references/{reference}', [CrmReferenceController::class, 'show'])->whereNumber('reference')->middleware('can:crm.configure')->name('crm.references.show');
            Route::patch('/crm/references/{reference}', [CrmReferenceController::class, 'update'])->whereNumber('reference')->middleware('can:crm.configure')->name('crm.references.update');
            Route::get('/crm/leads/create', [CrmManualLeadController::class, 'create'])->middleware('can:crm.manage')->name('crm.leads.create');
            Route::post('/crm/leads', [CrmManualLeadController::class, 'store'])->middleware('can:crm.manage')->name('crm.leads.store');
            Route::get('/crm/tasks', [CrmTaskController::class, 'index'])->middleware('can:crm.view')->name('crm.tasks.index');
            Route::get('/crm/tasks/create', [CrmTaskController::class, 'create'])->middleware('can:crm.manage')->name('crm.tasks.create');
            Route::post('/crm/tasks', [CrmTaskController::class, 'store'])->middleware('can:crm.manage')->name('crm.tasks.store');
            Route::get('/crm/tasks/{task}', [CrmTaskController::class, 'show'])->whereNumber('task')->middleware('can:crm.view')->name('crm.tasks.show');
            Route::patch('/crm/tasks/{task}', [CrmTaskController::class, 'update'])->whereNumber('task')->middleware('can:crm.manage')->name('crm.tasks.update');
            Route::get('/crm/follow-ups', [CrmFollowUpController::class, 'index'])->middleware('can:crm.view')->name('crm.follow-ups.index');
            Route::get('/crm/students/{student}/follow-ups/create', [CrmFollowUpController::class, 'create'])->whereNumber('student')->middleware('can:crm.manage')->name('crm.follow-ups.create');
            Route::post('/crm/students/{student}/follow-ups', [CrmFollowUpController::class, 'store'])->whereNumber('student')->middleware('can:crm.manage')->name('crm.follow-ups.store');
            Route::get('/crm/follow-ups/{followUp}', [CrmFollowUpController::class, 'show'])->whereNumber('followUp')->middleware('can:crm.view')->name('crm.follow-ups.show');
            Route::patch('/crm/follow-ups/{followUp}', [CrmFollowUpController::class, 'update'])->whereNumber('followUp')->middleware('can:crm.manage')->name('crm.follow-ups.update');
            Route::get('/crm/students/{student}', [CrmStudentController::class, 'show'])->whereNumber('student')->middleware('can:crm.view')->name('crm.students.show');
            Route::patch('/crm/students/{student}', [CrmStudentController::class, 'update'])->whereNumber('student')->middleware('can:crm.manage')->name('crm.students.update');
            Route::get('/enquiries', [AdminController::class, 'enquiries'])->middleware('can:enquiries.view')->name('enquiries.index');
            Route::get('/enquiries/export', [AdminController::class, 'leadExport'])->middleware('can:enquiries.export')->name('enquiries.export');
            Route::get('/duplicate-review', [LeadDuplicateReviewController::class, 'index'])->middleware('can:enquiries.view')->name('duplicates.index');
            Route::patch('/duplicate-review/{review}', [LeadDuplicateReviewController::class, 'update'])->whereNumber('review')->middleware('can:enquiries.review')->name('duplicates.update');
            Route::get('/landing-page-leads', [AdminController::class, 'landingPageLeads'])->middleware('can:enquiries.view')->name('landing-leads');

            Route::get('/ads', [AdminController::class, 'ads'])->middleware('can:ads.view')->name('ads.index');
            Route::post('/ads/accounts', [AdminController::class, 'storeAdAccount'])->middleware('can:ads.manage')->name('ads.accounts.store');
            Route::post('/ads/campaigns', [AdminController::class, 'storeAdCampaign'])->middleware('can:ads.manage')->name('ads.campaigns.store');
            Route::post('/ads/performance', [AdminController::class, 'storeAdPerformance'])->middleware('can:ads.manage')->name('ads.performance.store');
            Route::get('/expo-tracking', [ExpoTrackingController::class, 'edit'])->middleware('can:ads.manage')->name('expo-tracking.edit');
            Route::put('/expo-tracking', [ExpoTrackingController::class, 'update'])->middleware('can:ads.manage')->name('expo-tracking.update');
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

            Route::middleware('can:foris.manage')->group(function (): void {
                Route::get('/integrations/foris', [ForisSyncController::class, 'index'])->name('foris.index');
                Route::post('/integrations/foris/backfill', [ForisSyncController::class, 'backfill'])->middleware('throttle:3,1')->name('foris.backfill');
                Route::post('/integrations/foris/{delivery}/retry', [ForisSyncController::class, 'retry'])->whereNumber('delivery')->middleware('throttle:10,1')->name('foris.retry');
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
                Route::get('/users/{user}/password', [AdminUserController::class, 'editPassword'])->name('users.password.edit');
                Route::put('/users/{user}/password', [AdminUserController::class, 'updatePassword'])->middleware('throttle:6,1')->name('users.password.update');
            });

            Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        });
    });
}

Route::post('/landing/form-handler.php', [CounsellingEnquiryController::class, 'storeLanding'])
    ->middleware('throttle:10,1')
    ->name('landing.enquire');
Route::post('/promotions/{promotion}/form-handler.php', [CounsellingEnquiryController::class, 'storePromotion'])
    ->where('promotion', '[a-z0-9-]+')
    ->middleware('throttle:10,1')
    ->name('promotions.enquire');
Route::post('/uniexpo-dubai-europe/register', [CounsellingEnquiryController::class, 'storeExpo'])
    ->withoutMiddleware([PreventRequestForgery::class])
    ->middleware('throttle:10,1')
    ->name('uniexpo.register');
Route::post('/_geic_release/uniexpo-dubai-europe/register', [CounsellingEnquiryController::class, 'storeExpo'])
    ->withoutMiddleware([PreventRequestForgery::class])
    ->middleware('throttle:10,1')
    ->name('uniexpo.release.register');

Route::get('/landing', [MirrorPageController::class, 'landing'])->name('landing');
Route::get('/landing/{asset}', [MirrorPageController::class, 'landingAsset'])
    ->where('asset', '.*')
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ])
    ->name('landing.asset');

Route::get('/uniexpo-dubai-europe/js/expo-config.js', [ExpoTrackingController::class, 'config'])
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ]);
Route::get('/_geic_release/uniexpo-dubai-europe/js/expo-config.js', [ExpoTrackingController::class, 'config'])
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ]);

Route::get('/uniexpo-dubai-europe/{asset?}', function (?string $asset = null) {
    $assets = [
        '' => 'index.html',
        'css/expo.css' => 'expo.css',
        'js/expo.js' => 'expo.js',
        'js/expo-config.js' => 'expo-config.js',
    ];

    $file = $assets[$asset ?? ''] ?? null;
    abort_unless($file !== null, 404);

    $contentTypes = [
        'index.html' => 'text/html; charset=UTF-8',
        'expo.css' => 'text/css; charset=UTF-8',
        'expo.js' => 'application/javascript; charset=UTF-8',
        'expo-config.js' => 'application/javascript; charset=UTF-8',
    ];

    return response()->file(base_path("uniexpo-page/{$file}"), [
        'Content-Type' => $contentTypes[$file],
    ]);
})->where('asset', '.*')->name('uniexpo.indore');

Route::get('/_geic_release/uniexpo-dubai-europe/{asset?}', function (?string $asset = null) {
    $assets = [
        '' => 'index.html',
        'css/expo.css' => 'expo.css',
        'js/expo.js' => 'expo.js',
        'js/expo-config.js' => 'expo-config.js',
    ];

    $file = $assets[$asset ?? ''] ?? null;
    abort_unless($file !== null, 404);

    $contentTypes = [
        'index.html' => 'text/html; charset=UTF-8',
        'expo.css' => 'text/css; charset=UTF-8',
        'expo.js' => 'application/javascript; charset=UTF-8',
        'expo-config.js' => 'application/javascript; charset=UTF-8',
    ];

    return response()->file(base_path("uniexpo-page/{$file}"), [
        'Content-Type' => $contentTypes[$file],
    ]);
})->where('asset', '.*')->name('uniexpo.release');

// The agent workspace is an internal tool and is not part of the public site.
Route::get('/ai-agents', [MirrorPageController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('ai-agents.private');

Route::get('/{page?}', [MirrorPageController::class, 'show'])
    ->where('page', '.*')
    ->name('mirror.page');
