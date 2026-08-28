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
    .lead-table .panel__head{align-items:center}.lead-table .filters{display:grid;grid-template-columns:minmax(220px,1.5fr) repeat(3,minmax(150px,1fr));gap:10px}.lead-table .filters .button{min-height:42px}.lead-table td{vertical-align:top}.lead-origin{display:inline-flex;margin-top:6px;padding:4px 7px;border-radius:999px;background:#f4f6fa;color:#6d7990;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.04em}.lead-origin--wordpress{background:#fff0f1;color:#bd1724}.lead-row{cursor:pointer}.lead-row:hover{background:#fff8f8}.lead-path{display:block;max-width:210px;overflow:hidden;color:#6e7d96;font-size:10px;text-overflow:ellipsis;white-space:nowrap}.lead-pagination{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;padding:20px}.lead-pagination a,.lead-pagination span{display:inline-flex;min-width:34px;height:34px;align-items:center;justify-content:center;padding:0 9px;border:1px solid #dfe6f0;border-radius:8px;color:#52627d;font-size:11px}.lead-pagination .active{border-color:var(--admin-primary);background:var(--admin-primary);color:#fff}.lead-dialog{width:min(760px,calc(100% - 28px));padding:0;border:0;border-radius:18px;box-shadow:0 25px 80px rgba(14,33,69,.25)}.lead-dialog::backdrop{background:rgba(14,33,69,.62)}.lead-dialog__head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #e5eaf2}.lead-dialog__head h2{margin:0;color:var(--admin-ink);font-size:18px}.lead-dialog__close{border:0;background:transparent;color:#66758e;font-size:24px;cursor:pointer}.lead-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding:22px}.lead-detail{padding:12px;border-radius:10px;background:#f7f9fc}.lead-detail small{display:block;color:#8a98ae;font-size:9px;text-transform:uppercase}.lead-detail strong{display:block;margin-top:4px;color:#34445f;font-size:12px;word-break:break-word}@media(max-width:780px){.lead-metrics{grid-template-columns:1fr}.lead-table .filters{grid-template-columns:1fr}.lead-table .panel__head{align-items:flex-start;flex-direction:column}.lead-detail-grid{grid-template-columns:1fr}}
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
            <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone, course or path" aria-label="Search enquiries">
            <select class="select" name="destination" aria-label="Filter by destination"><option value="">All destinations</option>@foreach($destinationOptions as $option)<option value="{{ $option }}" @selected(request('destination') === $option)>{{ $option }}</option>@endforeach</select>
            <select class="select" name="source" aria-label="Filter by lead source"><option value="">All lead sources</option>@foreach($sourceOptions as $option)<option value="{{ $option }}" @selected(request('source') === $option)>{{ $option === 'wordpress' ? 'WordPress history' : 'New website' }}</option>@endforeach</select>
            <select class="select" name="source_page" aria-label="Filter by exact source page"><option value="">All source pages</option>@foreach($sourcePageOptions as $option)<option value="{{ $option }}" @selected(request('source_page') === $option)>{{ $option }}</option>@endforeach</select>
            <select class="select" name="source_form" aria-label="Filter by source form"><option value="">All forms</option>@foreach($formOptions as $option)<option value="{{ $option }}" @selected(request('source_form') === $option)>{{ $option }}</option>@endforeach</select>
            <select class="select" name="utm_campaign" aria-label="Filter by campaign"><option value="">All campaigns</option>@foreach($campaignOptions as $option)<option value="{{ $option }}" @selected(request('utm_campaign') === $option)>{{ $option }}</option>@endforeach</select>
            <input class="input" type="date" name="from" value="{{ request('from') }}" aria-label="Leads from date"><input class="input" type="date" name="to" value="{{ request('to') }}" aria-label="Leads to date">
            <button class="button" type="submit">Filter</button>
            @if(request()->query())<a class="button button--quiet" href="{{ route('admin.enquiries.index') }}">Clear</a>@endif
        </form>
        @if($enquiries->isEmpty())
            <div class="empty"><h3>No enquiries to show</h3><p>New public website enquiries will appear here.</p></div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Student</th><th>Destination</th><th>Study plan</th><th>Source / exact path</th><th>Received</th></tr></thead><tbody>@foreach($enquiries as $enquiry)<tr class="lead-row" data-lead='@json($enquiry)'><td><span class="student">{{ $enquiry->full_name }}</span><span class="sub">{{ $enquiry->email }} · {{ $enquiry->phone }}</span></td><td><span class="pill">{{ $enquiry->destination ?: 'Not specified' }}</span><span class="sub">{{ $enquiry->city }}</span></td><td><span class="student">{{ $enquiry->preferred_course ?: 'Course not provided' }}</span><span class="sub">{{ $enquiry->study_level }} · {{ $enquiry->preferred_intake }}</span></td><td><span class="lead-origin {{ $enquiry->source === 'wordpress' ? 'lead-origin--wordpress' : '' }}">{{ $enquiry->source === 'wordpress' ? 'WordPress history' : 'New website' }}</span><span class="lead-path" title="{{ $enquiry->source_page }}">{{ $enquiry->source_page ?: 'Path not captured' }}</span><span class="sub">{{ $enquiry->source_form ?: 'Form not captured' }}</span></td><td><span class="student">{{ \Carbon\Carbon::parse($enquiry->created_at)->format('d M Y') }}</span><span class="sub">{{ \Carbon\Carbon::parse($enquiry->created_at)->format('h:i A') }}</span></td></tr>@endforeach</tbody></table></div>
            <div class="lead-pagination">@if($enquiries->onFirstPage())<span aria-disabled="true">‹</span>@else<a href="{{ $enquiries->previousPageUrl() }}">‹</a>@endif @foreach($enquiries->getUrlRange(max(1,$enquiries->currentPage()-2), min($enquiries->lastPage(),$enquiries->currentPage()+2)) as $page=>$url)<a class="{{ $page === $enquiries->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>@endforeach @if($enquiries->hasMorePages())<a href="{{ $enquiries->nextPageUrl() }}">›</a>@else<span aria-disabled="true">›</span>@endif</div>
        @endif
    </section>

    <dialog class="lead-dialog" data-lead-dialog><div class="lead-dialog__head"><h2 data-lead-title>Lead details</h2><button class="lead-dialog__close" type="button" data-close-lead aria-label="Close lead details">×</button></div><div class="lead-detail-grid" data-lead-details></div></dialog>
    <script>(()=>{const d=document.querySelector('[data-lead-dialog]'),box=document.querySelector('[data-lead-details]');if(!d||!box)return;document.querySelectorAll('[data-lead]').forEach(row=>row.addEventListener('click',()=>{const lead=JSON.parse(row.dataset.lead);box.innerHTML=Object.entries(lead).map(([k,v])=>`<div class="lead-detail"><small>${k.replaceAll('_',' ')}</small><strong>${v??'Not provided'}</strong></div>`).join('');d.showModal()}));document.querySelector('[data-close-lead]')?.addEventListener('click',()=>d.close());d.addEventListener('click',e=>{if(e.target===d)d.close()})})();</script>
@endsection
