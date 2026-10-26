<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmBranchRequest;
use App\Models\CrmBranch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CrmBranchController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAccess();
        $branches = CrmBranch::query()->withCount('users')->orderBy('name')->paginate(25);

        return response()->view('admin.crm.branches.index', compact('branches'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        $this->authorizeAccess();

        return $this->form(new CrmBranch(['is_active' => true]));
    }

    public function show(int $branch): Response
    {
        $this->authorizeAccess();

        return $this->form(CrmBranch::findOrFail($branch));
    }

    public function store(CrmBranchRequest $request): RedirectResponse
    {
        return $this->save($request, new CrmBranch);
    }

    public function update(CrmBranchRequest $request, int $branch): RedirectResponse
    {
        return $this->save($request, CrmBranch::findOrFail($branch));
    }

    private function save(CrmBranchRequest $request, CrmBranch $branch): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $branch, $data): void {
            if ($branch->exists) {
                $branch = CrmBranch::whereKey($branch->id)->lockForUpdate()->firstOrFail();
                abort_unless($branch->version === (int) $data['version'], 409, 'This branch changed. Reload before saving.');
                $branch->version++;
            }
            $branch->fill([
                ...Arr::except($data, ['version', 'tax', 'banking']),
                'tax_details' => array_filter($data['tax'] ?? [], 'filled'),
                'banking_details' => array_filter($data['banking'] ?? [], 'filled'),
            ])->save();
            DB::table('crm_branch_events')->insert(['branch_id' => $branch->id, 'actor_id' => $request->user()->id, 'action' => $branch->wasRecentlyCreated ? 'created' : 'updated', 'created_at' => now()]);
        });

        return to_route('admin.crm.branches.show', $branch->id)->with('status', 'Branch details saved securely.');
    }

    private function authorizeAccess(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');
        Gate::authorize('crm.sensitive');
    }

    private function form(CrmBranch $branch): Response
    {
        return response()->view('admin.crm.branches.form', compact('branch'), 200, ['Cache-Control' => 'private, no-store']);
    }
}
