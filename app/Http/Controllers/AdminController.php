<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPageState;
use App\Models\MediaAsset;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\CmsPageCatalog;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
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
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $enquiries = $this->filteredEnquiries($request)->get();

        return response()->streamDownload(function () use ($enquiries): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Received', 'Student', 'Email', 'Phone', 'Destination', 'City', 'Study level', 'Intake', 'Course', 'English test', 'Message', 'Source page']);

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
                    $enquiry->source_page,
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
        ]);
    }

    public function leadExport(Request $request): View
    {
        Gate::authorize('enquiries.export');

        return view('admin.enquiries.export', [
            'matchingCount' => $this->filteredEnquiries($request)->count(),
            'total' => DB::table('counselling_enquiries')->count(),
            'destinationOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('destination')->pluck('destination'),
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
            ->where('field_key', 'hero_image')
            ->whereNotNull('value')
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
            'scholarships' => 'scholarship',
            'tests' => 'test',
            default => null,
        };
        $pageKey = $prefix ? $prefix.'.'.$slug : $slug;
        $path = $validated['group'] === 'landing' ? $slug : $validated['group'].'/'.$slug;

        if (CmsPageCatalog::find($pageKey) || CmsPage::query()->where('path', $path)->exists()) {
            return back()->withErrors(['slug' => 'A page already uses this URL. Choose a different page URL.'])->withInput();
        }

        DB::transaction(function () use ($validated, $slug, $pageKey, $path): void {
            CmsPage::create([
                'page_key' => $pageKey,
                'group' => $validated['group'],
                'name' => trim($validated['name']),
                'slug' => $slug,
                'path' => $path,
                'description' => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
            ]);

            $initialContent = [
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

    public function editPage(string $pageKey): View
    {
        $page = CmsPageCatalog::find($pageKey);
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
        $page = CmsPageCatalog::find($pageKey);
        abort_unless($page, 404);

        $validated = $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['nullable', 'string', 'max:12000'],
            'content_images' => ['nullable', 'array'],
            'content_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
            'intent' => ['required', 'in:draft,publish'],
        ]);
        $fieldMap = collect($page['fields'])->keyBy('key');

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

        DB::transaction(function () use ($validated, $fieldMap, $pageKey): void {
            $pageState = $this->initialisePageState($pageKey);

            foreach ($validated['content'] as $key => $value) {
                $field = $fieldMap->get($key);
                if (! $field) {
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
        abort_unless(CmsPageCatalog::find($pageKey), 404);

        CmsPageState::query()->updateOrCreate(
            ['page_key' => $pageKey],
            ['status' => 'unpublished', 'unpublished_at' => now()],
        );

        return redirect()->route('admin.pages.edit', $pageKey)->with('status', 'The published CMS version has been unpublished. Your draft is still available in this editor.');
    }

    public function media(): View
    {
        return view('admin.media.index', [
            'assets' => MediaAsset::query()->latest()->paginate(18),
        ]);
    }

    public function storeMedia(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'alt_text' => ['nullable', 'string', 'max:180'],
        ]);

        $file = $validated['image'];
        $storedPath = $file->store('cms', 'public');
        $asset = MediaAsset::create([
            'path' => 'storage/'.$storedPath,
            'original_name' => $file->getClientOriginalName(),
            'alt_text' => $validated['alt_text'] ?: null,
            'mime_type' => $file->getMimeType() ?: 'image/*',
            'size' => $file->getSize(),
        ]);

        return redirect()->route('admin.media.index')->with('status', 'Image uploaded. Copy its URL into any Hero image field.')->with('latestAsset', $asset->path);
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
                        ->orWhere('preferred_course', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('destination'), fn ($query) => $query->where('destination', $request->string('destination')->toString()))
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
