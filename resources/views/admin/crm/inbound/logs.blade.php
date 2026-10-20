@extends('admin.layout')
@section('title','Inbound submission logs | GEIC Indore')
@section('crumb','Inbound submission logs')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><h1>Submission logs</h1><p>{{$record->name}}</p></div><a class="button button--quiet" href="{{route('admin.crm.inbound.show',$record->id)}}">Endpoint configuration</a></section>
    <nav class="crm-actions" aria-label="Submission log views">
        <a class="button button--quiet" @if(!$attempts) aria-current="page" @endif href="{{route('admin.crm.inbound.logs',$record->id)}}">Accepted leads</a>
        <a class="button button--quiet" @if($attempts) aria-current="page" @endif href="{{route('admin.crm.inbound.logs',[$record->id,'view'=>'attempts'])}}">Request attempts</a>
    </nav>
    @if($attempts)<p class="crm-help">Last 30 days of requests reaching this endpoint after rate limiting. Origin/preflight rejections are not included. Retries appear separately; count accepted leads in the Accepted leads view. Raw bodies, contact details and API keys are never stored in this audit.</p>@endif
    <section class="panel crm-student-list">
        @forelse($logs as $entry)
            <article class="crm-student-row">
                <div><strong>{{$attempts?'Attempt':'Submission'}} #{{$entry->id}}</strong></div>
                @if($attempts)
                    <div>HTTP {{$entry->response_status}} · {{str_replace('_',' ',$entry->outcome)}}</div>
                    <div>@if($entry->invalid_fields)Check: {{implode(', ',json_decode($entry->invalid_fields,true)??[])}}@endif</div>
                @else
                    <div>GEIC-{{$entry->enquiry_id}}</div><div>{{$entry->response_status===202?'Saved for duplicate review':'Saved'}}</div>
                @endif
                <time>{{\Carbon\CarbonImmutable::parse($entry->created_at,'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A')}} IST</time>
            </article>
        @empty
            <div class="empty"><h2>{{$attempts?'No request attempts yet':'No accepted submissions yet'}}</h2><p>Keys and raw request bodies are never listed.</p></div>
        @endforelse
    </section>
    {{$logs->links()}}
</div>
@endsection
@include('admin.crm.students.styles')
