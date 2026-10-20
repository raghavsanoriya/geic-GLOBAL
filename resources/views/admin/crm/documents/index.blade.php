@extends('admin.layout')
@section('title','Private documents | GEIC Indore')
@section('crumb','Student documents')
@section('content')
@include('admin.crm.validation-feedback')
<div class="crm-workspace"><section class="page-head"><div><span class="eyebrow">Restricted · CRM-{{$student->id}}</span><h1>Private documents</h1><p class="sub">{{$student->full_name}} · Downloads require current student access and document permission.</p></div><a class="button button--quiet" href="{{route('admin.crm.students.show',$student->id)}}">Student profile</a></section>
@if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
@if(request()->filled('application_id') || request()->filled('visa_id'))<div class="notice" role="status">Showing documents linked to the selected case. <a href="{{route('admin.crm.documents.index',$student->id)}}">All student documents</a></div>@endif
@can('crm.documents.manage')<p class="crm-help">Optional case links attach the file to this student's application or visa. When both are selected, they must belong together. Leaving both blank keeps a general student document. No status or admission outcome changes.</p>@endcan
@can('crm.documents.manage')
<form class="panel crm-profile-form" method="post" enctype="multipart/form-data" action="{{route('admin.crm.documents.store',$student->id)}}">
    @csrf<input type="hidden" name="creation_key" value="{{old('creation_key',$creationKey)}}">
    <h2>Upload verified document</h2>
    <div class="crm-form-grid">
        <label>Document label<input class="input" name="label" required maxlength="160" value="{{old('label')}}"></label>
        <label>Category<select class="input" name="category">@foreach(['education','employment','english_test','application','visa','passport','other'] as $category)<option value="{{$category}}" @selected(old('category')===$category)>{{ucwords(str_replace('_',' ',$category))}}</option>@endforeach</select></label>
        <label>Application (optional)<select class="input" name="application_id"><option value="">General student document</option>@foreach($applications as $application)<option value="{{$application->id}}" @selected((string)old('application_id',request('application_id'))===(string)$application->id)>Application #{{$application->id}} · {{$application->course}}</option>@endforeach</select></label>
        <label>Visa (optional)<select class="input" name="visa_id"><option value="">No specific visa</option>@foreach($visas as $visa)<option value="{{$visa->id}}" @selected((string)old('visa_id',request('visa_id'))===(string)$visa->id)>Visa #{{$visa->id}} · {{$visa->visa_type}} · Application #{{$visa->application_id}}</option>@endforeach</select></label>
        <label>PDF, JPG or PNG · up to 10 MB<input class="input" type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"></label>
    </div>
    <p class="sub">Upload only relevant, verified documents with the student’s authorization. Files are private and are never forwarded to Foris or public media.</p>
    <button class="button" type="submit">Upload private document</button>
</form>
@endcan
<section class="panel crm-profile-form"><h2>Document case references</h2>
@forelse($documents->filter(fn($document)=>$document->application_id || $document->visa_id) as $document)
    <p><strong>{{$document->label}}</strong> · @if($document->application_id)<a href="{{route('admin.crm.applications.show',$document->application_id)}}">Application #{{$document->application_id}}</a>@endif @if($document->visa_id)<a href="{{route('admin.crm.visas.show',$document->visa_id)}}">Visa #{{$document->visa_id}}</a>@endif</p>
@empty<p class="sub">No specific case links on this page of documents.</p>@endforelse
</section>
<section class="panel crm-student-list">@forelse($documents as $document)<article class="crm-student-row"><div><strong>{{$document->label}}</strong><p>{{ucwords(str_replace('_',' ',$document->category))}} · {{number_format($document->bytes/1024)}} KB</p></div><span>{{$document->is_active?'Active':'Retired'}}</span>@if($document->is_active)@can('crm.documents.view')<a class="button button--quiet" href="{{route('admin.crm.documents.download',[$student->id,$document->id])}}">Download</a>@endcan @can('crm.documents.manage')<form method="post" action="{{route('admin.crm.documents.retire',[$student->id,$document->id])}}">@csrf<input type="hidden" name="version" value="{{$document->version}}"><button class="button button--quiet" type="submit">Retire</button></form>@endcan @endif</article>@empty<div class="empty"><h2>No documents uploaded</h2><p>Private files will be listed here after authorized upload.</p></div>@endforelse</section>{{$documents->links()}}</div>
@endsection
@include('admin.crm.students.styles')
