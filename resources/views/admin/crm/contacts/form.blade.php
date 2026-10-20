@extends('admin.layout')
@section('title','Business contact | GEIC Indore')
@section('crumb','Business contact')
@section('content')
<div class="crm-workspace"><section class="page-head"><h1>{{ $record?'Contact details':'Add business contact' }}</h1><a class="button button--quiet" href="{{ route('admin.crm.contacts.index') }}">Back to contacts</a></section>
@if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif @if($errors->any())<div class="notice" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="rocket-detail-layout">
<x-admin.rocket-profile-card :name="$record?->name ?: 'New business contact'" :subtitle="$record?->company ?: 'GEIC partner directory'" :reference="$record ? 'CONTACT-'.$record->id : 'INDORE'" :status="$record ? ($record->is_active ? 'Active contact' : 'Inactive contact') : 'New record'"
    :details="['Contact type' => $record?->contact_type, 'Email' => $record?->email, 'Phone' => $record?->phone, 'Location' => collect([$record?->city, $record?->country])->filter()->join(', ')]"
    :links="['All contacts' => route('admin.crm.contacts.index')]" />
<form class="panel crm-profile-form" method="post" action="{{ $record?route('admin.crm.contacts.update',$record->id):route('admin.crm.contacts.store') }}">@csrf @if($record)@method('PATCH')<input type="hidden" name="version" value="{{ old('version',$record->version) }}">@else<input type="hidden" name="creation_key" value="{{ old('creation_key',(string)\Illuminate\Support\Str::uuid()) }}">@endif
<fieldset @disabled(!auth()->user()->can('crm.manage'))><legend>Business contact information</legend><div class="crm-form-grid">@foreach($fields as $key=>[$label,$maximum])<label>{{ $label }}{{ in_array($key,['name','contact_type'],true)?' *':'' }}<input class="input" type="{{ $key==='email'?'email':($key==='phone'?'tel':'text') }}" name="{{ $key }}" maxlength="{{ $maximum }}" @required(in_array($key,['name','contact_type'],true)) value="{{ old($key,$record?->$key) }}">@error($key)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach<label>Availability<select class="select" name="is_active"><option value="1" @selected((string)old('is_active',(int)($record?->is_active??true))==='1')>Active</option><option value="0" @selected((string)old('is_active',(int)($record?->is_active??true))==='0')>Inactive</option></select></label></div></fieldset>@can('crm.manage')<button class="button" type="submit">Save contact</button>@endcan</form></div></div>
@endsection
@include('admin.crm.students.styles')
