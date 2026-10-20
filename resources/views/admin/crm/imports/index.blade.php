@extends('admin.layout')
@section('title','Historical imports | GEIC Indore')
@section('crumb','Historical imports')
@section('content')
@include('admin.crm.validation-feedback')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Migration · Staged and resumable</span><h1>Historical imports</h1><p class="sub">Map columns, check validation and relationships, then confirm processing. Imports never forward back to Foris. Missing details are not invented.</p></div></section>
    <form class="panel crm-profile-form" method="get" action="{{route('admin.crm.imports.template')}}">
        <h2>Start with a matching template</h2>
        <p class="crm-help">Download a header-only CSV for your record type. Fill it with original exported values—no example students or invented contact details are included. Keep stable source IDs and parent relationships.</p>
        <label>Template record type<select class="input" name="entity_type">@foreach($availableTypes as $type=>$label)<option value="{{$type}}">{{$label}}</option>@endforeach</select></label>
        <button class="button button--quiet" type="submit">Download CSV template</button>
    </form>
    <form class="panel crm-profile-form" method="post" enctype="multipart/form-data" action="{{route('admin.crm.imports.store')}}">
        @csrf<input type="hidden" name="creation_key" value="{{old('creation_key',$creationKey)}}">
        <h2>Stage CSV / XLSX export</h2>
        <p>UTF-8 CSV or one-sheet XLSX with a header, up to 2,000 rows / 5 MB. XLSX must contain values only, without formulas, macros or external links. Use ISO 8601 text for dates and text cells for phone numbers and source IDs. Remove password, secret, credential and API-key columns.</p>
        <label>Record type<select class="input" name="entity_type">@foreach($availableTypes as $type=>$label)<option value="{{$type}}" @selected(old('entity_type','lead')===$type)>{{$label}}</option>@endforeach</select></label>
        <details><summary>Record-specific mapping and migration guidance</summary>
        <p class="crm-help">Invoice imports require finance access, exact decimal-text amounts, original invoice numbers and creation/issue dates. Tax and totals must reconcile; source discrepancies stay invalid for correction. Receipts and instalments are not inferred from a balance.</p>
        <p class="crm-help">For financial history, import issued invoices first, then schedules, positive receipts and their explicit full reversals. Each child needs a stable source reference or GEIC parent ID and matching currency. Draft/void histories and partial adjustments remain correction cases rather than being silently changed.</p>
        <p class="crm-help">Financial child rows are staged after format checks. Their relationships and current balances are rechecked by the worker before saving. Place original receipts earlier than their reversals in a file; unresolved rows remain invalid and can be corrected and uploaded again.</p>
        <p class="crm-help">Loyalty history: import members first, then referrals and the original chronological points ledger. Original active states and timestamps are required. Earlier credits must exist before redemptions. No award rules run, missing opening balances are not invented, and existing member/referral relationships are never replaced.</p>
        <p class="crm-help">Reference dictionaries come before programs/applications. Use GEIC kind codes and stable codes; put a university before its campuses. Existing codes remain correction cases, not updates. Lifecycle levels stay the four GEIC levels. Configure transitions, defaults, transfers and protected agent details separately; imports do not grant access or infer status rules.</p>
        <p class="crm-help">Historical tasks retain their original dates, completion state and plain-text remarks. Leave the parent student blank only for an office task. Map existing staff IDs explicitly with assignment permission; blank assignees remain unassigned. No historical staff accounts or passwords are imported.</p>
        <p class="crm-help">For previously synced leads, map the optional Foris lead ID to link a unique confirmed delivery back to its existing GEIC profile. Existing submissions and GEIC edits remain untouched. Unknown/ambiguous references require reconciliation; ordinary email/phone matches still enter duplicate review.</p>
        </details>
        <label>Stable source name<input class="input" name="source" required maxlength="80" placeholder="Foris historical records" value="{{old('source')}}"></label>
        <p class="sub">Use exactly the same source name for related and repeated exports. Import leads first. Child records require the parent's source ID or an explicit GEIC student ID—not a guessed email/name match. Staff names in exports never create accounts or permissions.</p>
        <label>CSV / XLSX file<input class="input" type="file" name="file" required accept=".csv,.txt,.xlsx"></label>
        <button class="button" type="submit">Upload for mapping preview</button>
    </form>
    <section class="panel crm-student-list">
        @forelse($imports as $import)<article class="crm-student-row"><div><strong>{{$import->source}}</strong><p>{{\App\Models\CrmImport::TYPES[$import->entity_type]??$import->entity_type}} · Batch #{{$import->id}} · {{$import->row_count}} source rows</p></div><span>{{$import->status}}</span><a class="button button--quiet" href="{{route('admin.crm.imports.show',$import->id)}}">Mapping / reconciliation</a></article>
        @empty<div class="empty"><h2>No import batches</h2><p>Original source exports are required; unavailable history cannot be reconstructed.</p></div>@endforelse
    </section>{{$imports->links()}}
</div>
@endsection
@include('admin.crm.students.styles')
