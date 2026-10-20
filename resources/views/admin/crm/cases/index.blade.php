@extends('admin.layout')
@section('title', $title.' | GEIC Indore')
@section('crumb', 'Indore · '.$title)
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Student journey</span><h1>{{ $title }}</h1><p class="sub">{{ $type === 'application' ? 'University applications with explicit, audited status changes.' : 'Visa cases linked to university applications. Readiness for billing is a separate decision.' }}</p></div><a class="button button--quiet" href="{{ route('admin.crm.students.index') }}">Student profiles</a></section>
    <form class="panel crm-filters" method="get" action="{{ route('admin.crm.'.$type.'s.index') }}">
        @if(request('student'))<input type="hidden" name="student" value="{{ request('student') }}">@endif
        <label>Search<input class="input" name="q" maxlength="160" value="{{ request('q') }}" placeholder="Student name or {{ $type === 'application' ? 'course' : 'visa type' }}"></label>
        @if($type === 'visa')<label>Billing readiness<select class="select" name="ready_to_bill"><option value="">All cases</option><option value="1" @selected(request('ready_to_bill') === '1')>Ready to bill</option><option value="0" @selected(request('ready_to_bill') === '0')>Not marked ready</option></select></label>@endif
        <button class="button" type="submit">Apply filters</button>
    </form>
    @can('crm.export')<nav class="crm-stages" aria-label="Case exports"><a class="button button--quiet" href="{{route('admin.crm.'.$type.'s.index',[...request()->only(['q','student','status_id','ready_to_bill']),'format'=>'csv'])}}">Export filtered CSV</a><a class="button button--quiet" href="{{route('admin.crm.'.$type.'s.index',[...request()->only(['q','student','status_id','ready_to_bill']),'format'=>'print'])}}">Print / PDF</a></nav>@endcan
    <section class="panel crm-student-list">
        <div class="panel__head"><h2>{{ number_format($records->total()) }} accessible {{ strtolower($title) }} records</h2></div>
        @forelse($records as $item)<article class="crm-student-row">
            <div><strong>{{ $item->student->full_name }}</strong><p>{{ $type === 'application' ? $item->course : $item->visa_type }}</p></div>
            <div><p>{{ $type === 'application' ? $item->university->name : $item->application->university->name }}</p><p>{{ $type === 'application' ? $item->intake : 'Application #'.$item->application_id }}</p></div>
            <div><span class="pill">{{ $item->status->name }}</span><p>{{ $item->owner?->name ?: 'Owner unavailable' }}</p></div>
            <a class="button button--quiet" href="{{ route('admin.crm.'.$type.'s.show', $item->id) }}">Open {{ $type }}</a>
        </article>@empty<div class="empty"><h2>No matching records</h2><p>{{ $type === 'application' ? 'Open a student profile to add an application.' : 'Open an application to create its visa case.' }}</p></div>@endforelse
    </section>
    {{ $records->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
