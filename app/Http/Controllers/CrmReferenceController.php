<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmReferenceRequest;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use Database\Seeders\CrmWorkflowTemplateSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CrmReferenceController extends Controller
{
    public function installWorkflow(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeConfig();
        $request->validate(['confirm' => ['required', 'accepted']]);
        $seeder = app(CrmWorkflowTemplateSeeder::class);
        $seeder->actorId = $request->user()->id;
        $seeder->run();
        $message = 'Workflow template installed. Existing edits, students, applications and payments were not changed.';

        return $request->expectsJson() ? response()->json(['message' => $message], 200, ['Cache-Control' => 'private, no-store'])
            : to_route('admin.crm.references.index', ['kind' => 'level'])->with('status', $message);
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizeConfig();
        $filters = $request->validate(['kind' => ['nullable', Rule::in(array_keys(CrmReference::KINDS))], 'q' => ['nullable', 'string', 'max:160'], 'parent_id' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000']]);
        $kind = $filters['kind'] ?? 'university';
        $query = CrmReference::where('kind', $kind)->with('parent:id,name');
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $filters['parent_id']);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }
        $references = $query->orderBy('name')->orderBy('id')->paginate(30)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($references, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.references.index', ['references' => $references, 'kind' => $kind, 'kinds' => CrmReference::KINDS], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request): Response
    {
        $this->authorizeConfig();
        $data = $request->validate(['kind' => ['required', Rule::in(array_keys(CrmReference::KINDS))]]);

        return $this->form($data['kind']);
    }

    public function show(int $reference): Response
    {
        $this->authorizeConfig();
        $record = CrmReference::findOrFail($reference);

        return $this->form($record->kind, $record);
    }

    public function store(CrmReferenceRequest $request): RedirectResponse|JsonResponse
    {
        return $this->save($request);
    }

    public function update(CrmReferenceRequest $request, int $reference): RedirectResponse|JsonResponse
    {
        return $this->save($request, $reference);
    }

    private function save(CrmReferenceRequest $request, ?int $id = null): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $record = DB::transaction(function () use ($request, $data, $id): CrmReference {
            $snapshot = $id ? CrmReference::findOrFail($id) : null;
            if (($snapshot?->kind ?? ($data['kind'] ?? null)) === 'level_status') {
                CrmReference::whereIn('id', array_filter([$snapshot?->parent_id, $data['parent_id'] ?? null]))->orderBy('id')->lockForUpdate()->get();
            }
            $record = $id ? CrmReference::whereKey($id)->lockForUpdate()->firstOrFail() : new CrmReference(['version' => 1]);
            if ($id) {
                abort_unless($record->version === (int) $data['version'], 409, 'This reference has changed. Reload before saving.');
            }
            if ($record->exists && $record->kind === 'level_status' && isset($data['parent_id']) && (int) $data['parent_id'] !== $record->parent_id
                && CrmStudent::where('stage_status_id', $record->id)->exists()) {
                throw ValidationException::withMessages(['parent_id' => 'This status is already assigned to students. Create a new status for another level.']);
            }
            $configuration = array_key_exists('configuration', $data) ? array_replace($record->configuration ?? [], $data['configuration']) : null;
            if ($record->kind === 'level_status' || ($data['kind'] ?? null) === 'level_status') {
                if (array_key_exists('is_default', $configuration ?? [])) {
                    $configuration['is_default'] = (bool) $configuration['is_default'];
                }
                if (($configuration['is_default'] ?? $record->configuration['is_default'] ?? false) && ! $data['is_active']) {
                    throw ValidationException::withMessages(['is_active' => 'A default status must remain active. Choose another default before deactivating it.']);
                }
            }
            $record->fill(Arr::except($data, ['version', 'transitions', 'transitions_present', 'restricted', 'configuration']));
            if ($configuration !== null) {
                $record->configuration = $configuration;
            }
            if (array_key_exists('restricted', $data)) {
                $updates = array_filter($data['restricted'], fn ($value) => filled($value));
                if ($updates !== []) {
                    $record->restricted_details = array_replace($record->restricted_details ?? [], $updates);
                }
            }
            $fields = array_keys($record->getDirty());
            $old = $id ? DB::table('crm_status_transitions')->where('from_id', $id)->orderBy('to_id')->pluck('to_id')->map(fn ($id) => (int) $id)->all() : [];
            $targets = array_key_exists('transitions_present', $data) || array_key_exists('transitions', $data) ? array_map('intval', $data['transitions'] ?? []) : $old;
            sort($targets);
            if ($old !== $targets) {
                $fields[] = 'transitions';
            }
            if ($id && $fields !== []) {
                $record->version++;
            }
            $record->save();
            if ($record->kind === 'level_status' && ($record->configuration['is_default'] ?? false)) {
                foreach (CrmReference::where('kind', 'level_status')->where('parent_id', $record->parent_id)->where('id', '!=', $record->id)->where('configuration->is_default', true)->lockForUpdate()->get() as $oldDefault) {
                    $oldDefault->configuration = array_replace($oldDefault->configuration ?? [], ['is_default' => false]);
                    $oldDefault->version++;
                    $oldDefault->save();
                    DB::table('crm_reference_events')->insert(['reference_id' => $oldDefault->id, 'actor_id' => $request->user()->id, 'action' => 'reference.updated',
                        'changes' => json_encode(['version' => $oldDefault->version, 'fields' => ['configuration.is_default']], JSON_THROW_ON_ERROR), 'created_at' => now()]);
                }
            }
            if ($old !== $targets) {
                DB::table('crm_status_transitions')->where('from_id', $record->id)->delete();
                foreach ($targets as $target) {
                    DB::table('crm_status_transitions')->insert(['from_id' => $record->id, 'to_id' => $target]);
                }
            }
            if (! $id || $fields !== []) {
                DB::table('crm_reference_events')->insert(['reference_id' => $record->id, 'actor_id' => $request->user()->id,
                    'action' => $id ? 'reference.updated' : 'reference.created',
                    'changes' => json_encode(['version' => $record->version, 'fields' => $fields, 'transition_ids' => $targets], JSON_THROW_ON_ERROR), 'created_at' => now()]);
            }

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.references.show', $record->id)->with('status', 'Configuration saved. Existing student lifecycle stages were not changed.');
    }

    private function authorizeConfig(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');
    }

    private function form(string $kind, ?CrmReference $record = null): Response
    {
        return response()->view('admin.crm.references.form', [
            'record' => $record, 'kind' => $kind, 'kinds' => CrmReference::KINDS,
            'universities' => $kind === 'campus' ? CrmReference::where('kind', 'university')->where('is_active', true)->orderBy('name')->get(['id', 'name']) : collect(),
            'statuses' => in_array($kind, CrmReference::STATUS_KINDS, true) ? CrmReference::where('kind', $kind)->where('is_active', true)->when($record, fn ($q) => $q->where('id', '!=', $record->id))->orderBy('name')->get(['id', 'name']) : collect(),
            'transitions' => $record ? DB::table('crm_status_transitions')->where('from_id', $record->id)->pluck('to_id')->all() : [],
            'levels' => CrmReference::where('kind', 'level')->where('is_active', true)->orderBy('id')->get(['id', 'code', 'name']),
            'applicationStatuses' => $kind === 'visa_status' ? CrmReference::where('kind', 'application_status')->where('is_active', true)->orderBy('name')->get(['id', 'name']) : collect(),
            'detailFields' => CrmReference::DETAIL_FIELDS[$kind] ?? [],
        ], 200, ['Cache-Control' => 'private, no-store']);
    }
}
