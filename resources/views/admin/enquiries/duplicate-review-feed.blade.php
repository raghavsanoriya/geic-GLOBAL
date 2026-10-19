<div data-review-feed data-revision="{{ $revision }}">
    <div class="review-counts">
        @foreach(['pending' => 'Pending review', 'confirmed_duplicate' => 'Confirmed duplicate', 'distinct' => 'Kept separate'] as $key => $label)
            <a class="panel review-count" href="{{ route('admin.duplicates.index', ['status' => $key]) }}" @if($status === $key) aria-current="page" @endif><strong>{{ $counts[$key] ?? 0 }}</strong><span>{{ $label }}</span></a>
        @endforeach
    </div>
    <div class="review-list">
    @forelse($reviews as $review)
        <article class="panel review-card" aria-labelledby="review-title-{{ $review->id }}">
            <header class="review-head"><div><span class="eyebrow">GEIC-{{ $review->enquiry_id }} · {{ \Carbon\Carbon::parse($review->submitted_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</span><h2 id="review-title-{{ $review->id }}">{{ $review->full_name }}</h2><p>{{ $review->email }}<br>{{ $review->phone }}</p></div><span class="pill">{{ ['pending' => 'Needs review', 'confirmed_duplicate' => 'Confirmed duplicate', 'distinct' => 'Kept separate'][$review->status] }}</span></header>
            <p class="review-source">{{ $review->source_form ?: 'Website enquiry' }} · {{ $review->source_page }}<br>{{ $review->destination }}{{ $review->preferred_course ? ' · '.$review->preferred_course : '' }}</p>
            <h3>Matching earlier enquiries</h3>
            <div class="review-matches">
            @foreach($review->matched_contacts as $match)
                @if($contact = $contacts->get($match['enquiry_id']))
                <section class="review-match">
                    <div><strong>GEIC-{{ $contact->id }} · {{ $contact->full_name }}</strong><p>{{ $contact->email }}<br>{{ $contact->phone }}</p><p>{{ $contact->source_form ?: 'Website enquiry' }} · {{ $contact->source_page }}</p></div>
                    <span class="pill">Same {{ implode(' and ', $match['reasons']) }}</span>
                </section>
                @else <p>GEIC-{{ $match['enquiry_id'] }} is no longer available. No record has been merged.</p>
                @endif
            @endforeach
            </div>
            @if($review->status === 'pending')
                @can('enquiries.review')
                <form class="review-decision" method="post" action="{{ route('admin.duplicates.update', $review->id) }}">
                    @csrf @method('PATCH')
                    <div class="review-fields">
                        <label>Link to this earlier enquiry if confirmed<select class="select" name="linked_enquiry_id"><option value="">Select a matching enquiry</option>@foreach($review->matched_contacts as $match)@if($contact = $contacts->get($match['enquiry_id']))<option value="{{ $contact->id }}">GEIC-{{ $contact->id }} · {{ $contact->full_name }}</option>@endif @endforeach</select></label>
                        <label>Review note (optional)<input class="input" name="review_note" maxlength="500" placeholder="Reason for your decision"></label>
                    </div>
                    <div class="review-actions"><button class="button" name="decision" value="confirmed_duplicate" type="submit">Confirm duplicate</button><button class="button button--quiet" name="decision" value="distinct" type="submit">Keep as separate enquiry</button></div>
                    <p class="review-footnote">Both options keep all submitted details. Confirming links the records; it does not delete, merge, or resend them.</p>
                </form>
                @else <p class="review-footnote">An administrator with duplicate-review permission can make the decision.</p>
                @endcan
            @else
                <footer class="review-footnote">Reviewed by {{ $review->reviewer_name ?: 'Former administrator' }} · {{ \Carbon\Carbon::parse($review->reviewed_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST @if($review->linked_enquiry_id)· Linked to GEIC-{{ $review->linked_enquiry_id }}@endif @if($review->review_note)<p>{{ $review->review_note }}</p>@endif</footer>
            @endif
        </article>
    @empty
        <div class="panel empty"><h2>No {{ $status === 'pending' ? 'pending duplicates' : 'reviews in this queue' }}</h2><p>New submissions with matching contact details will appear here.</p></div>
    @endforelse
    </div>
    <div class="pagination">{{ $reviews->onEachSide(1)->links() }}</div>
</div>
