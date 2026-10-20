@extends('admin.layout')
@section('title', 'Create lead | GEIC Indore')
@section('crumb', 'Indore · New lead')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Lead operations</span><h1>Create a lead</h1><p class="sub">Contact matches go to admin review. No automatic merging or external CRM forwarding.</p></div><a class="button button--quiet" href="{{ route('admin.crm.students.index') }}">Cancel</a></section>
    @if($errors->any())<div class="notice" role="alert"><h2>Check these details</h2><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form class="panel crm-profile-form" method="post" action="{{ route('admin.crm.leads.store') }}">
        @csrf<input type="hidden" name="creation_key" value="{{ old('creation_key', (string) \Illuminate\Support\Str::uuid()) }}">
        <fieldset><legend>Contact details</legend><div class="crm-form-grid">
            <label>Full name *<input class="input" name="full_name" maxlength="120" required autocomplete="name" value="{{ old('full_name') }}">@error('full_name')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Phone number *<input class="input" name="phone" type="tel" maxlength="24" required autocomplete="tel" value="{{ old('phone') }}">@error('phone')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Email<input class="input" name="email" type="email" maxlength="160" autocomplete="email" value="{{ old('email') }}">@error('email')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>City<input class="input" name="city" maxlength="100" value="{{ old('city') }}">@error('city')<small class="crm-error">{{ $message }}</small>@enderror</label>
        </div></fieldset>
        <details><summary>Additional study details</summary><fieldset><legend>Optional profile</legend><div class="crm-form-grid">
        @foreach(['country' => ['Preferred country', 80], 'course' => ['Preferred course', 160], 'study_level' => ['Study level', 50], 'intake' => ['Preferred intake', 50], 'qualification' => ['Qualification', 160], 'english_test' => ['English test', 50], 'percentage' => ['Percentage / CGPA', 50], 'lead_category'=>['Lead category',160], 'source_reference'=>['Lead reference',160]] as $key => [$label, $maximum])
            @php($options=isset(\App\Support\CrmStudentFields::DICTIONARIES[$key])?$dictionaryOptions->get(\App\Support\CrmStudentFields::DICTIONARIES[$key],collect())->pluck('name')->unique():collect())
            <label>{{ $label }}@if($options->isNotEmpty())<select class="select" name="{{$key}}"><option value="">Not specified</option>@foreach($options as $option)<option value="{{$option}}" @selected(old($key)===$option)>{{$option}}</option>@endforeach</select>@else<input class="input" name="{{ $key }}" maxlength="{{ $maximum }}" value="{{ old($key) }}">@endif @error($key)<small class="crm-error">{{ $message }}</small>@enderror</label>
        @endforeach
            <label>Passing year<input class="input" name="passing_year" type="number" min="1950" max="2100" value="{{ old('passing_year') }}">@error('passing_year')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Enquiry notes<textarea class="input" name="message" rows="4" maxlength="4000">{{ old('message') }}</textarea>@error('message')<small class="crm-error">{{ $message }}</small>@enderror</label>
        </div></fieldset></details>
        <footer class="crm-form-footer"><p class="crm-help">New profiles are assigned to you. Administrator review is required for matching contacts.</p><button class="button" type="submit">Save lead</button></footer>
    </form>
</div>
@endsection
@include('admin.crm.students.styles')
