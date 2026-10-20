@extends('admin.layout')
@section('title','Marketing opt-outs | GEIC Indore')
@section('crumb','Marketing suppression list')
@section('content')
@include('admin.crm.validation-feedback')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Marketing · Recipient preferences</span><h1>Opt-outs &amp; suppressions</h1><p class="sub">Excluded from audience previews and checked again before delivery. Opt-outs are never automatically removed.</p></div><a class="button button--quiet" href="{{route('admin.crm.campaigns.index')}}">Campaigns</a></section>@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
<form class="panel crm-profile-form" method="post" action="{{route('admin.crm.campaigns.suppressions.store')}}">@csrf<h2>Exclude an address</h2><div class="crm-form-grid"><label>Channel<select class="input" name="channel"><option value="email">Email</option><option value="sms">SMS</option></select></label><label>Email or phone<input class="input" name="address" required maxlength="254"></label><label>Reason<input class="input" name="reason" required maxlength="160" placeholder="Recipient requested opt-out"></label></div><button class="button" type="submit">Suppress marketing address</button></form>
<section class="panel crm-student-list">@forelse($records as $record)<article class="crm-student-row"><strong>{{$record->address}}</strong><span>{{strtoupper($record->channel)}}</span><p>{{$record->reason}}</p><span class="pill">Suppressed</span></article>@empty<div class="empty"><h2>No suppressed addresses</h2><p>Recipient opt-outs and administrator exclusions appear here.</p></div>@endforelse</section>{{$records->links()}}</div>
@endsection
@include('admin.crm.students.styles')
