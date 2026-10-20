@extends('admin.layout')
@section('title','Campaigns | GEIC Indore')
@section('crumb','Marketing campaigns')
@section('content')
@if(\App\Support\CrmAccess::viewsAll(auth()->user()))<a class="button button--quiet" href="{{route('admin.crm.campaigns.suppressions')}}">Recipient opt-outs &amp; suppressions</a>@endif
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Marketing · Consent-based communication</span><h1>Campaigns &amp; reports</h1><p class="sub">Create email or SMS drafts, preview eligible recipients, schedule and inspect recipient-level delivery. Real sending is disabled until the provider and worker are enabled.</p></div><a class="button" href="{{route('admin.crm.campaigns.create')}}">Create campaign</a></section>
<section class="panel crm-student-list">@forelse($campaigns as $campaign)<article class="crm-student-row"><div><strong>{{$campaign->name}}</strong><p>{{strtoupper($campaign->channel)}} · {{$campaign->status}}</p></div><span>{{$campaign->scheduled_at?->setTimezone('Asia/Kolkata')->format('d M Y h:i A') ?? 'Not scheduled'}} IST</span><a class="button button--quiet" href="{{route('admin.crm.campaigns.show',$campaign->id)}}">Open / delivery report</a></article>@empty<div class="empty"><h2>No campaigns yet</h2><p>Create a draft before previewing or scheduling recipients.</p></div>@endforelse</section>{{$campaigns->links()}}</div>
@endsection
@include('admin.crm.students.styles')
