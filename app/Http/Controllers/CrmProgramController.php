<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmProgramRequest;
use App\Models\CrmProgram;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmProgramController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'country' => ['nullable', 'string', 'max:100'],
            'university_id' => ['nullable', 'integer', 'min:1'], 'campus_id' => ['nullable', 'integer', 'min:1'], 'qualification_id' => ['nullable', 'integer', 'min:1'],
            'study_level' => ['nullable', 'string', 'max:80'], 'subject' => ['nullable', 'string', 'max:100'],
            'ranking' => ['nullable', 'integer', 'between:1,100000'], 'ielts' => ['nullable', 'numeric', 'between:0,9'],
            'toefl' => ['nullable', 'integer', 'between:0,120'], 'pte' => ['nullable', 'integer', 'between:0,90'],
            'student' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'format' => ['nullable', Rule::in(['csv', 'print'])]]);
        $student = $request->filled('student') ? CrmStudent::visibleTo($request->user())->findOrFail($filters['student']) : null;
        $query = CrmProgram::with(['university:id,name', 'campus:id,name', 'qualification:id,name'])->where('is_active', true);
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }
        foreach (['country', 'university_id', 'campus_id', 'qualification_id', 'study_level', 'subject'] as $key) {
            if ($request->filled($key)) {
                $query->where($key, $filters[$key]);
            }
        }
        foreach (['ranking', 'ielts', 'toefl', 'pte'] as $key) {
            if ($request->filled($key)) {
                $query->whereNotNull($key)->where($key, '<=', $filters[$key]);
            }
        }
        if ($request->filled('format')) {
            Gate::authorize('crm.export');
            if ($filters['format'] === 'print') {
                abort_if((clone $query)->count() > 1000, 422, 'Narrow the catalogue to at most 1,000 programs for printing.');

                return response()->view('admin.crm.programs.print', ['programs' => $query->orderBy('name')->get()], 200, ['Cache-Control' => 'private, no-store']);
            }

            return response()->streamDownload(function () use ($query): void {
                $stream = fopen('php://output', 'wb');
                fputcsv($stream, ['Code', 'Program', 'University', 'Campus', 'Country', 'Study level', 'Subject', 'Ranking', 'IELTS', 'TOEFL', 'PTE'], ',', '"', '');
                foreach ($query->orderBy('id')->lazyById(200) as $program) {
                    $row = [$program->code, $program->name, $program->university?->name, $program->campus?->name, $program->country, $program->study_level, $program->subject, $program->ranking, $program->ielts, $program->toefl, $program->pte];
                    fputcsv($stream, array_map(fn ($value) => is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value, $row), ',', '"', '');
                }
                fclose($stream);
            }, 'geic-program-catalogue.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
        }
        $programs = $query->orderBy('name')->orderBy('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($programs, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.programs.index', [
            'programs' => $programs, 'student' => $student,
            'countries' => CrmProgram::where('is_active', true)->distinct()->orderBy('country')->pluck('country'),
            'references' => CrmReference::whereIn('kind', ['university', 'campus', 'qualification'])->where('is_active', true)->orderBy('name')->get(['id', 'name', 'kind']),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');

        return $this->form();
    }

    public function show(int $program): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');

        return $this->form(CrmProgram::findOrFail($program));
    }

    public function store(CrmProgramRequest $request): RedirectResponse|JsonResponse
    {
        return $this->save($request);
    }

    public function update(CrmProgramRequest $request, int $program): RedirectResponse|JsonResponse
    {
        return $this->save($request, $program);
    }

    private function save(CrmProgramRequest $request, ?int $id = null): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $record = DB::transaction(function () use ($request, $data, $id): CrmProgram {
            $record = $id ? CrmProgram::whereKey($id)->lockForUpdate()->firstOrFail() : new CrmProgram(['version' => 1]);
            if ($id) {
                abort_unless($record->version === (int) $data['version'], 409, 'This program has changed. Reload before saving.');
            }
            foreach (['university_id' => 'university', 'campus_id' => 'campus', 'qualification_id' => 'qualification'] as $field => $kind) {
                if (filled($data[$field] ?? null)) {
                    $reference = CrmReference::whereKey($data[$field])->where('kind', $kind)->first();
                    if (! $reference || (! $reference->is_active && (int) $record->$field !== $reference->id)
                        || ($kind === 'campus' && $reference->parent_id !== (int) $data['university_id'])) {
                        throw ValidationException::withMessages([$field => 'Choose a matching active '.$kind.'.']);
                    }
                }
            }
            $record->fill(Arr::except($data, ['version']));
            $fields = array_keys($record->getDirty());
            if ($id && $fields !== []) {
                $record->version++;
            }
            $record->save();
            if (! $id || $fields !== []) {
                DB::table('crm_program_events')->insert(['program_id' => $record->id, 'actor_id' => $request->user()->id, 'fields' => json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now()]);
            }

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.programs.show', $record->id)->with('status', 'Program saved.');
    }

    private function form(?CrmProgram $record = null): Response
    {
        return response()->view('admin.crm.programs.form', ['record' => $record,
            'references' => CrmReference::whereIn('kind', ['university', 'campus', 'qualification'])->orderBy('name')->get(),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }
}
