<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmContactRequest;
use App\Models\CrmContact;
use App\Support\CrmTableExport;
use App\Support\EnquiryPhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmContactController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'], 'city' => ['nullable', 'string', 'max:100'], 'contact_type' => ['nullable', 'string', 'max:80'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'format' => ['nullable', Rule::in(['csv', 'print'])]]);
        $query = CrmContact::visibleTo($request->user());
        if ($request->filled('q')) {
            $term = '%'.$filters['q'].'%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('company', 'like', $term)->orWhere('email', 'like', $term));
        }
        foreach (['country', 'state', 'city', 'contact_type'] as $key) {
            if ($request->filled($key)) {
                $query->where($key, $filters[$key]);
            }
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query->orderBy('name')->orderBy('id'), $filters['format'], 'Business contacts', 'geic-contacts.csv',
                ['Reference', 'Name', 'Company', 'Designation', 'Type', 'Email', 'Phone', 'City', 'State', 'Country', 'Status'],
                fn (CrmContact $contact): array => [$contact->id, $contact->name, $contact->company, $contact->designation, $contact->contact_type, $contact->email,
                    $contact->phone, $contact->city, $contact->state, $contact->country, $contact->is_active ? 'Active' : 'Inactive']);
        }
        $summary = $request->expectsJson() ? null : (clone $query)->toBase()->selectRaw('COUNT(*) as total, COUNT(CASE WHEN is_active = 1 THEN 1 END) as active, COUNT(DISTINCT NULLIF(company, ?)) as companies, COUNT(DISTINCT NULLIF(country, ?)) as countries', ['', ''])->first();
        $contacts = $query->orderBy('name')->orderBy('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($contacts, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.contacts.index', compact('contacts', 'summary'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.manage');

        return response()->view('admin.crm.contacts.form', ['record' => null, 'fields' => CrmContact::FIELDS], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function show(Request $request, int $contact): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $record = CrmContact::visibleTo($request->user())->findOrFail($contact);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.contacts.form', ['record' => $record, 'fields' => CrmContact::FIELDS], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(CrmContactRequest $request): RedirectResponse|JsonResponse
    {
        return $this->save($request);
    }

    public function update(CrmContactRequest $request, int $contact): RedirectResponse|JsonResponse
    {
        return $this->save($request, $contact);
    }

    private function save(CrmContactRequest $request, ?int $id = null): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $record = DB::transaction(function () use ($request, $data, $id): CrmContact {
            DB::table('users')->where('id', $request->user()->id)->lockForUpdate()->first();
            $record = $id ? CrmContact::visibleTo($request->user())->whereKey($id)->lockForUpdate()->firstOrFail() : null;
            if ($record) {
                abort_unless($record->version === (int) $data['version'], 409, 'This contact changed. Reload before saving.');
            } else {
                $existing = CrmContact::where('creation_key', $data['creation_key'])->first();
                if ($existing) {
                    abort_unless($existing->created_by === $request->user()->id, 409);

                    return $existing;
                }
            }
            $creating = $record === null;
            $record ??= new CrmContact(['creation_key' => $data['creation_key'], 'created_by' => $request->user()->id, 'version' => 1]);
            $record->fill(Arr::except($data, ['creation_key', 'version']));
            $record->email = filled($data['email'] ?? null) ? mb_strtolower(trim($data['email'])) : null;
            $record->phone = filled($data['phone'] ?? null) ? EnquiryPhoneNumber::normalize($data['phone']) : null;
            $fields = array_keys($record->getDirty());
            if (! $creating && $fields !== []) {
                $record->version++;
            }
            $record->save();
            if ($creating || $fields !== []) {
                DB::table('crm_contact_events')->insert(['contact_id' => $record->id, 'actor_id' => $request->user()->id, 'fields' => json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now()]);
            }

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.contacts.show', $record->id)->with('status', 'Business contact saved.');
    }
}
