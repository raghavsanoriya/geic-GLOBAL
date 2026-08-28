<?php

namespace App\Http\Controllers;

use App\Models\CmsForm;
use App\Models\CmsPage;
use App\Models\CmsPageState;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\CmsPageCatalog;
use App\Support\DestinationCatalog;
use App\Support\EventCatalog;
use App\Support\ScholarshipCatalog;
use App\Support\ServiceCatalog;
use App\Support\TestPrepCatalog;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function forms(Request $request): View
    {
        $this->ensureDetailPageForms();
        $query = CmsForm::query()->orderBy('destination')->orderBy('name');
        if ($request->filled('destination')) {
            $query->where('destination', $request->string('destination'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $forms = $query->get();

        return view('admin.forms.index', ['forms' => $forms, 'destinations' => CmsForm::query()->whereNotNull('destination')->distinct()->orderBy('destination')->pluck('destination')]);
    }

    private function ensureDetailPageForms(): void
    {
        $defaults = $this->defaultFormFields();
        $pages = [];
        foreach (DestinationCatalog::slugs() as $slug) {
            $item = DestinationCatalog::find($slug);
            $pages[] = ['destination' => $item['name'] ?? ucfirst($slug), 'page_key' => 'destination.'.$slug, 'name' => ($item['name'] ?? ucfirst($slug)).' enquiry'];
        }
        foreach ([ServiceCatalog::all(), EventCatalog::all(), ScholarshipCatalog::all(), TestPrepCatalog::all()] as $catalog) {
            foreach ($catalog as $item) {
                $slug = $item['slug'] ?? $item['id'] ?? Str::slug($item['name'] ?? $item['title'] ?? 'page');
                $label = $item['name'] ?? $item['title'] ?? ucfirst($slug);
                $type = str_contains($label, 'test') ? 'test' : (str_contains($label, 'scholar') ? 'scholarship' : (str_contains($label, 'event') ? 'event' : 'service'));
                $pages[] = ['destination' => ucfirst($type), 'page_key' => $type.'.'.$slug, 'name' => $label.' enquiry'];
            }
        }
        foreach ($pages as $page) {
            CmsForm::firstOrCreate(['slug' => Str::slug($page['page_key'].'-form')], [...$page, 'fields' => $defaults, 'description' => 'Default enquiry form for this detail page', 'status' => 'draft']);
        }
    }

    public function formsCreate(): View
    {
        return view('admin.forms.form', ['form' => new CmsForm(['fields' => $this->defaultFormFields()]), 'mode' => 'create']);
    }

    public function formsStore(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['status'] = 'draft';
        CmsForm::create($data);

        return redirect()->route('admin.forms.index')->with('status', 'Form created as a draft.');
    }

    public function formsEdit(CmsForm $form): View
    {
        return view('admin.forms.form', ['form' => $form, 'mode' => 'edit']);
    }

    public function formsUpdate(Request $request, CmsForm $form): RedirectResponse
    {
        $data = $this->validateForm($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $form->update($data);

        return redirect()->route('admin.forms.index')->with('status', 'Form updated.');
    }

    public function formsPublish(CmsForm $form): RedirectResponse
    {
        $form->update(['status' => 'published', 'published_at' => now()]);

        return back()->with('status', 'Form published.');
    }

    public function formsUnpublish(CmsForm $form): RedirectResponse
    {
        $form->update(['status' => 'draft', 'published_at' => null]);

        return back()->with('status', 'Form moved to draft.');
    }

    public function formsDestroy(CmsForm $form): RedirectResponse
    {
        $form->delete();

        return back()->with('status', 'Form deleted.');
    }

    private function validateForm(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:120'],
            'destination' => ['nullable', 'string', 'max:120'], 'page_key' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1000'], 'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:100'], 'fields.*.key' => ['required', 'alpha_dash', 'max:80'],
            'fields.*.type' => ['required', Rule::in(['text', 'email', 'tel', 'number', 'select', 'textarea', 'date'])],
            'fields.*.placeholder' => ['nullable', 'string', 'max:160'], 'fields.*.options' => ['nullable', 'string', 'max:1000'],
            'fields.*.required' => ['nullable', 'boolean'],
        ]);
        $data['fields'] = array_values(array_map(fn ($field) => [...$field, 'required' => ! empty($field['required'])], $data['fields']));

        return $data;
    }

    private function defaultFormFields(): array
    {
        return [['label' => 'Full name', 'key' => 'full_name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your name'], ['label' => 'Phone number', 'key' => 'phone', 'type' => 'tel', 'required' => true, 'placeholder' => '+91 00000 00000'], ['label' => 'Email address', 'key' => 'email', 'type' => 'email', 'required' => true, 'placeholder' => 'you@example.com']];
    }

    public function loginForm(): View|RedirectResponse
    {
        return Auth::check()
            ? redirect()->route($this->preferredAdminRoute(Auth::user()))
            : view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'max:160'],
            'password' => ['required', 'string'],
        ]);

        $key = 'admin-login:'.$request->ip().'|'.Str::lower($credentials['email']);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Too many sign-in attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.'])->onlyInput('email');
        }

        if (! Auth::attempt([...$credentials, 'is_admin' => true, 'is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => 'These administrator credentials do not match our records.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route($this->preferredAdminRoute(Auth::user())));
    }

    public function dashboard(Request $request): View
    {
        $canViewEnquiries = Gate::allows('enquiries.view');
        $query = $canViewEnquiries
            ? $this->filteredEnquiries($request)
            : DB::table('counselling_enquiries')->whereRaw('1 = 0');
        $total = $canViewEnquiries ? DB::table('counselling_enquiries')->count() : 0;
        $today = $canViewEnquiries ? DB::table('counselling_enquiries')->whereDate('created_at', today())->count() : 0;
        $week = $canViewEnquiries ? DB::table('counselling_enquiries')->where('created_at', '>=', now()->subDays(6)->startOfDay())->count() : 0;
        $dailyCounts = $canViewEnquiries
            ? DB::table('counselling_enquiries')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->selectRaw('DATE(created_at) as activity_date, count(*) as total')
                ->groupBy('activity_date')
                ->pluck('total', 'activity_date')
            : collect();
        $dailyEnquiries = collect(range(6, 0))->map(function (int $daysAgo) use ($dailyCounts): array {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'total' => (int) ($dailyCounts[$date->toDateString()] ?? 0),
            ];
        });
        $destinations = $canViewEnquiries
            ? DB::table('counselling_enquiries')
                ->select('destination', DB::raw('count(*) as total'))
                ->groupBy('destination')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
            : collect();
        $analyticsSince = DB::table('site_events')->min('created_at');
        $pageViews = DB::table('site_events')->where('event_type', 'page_view')->count();
        $uniqueVisitors = DB::table('site_events')->where('event_type', 'page_view')->whereNotNull('visitor_hash')->distinct()->count('visitor_hash');
        $ctaClicks = DB::table('site_events')->whereIn('event_type', ['cta_click', 'outbound_click'])->count();
        $trackedConversions = DB::table('site_events')->where('event_type', 'form_submit')->count();
        $topPages = DB::table('site_events')
            ->where('event_type', 'page_view')
            ->select('path', DB::raw('count(*) as total'))
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $topCampaigns = DB::table('site_events')
            ->whereNotNull('utm_campaign')
            ->select('utm_campaign', DB::raw('count(*) as total'))
            ->groupBy('utm_campaign')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $leadSources = $canViewEnquiries
            ? DB::table('counselling_enquiries')
                ->select('source', DB::raw('count(*) as total'))
                ->groupBy('source')
                ->orderByDesc('total')
                ->get()
            : collect();

        return view('admin.dashboard', [
            'enquiries' => $query->paginate(15)->withQueryString(),
            'total' => $total,
            'today' => $today,
            'week' => $week,
            'dailyEnquiries' => $dailyEnquiries,
            'dailyEnquiryMax' => max(1, $dailyEnquiries->max('total')),
            'destinations' => $destinations,
            'destinationOptions' => $canViewEnquiries
                ? DB::table('counselling_enquiries')->distinct()->orderBy('destination')->pluck('destination')
                : collect(),
            'canViewEnquiries' => $canViewEnquiries,
            'analyticsSince' => $analyticsSince,
            'pageViews' => $pageViews,
            'uniqueVisitors' => $uniqueVisitors,
            'ctaClicks' => $ctaClicks,
            'trackedConversions' => $trackedConversions,
            'conversionRate' => $pageViews > 0 ? round(($trackedConversions / $pageViews) * 100, 1) : 0,
            'topPages' => $topPages,
            'topCampaigns' => $topCampaigns,
            'leadSources' => $leadSources,
        ]);
    }

    public function ads(Request $request): View
    {
        Gate::authorize('ads.view');

        $from = Carbon::parse($request->input('from', now()->subDays(29)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $campaigns = DB::table('ad_campaigns as campaigns')
            ->join('ad_accounts as accounts', 'accounts.id', '=', 'campaigns.ad_account_id')
            ->leftJoin('ad_performance as performance', function ($join) use ($from, $to): void {
                $join->on('performance.ad_campaign_id', '=', 'campaigns.id')
                    ->whereBetween('performance.metric_date', [$from->toDateString(), $to->toDateString()]);
            })
            ->select('campaigns.*', 'accounts.name as account_name', 'accounts.provider',
                DB::raw('COALESCE(SUM(performance.spend), 0) as spend'),
                DB::raw('COALESCE(SUM(performance.impressions), 0) as impressions'),
                DB::raw('COALESCE(SUM(performance.clicks), 0) as clicks'),
                DB::raw('COALESCE(SUM(performance.leads), 0) as leads'),
                DB::raw('COALESCE(SUM(performance.qualified_leads), 0) as qualified_leads'),
                DB::raw('COALESCE(SUM(performance.conversions), 0) as conversions'))
            ->when($request->filled('provider'), fn ($q) => $q->where('accounts.provider', $request->string('provider')))
            ->when($request->filled('status'), fn ($q) => $q->where('campaigns.status', $request->string('status')))
            ->when($request->filled('destination'), fn ($q) => $q->where('campaigns.destination', $request->string('destination')))
            ->groupBy('campaigns.id', 'accounts.name', 'accounts.provider')
            ->orderByDesc('spend')->get();
        $totals = [
            'spend' => $campaigns->sum('spend'), 'impressions' => $campaigns->sum('impressions'),
            'clicks' => $campaigns->sum('clicks'), 'leads' => $campaigns->sum('leads'),
            'qualified_leads' => $campaigns->sum('qualified_leads'), 'conversions' => $campaigns->sum('conversions'),
        ];
        $daily = DB::table('ad_performance')->whereBetween('metric_date', [$from->toDateString(), $to->toDateString()])
            ->select('metric_date', DB::raw('SUM(spend) as spend'), DB::raw('SUM(leads) as leads'), DB::raw('SUM(qualified_leads) as qualified_leads'))
            ->groupBy('metric_date')->orderBy('metric_date')->get();

        return view('admin.ads.index', [
            'accounts' => DB::table('ad_accounts')->latest()->get(), 'campaigns' => $campaigns, 'daily' => $daily,
            'totals' => $totals, 'from' => $from->toDateString(), 'to' => $to->toDateString(),
            'destinations' => DB::table('ad_campaigns')->whereNotNull('destination')->distinct()->orderBy('destination')->pluck('destination'),
        ]);
    }

    public function storeAdAccount(Request $request): RedirectResponse
    {
        Gate::authorize('ads.manage');
        $data = $request->validate(['provider' => ['required', 'string', 'max:40'], 'name' => ['required', 'string', 'max:120'], 'external_account_id' => ['nullable', 'string', 'max:120']]);
        DB::table('ad_accounts')->insert([...$data, 'status' => 'not_connected', 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('status', 'Ad account added. Connect credentials to begin syncing.');
    }

    public function storeAdCampaign(Request $request): RedirectResponse
    {
        Gate::authorize('ads.manage');
        $data = $request->validate(['ad_account_id' => ['required', 'integer', 'exists:ad_accounts,id'], 'name' => ['required', 'string', 'max:160'], 'objective' => ['nullable', 'string', 'max:60'], 'status' => ['required', 'string', 'max:30'], 'daily_budget' => ['nullable', 'numeric', 'min:0'], 'landing_page' => ['nullable', 'string', 'max:180'], 'destination' => ['nullable', 'string', 'max:80']]);
        DB::table('ad_campaigns')->insert([...$data, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('status', 'Campaign added to the workspace.');
    }

    public function storeAdPerformance(Request $request): RedirectResponse
    {
        Gate::authorize('ads.manage');
        $data = $request->validate(['ad_campaign_id' => ['required', 'integer', 'exists:ad_campaigns,id'], 'metric_date' => ['required', 'date'], 'spend' => ['nullable', 'numeric', 'min:0'], 'impressions' => ['nullable', 'integer', 'min:0'], 'clicks' => ['nullable', 'integer', 'min:0'], 'leads' => ['nullable', 'integer', 'min:0'], 'qualified_leads' => ['nullable', 'integer', 'min:0'], 'conversions' => ['nullable', 'integer', 'min:0'], 'revenue' => ['nullable', 'numeric', 'min:0']]);
        $data = array_merge(array_fill_keys(['spend', 'impressions', 'clicks', 'leads', 'qualified_leads', 'conversions', 'revenue'], 0), $data);
        DB::table('ad_performance')->updateOrInsert(['ad_campaign_id' => $data['ad_campaign_id'], 'metric_date' => $data['metric_date']], [...$data, 'updated_at' => now(), 'created_at' => now()]);

        return back()->with('status', 'Daily ad performance saved.');
    }

    public function export(Request $request): StreamedResponse
    {
        $enquiries = $this->filteredEnquiries($request)->get();

        return response()->streamDownload(function () use ($enquiries): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Received', 'Student', 'Email', 'Phone', 'Destination', 'City', 'Study level', 'Intake', 'Course', 'English test', 'Message', 'Source', 'Source form', 'Source page', 'UTM source', 'UTM medium', 'UTM campaign']);

            foreach ($enquiries as $enquiry) {
                fputcsv($stream, [
                    Carbon::parse($enquiry->created_at)->format('d M Y, h:i A'),
                    $enquiry->full_name,
                    $enquiry->email,
                    $enquiry->phone,
                    $enquiry->destination,
                    $enquiry->city,
                    $enquiry->study_level,
                    $enquiry->preferred_intake,
                    $enquiry->preferred_course,
                    $enquiry->english_test,
                    $enquiry->message,
                    $enquiry->source,
                    $enquiry->source_form,
                    $enquiry->source_page,
                    $enquiry->utm_source,
                    $enquiry->utm_medium,
                    $enquiry->utm_campaign,
                ]);
            }

            fclose($stream);
        }, 'geic-counselling-enquiries-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function enquiries(Request $request): View
    {
        Gate::authorize('enquiries.view');

        $enquiries = $this->filteredEnquiries($request)->paginate(20)->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'total' => DB::table('counselling_enquiries')->count(),
            'today' => DB::table('counselling_enquiries')->whereDate('created_at', today())->count(),
            'week' => DB::table('counselling_enquiries')->where('created_at', '>=', now()->subDays(6)->startOfDay())->count(),
            'destinationOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('destination')->pluck('destination'),
            'sourceOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('source')->pluck('source'),
            'sourcePageOptions' => DB::table('counselling_enquiries')->whereNotNull('source_page')->distinct()->orderBy('source_page')->pluck('source_page'),
            'formOptions' => DB::table('counselling_enquiries')->whereNotNull('source_form')->distinct()->orderBy('source_form')->pluck('source_form'),
            'campaignOptions' => DB::table('counselling_enquiries')->whereNotNull('utm_campaign')->distinct()->orderBy('utm_campaign')->pluck('utm_campaign'),
        ]);
    }

    public function leadExport(Request $request): View
    {
        Gate::authorize('enquiries.export');

        return view('admin.enquiries.export', [
            'matchingCount' => $this->filteredEnquiries($request)->count(),
            'total' => DB::table('counselling_enquiries')->count(),
            'destinationOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('destination')->pluck('destination'),
            'sourceOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('source')->pluck('source'),
            'sourcePageOptions' => DB::table('counselling_enquiries')->whereNotNull('source_page')->distinct()->orderBy('source_page')->pluck('source_page'),
            'formOptions' => DB::table('counselling_enquiries')->whereNotNull('source_form')->distinct()->orderBy('source_form')->pluck('source_form'),
            'campaignOptions' => DB::table('counselling_enquiries')->whereNotNull('utm_campaign')->distinct()->orderBy('utm_campaign')->pluck('utm_campaign'),
        ]);
    }

    public function pages(Request $request): View
    {
        $catalog = collect(CmsPageCatalog::all());
        $groups = collect(CmsPageCatalog::groups())->map(function (array $group, string $key) use ($catalog): array {
            $group['key'] = $key;
            $group['count'] = $catalog->filter(fn (array $page): bool => CmsPageCatalog::groupFor($page['key']) === $key)->count();

            return $group;
        });
        $activeGroup = $request->string('group')->toString();

        if (! $groups->has($activeGroup)) {
            $activeGroup = 'landing';
        }

        $saved = SiteContent::query()
            ->select('page_key', DB::raw('count(*) as total'))
            ->groupBy('page_key')
            ->pluck('total', 'page_key');

        $heroImages = SiteContent::query()
            ->whereIn('field_key', ['thumbnail_image', 'hero_image'])
            ->whereNotNull('value')
            ->orderByRaw("case when field_key = 'thumbnail_image' then 0 else 1 end")
            ->pluck('value', 'page_key');

        return view('admin.pages.index', [
            'pages' => $catalog->filter(fn (array $page): bool => CmsPageCatalog::groupFor($page['key']) === $activeGroup)->values(),
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'saved' => $saved,
            'heroImages' => $heroImages,
            'states' => CmsPageState::query()->get()->keyBy('page_key'),
        ]);
    }

    public function createPage(Request $request): View
    {
        $groups = CmsPageCatalog::groups();
        $selectedGroup = $request->string('group')->toString();

        if (! array_key_exists($selectedGroup, $groups)) {
            $selectedGroup = 'landing';
        }

        return view('admin.pages.create', compact('groups', 'selectedGroup'));
    }

    public function storePage(Request $request): RedirectResponse
    {
        $groups = array_keys(CmsPageCatalog::groups());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'group' => ['required', Rule::in($groups)],
            'description' => ['nullable', 'string', 'max:500'],
            'hero_title' => ['required', 'string', 'max:220'],
            'hero_copy' => ['required', 'string', 'max:1200'],
            'hero_image' => ['nullable', 'string', 'max:1200'],
        ]);

        $slug = Str::slug($validated['slug']);
        if (in_array($slug, ['home', 'index', 'admin', 'public'], true)) {
            return back()->withErrors(['slug' => 'This URL is protected. Choose a different page URL.'])->withInput();
        }

        $prefix = match ($validated['group']) {
            'destinations' => 'destination',
            'services' => 'service',
            'events' => 'event',
            'scholarships' => 'scholarship',
            'tests' => 'test',
            'promotions' => 'promotion',
            default => null,
        };
        $pageKey = $prefix ? $prefix.'.'.$slug : $slug;
        $path = $validated['group'] === 'landing' ? $slug : $validated['group'].'/'.$slug;

        if (CmsPageCatalog::find($pageKey) || CmsPage::query()->where('path', $path)->exists()) {
            return back()->withErrors(['slug' => 'A page already uses this URL. Choose a different page URL.'])->withInput();
        }

        // Custom pages inherit the complete schema of their group baseline.
        // This keeps services, events, scholarships, tests and promotions fully
        // editable instead of seeding only the three generic hero fields.
        $catalog = match ($validated['group']) {
            'promotions' => CmsPageCatalog::find('promotion.landing'),
            'landing' => null,
            default => CmsPageCatalog::firstForGroup($validated['group']),
        };

        DB::transaction(function () use ($validated, $slug, $pageKey, $path, $catalog): void {
            CmsPage::create([
                'page_key' => $pageKey,
                'group' => $validated['group'],
                'name' => trim($validated['name']),
                'slug' => $slug,
                'path' => $path,
                'description' => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
            ]);

            // Seed every baseline field so the guided editor and public renderer
            // have a real dynamic content baseline for the selected group.
            $initialContent = $catalog
                ? collect($catalog['fields'])->mapWithKeys(function (array $field) use ($validated): array {
                    $value = match ($field['key']) {
                        'hero_title' => $validated['hero_title'],
                        'hero_copy' => $validated['hero_copy'],
                        'hero_image' => $validated['hero_image'] ?: ($field['default'] ?: 'assets/services/expert-counselling.jpg'),
                        default => $field['default'] ?? '',
                    };

                    return [$field['key'] => [$field['label'], $field['type'], $value]];
                })->all()
                : [
                    'hero_title' => ['Hero title', 'text', $validated['hero_title']],
                    'hero_copy' => ['Hero description', 'textarea', $validated['hero_copy']],
                    'hero_image' => ['Hero image URL', 'image', $validated['hero_image'] ?: 'assets/services/expert-counselling.jpg'],
                ];

            foreach ($initialContent as $fieldKey => [$label, $type, $value]) {
                SiteContent::create([
                    'page_key' => $pageKey,
                    'field_key' => $fieldKey,
                    'label' => $label,
                    'type' => $type,
                    'value' => trim($value),
                ]);
            }

            CmsPageState::create(['page_key' => $pageKey, 'status' => 'draft', 'drafted_at' => now()]);
        });

        return redirect()->route('admin.pages.edit', $pageKey)->with('status', 'Page created as a draft. Review the content, then publish it when ready.');
    }

    public function duplicatePage(string $pageKey): RedirectResponse
    {
        $source = CmsPageCatalog::find($pageKey);
        abort_unless($source && CmsPageCatalog::groupFor($pageKey) === 'promotions', 404);

        $sourceRecord = CmsPage::query()->where('page_key', $pageKey)->first();
        $baseSlug = Str::slug(($sourceRecord?->slug ?: Str::afterLast($pageKey, '.')).'-copy');
        $slug = $baseSlug;
        $suffix = 2;

        while (CmsPage::query()->where('path', 'promotions/'.$slug)->exists() || CmsPageCatalog::find('promotion.'.$slug)) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        $newPageKey = 'promotion.'.$slug;
        $savedValues = SiteContent::valuesForPage($pageKey);

        DB::transaction(function () use ($source, $slug, $newPageKey, $savedValues): void {
            CmsPage::create([
                'page_key' => $newPageKey,
                'group' => 'promotions',
                'name' => $source['name'].' Copy',
                'slug' => $slug,
                'path' => 'promotions/'.$slug,
                'description' => $source['description'],
            ]);

            foreach ($source['fields'] as $field) {
                SiteContent::create([
                    'page_key' => $newPageKey,
                    'field_key' => $field['key'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'value' => $savedValues[$field['key']] ?? $field['default'],
                ]);
            }

            CmsPageState::create(['page_key' => $newPageKey, 'status' => 'draft', 'drafted_at' => now()]);
        });

        return redirect()->route('admin.pages.edit', $newPageKey)
            ->with('status', 'Promotional page duplicated as a private draft. Update its content and publish when ready.');
    }

    public function editPage(string $pageKey): View
    {
        $page = $this->catalogForPage($pageKey);
        abort_unless($page, 404);

        $values = SiteContent::valuesForPage($pageKey);
        $workflow = $this->workflowForPage($pageKey);

        return view('admin.pages.edit', [
            'page' => $page,
            'values' => $values,
            'workflow' => $workflow,
            'mediaUsage' => $this->mediaUsageForPage($page, $values, $workflow['publishedValues']),
            'libraryAssets' => MediaAsset::query()->latest()->limit(60)->get(),
        ]);
    }

    public function updatePage(Request $request, string $pageKey): RedirectResponse
    {
        $page = $this->catalogForPage($pageKey);
        abort_unless($page, 404);

        $validated = $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['nullable', 'string', 'max:12000'],
            'content_images' => ['nullable', 'array'],
            'content_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
            'intent' => ['required', 'in:draft,publish'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['required', 'string', 'regex:/^[a-z][a-z0-9_\-]{1,79}$/'],
            'custom_fields.*.label' => ['required', 'string', 'max:120'],
            'custom_fields.*.type' => ['required', 'in:text,textarea,image'],
            'custom_fields.*.section' => ['nullable', 'string', 'max:80'],
            'remove_fields' => ['nullable', 'array'],
            'remove_fields.*' => ['string', 'regex:/^[a-z][a-z0-9_\-]{1,79}$/'],
        ]);
        $fieldMap = collect($page['fields'])->keyBy('key');
        foreach (collect($validated['custom_fields'] ?? [])->keyBy('key') as $key => $definition) {
            $fieldMap->put($key, ['key' => $key, 'label' => trim($definition['label']), 'default' => '', 'type' => $definition['type'], 'section' => trim($definition['section'] ?? '') ?: 'Custom fields', 'custom' => true]);
        }
        $remove = collect($validated['remove_fields'] ?? []);

        foreach ($request->file('content_images', []) as $key => $file) {
            $field = $fieldMap->get($key);
            if (! $field || $field['type'] !== 'image' || ! $file) {
                continue;
            }

            $storedPath = $file->store('cms/pages', 'public');
            $path = 'storage/'.$storedPath;
            MediaAsset::create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'alt_text' => null,
                'mime_type' => $file->getMimeType() ?: 'image/*',
                'size' => $file->getSize(),
            ]);
            $validated['content'][$key] = $path;
        }

        DB::transaction(function () use ($validated, $fieldMap, $pageKey, $remove): void {
            $pageState = $this->initialisePageState($pageKey);

            if ($remove->isNotEmpty()) {
                SiteContent::query()->where('page_key', $pageKey)->whereIn('field_key', $remove->all())->delete();
            }

            foreach ($validated['content'] as $key => $value) {
                $field = $fieldMap->get($key);
                if (! $field) {
                    continue;
                }
                if ($remove->contains($key)) {
                    continue;
                }

                SiteContent::updateOrCreate(
                    ['page_key' => $pageKey, 'field_key' => $key],
                    ['label' => $field['label'], 'type' => $field['type'], 'value' => blank($value) ? null : trim($value)],
                );
            }

            if ($validated['intent'] === 'publish') {
                SiteContent::query()
                    ->where('page_key', $pageKey)
                    ->update(['published_value' => DB::raw('value')]);

                $pageState->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'drafted_at' => null,
                    'unpublished_at' => null,
                ]);
            } else {
                $pageState->update(['drafted_at' => now()]);
            }
        });

        $message = $validated['intent'] === 'publish'
            ? 'Page published. Visitors can now see this version.'
            : 'Draft saved. Visitors still see the previously published version.';

        return redirect()->route('admin.pages.edit', $pageKey)->with('status', $message);
    }

    public function unpublishPage(string $pageKey): RedirectResponse
    {
        abort_unless($this->catalogForPage($pageKey), 404);

        CmsPageState::query()->updateOrCreate(
            ['page_key' => $pageKey],
            ['status' => 'unpublished', 'unpublished_at' => now()],
        );

        return redirect()->route('admin.pages.edit', $pageKey)->with('status', 'The published CMS version has been unpublished. Your draft is still available in this editor.');
    }

    public function media(): View
    {
        $search = request()->string('q')->toString();
        $folder = request()->string('folder')->toString();
        $sort = request()->string('sort')->toString() ?: 'newest';
        $query = MediaAsset::query()
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('original_name', 'like', "%{$search}%")->orWhere('path', 'like', "%{$search}%")->orWhere('alt_text', 'like', "%{$search}%");
            }))
            ->when($folder, fn ($q) => $q->where('folder', $folder));
        $sortMap = ['name' => ['original_name', 'asc'], 'oldest' => ['created_at', 'asc'], 'size' => ['size', 'desc'], 'newest' => ['created_at', 'desc']];
        [$column, $direction] = $sortMap[$sort] ?? $sortMap['newest'];

        $folders = MediaAsset::query()->select('folder')->whereNotNull('folder')->distinct()->pluck('folder');
        try {
            if (Schema::hasTable('media_folders')) {
                $folders = MediaFolder::query()->orderBy('name')->pluck('name')->merge($folders)->unique()->values();
            }
        } catch (\Throwable $exception) {
            // Keep the media library available when an older cPanel release
            // has an unavailable or partially migrated folder table.
            report($exception);
        }

        return view('admin.media.index', [
            'assets' => $query->orderBy($column, $direction)->paginate(18)->withQueryString(),
            'folders' => $folders,
        ]);
    }

    public function createMediaFolder(Request $request): RedirectResponse
    {
        $name = trim($request->validate([
            'folder' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (str_contains($value, '/') || str_contains($value, '\\')) {
                        $fail('Folder names cannot contain slashes.');
                    }
                },
            ],
        ])['folder']);

        // Older cPanel releases may not have run the folder migration yet.
        // The media asset folder column still supports the workflow safely.
        try {
            if (Schema::hasTable('media_folders')) {
                MediaFolder::firstOrCreate(['name' => $name]);
            }
        } catch (\Throwable $exception) {
            // Keep the media workflow usable if a cPanel database migration is
            // temporarily unavailable; the selected folder is still carried
            // into the upload form and can be persisted with the asset record.
            report($exception);
        }

        return redirect()->route('admin.media.index', ['folder' => $name])->with('status', "Folder '{$name}' is ready. Select it before uploading images.");
    }

    public function storeMedia(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:30'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'folder' => ['nullable', 'string', 'max:100'],
        ]);
        $assets = collect($validated['images'])->map(function ($file) use ($validated) {
            $storedPath = $file->store('cms', 'public');

            return MediaAsset::create([
                'path' => 'storage/'.$storedPath,
                'folder' => trim($validated['folder'] ?? '') ?: 'General',
                'original_name' => $file->getClientOriginalName(),
                'alt_text' => $validated['alt_text'] ?: null,
                'mime_type' => $file->getMimeType() ?: 'image/*',
                'size' => $file->getSize(),
            ]);
        });

        return redirect()->route('admin.media.index')
            ->with('status', $assets->count().' image'.($assets->count() === 1 ? '' : 's').' uploaded successfully. They are now available in every page editor.')
            ->with('latestAsset', $assets->pluck('path')->implode(', '));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * A local-only route to make the dashboard easy to review during development.
     */
    public function preview(Request $request): RedirectResponse|Response
    {
        if (! app()->environment('local')) {
            abort(404);
        }

        $user = User::firstOrCreate(
            ['email' => 'local-preview@geic.test'],
            ['name' => 'Local Preview Administrator', 'password' => Hash::make(Str::random(48)), 'is_admin' => true],
        );

        if (! $user->is_admin || ! $user->is_active || $user->admin_role !== 'super_admin') {
            $user->forceFill([
                'is_admin' => true,
                'is_active' => true,
                'admin_role' => 'super_admin',
                'admin_permissions' => [],
            ])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    private function filteredEnquiries(Request $request)
    {
        return DB::table('counselling_enquiries')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q')->toString());

                $query->where(function ($search) use ($term) {
                    $search->where('full_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('preferred_course', 'like', "%{$term}%")
                        ->orWhere('source_page', 'like', "%{$term}%")
                        ->orWhere('source_form', 'like', "%{$term}%")
                        ->orWhere('utm_campaign', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('destination'), fn ($query) => $query->where('destination', $request->string('destination')->toString()))
            ->when($request->filled('source'), fn ($query) => $query->where('source', $request->string('source')->toString()))
            ->when($request->filled('source_page'), fn ($query) => $query->where('source_page', $request->string('source_page')->toString()))
            ->when($request->filled('source_form'), fn ($query) => $query->where('source_form', $request->string('source_form')->toString()))
            ->when($request->filled('utm_campaign'), fn ($query) => $query->where('utm_campaign', $request->string('utm_campaign')->toString()))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->string('from')->toString()))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->string('to')->toString()))
            ->orderByDesc('created_at');
    }

    private function preferredAdminRoute(User $user): string
    {
        $preferred = $user->admin_preferences['default_screen'] ?? 'dashboard';

        return match ($preferred) {
            'pages' => $user->hasAdminPermission('content.manage') ? 'admin.pages.index' : 'admin.dashboard',
            'media' => $user->hasAdminPermission('media.manage') ? 'admin.media.index' : 'admin.dashboard',
            default => 'admin.dashboard',
        };
    }

    /**
     * @return array{status: string, label: string, publishedAt: CarbonInterface|null, publishedValues: array<string, string>, hasDraftChanges: bool}
     */
    private function workflowForPage(string $pageKey): array
    {
        $pageState = CmsPageState::query()->where('page_key', $pageKey)->first();
        if (! $pageState) {
            return [
                'status' => 'published',
                'label' => 'Published baseline',
                'publishedAt' => null,
                'publishedValues' => SiteContent::valuesForPage($pageKey),
                'hasDraftChanges' => false,
            ];
        }

        $hasDraftChanges = $pageState->status === 'draft'
            || ($pageState->drafted_at && (! $pageState->published_at || $pageState->drafted_at->greaterThan($pageState->published_at)));

        $label = match ($pageState->status) {
            'unpublished' => 'Unpublished',
            'draft' => 'Draft only',
            default => $hasDraftChanges ? 'Published + draft changes' : 'Published',
        };

        return [
            'status' => $pageState->status,
            'label' => $label,
            'publishedAt' => $pageState->published_at,
            'publishedValues' => $pageState->status === 'published'
                ? SiteContent::query()
                    ->where('page_key', $pageKey)
                    ->whereNotNull('published_value')
                    ->pluck('published_value', 'field_key')
                    ->all()
                : [],
            'hasDraftChanges' => (bool) $hasDraftChanges,
        ];
    }

    /**
     * Resolve the editable schema for a catalogue page or a custom CMS page.
     * Promotional pages share the canonical landing schema so custom slugs
     * expose every section in the guided editor.
     */
    private function catalogForPage(string $pageKey): ?array
    {
        $customPage = CmsPage::query()->where('page_key', $pageKey)->first();
        $catalog = null;

        if ($customPage) {
            $catalog = $customPage->group === 'promotions'
                ? CmsPageCatalog::find('promotion.landing')
                : CmsPageCatalog::firstForGroup($customPage->group);

            if ($catalog) {
                $catalog = [
                    ...$catalog,
                    'key' => $customPage->page_key,
                    'name' => $customPage->name,
                    'description' => $customPage->description ?: $catalog['description'],
                ];
            }
        }

        $catalog = $catalog ?? CmsPageCatalog::find($pageKey);
        if (! $catalog) {
            return null;
        }

        $known = collect($catalog['fields'])->pluck('key')->all();
        $customFields = SiteContent::query()->where('page_key', $pageKey)
            ->whereNotIn('field_key', $known)->get()
            ->map(fn (SiteContent $item): array => [
                'key' => $item->field_key,
                'label' => $item->label ?: $item->field_key,
                'default' => '',
                'type' => in_array($item->type, ['textarea', 'image', 'text'], true) ? $item->type : 'text',
                'section' => 'Custom fields',
                'custom' => true,
            ])->values()->all();

        return $customFields ? [...$catalog, 'fields' => [...$catalog['fields'], ...$customFields]] : $catalog;
    }

    private function initialisePageState(string $pageKey): CmsPageState
    {
        $pageState = CmsPageState::query()->where('page_key', $pageKey)->first();

        if ($pageState) {
            return $pageState;
        }

        $hasSavedContent = SiteContent::query()->where('page_key', $pageKey)->exists();

        if ($hasSavedContent) {
            SiteContent::query()
                ->where('page_key', $pageKey)
                ->whereNull('published_value')
                ->update(['published_value' => DB::raw('value')]);
        }

        return CmsPageState::query()->create([
            'page_key' => $pageKey,
            'status' => $hasSavedContent ? 'published' : 'draft',
            'published_at' => $hasSavedContent ? now() : null,
        ]);
    }

    /**
     * @param  array{key: string, fields: array<int, array{key: string, label: string, default: string, type: string, section: string}>}  $page
     * @param  array<string, string>  $values
     * @param  array<string, string>  $publishedValues
     * @return array<int, array{fieldKey: string|null, label: string, section: string, draftPath: string, publishedPath: string, status: string, usageCount: int, editable: bool, libraryAsset: MediaAsset|null}>
     */
    private function mediaUsageForPage(array $page, array $values, array $publishedValues): array
    {
        $mediaUsage = collect($page['fields'])
            ->filter(fn (array $field): bool => $field['type'] === 'image')
            ->map(function (array $field) use ($values, $publishedValues): array {
                $draftPath = $values[$field['key']] ?? $field['default'];
                $publishedPath = $publishedValues[$field['key']] ?? $field['default'];

                return [
                    'fieldKey' => $field['key'],
                    'label' => $field['label'],
                    'section' => $field['section'] ?? 'Page content',
                    'draftPath' => $draftPath,
                    'publishedPath' => $publishedPath,
                    'status' => $draftPath === $publishedPath ? 'Matches published image' : 'Draft replacement',
                    'usageCount' => 1,
                    'editable' => true,
                ];
            })
            ->merge(collect(CmsPageCatalog::mediaGroupsForPage($page['key']))->map(fn (array $media): array => [
                'fieldKey' => null,
                'label' => $media['label'],
                'section' => $media['section'],
                'draftPath' => $media['path'],
                'publishedPath' => $media['path'],
                'status' => 'Shared site asset',
                'usageCount' => $media['usageCount'],
                'editable' => false,
            ]))
            ->values();

        $libraryAssets = MediaAsset::query()
            ->whereIn('path', $mediaUsage->pluck('draftPath')->filter()->unique()->all())
            ->get()
            ->keyBy('path');

        return $mediaUsage
            ->map(fn (array $item): array => [...$item, 'libraryAsset' => $libraryAssets->get($item['draftPath'])])
            ->all();
    }
}
