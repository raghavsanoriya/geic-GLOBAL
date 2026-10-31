@extends('admin.layout')
@section('title', ucfirst($type).' details | GEIC Indore')
@section('crumb', 'Indore · '.ucfirst($type))
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">CRM-{{ $student->id }} · {{ $student->full_name }}</span><h1>{{ $record ? ucfirst($type).' details' : 'Create '.$type }}</h1></div><a class="button button--quiet" href="{{ route('admin.crm.students.show', $student->id) }}">Student profile</a></section>
    <nav class="crm-stages"><a class="button button--quiet" href="{{ route('admin.crm.'.$type.'s.index', ['student' => $student->id]) }}">All student {{ $type }}s</a>@if($type === 'application' && $record)@can('crm.manage')<a class="button" href="{{ route('admin.crm.visas.create', ['application' => $record->id]) }}">Create visa case</a>@endcan @endif</nav>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($record)@can('crm.documents.view')<nav class="crm-stages" aria-label="Private case documents"><a class="button button--quiet" href="{{route('admin.crm.documents.index',['student'=>$student->id,($type==='application'?'application_id':'visa_id')=>$record->id])}}">Private {{$type}} documents</a></nav>@endcan @endif
    @if($errors->any())<div class="notice" role="alert"><h2>Check these details</h2><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="rocket-detail-layout">
    <x-admin.rocket-profile-card :name="$student->full_name" :subtitle="ucfirst($type).' workspace'" :reference="$record ? strtoupper($type).'-'.$record->id : 'NEW '.strtoupper($type)"
        :status="$record ? $references->firstWhere('id',$record->status_id)?->name : 'Not yet created'"
        :details="$type==='application' ? ['Course'=>$record?->course ?? $program?->name,'Intake'=>$record?->intake] : ['Course'=>$application->course,'Intake'=>$application->intake,'Visa type'=>$record?->visa_type]"
        :links="['Student profile'=>route('admin.crm.students.show',$student->id),'All '.ucfirst($type).'s'=>route('admin.crm.'.$type.'s.index',['student'=>$student->id])]" />
    <form class="panel crm-profile-form" method="post" action="{{ $record ? route('admin.crm.'.$type.'s.update', $record->id) : route('admin.crm.'.$type.'s.store') }}"
        @if($record) data-crm-student-form data-student-id="{{ $record->id }}" data-saved-message="{{ ucfirst($type) }} saved. Student lifecycle is unchanged." @endif>
        @csrf
        @if($type === 'application' && ($program || $record?->program_id))<input type="hidden" name="program_id" value="{{ $program?->id ?? $record?->program_id }}">@endif
        @if($record)@method('PATCH')<input type="hidden" name="version" value="{{ old('version', $record->version) }}">@else<input type="hidden" name="creation_key" value="{{ old('creation_key', (string) \Illuminate\Support\Str::uuid()) }}"><input type="hidden" name="{{ $type === 'application' ? 'student_id' : 'application_id' }}" value="{{ $type === 'application' ? $student->id : $application->id }}">@endif
        <div class="notice" role="alert" tabindex="-1" data-crm-errors hidden></div><p role="status" data-crm-status aria-atomic="true"></p>@if($record)<a class="button button--quiet" href="{{ route('admin.crm.'.$type.'s.show', $record->id) }}" data-crm-reload hidden>Reload latest record</a>@endif
        <fieldset @disabled(!auth()->user()->can('crm.manage'))><legend>{{ ucfirst($type) }} information</legend><div class="crm-form-grid">
            @if($type === 'application')
                @foreach(['university_id' => ['University', 'university', true], 'campus_id' => ['Campus', 'campus', false], 'agent_id' => ['Agent', 'agent', false]] as $field => [$label, $kind, $required])
                <label>{{ $label }}{{ $required ? ' *' : '' }}<select class="select" name="{{ $field }}" @required($required)><option value="">Choose {{ strtolower($label) }}</option>@foreach($references->where('kind', $kind) as $reference)@if($reference->is_active || (int) $record?->$field === $reference->id)<option value="{{ $reference->id }}" @selected((string) old($field, $record?->$field ?? ($program ? $program->$field : null)) === (string) $reference->id)>{{ $reference->name }}{{ !$reference->is_active ? ' (inactive)' : '' }}</option>@endif @endforeach</select>@error($field)<small class="crm-error">{{ $message }}</small>@enderror</label>
                @endforeach
                <label>Course *<input class="input" name="course" maxlength="1000" required value="{{ old('course', $record?->course ?? $program?->name) }}">@error('course')<small class="crm-error">{{ $message }}</small>@enderror</label>
                <label>Intake *<input class="input" name="intake" maxlength="80" required value="{{ old('intake', $record?->intake) }}">@error('intake')<small class="crm-error">{{ $message }}</small>@enderror</label>
                <label>Submission date<input class="input" type="date" name="submitted_on" value="{{ old('submitted_on', $record?->submitted_on?->format('Y-m-d')) }}"></label>
                <fieldset><legend>Tags</legend>@foreach([...old('tags', $record?->tags ?? []), ''] as $tag)<label>Tag<input class="input" name="tags[]" maxlength="50" value="{{ $tag }}"></label>@endforeach<p class="crm-help">Up to 10 tags. Blank entries are ignored.</p></fieldset>
            @else
                <p class="crm-help">Linked application #{{ $application->id }} · {{ $application->course }} · {{ $application->intake }}</p>
                <input type="hidden" name="application_version" value="{{old('application_version',$application->version)}}">
                <label><span><input type="checkbox" name="apply_linked_application_status" value="1" @checked(old('apply_linked_application_status'))> Also apply the selected visa status’s related application status</span><small class="crm-help">Optional, explicit update. Both record versions and the application’s allowed transitions are checked. Never changes the student’s lifecycle or creates an invoice.</small>@error('apply_linked_application_status')<small class="crm-error">{{$message}}</small>@enderror</label>
                <label>Visa type *<input class="input" name="visa_type" maxlength="100" required value="{{ old('visa_type', $record?->visa_type) }}">@error('visa_type')<small class="crm-error">{{ $message }}</small>@enderror</label>
                @foreach(['lodged_on' => 'Lodgement date', 'decision_on' => 'Decision date', 'admission_on' => 'Admission date', 'census_on' => 'Census date'] as $field => $label)<label>{{ $label }}<input class="input" type="date" name="{{ $field }}" value="{{ old($field, $record?->$field?->format('Y-m-d')) }}">@error($field)<small class="crm-error">{{ $message }}</small>@enderror</label>@endforeach
                <label>Billing readiness<select class="select" name="ready_to_bill"><option value="0" @selected((string) old('ready_to_bill', (int) ($record?->ready_to_bill ?? false)) === '0')>Not marked ready</option><option value="1" @selected((string) old('ready_to_bill', (int) ($record?->ready_to_bill ?? false)) === '1')>Ready to bill</option></select><small class="crm-help">Explicit readiness flag only. Does not create an invoice or payment.</small></label>
            @endif
            <label>Status *<select class="select" name="status_id" required><option value="">Choose a status</option>@foreach($references->where('kind', $type.'_status') as $status)@if(($status->is_active || $record?->status_id === $status->id) && (!$record || in_array($status->id, $allowedStatusIds, true)))<option value="{{ $status->id }}" @selected((string) old('status_id', $record?->status_id) === (string) $status->id)>{{ $status->name }}</option>@endif @endforeach</select><small class="crm-help">Only configured next statuses are available for existing records.</small>@error('status_id')<small class="crm-error">{{ $message }}</small>@enderror</label>
            @can('crm.assign')<label>Owner<select class="select" name="owner_id">@foreach($staff as $member)<option value="{{ $member->id }}" @selected((string) old('owner_id', $record?->owner_id ?? auth()->id()) === (string) $member->id)>{{ $member->name }}</option>@endforeach</select>@error('owner_id')<small class="crm-error">{{ $message }}</small>@enderror</label>@endcan
            <label>Add remark<textarea class="input" name="remark" rows="4" maxlength="4000">{{ old('remark') }}</textarea></label>
        </div></fieldset>
        @can('crm.manage')<footer class="crm-form-footer"><p class="crm-help">Student stages, website submissions, Google Sheets and Foris deliveries are not changed by saving this record.</p><button class="button" type="submit">Save {{ $type }}</button></footer>@endcan
    </form></div>
    @if($record && $type === 'application' && filled($record->source_data))
        <section class="panel crm-history"><h2>Original Foris application data</h2><div class="crm-detail-grid">
            @foreach($record->source_data as $label => $value)
                @if(filled($value))<article><strong>{{ $label }}</strong><p style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $value }}</p></article>@endif
            @endforeach
        </div></section>
    @endif
    @if($record)<section class="panel crm-history"><h2>Remarks</h2>@forelse($notes as $note)<article><strong>{{ $note->actor_name ?: 'Former staff member' }}</strong><p style="white-space:pre-wrap">{{ $note->remark }}</p><time>{{ \Carbon\CarbonImmutable::parse($note->created_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</time></article>@empty<p>No remarks yet.</p>@endforelse{{ $notes->links() }}</section>@endif
</div>
@endsection
@include('admin.crm.students.styles')
@if($record)@can('crm.manage')@push('scripts')<script type="module" src="{{ asset('assets/transglobe/js/crm-student-form.js') }}?v=20261020-4"></script>@endpush @endcan @endif
