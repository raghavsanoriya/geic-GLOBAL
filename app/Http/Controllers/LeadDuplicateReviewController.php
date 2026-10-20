<?php

namespace App\Http\Controllers;

use App\Support\CaptureCrmStudent;
use App\Support\LeadDuplicateReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadDuplicateReviewController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('enquiries.view');
        $request->validate([
            'status' => ['nullable', Rule::in(LeadDuplicateReview::STATUSES)],
            'q' => ['nullable', 'string', 'max:160'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'revision' => ['nullable', 'string', 'regex:/^[a-f0-9]{64}$/'],
        ]);
        $status = $request->input('status') ?: 'pending';
        $query = DB::table('lead_duplicate_reviews as r')
            ->join('counselling_enquiries as e', 'e.id', '=', 'r.enquiry_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.reviewed_by')
            ->where('r.status', $status);
        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->where(fn ($q) => $q->where('e.full_name', 'like', $term)
                ->orWhere('e.email', 'like', $term)->orWhere('e.phone', 'like', $term));
        }
        $reviews = $query->select('r.*', 'e.full_name', 'e.email', 'e.phone', 'e.source_page',
            'e.source_form', 'e.destination', 'e.preferred_course', 'e.created_at as submitted_at',
            'u.name as reviewer_name')->orderByDesc('r.id')->paginate(15)->appends($request->only(['status', 'q']));
        $matchIds = [];
        foreach ($reviews as $review) {
            $review->matched_contacts = json_decode($review->matches, true, 512, JSON_THROW_ON_ERROR);
            foreach ($review->matched_contacts as $match) {
                $matchIds[] = $match['enquiry_id'];
            }
        }

        $data = [
            'reviews' => $reviews,
            'status' => $status,
            'counts' => DB::table('lead_duplicate_reviews')->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'contacts' => DB::table('counselling_enquiries')->whereIn('id', array_unique($matchIds))
                ->get(['id', 'full_name', 'email', 'phone', 'destination', 'source_page', 'source_form', 'created_at'])->keyBy('id'),
        ];
        $data['revision'] = hash('sha256', json_encode([
            $status,
            (string) $request->input('q'),
            $reviews->toArray(),
            $data['counts']->all(),
            $data['contacts']->all(),
            Gate::allows('enquiries.review'),
        ], JSON_THROW_ON_ERROR));
        if ($request->expectsJson()) {
            $payload = [
                'revision' => $data['revision'],
                'unchanged' => hash_equals($data['revision'], (string) $request->input('revision', '')),
            ];
            if (! $payload['unchanged']) {
                $payload['html'] = view('admin.enquiries.duplicate-review-feed', $data)->render();
            }

            return response()->json($payload, 200, ['Cache-Control' => 'private, no-store', 'Vary' => 'Accept']);
        }

        return view('admin.enquiries.duplicate-review', $data);
    }

    public function update(Request $request, int $review, CaptureCrmStudent $crm): RedirectResponse
    {
        Gate::authorize('enquiries.review');
        $record = DB::table('lead_duplicate_reviews')->find($review);
        abort_unless($record, 404);
        $matchIds = array_column(json_decode($record->matches, true, 512, JSON_THROW_ON_ERROR), 'enquiry_id');
        $data = $request->validate([
            'decision' => ['required', Rule::in(['confirmed_duplicate', 'distinct'])],
            'linked_enquiry_id' => ['required_if:decision,confirmed_duplicate', 'nullable', 'integer', Rule::in($matchIds)],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);
        $updated = DB::transaction(function () use ($review, $data, $request, $crm, $record): int {
            $updated = DB::table('lead_duplicate_reviews')->where('id', $review)->where('status', 'pending')->update([
                'status' => $data['decision'],
                'linked_enquiry_id' => $data['decision'] === 'confirmed_duplicate' ? $data['linked_enquiry_id'] : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_note' => $data['review_note'] ?? null,
                'updated_at' => now(),
            ]);
            if ($updated) {
                $crm->capture((int) $record->enquiry_id, $request->user());
            }

            return $updated;
        });

        return back()->with('status', $updated
            ? 'Review saved. Both enquiries and their Foris delivery history have been preserved.'
            : 'This enquiry has already been reviewed. No changes were made.');
    }
}
