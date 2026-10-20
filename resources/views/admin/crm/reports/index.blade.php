@extends('admin.layout')
@section('title','Reports | GEIC Indore')
@section('crumb','CRM Reports')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Operational reports</span><h1>Reports</h1><p class="sub">Student profiles, original submissions and operational records are counted separately. All cohort boundaries use India time.</p></div></section>
    @include('admin.crm.reports.overview')
    @include('admin.crm.reports.bento')
    <form class="panel crm-filters" method="get" action="{{route('admin.crm.reports.index')}}"><label>From year<input class="input" type="number" name="from_year" min="2000" max="{{now('Asia/Kolkata')->year}}" value="{{$report['from_year']}}" required></label><label>To year<input class="input" type="number" name="to_year" min="2000" max="{{now('Asia/Kolkata')->year}}" value="{{$report['to_year']}}" required></label><button class="button" type="submit">Update charts</button></form>
    @if($report['history_gaps'])<aside class="crm-report-data-note"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v1"/></svg><p><strong>Historical data note:</strong> {{number_format($report['history_gaps'])}} profiles have no recorded stage history. Conversion charts include verified milestones only.</p></aside>@endif
    @foreach(['last12'=>'Last 12 months','overall'=>'Overall'] as $scope=>$label)
        @include('admin.crm.reports.conversion')
    @endforeach
    <p role="status" data-chart-download-status class="crm-help"></p>
    <div class="crm-report-grid">
    @foreach(['conversions'=>'Lead conversion rate per year','years'=>'Leads per year','applications_by_country'=>'Applications by country','visas_by_country'=>'Visa by country','counsellors'=>'Counsellor performance'] as $section=>$title)
        @include('admin.crm.reports.report-panel')
    @endforeach
    </div>
    <details class="panel crm-history crm-report-methodology"><summary>How these reports count</summary><p>{{number_format($report['overall']['leads'])}} accessible student profiles · {{number_format($report['submission_count'])}} linked original submissions.</p><p>Conversion uses recorded GEIC lifecycle milestones, not inferred stage history. Cohorts use the original enquiry submission date and show progress recorded through today. Skipped stages are not invented; stage counts may not form a descending funnel.</p><p>Country charts count actual application/visa records. Successful outcomes depend on each status's configured reporting classification. Counsellor attribution uses the student's current assigned counsellor. Charts show up to 20 groups; CSV and data tables contain up to 1,000 groups. Missing history cannot be reconstructed without an authorised export.</p></details>
</div>
@endsection
@include('admin.crm.students.styles')
@include('admin.crm.reports.infographic-styles')
@push('styles')
<style>.crm-report-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.crm-report-panel{padding:24px;min-width:0}.crm-report-panel header{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px}.crm-report-panel h2{font-size:17px;margin:0}.crm-report-panel svg{display:block;width:100%;height:auto}.crm-report-downloads{display:flex;gap:8px}.crm-report-downloads .button{padding:8px 12px;font-size:12px}.crm-report-cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.crm-report-cards article{padding:16px;border:1px solid var(--admin-line);border-radius:12px}.crm-report-cards h3{font-size:13px;margin:0 0 12px;color:var(--admin-muted)}.crm-report-cards strong{font-size:23px}.crm-report-table{overflow:auto}.crm-report-table table{width:100%;border-collapse:collapse;font-size:13px}.crm-report-table th,.crm-report-table td{text-align:left;padding:10px;border-bottom:1px solid var(--admin-line);overflow-wrap:anywhere}.crm-report-panel summary{cursor:pointer;padding:12px 0;font-weight:600}@media(max-width:1100px){.crm-report-grid{grid-template-columns:minmax(0,1fr)}.crm-report-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.crm-report-panel{padding:16px}.crm-report-cards{grid-template-columns:minmax(0,1fr)}}</style>
@endpush
@push('scripts')<script type="module" src="{{asset('assets/transglobe/js/crm-report-downloads.js')}}"></script>@endpush
