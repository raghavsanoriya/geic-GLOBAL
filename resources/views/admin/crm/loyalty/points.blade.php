@extends('admin.layout')
@section('title','Points ledger | GEIC Indore')
@section('crumb','Loyalty · Points')
@section('content')
@can('crm.export')<nav class="crm-actions" aria-label="Points ledger exports"><a class="button button--quiet" href="{{route('admin.crm.loyalty.points',array_merge(request()->only('member'),['format'=>'csv']))}}">Export CSV</a><a class="button button--quiet" href="{{route('admin.crm.loyalty.points',array_merge(request()->only('member'),['format'=>'print']))}}">Print points ledger</a></nav>@endcan
<div class="crm-workspace"><section class="page-head"><h1>Points ledger</h1><a class="button button--quiet" href="{{route('admin.crm.loyalty.index')}}">Members</a></section><section class="panel crm-student-list">@forelse($points as $entry)<article class="crm-student-row"><div><strong>{{$entry->name}}</strong><p>{{$entry->reason}}</p></div><div>{{$entry->points>0?'+':''}}{{number_format($entry->points)}} points</div><div>{{\Carbon\CarbonImmutable::parse($entry->created_at,'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A')}} IST</div><a class="button button--quiet" href="{{route('admin.crm.loyalty.show',$entry->member_id)}}">Open member</a></article>@empty<div class="empty"><h2>No points recorded</h2><p>Configured awards and manual adjustments will appear here.</p></div>@endforelse</section>{{$points->links()}}</div>
@endsection
@include('admin.crm.students.styles')
