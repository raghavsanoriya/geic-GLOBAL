@extends('admin.layout')
@section('title','Branches | GEIC CRM')
@section('crumb','Branch management')
@section('content')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Organisation</span><h1>Branch management</h1><p class="sub">Maintain office, tax, banking and staff assignment details. Restricted values remain encrypted.</p></div><a class="button" href="{{route('admin.crm.branches.create')}}">Add branch</a></section>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
<section class="panel crm-student-list">@forelse($branches as $branch)<article class="crm-student-row"><div><strong>{{$branch->name}}</strong><p>{{$branch->state ?: 'State not set'}}{{$branch->state_code?' · '.$branch->state_code:''}}</p></div><div><strong>{{$branch->users_count}}</strong><p>assigned user{{$branch->users_count===1?'':'s'}}</p></div><span>{{$branch->is_active?'Active':'Inactive'}}</span><a class="button button--quiet" href="{{route('admin.crm.branches.show',$branch->id)}}">Edit branch</a></article>@empty<div class="empty"><h2>No branches yet</h2><p>Add the first operating branch, then assign staff from Team access.</p></div>@endforelse</section>{{$branches->links()}}</div>
@endsection
@include('admin.crm.students.styles')
