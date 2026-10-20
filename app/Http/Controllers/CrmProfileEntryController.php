<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmProfileEntryRequest;
use App\Models\CrmProfileEntry;
use App\Models\CrmStudent;
use App\Support\CrmAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CrmProfileEntryController extends Controller
{
    public function index(Request $request, int $student, string $kind): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $student = CrmStudent::visibleTo($request->user())->findOrFail($student);
        $entries = CrmProfileEntry::where('student_id', $student->id)->where('kind', $kind)->orderByDesc('id')->paginate(25);

        return $request->expectsJson() ? response()->json($entries, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.profile-entries.index', ['student' => $student, 'kind' => $kind, 'entries' => $entries, 'fields' => CrmProfileEntry::FIELDS[$kind], 'creationKey' => (string) Str::uuid()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function save(CrmProfileEntryRequest $request, int $student, string $kind, CrmAudit $audit, ?int $entry = null): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $record = DB::transaction(function () use ($request, $student, $kind, $entry, $audit, $data): CrmProfileEntry {
            $profile = CrmStudent::visibleTo($request->user())->whereKey($student)->lockForUpdate()->firstOrFail();
            if ($entry) {
                $record = CrmProfileEntry::where('student_id', $student)->where('kind', $kind)->whereKey($entry)->lockForUpdate()->firstOrFail();
                abort_unless($record->version === (int) $data['version'], 409, 'This profile entry changed. Reload before saving.');
                $record->fill(['details' => $data['details'], 'is_active' => $data['is_active']]);
                $record->version++;
                $record->save();
            } else {
                $record = CrmProfileEntry::where('creation_key', $data['creation_key'])->first();
                if ($record) {
                    abort_unless($record->student_id === $student && $record->kind === $kind && $record->created_by === $request->user()->id, 409);

                    return $record;
                }
                $record = CrmProfileEntry::create([...$data, 'student_id' => $student, 'kind' => $kind, 'created_by' => $request->user()->id]);
            }
            $audit->record($profile, 'profile_entry.'.($entry ? 'updated' : 'created'), $request->user(), [$kind, 'entry_id:'.$record->id]);

            return $record;
        });

        return $request->expectsJson() ? response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.profile-entries.index', [$student, $kind])->with('status', 'Profile entry saved.');
    }
}
