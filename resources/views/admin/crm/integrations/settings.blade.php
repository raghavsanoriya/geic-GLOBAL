@extends('admin.layout')
@section('title',$providers[$provider].' settings | GEIC Indore')
@section('crumb','Config · '.$providers[$provider])
@section('content')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Indore · Provider settings</span><h1>{{$providers[$provider]}}</h1><p class="sub">Credentials are encrypted and never shown after saving. Save disabled, verify the connection, then explicitly enable sending.</p></div></section>
<nav class="crm-stages">@foreach($providers as $key=>$label)<a class="button button--quiet" href="{{route('admin.crm.integrations.show',$key)}}" @if($provider===$key) aria-current="page" @endif>{{$label}}</a>@endforeach</nav>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif @if($errors->any())<div class="notice" role="alert"><ul>@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
<section class="panel crm-history"><h2>{{$setting?->ready()?'Verified and enabled':'Sending disabled'}}</h2><p>{{$setting?->last_test_result?:'No successful connection test has been recorded.'}}</p><p>Secret: {{filled($setting?->secret)?'Saved securely':'Not configured'}}</p>@if($setting?->verified_at)<time>Last verified {{$setting->verified_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')}} IST</time>@endif</section>
<form class="panel crm-profile-form" method="post" action="{{route('admin.crm.integrations.update',$provider)}}">@csrf @method('PUT')<input type="hidden" name="version" value="{{old('version',$setting?->version??0)}}">
<fieldset><legend>Connection configuration</legend><div class="crm-form-grid">
@if($provider==='email')
<label>SMTP host *<input class="input" name="configuration[host]" maxlength="253" required value="{{old('configuration.host',$setting?->configuration['host']??'')}}"><small class="crm-help">Public mail host only. TLS verification is always enabled.</small></label>
<label>Port<select class="select" name="configuration[port]">@foreach([587,465] as $port)<option value="{{$port}}" @selected((int)old('configuration.port',$setting?->configuration['port']??587)===$port)>{{$port}}</option>@endforeach</select></label>
<label>Encryption<select class="select" name="configuration[scheme]"><option value="smtp" @selected(old('configuration.scheme',$setting?->configuration['scheme']??'smtp')==='smtp')>STARTTLS (587)</option><option value="smtps" @selected(old('configuration.scheme',$setting?->configuration['scheme']??'smtp')==='smtps')>Implicit TLS (465)</option></select></label>
<label>SMTP username *<input class="input" name="configuration[username]" maxlength="255" required autocomplete="off" value="{{old('configuration.username',$setting?->configuration['username']??'')}}"></label>
<label>Sender email *<input class="input" type="email" name="configuration[from_address]" maxlength="160" required value="{{old('configuration.from_address',$setting?->configuration['from_address']??'')}}"></label>
<label>Sender name *<input class="input" name="configuration[from_name]" maxlength="120" required value="{{old('configuration.from_name',$setting?->configuration['from_name']??'GEIC Indore')}}"></label>
@else
<label>OneSignal App ID *<input class="input" name="configuration[app_id]" maxlength="36" required value="{{old('configuration.app_id',$setting?->configuration['app_id']??'')}}"></label>
@endif
<label>{{$provider==='email'?'SMTP password':'OneSignal App API key'}}<input class="input" type="password" name="secret" maxlength="4096" autocomplete="new-password"><small class="crm-help">Leave blank to keep the saved credential. Values are never repopulated.</small></label>
<label><span><input type="checkbox" name="clear_secret" value="1"> Remove the saved credential</span></label>
<label>Sending<select class="select" name="is_enabled"><option value="0" @selected(!(bool)old('is_enabled',$setting?->is_enabled??false))>Disabled</option><option value="1" @selected((bool)old('is_enabled',$setting?->is_enabled??false))>Enabled after successful test</option></select></label>
</div></fieldset><button class="button" type="submit">Save settings</button></form>
@if($setting)<form class="panel crm-profile-form" method="post" action="{{route('admin.crm.integrations.test',$provider)}}">@csrf<input type="hidden" name="version" value="{{$setting->version}}"><p class="crm-help">Tests only the saved configuration. SMTP authenticates without sending mail. OneSignal checks App ID access without sending push notifications.</p><button class="button button--quiet" type="submit">Test saved connection</button></form>@endif
</div>
@endsection
@include('admin.crm.students.styles')
