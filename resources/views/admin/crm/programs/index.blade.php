@extends('admin.layout')
@section('title', 'Programs | GEIC Indore')
@section('crumb', 'Indore · Program catalogue')
@section('content')
@can('crm.export')<nav class="crm-stages" aria-label="Program exports"><a class="button button--quiet" href="{{route('admin.crm.programs.index',[...request()->except(['page','format']),'format'=>'csv'])}}">Export filtered catalogue</a><a class="button button--quiet" href="{{route('admin.crm.programs.index',[...request()->except(['page','format']),'format'=>'print'])}}">Print / PDF shortlist</a></nav>@endcan
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Program catalogue</span><h1>Programs</h1><p class="sub">Search published requirements. Score matches are not an admission or visa eligibility decision.</p></div>@can('crm.configure')<a class="button" href="{{ route('admin.crm.programs.create') }}">Add program</a>@endcan</section>
    @if($student)<div class="notice">Choosing a program for <strong>{{ $student->full_name }}</strong>. <a href="{{ route('admin.crm.students.show', $student->id) }}">Back to profile</a></div>@endif
    <form class="panel crm-profile-form" method="get" action="{{ route('admin.crm.programs.index') }}">
        @if($student)<input type="hidden" name="student" value="{{ $student->id }}">@endif
        <fieldset><legend>Find programs</legend><div class="crm-form-grid">
            <label>Program name<input class="input" name="q" maxlength="160" value="{{ request('q') }}"></label>
            <label>Country<select class="select" name="country"><option value="">All countries</option>@foreach($countries as $country)<option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>@endforeach</select></label>
            @foreach(['university_id' => ['University', 'university'], 'campus_id' => ['Campus', 'campus'], 'qualification_id' => ['Qualification', 'qualification']] as $field => [$label, $kind])<label>{{ $label }}<select class="select" name="{{ $field }}"><option value="">All</option>@foreach($references->where('kind', $kind) as $reference)<option value="{{ $reference->id }}" @selected((string) request($field) === (string) $reference->id)>{{ $reference->name }}</option>@endforeach</select></label>@endforeach
            <label>Study level<input class="input" name="study_level" maxlength="80" value="{{ request('study_level') }}"></label>
            <label>Subject<input class="input" name="subject" maxlength="100" value="{{ request('subject') }}"></label>
            <label>Maximum ranking<input class="input" type="number" name="ranking" min="1" max="100000" value="{{ request('ranking') }}"></label>
            <label>Available IELTS score<input class="input" type="number" name="ielts" min="0" max="9" step=".5" value="{{ request('ielts') }}"></label>
            <label>Available TOEFL score<input class="input" type="number" name="toefl" min="0" max="120" value="{{ request('toefl') }}"></label>
            <label>Available PTE score<input class="input" type="number" name="pte" min="0" max="90" value="{{ request('pte') }}"></label>
        </div></fieldset>
        <footer class="crm-form-footer"><p class="crm-help">Score filters show only programs with known minimum requirements at or below the entered score.</p><button class="button" type="submit">Search programs</button></footer>
    </form>
    <section class="panel crm-student-list"><div class="panel__head"><h2>{{ number_format($programs->total()) }} matching programs</h2></div>
    @forelse($programs as $program)<article class="crm-student-row">
        <div><strong>{{ $program->name }}</strong><p>{{ $program->subject }} · {{ $program->study_level }}</p></div>
        <div><p>{{ $program->university->name }}</p><p>{{ $program->campus?->name }} · {{ $program->country }}</p></div>
        <div><p>Ranking: {{ $program->ranking ?? 'Not recorded' }}</p><p>IELTS: {{ $program->ielts ?? 'Not recorded' }} · TOEFL: {{ $program->toefl ?? 'Not recorded' }} · PTE: {{ $program->pte ?? 'Not recorded' }}</p></div>
        <div>@if($student)@can('crm.manage')<a class="button" href="{{ route('admin.crm.applications.create', ['student' => $student->id, 'program' => $program->id]) }}">Choose program</a>@endcan @endif @can('crm.configure')<a class="button button--quiet" href="{{ route('admin.crm.programs.show', $program->id) }}">Edit program</a>@endcan</div>
    </article>@empty<div class="empty"><h2>No matching programs</h2><p>Change filters, or ask a configuration administrator to add verified program requirements.</p></div>@endforelse</section>
    {{ $programs->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
