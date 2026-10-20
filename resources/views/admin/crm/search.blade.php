@extends('admin.layout')
@section('title','Search workspace | GEIC Indore')
@section('crumb','Search CRM')
@section('content')
@include('admin.crm.validation-feedback')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Indore · Find your work</span><h1>Search workspace</h1><p class="sub">Search accessible student names, email, phone, tasks, follow-ups, applications, contacts and programs. Restricted profile fields and document contents are never searched.</p></div></section>
<form class="panel crm-profile-form" method="get" action="{{route('admin.crm.search')}}"><label>Search<input class="input" name="q" type="search" minlength="2" maxlength="120" value="{{$term}}" placeholder="Enter at least two characters" required></label><button class="button" type="submit">Search accessible records</button></form>
@if($term==='')<section class="panel crm-profile-form"><h2>One search, your accessible records</h2><p class="crm-help">Results are limited to 12 per module. Open the full module for detailed filters and exports.</p></section>@else
@foreach($groups as $label=>$rows)<section class="panel crm-student-list"><div class="panel__head"><h2>{{$label}}</h2><span>{{count($rows)}} shown · up to 12</span></div>@forelse($rows as $row)<article class="crm-student-row"><div><strong>{{$row['label']}}</strong><p class="sub">{{$row['reference']}}</p></div><a class="button button--quiet" href="{{$row['url']}}">Open record</a></article>@empty<div class="crm-profile-form"><p class="crm-help">No matching accessible records.</p></div>@endforelse</section>@endforeach
@endif</div>
@endsection
@include('admin.crm.students.styles')
