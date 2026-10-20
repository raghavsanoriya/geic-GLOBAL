@php
    $countryChart=in_array($section,['applications_by_country','visas_by_country'],true);
    $isConversion=$section==='conversions';
    $isYears=$section==='years';
    $isCounsellors=$section==='counsellors';
    $yearLeads=array_sum(array_column($report['years'],'leads'));
    $yearBilling=array_sum(array_column($report['years'],'billing'));
    $yearRate=$reports->percent($yearBilling,$yearLeads);
    $recordTotal=$countryChart?array_sum(array_column($report[$section],'registered')):0;
    $successfulTotal=$countryChart?array_sum(array_column($report[$section],'successful')):0;
    $emptyCountry=$countryChart&&$report[$section]===[];
    $subtitle=$countryChart?'Actual records · configured outcomes':($isCounsellors?'Current assignment · unique student profiles':'Original enquiry cohorts · recorded progress');
    $exportParameters=['from_year'=>$report['from_year'],'to_year'=>$report['to_year'],'section'=>$section];
@endphp
<section class="panel crm-report-panel crm-report-panel--{{$section}} crm-report-insight" aria-labelledby="report-title-{{$section}}">
    <header class="crm-report-insight__header">
        <div class="crm-report-insight__identity"><span class="crm-report-insight__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="{{$countryChart?'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM3 12h18M12 3a17 17 0 0 1 0 18 17 17 0 0 1 0-18':($isConversion?'m4 17 5-6 5 3 6-9M15 5h5v5':'M5 19V9m7 10V4m7 15v-7')}}"/></svg></span><div><h2 id="report-title-{{$section}}">{{$title}}</h2><p class="crm-report-chart-subtitle">{{$subtitle}}</p></div></div>
        <span class="crm-report-insight__period">{{$countryChart?'Geography':($isCounsellors?'Team overview':$report['from_year'].'–'.$report['to_year'])}}</span>
    </header>
    <div class="crm-report-insight__summary">
        <div><strong>{{$isConversion?($yearRate===null?'—':number_format($yearRate,1).'%'):number_format($isYears?$yearLeads:($countryChart?$recordTotal:count($report['counsellors'])))}}</strong><span>{{$isConversion?'Lead-to-billing conversion':($isYears?'Profiles in selected cohorts':($countryChart?($section==='visas_by_country'?'Visa records':'Application records'):'Counsellor / unassigned groups'))}}</span></div>
        <div class="crm-report-insight__context">
            @if($countryChart)<span class="crm-report-insight__context-value">{{number_format($successfulTotal)}}</span><span>marked successful</span>
            @elseif($isConversion)<span class="crm-report-insight__context-value">{{number_format($yearBilling)}} / {{number_format($yearLeads)}}</span><span>reached billing / leads</span>
            @elseif($isYears)<span class="crm-report-insight__context-value">{{count($report['years'])}}</span><span>years in view</span>
            @else<span class="crm-report-insight__context-value">{{number_format($report['overall']['leads'])}}</span><span>accessible profiles</span>@endif
        </div>
    </div>
    <div class="crm-report-insight__plot {{$emptyCountry?'crm-report-insight__plot--empty':''}}">
        @if($emptyCountry)
            <div class="crm-report-country-empty">
                <svg viewBox="0 0 100 80" aria-hidden="true"><ellipse cx="50" cy="71" rx="39" ry="5" fill="#edf2fc"/><circle cx="50" cy="35" r="30" fill="#f1f5ff"/><path d="M30 19h31l9 9v34H30V19Z" fill="#fff" stroke="#c7d4f1" stroke-width="1.7"/><path d="M60 19v11h10M38 52V41m11 11V34m11 18v-7" fill="none" stroke="#829cdf" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="75" cy="17" r="10" fill="#e7f8f7"/><path d="M75 13v8m-4-4h8" stroke="#41978a" stroke-width="2" stroke-linecap="round"/></svg>
                <h3>No {{$section==='visas_by_country'?'visa':'application'}} records yet</h3><p>Your country breakdown will appear here when accessible records are added.</p>
            </div>
            <div hidden>@include('admin.crm.reports.chart',['rows'=>$report[$section],'chartId'=>$section,'title'=>$title])</div>
        @else
            @include('admin.crm.reports.chart',['rows'=>$report[$section],'chartId'=>$section,'title'=>$title])
        @endif
    </div>
    @if($countryChart&&!$emptyCountry)<div class="crm-report-insight__legend"><span><i></i> Registered records</span><span><i></i> Successful outcomes</span></div>@endif
    <footer class="crm-report-insight__footer">
        <details class="crm-report-insight__data"><summary><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 10h16M10 10v10"/></svg>View full data table</summary><div class="crm-report-table"><table><thead><tr>@foreach(array_keys($report[$section][0]??['label'=>'']) as $column)<th scope="col">{{ucwords(str_replace('_',' ',$column))}}</th>@endforeach</tr></thead><tbody>@forelse($report[$section] as $row)<tr>@foreach($row as $value)<td>{{$value===null?'Not applicable':$value}}</td>@endforeach</tr>@empty<tr><td>No recorded data.</td></tr>@endforelse</tbody></table></div></details>
        @can('crm.export')<div class="crm-report-downloads crm-report-downloads--compact" aria-label="{{$title}} downloads"><a class="button button--quiet" href="{{route('admin.crm.reports.index',[...$exportParameters,'format'=>'csv'])}}" aria-label="Download {{$title}} as CSV">CSV</a><a class="button button--quiet" href="{{route('admin.crm.reports.index',[...$exportParameters,'format'=>'svg'])}}" aria-label="Download {{$title}} as SVG">SVG</a><button class="button button--quiet" type="button" data-report-png="{{$section}}" aria-label="Download {{$title}} as PNG">PNG</button></div>@endcan
    </footer>
    @if($isCounsellors)<p class="crm-help">Counts reflect unique profiles with recorded milestones, not child-record totals.</p>@endif
</section>
