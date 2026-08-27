<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        return Auth::check()
            ? redirect()->route('admin.dashboard')
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

        if (! Auth::attempt([...$credentials, 'is_admin' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => 'These administrator credentials do not match our records.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function dashboard(Request $request): View
    {
        $query = $this->filteredEnquiries($request);
        $total = DB::table('counselling_enquiries')->count();
        $today = DB::table('counselling_enquiries')->whereDate('created_at', today())->count();
        $week = DB::table('counselling_enquiries')->where('created_at', '>=', now()->subDays(6)->startOfDay())->count();
        $dailyCounts = DB::table('counselling_enquiries')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as activity_date, count(*) as total')
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');
        $dailyEnquiries = collect(range(6, 0))->map(function (int $daysAgo) use ($dailyCounts): array {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'total' => (int) ($dailyCounts[$date->toDateString()] ?? 0),
            ];
        });
        $destinations = DB::table('counselling_enquiries')
            ->select('destination', DB::raw('count(*) as total'))
            ->groupBy('destination')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'enquiries' => $query->paginate(15)->withQueryString(),
            'total' => $total,
            'today' => $today,
            'week' => $week,
            'dailyEnquiries' => $dailyEnquiries,
            'dailyEnquiryMax' => max(1, $dailyEnquiries->max('total')),
            'destinations' => $destinations,
            'destinationOptions' => DB::table('counselling_enquiries')->distinct()->orderBy('destination')->pluck('destination'),
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

    public function pages(): View
    {
        $saved = SiteContent::query()
            ->select('page_key', DB::raw('count(*) as total'))
            ->groupBy('page_key')
            ->pluck('total', 'page_key');

        return view('admin.pages.index', [
            'pages' => CmsPageCatalog::all(),
            'saved' => $saved,
            'states' => CmsPageState::query()->get()->keyBy('page_key'),
        ]);
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
        ]);
    }

    public function updatePage(Request $request, string $pageKey): RedirectResponse
    {
        $page = CmsPageCatalog::find($pageKey);
        abort_unless($page, 404);

        $validated = $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['nullable', 'string', 'max:12000'],
            'intent' => ['required', 'in:draft,publish'],
        ]);
        $fieldMap = collect($page['fields'])->keyBy('key');

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

        if (! $user->is_admin) {
            $user->forceFill(['is_admin' => true])->save();
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
