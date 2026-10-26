@extends('admin.layout')
@section('title','Role | GEIC')
@section('crumb','Reusable roles')
@section('backUrl',route('admin.roles.index'))
@section('backLabel','Back to reusable roles')
@section('content')
<section class="page-head"><div><span class="eyebrow">Access &amp; permissions</span><h1>{{$role->exists?$role->label:'Create reusable role'}}</h1><p>Changes synchronize the permission snapshot of every user assigned to this role.</p></div></section>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
<form class="panel account-card account-form" method="post" action="{{$role->exists?route('admin.roles.update',$role->id):route('admin.roles.store')}}">@csrf @if($role->exists)@method('PATCH')<input type="hidden" name="version" value="{{$role->version}}">@endif
@if($errors->any())<div class="error-summary" role="alert" tabindex="-1"><strong>Please review the highlighted information.</strong><ul>@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
<div class="account-form__grid">@unless($role->exists)<div class="field"><label>Role key<input class="input" name="key" required maxlength="40" pattern="[a-z][a-z0-9_]{2,39}" value="{{old('key')}}"></label><small>Permanent lowercase identifier, for example senior_counsellor.</small></div>@endunless<div class="field"><label>Role label<input class="input" name="label" required maxlength="120" value="{{old('label',$role->label)}}"></label></div><div class="field account-form__full"><label>Description<textarea class="input" name="description" required maxlength="500" rows="3">{{old('description',$role->description)}}</textarea></label></div></div>
<fieldset style="border:0;padding:0"><legend>Permissions</legend><div class="permission-grid">@foreach($permissions as $key=>$permission)<label class="permission-option"><input type="checkbox" name="permissions[]" value="{{$key}}" @checked(in_array($key,old('permissions',$role->permissions??[]),true))><span><strong>{{$permission['label']}}</strong><span>{{$permission['description']}}</span></span></label>@endforeach</div></fieldset>
<label class="access-switch"><span><strong>Role active</strong><span>Inactive roles cannot be selected for new assignments.</span></span><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked((bool)old('is_active',$role->is_active??true))></label><div class="account-actions"><a class="button button--quiet" href="{{route('admin.roles.index')}}">Cancel</a><button class="button" type="submit">Save role</button></div></form>
@endsection
