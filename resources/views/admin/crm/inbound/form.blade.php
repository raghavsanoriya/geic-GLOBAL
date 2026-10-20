@extends('admin.layout')
@section('title','Inbound endpoint | GEIC Indore')
@section('crumb','Inbound API configuration')
@section('content')
<div class="crm-workspace"><section class="page-head"><h1>{{$record?'Configure inbound endpoint':'Create inbound endpoint'}}</h1><a class="button button--quiet" href="{{route('admin.crm.inbound.index')}}">Back to endpoints</a></section>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif @if($errors->any())<div class="notice" role="alert"><ul>@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
@if(session('crm_inbound_secret'))<section class="panel crm-history"><h2>Copy the API key now</h2><p>Shown once. Store it in the calling server's protected environment, never in browser JavaScript.</p><code style="overflow-wrap:anywhere">{{session('crm_inbound_secret')}}</code></section>@endif
<form method="post" class="panel crm-profile-form" action="{{$record?route('admin.crm.inbound.update',$record->id):route('admin.crm.inbound.store')}}">@csrf @if($record)@method('PATCH')<input type="hidden" name="version" value="{{old('version',$record->version)}}">@endif
<fieldset><legend>Endpoint settings</legend><div class="crm-form-grid"><label>Name *<input class="input" name="name" maxlength="160" required value="{{old('name',$record?->name)}}"></label><label>Accept submissions<select class="select" name="is_active"><option value="0" @selected(!(bool)old('is_active',$record?->is_active??false))>Disabled</option><option value="1" @selected((bool)old('is_active',$record?->is_active??false))>Active</option></select></label><label>Duplicate policy<select class="select" name="duplicate_policy"><option value="review" @selected(old('duplicate_policy',$record?->duplicate_policy??'review')==='review')>Save for admin review</option><option value="reject" @selected(old('duplicate_policy',$record?->duplicate_policy??'review')==='reject')>Reject a matching contact</option></select><small class="crm-help">Applies only to this API. Website forms remain admin-review based.</small></label><label>Forward to Foris<select class="select" name="forward_to_foris"><option value="0" @selected(!(bool)old('forward_to_foris',$record?->forward_to_foris??false))>Disabled</option><option value="1" @selected((bool)old('forward_to_foris',$record?->forward_to_foris??false))>Enabled via existing queue</option></select><small class="crm-help">Never use this for importing Foris records.</small></label><label>API key expires after (IST)<input class="input" type="date" name="key_expires_at" value="{{old('key_expires_at',$record?->key_expires_at?->timezone('Asia/Kolkata')->format('Y-m-d'))}}"></label></div></fieldset>
<fieldset><legend>JSON field mappings</legend><p class="crm-help">Map each GEIC field to a top-level input key. Name and phone mappings are required. Unknown input fields are discarded.</p><div class="crm-form-grid">@foreach($fields as $key=>$label)<label>{{$label}}{{in_array($key,['full_name','phone'],true)?' *':''}}<input class="input" name="field_map[{{$key}}]" maxlength="60" pattern="[A-Za-z][A-Za-z0-9_]{0,59}" @required(in_array($key,['full_name','phone'],true)) value="{{old('field_map.'.$key,$record?->field_map[$key]??($record?'':$key))}}"></label>@endforeach</div></fieldset>
<fieldset><legend>Optional defaults</legend><div class="crm-form-grid">@foreach(['country'=>'Country','study_level'=>'Study level','intake'=>'Intake'] as $key=>$label)<label>{{$label}}<input class="input" name="defaults[{{$key}}]" maxlength="{{$key==='country'?80:50}}" value="{{old('defaults.'.$key,$record?->defaults[$key]??'')}}"></label>@endforeach</div></fieldset>
<fieldset><legend>Allowed browser origins</legend><p class="crm-help">Leave blank for server-to-server use. Exact HTTPS origins only, such as https://www.geic.in. Do not put API keys in browser forms.</p>@foreach([...old('allowed_origins',$record?->allowed_origins??[]),''] as $origin)<label>Origin<input class="input" name="allowed_origins[]" maxlength="255" value="{{$origin}}"></label>@endforeach</fieldset><button class="button" type="submit">Save endpoint</button></form>
@if($record)<section class="panel crm-history"><h2>Integration guide</h2><p>POST <code style="overflow-wrap:anywhere">{{route('api.inbound-forms.submissions',$record->form_key)}}</code></p><p>Headers: Content-Type: application/json; X-API-KEY: your saved server key; Idempotency-Key: a unique request token of 8–120 letters, digits, hyphens or underscores.</p><p>Reuse the same idempotency key only for a retry of identical form details. A changed payload returns 409. Success returns 201; a saved duplicate pending review returns 202.</p><p>Required details: full name and valid phone. Optional fields follow the mapping above. Save succeeds locally without waiting for Foris.</p><a class="button button--quiet" href="{{route('admin.crm.inbound.logs',$record->id)}}">Submission logs</a></section><form class="panel crm-profile-form" method="post" action="{{route('admin.crm.inbound.rotate',$record->id)}}">@csrf<input type="hidden" name="version" value="{{$record->version}}"><p class="crm-help">Rotating invalidates the current key. Update the calling server after copying the new key.</p><button class="button button--quiet" type="submit">Rotate API key</button></form>@endif</div>
@if($record)
<section class="panel crm-history">
    <h2>Server-side request example</h2>
    <p>Replace the API key, unique request token and phone placeholder on your server. Never publish your API key in a web page.</p>
    @php
        $examplePayload = [
            $record->field_map['full_name'] => 'Example Student',
            $record->field_map['phone'] => 'YOUR_VALID_PHONE',
        ];
    @endphp
    <pre style="max-width:100%;overflow:auto;white-space:pre-wrap;overflow-wrap:anywhere"><code>curl --request POST '{{route('api.inbound-forms.submissions',$record->form_key)}}'
  --header 'Content-Type: application/json'
  --header 'X-API-KEY: YOUR_SERVER_API_KEY'
  --header 'Idempotency-Key: YOUR_UNIQUE_REQUEST_TOKEN'
  --data '{{json_encode($examplePayload,JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)}}'</code></pre>
    <p class="crm-help">Shown as separate lines for readability; join them with spaces when running the command. Use the configured JSON keys above.</p>
    <dl>
        <dt>201 / 202</dt><dd>Saved locally / saved for duplicate review. A repeated identical request returns its original receipt.</dd>
        <dt>401 / 403</dt><dd>Check the key, expiry, endpoint activation or configured origin. Do not retry until corrected.</dd>
        <dt>409</dt><dd>The request token was already used with different data. Do not change a token merely to bypass this error.</dd>
        <dt>415 / 422</dt><dd>Use application/json or correct the fields listed in the response. Rejected fields do not create leads.</dd>
        <dt>429 / timeout / temporary server failure</dt><dd>Wait before retrying. Reuse the same request token and unchanged JSON body so a potentially saved lead is not registered twice.</dd>
    </dl>
</section>
@endif
@endsection
@include('admin.crm.students.styles')
