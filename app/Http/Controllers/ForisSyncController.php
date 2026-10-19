<?php

namespace App\Http\Controllers;

use App\Support\ForisLeadSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ForisSyncController extends Controller
{
    public function index(Request $request, ForisLeadSync $sync): View
    {
        Gate::authorize('foris.manage');
        $request->validate(['status' => ['nullable', Rule::in(ForisLeadSync::STATUSES)], 'q' => ['nullable', 'string', 'max:160']]);
        $query = DB::table('foris_lead_deliveries as d')
            ->join('counselling_enquiries as e', 'e.id', '=', 'd.enquiry_id');
        if ($request->filled('status')) {
            $query->where('d.status', $request->string('status')->toString());
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->where(fn ($q) => $q->where('e.full_name', 'like', $term)->orWhere('e.email', 'like', $term)->orWhere('e.phone', 'like', $term));
        }

        return view('admin.integrations.foris', [
            'deliveries' => $query->select('d.*', 'e.full_name', 'e.email', 'e.phone', 'e.source_page', 'e.source_form')->orderByDesc('d.id')->paginate(25)->withQueryString(),
            'counts' => DB::table('foris_lead_deliveries')->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'statuses' => ForisLeadSync::STATUSES,
            'configured' => $sync->configured(),
            'heartbeat' => Cache::store(config('foris.cache_store', 'file'))->get('foris-worker-heartbeat'),
            'remaining' => DB::table('counselling_enquiries as e')->leftJoin('foris_lead_deliveries as d', 'd.enquiry_id', '=', 'e.id')
                ->whereIn('e.source', ['website', 'wordpress'])->whereNull('d.id')->count(),
        ]);
    }

    public function retry(int $delivery, ForisLeadSync $sync): RedirectResponse
    {
        Gate::authorize('foris.manage');
        abort_unless(DB::table('foris_lead_deliveries')->where('id', $delivery)->exists(), 404);
        $retried = $sync->retry($delivery);

        return back()->with('status', $retried ? 'Delivery scheduled again.' : 'Only a confirmed failed delivery can be retried. Reconcile uncertain deliveries in Foris first.');
    }

    public function backfill(): RedirectResponse
    {
        Gate::authorize('foris.manage');
        Artisan::call('crm:foris-backfill', ['--limit' => 200]);

        return back()->with('status', trim(Artisan::output()));
    }
}
