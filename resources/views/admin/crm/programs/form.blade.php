@extends('admin.layout')
@section('title', 'Program details | GEIC Indore')
@section('crumb', 'Program management')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Catalogue management</span><h1>{{ $record ? 'Edit program' : 'Add program' }}</h1></div><a class="button button--quiet" href="{{ route('admin.crm.programs.index') }}">Back to programs</a></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice" role="alert"><h2>Check these details</h2><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="rocket-detail-layout">
    <x-admin.rocket-profile-card :name="$record?->name ?: 'New study program'" :subtitle="$record?->country ?: 'GEIC study catalogue'" :reference="$record?->code ?: 'NEW PROGRAM'"
        :status="$record ? ($record->is_active ? 'Active program' : 'Inactive program') : 'Draft entry'"
        :details="['University'=>$references->firstWhere('id',$record?->university_id)?->name,'Study level'=>$record?->study_level,'Subject'=>$record?->subject]"
        :links="['Program catalogue'=>route('admin.crm.programs.index')]" />
    <form class="panel crm-profile-form" method="post" action="{{ $record ? route('admin.crm.programs.update', $record->id) : route('admin.crm.programs.store') }}">
        @csrf @if($record)@method('PATCH')<input type="hidden" name="version" value="{{ old('version', $record->version) }}">@endif
        <fieldset><legend>Program information</legend><div class="crm-form-grid">
            @if(!$record)<label>Stable code *<input class="input" name="code" maxlength="60" required pattern="[a-z0-9][a-z0-9_-]{0,59}" value="{{ old('code') }}"></label>@else<p class="crm-help">Stable code: {{ $record->code }}</p>@endif
            @foreach(['name' => ['Program name',160], 'country' => ['Country',100], 'study_level' => ['Study level',80], 'subject' => ['Subject',100]] as $field => [$label, $maximum])<label>{{ $label }} *<input class="input" name="{{ $field }}" maxlength="{{ $maximum }}" required value="{{ old($field, $record?->$field) }}">@error($field)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach
            @foreach(['university_id' => ['University','university',true], 'campus_id' => ['Campus','campus',false], 'qualification_id' => ['Qualification','qualification',false]] as $field => [$label,$kind,$required])<label>{{ $label }}{{ $required ? ' *' : '' }}<select class="select" name="{{ $field }}" @required($required)><option value="">Choose {{ strtolower($label) }}</option>@foreach($references->where('kind', $kind) as $reference)@if($reference->is_active || (int) $record?->$field === $reference->id)<option value="{{ $reference->id }}" @selected((string) old($field, $record?->$field) === (string) $reference->id)>{{ $reference->name }}</option>@endif @endforeach</select>@error($field)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach
            @foreach(['ranking' => ['Ranking',100000,1], 'ielts' => ['Minimum IELTS',9,.1], 'toefl' => ['Minimum TOEFL',120,1], 'pte' => ['Minimum PTE',90,1]] as $field => [$label,$maximum,$step])<label>{{ $label }}<input class="input" type="number" name="{{ $field }}" min="{{ $field === 'ranking' ? 1 : 0 }}" max="{{ $maximum }}" step="{{ $step }}" value="{{ old($field, $record?->$field) }}">@error($field)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach
            <label>Availability<select class="select" name="is_active"><option value="1" @selected((string) old('is_active',(int)($record?->is_active ?? true)) === '1')>Active</option><option value="0" @selected((string) old('is_active',(int)($record?->is_active ?? true)) === '0')>Inactive</option></select></label>
            <label>Description<textarea class="input" name="description" rows="5" maxlength="12000">{{ old('description',$record?->description) }}</textarea></label>
        </div></fieldset><footer class="crm-form-footer"><p class="crm-help">Leave unknown exam requirements blank. Existing applications keep their recorded course details.</p><button class="button" type="submit">Save program</button></footer>
    </form></div>
</div>
@endsection
@include('admin.crm.students.styles')
