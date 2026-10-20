@extends('admin.layout')
@section('title', 'Indore student CRM | GEIC')
@section('crumb', 'Indore student CRM')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Student management</span><h1>Student CRM</h1></div><p>Operational profiles stay separate from original enquiries. Staff see only cases they are allowed to access.</p></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @can('crm.manage')<div><a class="button" href="{{ route('admin.crm.leads.create') }}">Create lead</a></div>@endcan
    @can('crm.export')<nav class="crm-stages" aria-label="Student exports"><a class="button button--quiet" href="{{route('admin.crm.students.export',request()->only($filterKeys))}}">Export filtered CSV</a><a class="button button--quiet" href="{{route('admin.crm.students.export',[...request()->only($filterKeys),'format'=>'print'])}}">Printable filtered list</a></nav>@endcan
    @if($savedViews->isNotEmpty())<section class="panel crm-profile-form" aria-label="My saved searches"><h2>My saved searches</h2><div class="crm-saved-searches">@foreach($savedViews as $saved)<div class="crm-saved-search"><a class="button button--quiet" href="{{route('admin.crm.students.index',json_decode($saved->filters,true))}}">{{$saved->name}}</a><form method="post" action="{{route('admin.crm.students.views.remove',$saved->id)}}">@csrf @method('delete')<button class="button button--quiet" type="submit" aria-label="Remove saved search: {{$saved->name}}">Remove</button></form></div>@endforeach</div><p class="crm-help">Removing a saved search only removes your shortcut, never a student or enquiry.</p></section>@endif
    <details class="panel crm-profile-form"><summary>Save current filters</summary><form method="post" action="{{route('admin.crm.students.views')}}">@csrf @foreach($filterKeys as $key)<input type="hidden" name="filters[{{$key}}]" value="{{request($key)}}">@endforeach<label>Saved search name<input class="input" name="name" required maxlength="80"></label><button class="button" type="submit">Save for my account</button></form></details>
    @if($errors->any())<div class="notice" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <nav class="crm-stages" aria-label="Student lifecycle">
        <a class="button button--quiet" href="{{ route('admin.crm.students.index') }}" @if(!request('stage')) aria-current="page" @endif>All students</a>
        @foreach($stages as $value => $label)<a class="button button--quiet" href="{{ route('admin.crm.students.index', ['stage' => $value]) }}" @if(request('stage') === $value) aria-current="page" @endif>{{ $loop->iteration }} · {{ $label }}</a>@endforeach
    </nav>
    <form class="panel crm-filters" method="get" action="{{ route('admin.crm.students.index') }}">
        @if(collect(['dimension','assigned_counsellor_id','assigned_re_id','unassigned','unassigned_counsellor','unassigned_re'])->contains(fn($key)=>request()->filled($key)))
            <div class="notice" role="status">Dashboard breakdown filter applied. <a href="{{route('admin.crm.students.index',request()->only(['q','stage','lead_status','stage_status_id','list','active']))}}">Clear breakdown</a></div>
        @endif
        @foreach(['dimension','dimension_value','dimension_missing','assigned_counsellor_id','assigned_re_id','unassigned','unassigned_counsellor','unassigned_re'] as $drilldown)
            @if(request()->filled($drilldown))<input type="hidden" name="{{$drilldown}}" value="{{request($drilldown)}}">@endif
        @endforeach
        <label>Search students<input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Name, email or full phone number" maxlength="160"></label>
        <label>Lifecycle<select class="select" name="stage"><option value="">All levels</option>@foreach($stages as $value => $label)<option value="{{ $value }}" @selected(request('stage') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label>Lead status<select class="select" name="lead_status"><option value="">All statuses</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('lead_status') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label>Configured level status<select class="select" name="stage_status_id"><option value="">All configured statuses</option>@foreach($levelStatuses as $status)<option value="{{$status->id}}" @selected((string)request('stage_status_id')===(string)$status->id)>{{$status->parent?->name??'Level'}} · {{$status->name}}</option>@endforeach</select></label>
        <label>List placement<select class="select" name="list">@foreach(['all'=>'All lists','main'=>'Main list','dead'=>'Dead / unqualified list'] as $key=>$label)<option value="{{$key}}" @selected(request('list','all')===$key)>{{$label}}</option>@endforeach</select></label>
        <label>Activity<select class="select" name="active"><option value="">Active and inactive</option><option value="1" @selected(request('active') === '1')>Active</option><option value="0" @selected(request('active') === '0')>Inactive</option></select></label>
        <details class="crm-advanced-filters" @if(collect(['student_id','enquiry_id','city','country','qualification','study_level','lead_category','source_reference','created_from','created_to','my_cases','application_status_id'])->contains(fn($key)=>request()->filled($key))) open @endif>
            <summary>More search filters</summary>
            <div class="crm-form-grid">
                <label>GEIC profile ID<input class="input" type="number" min="1" name="student_id" value="{{request('student_id')}}"></label>
                <label>Original GEIC enquiry ID<input class="input" type="number" min="1" name="enquiry_id" value="{{request('enquiry_id')}}"></label>
                @foreach(['city'=>'City','country'=>'Preferred country','qualification'=>'Current qualification','study_level'=>'Intended qualification','lead_category'=>'Lead category','source_reference'=>'Enquiry reference'] as $key=>$label)
                    <label>{{$label}} (exact)<input class="input" name="{{$key}}" maxlength="160" value="{{request($key)}}"></label>
                @endforeach
                <label>Profile created from (India)<input class="input" type="date" name="created_from" value="{{request('created_from')}}"></label>
                <label>Profile created through (India)<input class="input" type="date" name="created_to" value="{{request('created_to')}}"></label>
                <label>Application status<select class="select" name="application_status_id"><option value="">Any application status</option>@foreach($applicationStatuses as $status)<option value="{{$status->id}}" @selected((string)request('application_status_id')===(string)$status->id)>{{$status->name}}</option>@endforeach</select></label>
                <label>Assignment<select class="select" name="my_cases"><option value="">All accessible cases</option><option value="1" @selected(request('my_cases')==='1')>My counsellor / RE cases</option></select></label>
            </div>
            <p class="crm-help">Filters combine with the current lifecycle and dashboard selection. Dates include the full India calendar day; profile creation is separate from an original enquiry's submission time.</p>
        </details>
        <button class="button" type="submit">Apply filters</button>
        <a class="button button--quiet" href="{{route('admin.crm.students.index')}}">Reset filters</a>
    </form>
    @can('crm.manage')
    <form id="crm-bulk-students" class="panel crm-profile-form" method="post" action="{{route('admin.crm.students.bulk')}}">
        @csrf<h2>Selected profile actions</h2><p>Select profiles below. Actions check each displayed version and apply atomically; changed profiles require a reload.</p>
        <div class="crm-form-grid">
            <label>Action<select class="input" name="action"><option value="activate">Activate</option><option value="deactivate">Deactivate</option>@can('crm.assign')<option value="assign">Assign / transfer counsellor</option><option value="assign_re">Assign / transfer RE</option>@endcan</select></label>
            @can('crm.assign')
                @foreach(['assigned_counsellor_id'=>'Counsellor for counsellor assignment','assigned_re_id'=>'RE for RE assignment'] as $field=>$label)
                <label>{{$label}}<select class="input" name="{{$field}}"><option value="">Unassigned</option>@foreach($assignableStaff as $staff)<option value="{{$staff->id}}">{{$staff->name}}</option>@endforeach</select></label>
                @endforeach
            @endcan
        </div>
        <p class="crm-help">Only the selected assignment role changes. The other role, task ownership and original submissions stay unchanged. Choosing Unassigned removes that role's case access unless the staff member retains access through another assignment.</p>
        <button class="button" type="submit">Apply to selected profiles</button>
    </form>
    @endcan
    <section class="panel crm-student-list" aria-label="Accessible students">
        <div class="panel__head"><h2>{{ number_format($students->total()) }} accessible students</h2><span>Indore office</span></div>
        @forelse($students as $student)
        <article class="crm-student-row">
            <div>@can('crm.manage')<label><input type="checkbox" form="crm-bulk-students" name="students[]" value="{{$student->id}}"> Select CRM-{{$student->id}}</label><input form="crm-bulk-students" type="hidden" name="versions[{{$student->id}}]" value="{{$student->version}}">@endcan<a class="student" href="{{ route('admin.crm.students.show', $student->id) }}">{{ $student->full_name }}</a><p class="sub">CRM-{{ $student->id }} · Original GEIC-{{ $student->original_enquiry_id }}</p></div>
            <div><p>{{ $student->email ?: 'Email not provided' }}</p><p class="sub">{{ $student->phone ?: 'Phone needs correction' }}</p></div>
            <div><span class="pill">{{ $stages[$student->stage] }}</span><p class="sub">{{ $student->stageStatus?->name ?: $statuses[$student->lead_status] }} · {{ $student->is_active ? 'Active' : 'Inactive' }}</p></div>
            <a class="button button--quiet" href="{{ route('admin.crm.students.show', $student->id) }}">Open profile</a>
        </article>
        @empty
        <div class="empty"><h2>No accessible student profiles</h2><p>New website enquiries appear after CRM activation. Existing enquiries can be captured in bounded batches; pending duplicates remain in admin review.</p></div>
        @endforelse
        <div class="pagination">{{ $students->onEachSide(1)->links() }}</div>
    </section>
</div>
@endsection
@include('admin.crm.students.styles')
