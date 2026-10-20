@extends('admin.layout')
@section('title', 'Student profile | GEIC Indore')
@section('crumb', 'Indore student profile')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · CRM-{{ $student->id }}</span><h1>{{ $student->full_name }}</h1></div><a class="button button--quiet" href="{{ route('admin.crm.students.index') }}">Back to students</a></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <details class="panel crm-profile-form"><summary>Student workspace · records and actions</summary>
    <nav class="crm-stages" aria-label="Detailed profile records">@foreach(\App\Models\CrmProfileEntry::FIELDS as $kind=>$definition)<a class="button button--quiet" href="{{route('admin.crm.profile-entries.index',[$student->id,$kind])}}">{{ucwords(str_replace('_',' ',$kind))}}</a>@endforeach<a class="button button--quiet" href="{{route('admin.crm.logs.index',['student'=>$student->id])}}">Activity logs</a></nav>
    @can('crm.documents.view')<div><a class="button button--quiet" href="{{route('admin.crm.documents.index',$student->id)}}">Private documents</a></div>@endcan
    <nav class="crm-stages" aria-label="Student records"><a class="button button--quiet" href="{{ route('admin.crm.follow-ups.index', ['student' => $student->id, 'queue' => 'all']) }}">Follow-ups</a>@can('crm.manage')<a class="button" href="{{ route('admin.crm.follow-ups.create', $student->id) }}">Schedule follow-up</a>@endcan</nav>
    @if($errors->any())<div class="notice" role="alert" tabindex="-1"><h2>Check these details</h2><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><p>Restricted fields are not retained in validation flash data; re-enter them if needed.</p></div>@endif
    @if($student->correction_reason)<div class="notice" role="status">{{ $student->correction_reason }}</div>@endif
    @can('crm.loyalty.manage')<div><a class="button button--quiet" href="{{route('admin.crm.loyalty.create',['student'=>$student->id])}}">Create loyalty membership</a></div>@endcan
    @can('crm.finance.view')<nav class="crm-stages" aria-label="Student finance"><a class="button button--quiet" href="{{ route('admin.crm.invoices.index', ['student' => $student->id]) }}">Invoices</a>@can('crm.finance.manage')<a class="button button--quiet" href="{{ route('admin.crm.invoices.create', ['student' => $student->id]) }}">Create invoice</a>@endcan</nav>@endcan
    <div><a class="button button--quiet" href="{{ route('admin.crm.programs.index', ['student' => $student->id]) }}">Find a program</a></div>
    <nav class="crm-stages" aria-label="Applications and visa"><a class="button button--quiet" href="{{ route('admin.crm.applications.index', ['student' => $student->id]) }}">Applications</a><a class="button button--quiet" href="{{ route('admin.crm.visas.index', ['student' => $student->id]) }}">Visa cases</a>@can('crm.manage')<a class="button" href="{{ route('admin.crm.applications.create', ['student' => $student->id]) }}">Create application</a>@endcan</nav>
    @can('crm.manage')<div><a class="button button--quiet" href="{{ route('admin.crm.tasks.create', ['student' => $student->id]) }}">Create student task</a></div>@endcan
    </details>
    <div class="rocket-detail-layout">
    <x-admin.rocket-profile-card :name="$student->full_name" :subtitle="$stages[$student->stage]" :reference="'CRM-'.$student->id" :status="$statuses[$student->lead_status]"
        :details="['Email'=>$student->email,'Phone'=>$student->phone,'Activity'=>$student->is_active ? 'Active' : 'Inactive']"
        :links="['Follow-ups'=>route('admin.crm.follow-ups.index',['student'=>$student->id,'queue'=>'all']),'Applications'=>route('admin.crm.applications.index',['student'=>$student->id]),'Visa cases'=>route('admin.crm.visas.index',['student'=>$student->id]),'Find a program'=>route('admin.crm.programs.index',['student'=>$student->id])]" />
    <form method="post" action="{{ route('admin.crm.students.update', $student->id) }}" class="panel crm-profile-form" data-crm-student-form data-student-id="{{ $student->id }}">
        @csrf @method('PATCH')
        <input type="hidden" name="version" value="{{ old('version', $student->version) }}">
        <div class="notice" role="alert" tabindex="-1" data-crm-errors hidden></div>
        <p role="status" aria-atomic="true" data-crm-status></p>
        <a class="button button--quiet" href="{{ route('admin.crm.students.show', $student->id) }}" data-crm-reload hidden>Reload the latest profile</a>
        <fieldset @disabled(!auth()->user()->can('crm.manage'))>
            <legend>Contact and lifecycle</legend>
            <div class="crm-form-grid">
                <label>Full name *<input class="input" name="full_name" maxlength="120" required value="{{ old('full_name', $student->full_name) }}">@error('full_name')<small class="crm-error">{{ $message }}</small>@enderror</label>
                <label>Email address<input class="input" name="email" type="email" maxlength="160" value="{{ old('email', $student->email) }}">@error('email')<small class="crm-error">{{ $message }}</small>@enderror</label>
                <label>Phone number *<input class="input" name="phone" type="tel" maxlength="24" required value="{{ old('phone', $student->phone) }}">@error('phone')<small class="crm-error">{{ $message }}</small>@enderror</label>
                <label>Lifecycle<select class="select" name="stage">@foreach($stages as $value => $label)<option value="{{ $value }}" @selected(old('stage', $student->stage) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label>Lead status<select class="select" name="lead_status">@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(old('lead_status', $student->lead_status) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label>Configured level status<select class="select" name="stage_status_id"><option value="">Not assigned / keep legacy status</option>@foreach($levelStatuses->groupBy(fn($item)=>$item->parent?->name??'Level unavailable') as $group=>$items)<optgroup label="{{$group}}">@foreach($items as $item)<option value="{{$item->id}}" @selected((string)old('stage_status_id',$student->stage_status_id)===(string)$item->id)>{{$item->name}}{{$item->is_active?'':' (inactive)'}}</option>@endforeach</optgroup>@endforeach</select><small class="crm-help">Must belong to the selected lifecycle. Existing status history is retained.</small>@error('stage_status_id')<small class="crm-error">{{$message}}</small>@enderror</label>
                <label><span><input type="checkbox" name="apply_status_transfer" value="1" @checked(old('apply_status_transfer'))> Apply this status’s configured level transfer</span><small class="crm-help">Explicitly moves this student to the configured destination only. Creates no child cases or financial records.</small></label>
                <label>Activity<select class="select" name="is_active"><option value="1" @selected((int) old('is_active', $student->is_active) === 1)>Active</option><option value="0" @selected((int) old('is_active', $student->is_active) === 0)>Inactive</option></select></label>
                @can('crm.assign')
                    @foreach(['assigned_counsellor_id' => 'Assigned counsellor', 'assigned_re_id' => 'Assigned RE'] as $key => $label)
                    <label>{{ $label }}<select class="select" name="{{ $key }}"><option value="">Unassigned</option>@foreach($staff as $member)<option value="{{ $member->id }}" @selected((string) old($key, $student->$key) === (string) $member->id)>{{ $member->name }}</option>@endforeach</select>@error($key)<small class="crm-error">{{ $message }}</small>@enderror</label>
                    @endforeach
                @endcan
            </div>
        </fieldset>
        <fieldset @disabled(!auth()->user()->can('crm.manage'))>
            <legend>Study profile</legend>
            <div class="crm-form-grid">@foreach($profileFields as $key => $label)@php($options=isset(\App\Support\CrmStudentFields::DICTIONARIES[$key]) ? $dictionaryOptions->get(\App\Support\CrmStudentFields::DICTIONARIES[$key],collect())->pluck('name')->unique() : collect())<label>{{ $label }}
            @if($options->isNotEmpty())<select class="select" name="profile[{{$key}}]"><option value="">Not specified</option>@php($current=old('profile.'.$key,$student->profile[$key]??''))@if(filled($current)&&!$options->contains($current))<option value="{{$current}}" selected>{{$current}} (existing value)</option>@endif @foreach($options as $option)<option value="{{$option}}" @selected($current===$option)>{{$option}}</option>@endforeach</select>
            @else<input class="input" name="profile[{{ $key }}]" maxlength="500" value="{{ old('profile.'.$key, $student->profile[$key] ?? '') }}">@endif
            @error('profile.'.$key)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach</div>
        </fieldset>
        @can('crm.sensitive')
        <fieldset @disabled(!auth()->user()->can('crm.manage'))>
            <legend>Restricted optional details</legend><p class="crm-help">Optional and permission-protected. These values are encrypted in the student store and are not used for automated eligibility decisions.</p>
            <div class="crm-form-grid">@foreach($sensitiveFields as $key => $label)<label>{{ $label }}<input class="input" name="sensitive_profile[{{ $key }}]" maxlength="500" value="{{ $sensitive[$key] ?? '' }}">@error('sensitive_profile.'.$key)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach</div>
        </fieldset>
        @endcan
        @can('crm.manage')<footer class="crm-form-footer"><p class="crm-help">Saving changes the operational profile only. Original submissions and Foris delivery history are preserved.</p><button class="button" type="submit">Save profile</button></footer>@endcan
    </form></div>
    <section class="panel crm-history"><h2>Original submissions</h2>@forelse($submissions as $submission)<article><strong>GEIC-{{ $submission->id }}</strong><p>{{ $submission->source_form ?: 'Original enquiry' }} · {{ $submission->source_page }}</p><time>{{ \Carbon\Carbon::parse($submission->created_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</time></article>@empty<p>No linked submissions.</p>@endforelse</section>
    <section class="panel crm-history"><h2>Profile activity</h2>@forelse($events as $event)<article><strong>{{ $event->action }}</strong><p>{{ $event->actor_name ?: 'System capture' }} · {{ \Carbon\Carbon::parse($event->created_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</p><p class="sub">{{ implode(', ', json_decode($event->changes, true)['fields'] ?? []) }}</p></article>@empty<p>No changes recorded yet.</p>@endforelse</section>
</div>
@endsection
@include('admin.crm.students.styles')
@can('crm.manage')
@push('scripts')
<script type="module" src="{{ asset('assets/transglobe/js/crm-student-form.js') }}?v=20261020-4"></script>
@endpush
@endcan
