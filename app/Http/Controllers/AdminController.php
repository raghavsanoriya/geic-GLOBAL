<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\CmsPageCatalog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
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
        ]);
    }

    public function editPage(string $pageKey): View
    {
        $page = CmsPageCatalog::find($pageKey);
        abort_unless($page, 404);

        return view('admin.pages.edit', [
            'page' => $page,
            'values' => SiteContent::valuesForPage($pageKey),
        ]);
    }

    public function updatePage(Request $request, string $pageKey): RedirectResponse
    {
        $page = CmsPageCatalog::find($pageKey);
        abort_unless($page, 404);

        $validated = $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['nullable', 'string', 'max:12000'],
        ]);
        $fieldMap = collect($page['fields'])->keyBy('key');

        DB::transaction(function () use ($validated, $fieldMap, $pageKey): void {
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
        });

        return redirect()->route('admin.pages.edit', $pageKey)->with('status', 'Website content saved. It is live on the public page now.');
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
}
