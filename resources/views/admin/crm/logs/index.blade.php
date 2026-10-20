@extends('admin.layout')
@section('title','Activity logs | GEIC Indore')
@section('crumb','CRM activity logs')
@section('content')
<div class="crm-workspace">
@if(\App\Support\CrmAccess::viewsAll(auth()->user()))<a class="button button--quiet" href="{{route('admin.crm.logs.index',['scope'=>'organization'])}}">Organization audit trail</a>@endif
<section class="page-head"><div><span class="eyebrow">Indore · Recorded activity</span><h1>Activity logs</h1><p class="sub">Student operational actions within your access. Original values, private profile fields and credentials are not exposed.</p></div></section>
<form class="panel crm-profile-form" method="get" action="{{ route('admin.crm.logs.index') }}">
@if($student)<input type="hidden" name="student" value="{{ $student->id }}"><p>Student: <strong>{{ $student->full_name }}</strong></p>@endif
<div class="crm-form-grid"><label>Exact action<input class="input" name="action" maxlength="80" value="{{ request('action') }}" placeholder="For example: student.updated"></label><label>From date (India time)<input class="input" type="date" name="from" value="{{ request('from') }}"></label><label>Through date (India time)<input class="input" type="date" name="to" value="{{ request('to') }}"></label></div><button class="button" type="submit">Filter activity</button></form>
<section class="panel crm-student-list">@forelse($events as $event)<article class="crm-student-row"><div><strong>{{ $event->action }}</strong><p>{{ \Carbon\CarbonImmutable::parse($event->created_at,'UTC')->setTimezone('Asia/Kolkata')->format('d M Y · h:i A') }} IST</p></div><a href="{{ route('admin.crm.students.show',$event->student_id) }}">{{ $event->student_name }}</a><span>{{ $event->actor_name ?: 'System / former staff' }}</span><small>Event #{{ $event->id }}</small></article>@empty<div class="empty"><h2>No recorded activity</h2><p>Change the filters or open a student you can access.</p></div>@endforelse</section>{{ $events->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
