@extends('admin.layout')
@section('title', 'Follow-up | GEIC Indore')
@section('crumb', 'Indore follow-up')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">CRM-{{ $student->id }} · {{ $student->full_name }}</span><h1>{{ $record ? 'Follow-up details' : 'Schedule follow-up' }}</h1></div><a class="button button--quiet" href="{{ route('admin.crm.follow-ups.index', ['student' => $student->id, 'queue' => 'all']) }}">Back to follow-ups</a></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice" role="alert"><h2>Check your details</h2><ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
    @include('admin.crm.follow-ups.details')
</div>
@endsection
@include('admin.crm.students.styles')
@if($record)
@can('crm.manage')
@push('scripts')<script type="module" src="{{ asset('assets/transglobe/js/crm-student-form.js') }}?v=20261020-2"></script>@endpush
@endcan
@endif
