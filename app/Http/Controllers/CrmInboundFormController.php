<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmInboundFormRequest;
use App\Models\CrmInboundForm;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CrmInboundFormController extends Controller
{
    private function authorizeForms(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.inbound.manage');
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizeForms();
        $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000']]);
        $forms = CrmInboundForm::orderByDesc('id')->paginate(20);
        if ($request->expectsJson()) {
            return response()->json($forms, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.inbound.index', compact('forms'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        $this->authorizeForms();

        return $this->form();
    }

    public function show(Request $request, int $form): Response|JsonResponse
    {
        $this->authorizeForms();
        $record = CrmInboundForm::findOrFail($form);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return $this->form($record);
    }

    public function store(CrmInboundFormRequest $request): RedirectResponse|JsonResponse
    {
        return $this->save($request);
    }

    public function update(CrmInboundFormRequest $request, int $form): RedirectResponse|JsonResponse
    {
        return $this->save($request, $form);
    }

    private function save(CrmInboundFormRequest $request, ?int $id = null): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $secret = $id ? null : Str::random(64);
        $record = DB::transaction(function () use ($request, $data, $id, $secret): CrmInboundForm {
            $record = $id ? CrmInboundForm::whereKey($id)->lockForUpdate()->firstOrFail() : new CrmInboundForm(['form_key' => Str::random(24), 'api_key_hash' => hash('sha256', $secret), 'created_by' => $request->user()->id, 'version' => 1]);
            if ($id) {
                abort_unless($record->version === (int) $data['version'], 409, 'This endpoint changed. Reload before saving.');
                $record->version++;
            }
            $record->fill([...Arr::except($data, ['version']), 'allowed_origins' => $data['allowed_origins'] ?? [], 'defaults' => $data['defaults'] ?? []]);
            if (filled($data['key_expires_at'] ?? null)) {
                $record->key_expires_at = CarbonImmutable::parse($data['key_expires_at'], 'Asia/Kolkata')->endOfDay()->utc();
            }
            $record->save();
            $this->event($record, $request->user()->id, $id ? 'form.updated' : 'form.created');

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, ...($secret ? ['api_key' => $secret] : [])], 200, ['Cache-Control' => 'private, no-store']);
        }
        $redirect = to_route('admin.crm.inbound.show', $record->id)->with('status', 'Endpoint configuration saved.');

        return $secret ? $redirect->with('crm_inbound_secret', $secret) : $redirect;
    }

    public function rotate(Request $request, int $form): RedirectResponse|JsonResponse
    {
        $this->authorizeForms();
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $secret = Str::random(64);
        $record = DB::transaction(function () use ($request, $form, $data, $secret): CrmInboundForm {
            $record = CrmInboundForm::whereKey($form)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === (int) $data['version'], 409, 'This endpoint changed. Reload before rotating.');
            $record->api_key_hash = hash('sha256', $secret);
            $record->version++;
            $record->save();
            $this->event($record, $request->user()->id, 'key.rotated');

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, 'api_key' => $secret], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.inbound.show', $record->id)->with('crm_inbound_secret', $secret)->with('status', 'API key rotated. The previous key no longer works.');
    }

    public function logs(Request $request, int $form): Response|JsonResponse
    {
        $this->authorizeForms();
        $filters = $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'view' => ['nullable', 'in:accepted,attempts']]);
        $record = CrmInboundForm::findOrFail($form);
        $attempts = ($filters['view'] ?? 'accepted') === 'attempts';
        $logs = DB::table($attempts ? 'crm_inbound_attempts' : 'crm_inbound_submissions')->where('form_id', $form)
            ->when($attempts, fn ($query) => $query->where('created_at', '>=', now()->subDays(30)))->orderByDesc('id')
            ->paginate(30, $attempts ? ['id', 'response_status', 'outcome', 'invalid_fields', 'created_at'] : ['id', 'enquiry_id', 'response_status', 'created_at'])->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($logs, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.inbound.logs', ['record' => $record, 'logs' => $logs, 'attempts' => $attempts], 200, ['Cache-Control' => 'private, no-store']);
    }

    private function form(?CrmInboundForm $record = null): Response
    {
        return response()->view('admin.crm.inbound.form', ['record' => $record, 'fields' => CrmInboundForm::FIELDS], 200, ['Cache-Control' => 'private, no-store']);
    }

    private function event(CrmInboundForm $form, int $actor, string $action): void
    {
        DB::table('crm_inbound_events')->insert(['form_id' => $form->id, 'actor_id' => $actor, 'action' => $action, 'created_at' => now()]);
    }
}
