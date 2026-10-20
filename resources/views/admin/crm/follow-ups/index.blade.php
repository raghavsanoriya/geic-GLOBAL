@extends('admin.layout')
@section('title', 'Follow-ups | GEIC Indore')
@section('crumb', 'Indore follow-ups')
@section('content')
<div class="crm-workspace" data-follow-up-workspace>
    <section class="page-head"><div><span class="eyebrow">Indore · Student care</span><h1 tabindex="-1">Follow-ups</h1><p class="sub">View details, update a schedule and add remarks without leaving the list. All times are IST.</p></div>@if($student)<a class="button button--quiet" href="{{ route('admin.crm.students.show', $student->id) }}">Back to {{ $student->full_name }}</a>@endif</section>
    @if($student)@can('crm.manage')<div><a class="button" href="{{ route('admin.crm.follow-ups.create', $student->id) }}">Schedule follow-up</a></div>@endcan @endif
    <nav class="crm-stages" aria-label="Follow-up queues">
        @foreach(['today' => 'Today', 'new' => 'New', 'pending' => 'Pending', 'missed' => 'Missed', 'ended' => 'End', 'all' => 'All'] as $key => $label)
        <a class="button button--quiet" href="{{ route('admin.crm.follow-ups.index', array_filter(['queue' => $key, 'student' => $student?->id])) }}" @if($queue === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    @can('crm.export')<div class="crm-stages"><a class="button button--quiet" href="{{ route('admin.crm.follow-ups.index', array_filter(['queue'=>$queue,'student'=>$student?->id,'format'=>'csv'])) }}">Export CSV</a><a class="button button--quiet" href="{{ route('admin.crm.follow-ups.index', array_filter(['queue'=>$queue,'student'=>$student?->id,'format'=>'print'])) }}">Print / PDF</a></div>@endcan
    <p class="crm-help" role="status" aria-atomic="true" data-follow-up-list-status></p>
    <section class="panel crm-follow-up-list" aria-label="Follow-up list" data-follow-up-list>
    @if($followUps->isNotEmpty())
        <table class="crm-follow-up-table">
            <caption class="crm-visually-hidden">Follow-up schedules and assigned owners</caption>
            <thead><tr><th scope="col">Student / subject</th><th scope="col">Due (IST)</th><th scope="col">Channel</th><th scope="col">Status</th><th scope="col">Owner</th><th scope="col">Details</th></tr></thead>
            <tbody>@foreach($followUps as $item)
                <tr data-follow-up-row="{{ $item->id }}">
                    <td data-label="Student / subject"><strong>{{ $item->student->full_name }}</strong><span class="crm-help">{{ $item->subject }}</span><span class="crm-help">CRM-{{ $item->student_id }}</span></td>
                    <td data-label="Due (IST)"><time datetime="{{ $item->due_at->toIso8601String() }}">{{ $item->due_at->timezone('Asia/Kolkata')->format('d M Y') }}<br>{{ $item->due_at->timezone('Asia/Kolkata')->format('h:i A') }}</time></td>
                    <td data-label="Channel">{{ \App\Models\CrmFollowUp::CHANNELS[$item->channel] }}</td>
                    <td data-label="Status"><span class="pill">{{ \App\Models\CrmFollowUp::STATUSES[$item->status] }}</span>@if($item->status !== 'ended' && $item->due_at->isPast())<span class="crm-help">Overdue</span>@endif</td>
                    <td data-label="Owner">{{ $item->owner?->name ?: 'Owner unavailable' }}</td>
                    <td data-label="Details"><a class="button button--quiet" href="{{ route('admin.crm.follow-ups.show', $item->id) }}" data-follow-up-open data-follow-up-id="{{ $item->id }}" aria-label="View follow-up for {{ $item->student->full_name }}">View details</a></td>
                </tr>
            @endforeach</tbody>
        </table>
    @else
        <div class="empty"><h2>No follow-ups in this queue</h2><p>Open a student profile to schedule a follow-up. Only cases you can access appear here.</p></div>
    @endif
    {{ $followUps->links() }}
    </section>
    <dialog class="crm-follow-up-dialog" data-follow-up-dialog aria-labelledby="follow-up-popup-title">
        <header class="crm-dialog-header"><div><span class="eyebrow">Indore · Student care</span><h2 id="follow-up-popup-title" tabindex="-1">Follow-up details</h2></div><button class="button button--quiet" type="button" data-follow-up-close aria-label="Close follow-up details">Close</button></header>
        <div class="crm-dialog-body" data-follow-up-body></div>
    </dialog>
</div>
@endsection
@include('admin.crm.students.styles')
@push('styles')
<style>
.crm-follow-up-list{min-width:0;overflow:hidden}.crm-follow-up-table{width:100%;border-collapse:collapse;table-layout:fixed}.crm-follow-up-table th,.crm-follow-up-table td{padding:18px 14px;text-align:left;border-bottom:1px solid var(--admin-line);vertical-align:middle;overflow-wrap:anywhere;font-size:13px}.crm-follow-up-table th{font-size:12px;color:var(--admin-muted)}.crm-follow-up-table th:first-child{width:27%}.crm-follow-up-table th:last-child{width:130px}.crm-follow-up-table td:first-child strong,.crm-follow-up-table td .crm-help{display:block}.crm-follow-up-table .button{font-size:12px;white-space:normal}.crm-visually-hidden{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}.crm-follow-up-dialog{width:min(940px,calc(100% - 32px));max-width:none;max-height:calc(100dvh - 32px);padding:0;border:1px solid var(--admin-line);border-radius:20px;background:var(--admin-panel,#fff);color:var(--admin-ink);box-shadow:0 24px 80px #0003}.crm-follow-up-dialog::backdrop{background:#10203c99}.crm-dialog-header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:20px 24px;border-bottom:1px solid var(--admin-line)}.crm-dialog-header h2{font-size:22px;margin:6px 0 0}.crm-dialog-body{max-height:calc(100dvh - 150px);overflow:auto;padding:20px;overscroll-behavior:contain}.crm-dialog-body .crm-profile-form,.crm-dialog-body .crm-history{box-shadow:none}.crm-dialog-body [data-follow-up-details]{display:grid;gap:20px}.crm-follow-up-dialog:not([open]){display:none}
@media(max-width:700px){.crm-follow-up-table,.crm-follow-up-table tbody,.crm-follow-up-table tr,.crm-follow-up-table td{display:block;width:100%}.crm-follow-up-table thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)}.crm-follow-up-table tr{padding:16px;border-bottom:1px solid var(--admin-line)}.crm-follow-up-table td{padding:6px 0;border:0}.crm-follow-up-table td::before{content:attr(data-label);display:block;color:var(--admin-muted);font-size:11px;margin-bottom:3px}.crm-dialog-header{padding:16px}.crm-dialog-header h2{font-size:19px}.crm-dialog-body{padding:12px}.crm-follow-up-dialog{width:calc(100% - 16px);max-height:calc(100dvh - 16px);border-radius:16px}.crm-dialog-body .crm-profile-form{padding:12px}}
</style>
@endpush
@push('scripts')<script type="module" src="{{ asset('assets/transglobe/js/follow-up-list.js') }}?v=20261020-1"></script>@endpush
