@extends('admin.layout')
@section('title', 'Task | GEIC Indore')
@section('crumb', 'Indore task')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">{{ $student ? 'CRM-'.$student->id.' · '.$student->full_name : 'Indore · Office task' }}</span><h1>{{ $record ? 'Task details' : 'Create task' }}</h1></div><a class="button button--quiet" href="{{ route('admin.crm.tasks.index') }}">Back to tasks</a></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice" role="alert"><h2>Check these details</h2><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="rocket-detail-layout">
    <x-admin.rocket-profile-card :name="$record?->title ?: 'New team task'" :subtitle="$student?->full_name ?: 'GEIC Indore · Office workspace'" :reference="$record ? 'TASK-'.$record->id : 'INDORE'" :status="$statuses[$record?->status ?? 'new']"
        :details="['Priority'=>$priorities[$record?->priority ?? 'normal'], 'Due (IST)'=>$record?->due_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A'), 'Assignees'=>$record?->assignees->pluck('name')->join(', ')]"
        :links="['All tasks'=>route('admin.crm.tasks.index')]" />
    <form class="panel crm-profile-form" method="post" action="{{ $record ? route('admin.crm.tasks.update', $record->id) : route('admin.crm.tasks.store') }}"
        @if($record) data-crm-student-form data-student-id="{{ $record->id }}" data-saved-message="Task saved. Remark history is preserved." @endif>
        @csrf
        @can('crm.assign')<input type="hidden" name="assignees_present" value="1">@endcan
        @if($record)@method('PATCH')<input type="hidden" name="version" value="{{ old('version', $record->version) }}">@else<input type="hidden" name="creation_key" value="{{ old('creation_key', (string) \Illuminate\Support\Str::uuid()) }}">@if($student)<input type="hidden" name="student_id" value="{{ $student->id }}">@endif @endif
        <div class="notice" role="alert" tabindex="-1" data-crm-errors hidden></div><p role="status" data-crm-status aria-atomic="true"></p>
        @if($record)<a class="button button--quiet" href="{{ route('admin.crm.tasks.show', $record->id) }}" data-crm-reload hidden>Reload latest task</a>@endif
        <fieldset @disabled(!auth()->user()->can('crm.manage'))><legend>Task information</legend><div class="crm-form-grid">
            <label>Title *<input class="input" name="title" required maxlength="160" value="{{ old('title', $record?->title) }}">@error('title')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Priority<select class="select" name="priority">@foreach($priorities as $key => $label)<option value="{{ $key }}" @selected(old('priority', $record?->priority ?? 'normal') === $key)>{{ $label }}</option>@endforeach</select></label>
            <label>Start (IST) *<input class="input" type="datetime-local" name="starts_at" required value="{{ old('starts_at', $record?->starts_at->timezone('Asia/Kolkata')->format('Y-m-d\TH:i')) }}">@error('starts_at')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Due (IST) *<input class="input" type="datetime-local" name="due_at" required value="{{ old('due_at', $record?->due_at->timezone('Asia/Kolkata')->format('Y-m-d\TH:i')) }}">@error('due_at')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Status<select class="select" name="status">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(old('status', $record?->status ?? 'new') === $key)>{{ $label }}</option>@endforeach</select></label>
            <label>Description<textarea class="input" rows="6" name="description" maxlength="12000">{{ old('description', $record?->description) }}</textarea>@error('description')<small class="crm-error">{{ $message }}</small>@enderror</label>
            @can('crm.assign')<fieldset><legend>Assignees</legend><p class="crm-help">Select one or more staff members.</p>@foreach($staff as $member)<label><span><input type="checkbox" name="assignees[]" value="{{ $member->id }}" @checked(in_array((string) $member->id, array_map('strval', old('assignees', $record ? $record->assignees->pluck('id')->all() : [auth()->id()])), true))> {{ $member->name }}</span></label>@endforeach @error('assignees')<small class="crm-error">{{ $message }}</small>@enderror</fieldset>@else<p class="crm-help">Assignment is managed by administrators.</p>@endcan
            <label>Add remark<textarea class="input" rows="4" name="remark" maxlength="4000">{{ old('remark') }}</textarea><small class="crm-help">Previous remarks are retained.</small>@error('remark')<small class="crm-error">{{ $message }}</small>@enderror</label>
        </div></fieldset>
        @can('crm.manage')<button class="button" type="submit">{{ $record ? 'Save task' : 'Create task' }}</button>@endcan
    </form></div>
    @if($record)<section class="panel crm-history"><h2>Remarks</h2>@forelse($notes as $note)<article><strong>{{ $note->actor_name ?: 'Former staff member' }}</strong><p style="white-space:pre-wrap">{{ $note->remark }}</p><time>{{ \Carbon\CarbonImmutable::parse($note->created_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</time></article>@empty<p>No remarks yet.</p>@endforelse{{ $notes->links() }}</section>@endif
</div>
@endsection
@include('admin.crm.students.styles')
@if($record)@can('crm.manage')@push('scripts')<script type="module" src="{{ asset('assets/transglobe/js/crm-student-form.js') }}?v=20261020-4"></script>@endpush @endcan @endif
