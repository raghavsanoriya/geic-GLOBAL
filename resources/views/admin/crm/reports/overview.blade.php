@php
    $overviewTiles = [
        ['Student profiles',$report['overall']['leads'],'blue','M7 21v-2a5 5 0 0 1 10 0v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8','Unique student profiles','Accessible to your account'],
        ['Submissions',$report['submission_count'],'mint','M5 4h14v17H5V4Zm4-2h6v4H9V2Zm0 8h6m-6 4h6m-6 4h3','Original linked enquiries','Original records preserved'],
        ['Application stage',$report['overall']['applications'],'violet','M6 3h9l4 4v14H6V3Zm8 0v5h5M9 12h7m-7 4h5','Reached application','Recorded lifecycle milestone'],
        ['Visa stage',$report['overall']['visas'],'cyan','M5 3h14v18H5V3Zm3 5h8m-4-3v8m-3-7 6 6m0-6-6 6m0 5h6','Reached visa stage','Recorded lifecycle milestone'],
        ['Billing stage',$report['overall']['billing'],'amber','M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6m-6 4h6m-6 4h3','Reached billing stage','Recorded lifecycle milestone'],
        ['History gaps',$report['history_gaps'],'rose','M12 3 2 21h20L12 3Zm0 6v5m0 3v1','Missing lifecycle history','Not included as inferred progress'],
    ];
@endphp
<section class="crm-report-overview" aria-label="Recorded CRM totals">
    @foreach($overviewTiles as [$title,$value,$tone,$path,$description,$context])
    <article class="crm-report-tile crm-report-tile--{{$tone}}" aria-labelledby="crm-report-tile-title-{{$loop->index}}">
        <div class="crm-report-tile__top"><span class="crm-report-tile__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="{{$path}}"/></svg></span><span class="crm-report-tile__scope">{{$tone==='rose'?'Data quality':'All time'}}</span></div>
        <h2 id="crm-report-tile-title-{{$loop->index}}">{{$title}}</h2>
        <div class="crm-report-tile__value"><strong>{{number_format($value)}}</strong><svg class="crm-report-tile__watermark" viewBox="0 0 64 40" aria-hidden="true"><rect x="25" y="5" width="28" height="28" rx="8" fill="currentColor" opacity=".06"/><rect x="17" y="9" width="28" height="28" rx="8" fill="none" stroke="currentColor" stroke-width="1.4" opacity=".15"/><rect x="9" y="13" width="28" height="24" rx="7" fill="currentColor" opacity=".09"/></svg></div>
        <p class="crm-report-tile__description">{{$description}}</p>
        <footer class="crm-report-tile__footer"><span aria-hidden="true"></span>{{$context}}</footer>
    </article>
    @endforeach
</section>
