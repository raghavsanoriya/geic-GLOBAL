@extends('admin.layout')
@section('title','CRM dashboard | GEIC Indore')
@section('crumb','Indore CRM dashboard')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Operational overview</span><h1>Your CRM, at a glance</h1><p class="sub">Accessible profiles, original submissions and child records are separate counts.</p></div><a class="button button--quiet" href="{{route('admin.crm.reports.index')}}">Detailed reports</a></section>
    <section class="rocket-summary-grid" aria-label="Workspace totals">
        @foreach([['count'=>$profiles,'label'=>'Student profiles','detail'=>$active.' active profiles','icon'=>'M5 21v-3a7 7 0 0 1 14 0v3M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z'],['count'=>$submissions,'label'=>'Original submissions','detail'=>'Linked to these profiles','icon'=>'M9 4H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3M9 2h6v4H9zM8 11h8m-8 4h6'],['count'=>$today,'label'=>'Follow-ups today','detail'=>'India-time calendar day','icon'=>'M8 2v4m8-4v4M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Zm3 10h2m4 0h2'],['count'=>$missed,'label'=>'Overdue follow-ups','detail'=>'Outstanding as of now','icon'=>'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0ZM12 6v6l4 2']] as $metric)
            <article class="rocket-summary-card"><span class="rocket-summary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{$metric['icon']}}"/></svg></span><div><strong>{{number_format($metric['count'])}}</strong><p>{{$metric['label']}}</p><small>{{$metric['detail']}}</small></div></article>
        @endforeach
    </section>
    <div class="crm-dashboard-grid">
        <section class="panel crm-profile-form"><h2>Student pipeline</h2><p class="sub">Current-stage distribution, not a cohort conversion rate.</p>
            @php($pipelineMax=max(1,max($pipeline)))
            <div class="crm-pipeline-bars">
                @foreach($pipeline as $stage=>$count)
                    <a href="{{route('admin.crm.students.index',['stage'=>$stage])}}" class="crm-pipeline-row">
                        <span>{{$stages[$stage]}}</span><strong>{{number_format($count)}}</strong>
                        <svg viewBox="0 0 300 12" preserveAspectRatio="none" aria-hidden="true"><rect width="300" height="12" rx="6" fill="#edf2fb"/><rect width="{{round(300*$count/$pipelineMax,2)}}" height="12" rx="6" fill="currentColor"/></svg>
                    </a>
                @endforeach
            </div>
        </section>
        <section class="panel crm-profile-form">
            <div class="rocket-section-heading"><div><h2>Lead breakdown</h2><p>Every count stays within your accessible cases.</p></div></div>
            <form method="get" class="crm-dashboard-breakdown-filter"><label>Group profiles by<select class="select" name="breakdown">@foreach($breakdownOptions as $key=>$label)<option value="{{$key}}" @selected($breakdown['dimension']===$key)>{{$label}}</option>@endforeach</select></label><button class="button button--quiet" type="submit">Update breakdown</button></form>
            <div class="crm-dashboard-table-wrap" role="region" aria-label="{{$breakdown['label']}} breakdown" tabindex="0">
                <table class="crm-dashboard-table"><thead><tr><th scope="col">{{$breakdown['label']}}</th><th scope="col">Profiles</th></tr></thead><tbody>
                    @forelse($breakdown['rows'] as $row)<tr><td>{{$row['label']}}</td><td><a class="crm-dashboard-count" href="{{$row['url']}}" aria-label="{{$row['count']}} profiles · {{$row['label']}}">{{number_format($row['count'])}} <span aria-hidden="true">↗</span></a></td></tr>
                    @empty<tr><td colspan="2">No accessible profiles yet.</td></tr>@endforelse
                </tbody></table>
            </div>
            @if($breakdown['other_count'])<p class="sub">{{number_format($breakdown['other_count'])}} profiles have additional values beyond the top 200 groups. <a href="{{route('admin.crm.students.index')}}">Open all accessible profiles</a></p>@endif
        </section>
    </div>
    <section class="panel crm-profile-form"><h2>Action queues</h2><div class="crm-form-grid">
        <a class="button button--quiet" href="{{route('admin.crm.follow-ups.index',['queue'=>'today'])}}">{{$today}} follow-ups today</a>
        <a class="button button--quiet" href="{{route('admin.crm.follow-ups.index',['queue'=>'missed'])}}">{{$missed}} missed follow-ups</a>
        <a class="button button--quiet" href="{{route('admin.crm.applications.index')}}">{{$applications}} applications</a>
        <a class="button button--quiet" href="{{route('admin.crm.visas.index')}}">{{$visas}} visa cases</a>
        <a class="button button--quiet" href="{{route('admin.crm.tasks.index')}}">{{$tasks}} open tasks</a>
        @if($unassigned!==null)<a class="button button--quiet" href="{{route('admin.crm.students.index',['unassigned'=>'1'])}}">{{$unassigned}} unassigned cases</a>
            <a class="button button--quiet" href="{{route('admin.crm.students.index',['unassigned_counsellor'=>'1'])}}">{{$unassigned_counsellor}} without counsellor</a>
            <a class="button button--quiet" href="{{route('admin.crm.students.index',['unassigned_re'=>'1'])}}">{{$unassigned_re}} without RE</a>@endif
    </div></section>
    <section class="panel crm-profile-form"><h2>Lead quality breakdown</h2><div class="crm-form-grid">@forelse($lead_statuses as $status=>$count)<a href="{{route('admin.crm.students.index',['lead_status'=>$status])}}">{{$statuses[$status]??$status}} · {{$count}}</a>@empty<p>No accessible profiles yet.</p>@endforelse</div></section>
    <section class="panel crm-student-list"><div class="crm-profile-form"><h2>Next follow-ups</h2><p class="sub">Oldest outstanding first. India time.</p></div>@forelse($upcoming as $followup)<article class="crm-student-row"><div><strong>{{$followup->subject}}</strong><p>{{$followup->student->full_name}}</p></div><span>{{$followup->due_at->setTimezone('Asia/Kolkata')->format('d M Y h:i A')}} IST</span><span>{{$followup->owner?->name??'Unassigned'}}</span><a class="button button--quiet" href="{{route('admin.crm.follow-ups.show',$followup->id)}}">Open details</a></article>@empty<div class="empty"><h2>No outstanding follow-ups</h2></div>@endforelse</section>
</div>
@endsection
@include('admin.crm.students.styles')
@include('admin.crm.rocket-styles')
@push('styles')
<style>
    .crm-dashboard-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:24px;align-items:start}
    .crm-dashboard-grid>.panel{margin:0;min-width:0}.crm-dashboard-grid .rocket-section-heading{padding:0 0 16px}
    .rocket-summary-card small{display:block;margin-top:6px;color:var(--admin-muted);font-size:12px}
    .crm-pipeline-bars{display:grid;gap:20px;margin-top:24px}
    .crm-pipeline-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px 20px;min-height:44px;color:var(--rocket-blue)}
    .crm-pipeline-row:nth-child(2){color:var(--rocket-violet)}.crm-pipeline-row:nth-child(3){color:var(--rocket-green)}.crm-pipeline-row:nth-child(4){color:var(--rocket-amber)}
    .crm-pipeline-row span{color:var(--admin-ink);font-size:14px;overflow-wrap:anywhere}.crm-pipeline-row strong{font-size:18px;font-variant-numeric:tabular-nums}.crm-pipeline-row svg{grid-column:1/-1;width:100%;height:12px}
    .crm-pipeline-row:focus-visible,.crm-dashboard-count:focus-visible{outline:3px solid var(--admin-primary);outline-offset:4px}
    .crm-dashboard-breakdown-filter{display:flex;align-items:end;gap:12px;flex-wrap:wrap}.crm-dashboard-breakdown-filter label{display:grid;gap:8px;flex:1;min-width:140px}
    .crm-dashboard-table-wrap{max-height:380px;overflow:auto;margin-top:20px;border:1px solid var(--admin-line);border-radius:14px}
    .crm-dashboard-table{min-width:0;table-layout:fixed}.crm-dashboard-table th:first-child{width:70%}.crm-dashboard-table td{font-size:13px;overflow-wrap:anywhere}
    .crm-dashboard-count{display:inline-flex;align-items:center;gap:10px;min-height:44px;color:var(--rocket-blue);font-weight:700}.crm-dashboard-table td:last-child{text-align:right}
    @media(max-width:1100px){.crm-dashboard-grid{grid-template-columns:minmax(0,1fr)}}
</style>
@endpush
