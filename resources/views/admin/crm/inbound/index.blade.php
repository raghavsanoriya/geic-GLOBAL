@extends('admin.layout')
@section('title','Inbound Forms & APIs | GEIC Indore')
@section('crumb','Marketing · Inbound APIs')
@section('content')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Indore · Marketing integrations</span><h1>Inbound Forms & APIs</h1><p class="sub">Server-side API connections with field mappings, exact origins, duplicate policies and request idempotency.</p></div><a class="button" href="{{route('admin.crm.inbound.create')}}">Create endpoint</a></section><section class="panel crm-student-list">@forelse($forms as $form)<article class="crm-student-row"><div><strong>{{$form->name}}</strong><p>{{$form->form_key}}</p></div><div>{{$form->is_active?'Active':'Disabled'}}<p>Duplicates: {{$form->duplicate_policy}}</p></div><div>Foris forwarding: {{$form->forward_to_foris?'Enabled':'Disabled'}}</div><a class="button button--quiet" href="{{route('admin.crm.inbound.show',$form->id)}}">Configure / guide</a></article>@empty<div class="empty"><h2>No inbound endpoints</h2><p>Existing GEIC website forms continue to work independently.</p></div>@endforelse</section>{{$forms->links()}}</div>
@endsection
@include('admin.crm.students.styles')
