@extends('admin.layout')
@section('title', 'Tasks | GEIC Indore')
@section('crumb', 'Indore tasks')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Team work</span><h1>Tasks</h1><p class="sub">Your created or assigned tasks. Student-linked tasks also require case access.</p></div>@can('crm.manage')<a class="button" href="{{ route('admin.crm.tasks.create') }}">Create task</a>@endcan</section>
    @include('admin.crm.validation-feedback')
    @include('admin.crm.summary-cards', ['cards'=>[
        ['Matching tasks', $summary->total, 'M9 5h6 M9 3h6v4H9z M7 5H4v16h16V5h-3 M8 12h8 M8 16h5'],
        ['Open tasks', $summary->open, 'M12 8v4l3 2 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0'],
        ['Overdue tasks', $summary->overdue, 'M12 8v5 M12 16h.01 M12 3 2 21h20L12 3'],
        ['Completed', $summary->completed, 'M8 12l3 3 5-6 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0']
    ]])
    <form method="get" class="panel crm-filters" action="{{ route('admin.crm.tasks.index') }}">
        <label>Status<select class="select" name="status"><option value="">All statuses</option>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select></label>
        <label>Priority<select class="select" name="priority"><option value="">All priorities</option>@foreach($priorities as $key => $label)<option value="{{ $key }}" @selected(request('priority') === $key)>{{ $label }}</option>@endforeach</select></label>
        <label>Due filter<select class="select" name="overdue"><option value="">All dates</option><option value="1" @selected(request('overdue') === '1')>Overdue open tasks</option></select></label><button class="button" type="submit">Apply filters</button>
    </form>
    @can('crm.export')<div class="rocket-toolbar"><p class="crm-help">Summary and exports reflect the current filters.</p><div><a class="button button--quiet" href="{{ route('admin.crm.tasks.index', [...request()->only(['status','priority','overdue']), 'format'=>'csv']) }}">Export CSV</a><a class="button button--quiet" href="{{ route('admin.crm.tasks.index', [...request()->only(['status','priority','overdue']), 'format'=>'print']) }}">Print / PDF</a></div></div>@endcan
    <section class="panel crm-student-list" aria-label="Task list"><header class="rocket-section-heading"><div><h2>Team worklist</h2><p>{{ number_format($tasks->total()) }} matching tasks · earliest due first</p></div><span class="rocket-state rocket-state--muted">All times IST</span></header>@forelse($tasks as $task)
    <article class="crm-student-row"><div><strong>{{ $task->title }}</strong><p>{{ $task->student?->full_name ?: 'Office task' }}</p></div><div><time datetime="{{ $task->due_at->toIso8601String() }}">{{ $task->due_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</time><p>{{ $priorities[$task->priority] }} priority</p></div><div><strong>{{ $statuses[$task->status] }}</strong><p>{{ $task->assignees->pluck('name')->join(', ') ?: 'No active assignees' }}</p></div><a class="button button--quiet" href="{{ route('admin.crm.tasks.show', $task->id) }}">Open task</a></article>
    @empty<div class="rocket-empty"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 8H9v35h30V8h-7 M17 4h14v9H17z M16 24l5 5 11-12"/></svg><h2>No matching tasks</h2><p>Start with an office task, or create a student-linked task from a student profile.</p></div>@endforelse</section>
    {{ $tasks->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
@include('admin.crm.rocket-styles')
