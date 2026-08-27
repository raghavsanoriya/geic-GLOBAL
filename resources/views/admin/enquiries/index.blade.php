@extends('admin.layout')

@section('title', 'Student enquiries')
@section('crumb', 'Student enquiries')
@section('backUrl', route('admin.dashboard'))
@section('backLabel', 'Back to dashboard')

@push('styles')
<style>
    .lead-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px}
    .lead-metric{display:flex;min-height:112px;align-items:center;gap:15px;padding:20px 22px;border-radius:var(--admin-radius-card);background:#fff}
    .lead-metric__icon{display:grid;width:48px;height:48px;place-items:center;flex:0 0 48px;border-radius:14px;background:var(--admin-primary-soft);color:var(--admin-primary)}
    .lead-metric__icon--fresh{background:#eafaf5;color:#13966f}.lead-metric__icon--today{background:#fff6e7;color:#d98500}
    .lead-metric__icon svg{width:23px;height:23px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}
    .lead-metric span,.lead-metric small{display:block;color:var(--admin-muted)}.lead-metric span{font-size:12px}.lead-metric strong{display:block;margin:3px 0;color:var(--admin-ink);font-size:25px;line-height:1}.lead-metric small{font-size:10px}
    .lead-table .panel__head{align-items:center}.lead-table .filters{grid-template-columns:minmax(220px,1fr) 190px auto auto}.lead-table td{vertical-align:top}
    @media(max-width:780px){.lead-metrics{grid-template-columns:1fr}.lead-table .filters{grid-template-columns:1fr}.lead-table .panel__head{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Lead management</span><h1>Student enquiries</h1></div>
        <p>Review every student request, filter the list, and move qualified leads into counselling.</p>
    </section>

    <section class="lead-metrics" aria-label="Enquiry overview">
        <article class="lead-metric"><span class="lead-metric__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 5h14v11H9l-4 4V5Z"/><path d="M8 10h8M8 13h5"/></svg></span><div><span>Total enquiries</span><strong>{{ $total }}</strong><small>All website forms</small></div></article>
        <article class="lead-metric"><span class="lead-metric__icon lead-metric__icon--fresh" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 18V9M10 18V5M16 18v-7M22 18V3"/></svg></span><div><span>Last 7 days</span><strong>{{ $week }}</strong><small>Recent student interest</small></div></article>
        <article class="lead-metric"><span class="lead-metric__icon lead-metric__icon--today" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg></span><div><span>Today</span><strong>{{ $today }}</strong><small>New enquiries received</small></div></article>
    </section>

    <section class="panel lead-table">
        <div class="panel__head"><div><h2>All student enquiries</h2><span>{{ $enquiries->total() }} matching record{{ $enquiries->total() === 1 ? '' : 's' }}</span></div>@can('enquiries.export')<a class="button button--quiet" href="{{ route('admin.enquiries.export', request()->query()) }}">Open export center <span aria-hidden="true">→</span></a>@endcan</div>
        <form class="filters" method="get" action="{{ route('admin.enquiries.index') }}">
            <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone or course" aria-label="Search enquiries">
            <select class="select" name="destination" aria-label="Filter by destination"><option value="">All destinations</option>@foreach($destinationOptions as $option)<option value="{{ $option }}" @selected(request('destination') === $option)>{{ $option }}</option>@endforeach</select>
            <button class="button" type="submit">Filter</button>
            @if(request()->filled('q') || request()->filled('destination'))<a class="button button--quiet" href="{{ route('admin.enquiries.index') }}">Clear</a>@endif
        </form>
        @if($enquiries->isEmpty())
            <div class="empty"><h3>No enquiries to show</h3><p>New public website enquiries will appear here.</p></div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Student</th><th>Destination</th><th>Study plan</th><th>Received</th></tr></thead><tbody>@foreach($enquiries as $enquiry)<tr><td><span class="student">{{ $enquiry->full_name }}</span><span class="sub">{{ $enquiry->email }} · {{ $enquiry->phone }}</span></td><td><span class="pill">{{ $enquiry->destination ?: 'Not specified' }}</span><span class="sub">{{ $enquiry->city }}</span></td><td><span class="student">{{ $enquiry->preferred_course ?: 'Course not provided' }}</span><span class="sub">{{ $enquiry->study_level }} · {{ $enquiry->preferred_intake }}</span></td><td><span class="student">{{ \Carbon\Carbon::parse($enquiry->created_at)->format('d M Y') }}</span><span class="sub">{{ \Carbon\Carbon::parse($enquiry->created_at)->format('h:i A') }}</span></td></tr>@endforeach</tbody></table></div>
            <div class="pagination">{{ $enquiries->onEachSide(1)->links() }}</div>
        @endif
    </section>
@endsection
