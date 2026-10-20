@extends('admin.layout')
@section('title','Campaign reports | GEIC Indore')
@section('crumb','Marketing · Campaign reports')
@section('content')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Marketing intelligence</span><h1>Campaign reports</h1><p class="sub">Provider acceptance is not confirmed inbox delivery. Uncertain sends require reconciliation, not blind retry.</p></div><a class="button button--quiet" href="{{route('admin.crm.campaigns.index')}}">Manage campaigns</a></section>
@include('admin.crm.validation-feedback')
@include('admin.crm.summary-cards',['cards'=>[
    ['Filtered recipients',array_sum($counts),'M4 5h16v14H4z M8 9h8 M8 13h5'],
    ['Provider accepted',$counts['accepted']??0,'M5 12l4 4L19 6'],
    ['Awaiting processing',($counts['pending']??0)+($counts['queued']??0)+($counts['processing']??0),'M12 8v4l3 2 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0'],
    ['Requires attention',($counts['blocked']??0)+($counts['uncertain']??0),'M12 8v5 M12 16h.01 M12 3 2 21h20z']
]])
<form class="panel crm-profile-form" method="get" action="{{route('admin.crm.campaign-reports')}}"><div class="crm-form-grid">
<label>Campaign<select name="campaign" class="select"><option value="">All accessible campaigns</option>@foreach($campaignOptions as $campaign)<option value="{{$campaign->id}}" @selected((string)request('campaign')===(string)$campaign->id)>{{$campaign->name}} · #{{$campaign->id}}</option>@endforeach</select></label>
<label>Recipient status<select name="status" class="select"><option value="">All statuses</option>@foreach($states as $key=>$label)<option value="{{$key}}" @selected(request('status')===$key)>{{$label}}</option>@endforeach</select></label>
<label>Channel<select name="channel" class="select"><option value="">Email and SMS</option>@foreach(['email'=>'Email','sms'=>'SMS'] as $key=>$label)<option value="{{$key}}" @selected(request('channel')===$key)>{{$label}}</option>@endforeach</select></label>
<label>Attempted from (IST)<input class="input" type="date" name="from" value="{{request('from')}}"></label><label>Attempted through (IST)<input class="input" type="date" name="to" value="{{request('to')}}"></label></div>
<div class="rocket-toolbar"><div><button class="button">Apply filters</button><a class="button button--quiet" href="{{route('admin.crm.campaign-reports')}}">Reset</a></div>@can('crm.export')<div><button class="button button--quiet" name="format" value="csv">Export CSV</button><button class="button button--quiet" name="format" value="print">Print / PDF</button></div>@endcan</div></form>
<section class="panel crm-student-list"><header class="rocket-section-heading"><div><h2>Recipient activity</h2><p>{{number_format($recipients->total())}} matching recipients · times in India time</p></div></header>
@forelse($recipients as $recipient)<article class="crm-student-row"><div><strong>{{$recipient->recipient_name ?: 'Recipient'}}</strong><p>{{$recipient->address}}</p><span class="rocket-record-reference">RECIPIENT-{{$recipient->id}}</span></div><div><a href="{{route('admin.crm.campaigns.show',$recipient->campaign_id)}}">{{$recipient->campaign_name}}</a><p>{{strtoupper($recipient->channel)}}</p></div><div><span class="rocket-state {{in_array($recipient->status,['blocked','uncertain'],true)?'rocket-state--attention':''}}">{{$states[$recipient->status]??$recipient->status}}</span><p>{{$recipient->attempted_at?\Carbon\CarbonImmutable::parse($recipient->attempted_at,'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A').' IST':'Not attempted'}}</p><p class="crm-help">{{$recipient->error_code}}</p></div><div><span class="crm-help">Provider reference</span><p>{{$recipient->provider_reference ?: 'Not available'}}</p></div></article>
@empty<div class="rocket-empty"><h2>No matching recipient activity</h2><p>Scheduled campaign recipients appear here after the audience is confirmed. No messages are sent by viewing this page.</p></div>@endforelse</section>{{$recipients->links()}}</div>
@endsection
@include('admin.crm.students.styles')
@include('admin.crm.rocket-styles')
