@extends('admin.layout')
@section('title','Import reconciliation | GEIC Indore')
@section('crumb','Import batch')
@section('content')
@include('admin.crm.validation-feedback')
@php($importFields=\App\Models\CrmImport::fields($import->entity_type))
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Batch #{{$import->id}} · {{$import->status}}</span><h1>{{$import->source}}</h1><p class="sub">{{\App\Models\CrmImport::TYPES[$import->entity_type]}} · {{$import->row_count}} rows. Original timestamps use the supplied timezone, or India time when absent. Existing edits are never overwritten.</p></div><a class="button button--quiet" href="{{route('admin.crm.imports.index')}}">All imports</a></section>
    @if(session('status'))<div class="notice" role="status">{{session('status')}}</div>@endif
    @if($import->entity_type==='reference')<div class="notice" role="status">Dictionary addition only. Existing GEIC codes are not overwritten. Put parent universities before campuses. Status transitions, default statuses, transfers, linked outcomes and restricted agent details need explicit Config review; imported statuses have no inferred transitions. Dictionary reversal always requires dependency review.</div>@endif
    @if(in_array($import->entity_type,\App\Models\CrmImport::LOYALTY_TYPES,true))<div class="notice" role="status">Original loyalty history only. No milestone awards, staff accounts or student-stage changes are triggered. Parent references and balances are checked by the worker; missing earlier credits remain correction cases. Batch reversal retains memberships, referral links and the points ledger for traceable manual review.</div>@endif
    @if($import->entity_type==='invoice')<div class="notice" role="status">Historical invoice snapshot only. No receipts, instalments, student lifecycle changes or loyalty points are generated. Imported financial records cannot be deleted or silently deactivated through batch reversal; use the finance correction workflow after review.</div>@endif
    @if(in_array($import->entity_type,['receipt','instalment'],true))<div class="notice" role="status">Historical financial records only—no bank transfer, message, loyalty award or student-stage change. Import issued invoices first, then instalments and positive receipts before their reversals. Currency and balances must reconcile. Negative adjustments require an explicit original receipt and reason; partial or ambiguous adjustments stay invalid for review. Batch reversal retains all financial history.</div>@endif
    @if($import->status==='preview')
        <form class="panel crm-profile-form" method="post" action="{{route('admin.crm.imports.map',$import->id)}}">
            @csrf @method('PATCH')<input type="hidden" name="version" value="{{$import->version}}"><h2>Field mapping</h2>
            <p>Map required fields marked *. For student-linked records, map at least one parent identifier. Invalid or unresolved rows will not create records.</p>
            <div class="crm-form-grid">@foreach($importFields as $field=>$label)<label>{{$label}} {{in_array($field,\App\Models\CrmImport::requiredFields($import->entity_type))?'*':''}}<select class="input" name="mapping[{{$field}}]"><option value="">Not provided</option>@foreach($import->headers as $header)<option value="{{$header}}" @selected(($import->mapping[$field]??null)===$header)>{{$header}}</option>@endforeach</select></label>@endforeach</div>
            <button class="button" type="submit">Save mapping &amp; recheck preview</button>
        </form>
        <form class="panel crm-profile-form" method="post" action="{{route('admin.crm.imports.confirm',$import->id)}}">@csrf<input type="hidden" name="version" value="{{$import->version}}"><p>Confirmation validates every row. Valid rows enter the bounded worker; incomplete rows remain visible with correction reasons. Correct invalid source rows and upload again using the same source IDs.</p><button class="button" type="submit">Confirm validated rows for import</button></form>
    @endif
    @if($import->status==='paused_permissions')<form class="panel crm-profile-form" method="post" action="{{route('admin.crm.imports.confirm',$import->id)}}">@csrf<input type="hidden" name="version" value="{{$import->version}}"><h2>Import paused</h2><p>The approving user's access changed. No pending rows will be imported until an authorized administrator takes responsibility. Already imported records remain unchanged.</p><button class="button" type="submit">Resume pending rows under my access</button></form>@endif
    <section class="panel crm-profile-form"><h2>Reconciliation</h2><div class="crm-form-grid">@foreach($counts as $status=>$count)<p><strong>{{$count}}</strong> {{str_replace('_',' ',$status)}}</p>@endforeach</div><p class="sub">Duplicate-review rows require an explicit decision. “Existing” means this source record was already imported; no values were replaced.</p>
        <div class="crm-form-grid"><a class="button button--quiet" href="{{route('admin.crm.imports.export',[$import->id,'scope'=>'corrections'])}}">Download correction report</a><a class="button button--quiet" href="{{route('admin.crm.imports.export',[$import->id,'scope'=>'all'])}}">Download all row results</a></div>
        <p class="crm-help">Reports include only mapped fields and protect spreadsheet cells from formula execution. They are for reconciliation, not direct re-import. Correct the original source export, retaining source IDs, and upload it again. Store downloaded personal data securely.</p>
    </section>
    <section class="panel crm-profile-form"><h2>Rows &amp; correction reasons</h2>
        @foreach($preview as $row)<details><summary>Row {{$row['row_number']}} · {{$row['fields']['full_name']??$row['fields']['name']??$row['fields']['subject']??$row['fields']['source_id']??'Source record'}} · {{$row['status']}}</summary>
            <dl>@foreach($row['fields'] as $field=>$value)<dt>{{$importFields[$field]??$field}}</dt><dd>{{$value !== '' ? $value : 'Not provided'}}</dd>@endforeach</dl>
            @if($row['reason'])<p role="status">{{$row['reason']}}</p>@endif @foreach($row['errors'] as $error)<p class="crm-error">{{$error}}</p>@endforeach
            @if($row['student_id'])<a href="{{route('admin.crm.students.show',$row['student_id'])}}">Open CRM-{{$row['student_id']}}</a>@endif
        </details>@endforeach{{$rows->links()}}
    </section>
    @if($import->status==='completed')<form class="panel crm-profile-form" method="post" action="{{route('admin.crm.imports.reverse',$import->id)}}">@csrf<input type="hidden" name="version" value="{{$import->version}}"><h2>Controlled reversal</h2><p>Unchanged profiles, contacts or unused programs created by this batch can be deactivated. Changed records, operational follow-ups and subsequent activity require manual review. Original source rows, identities and audit history remain.</p><button class="button button--quiet" type="submit">Review and reverse unchanged imported records</button></form>@endif
</div>
@endsection
@include('admin.crm.students.styles')
