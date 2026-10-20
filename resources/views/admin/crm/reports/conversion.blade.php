@php
    $totals = $report[$scope];
    $totalRate = $reports->percent($totals['billing'], $totals['leads']);
    $stages = [
        ['leads','Leads','M7 21v-2a5 5 0 0 1 10 0v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
        ['applications','Applications','M6 3h9l4 4v14H6V3Zm8 0v5h5M9 12h7M9 16h5'],
        ['visas','Visas','M5 3h14v18H5V3Zm4 14h6M8 9h8M12 5v8M9 6l6 6M15 6l-6 6'],
        ['billing','Billing','M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6M9 16h3'],
    ];
    $metrics = [
        ['Total conversion','Lead → Billing','leads','billing','M5 16l5-5 4 3 5-8M14 6h5v5'],
        ['Level 1 → Level 2','Lead → Application','leads','applications',$stages[1][2]],
        ['Level 2 → Level 3','Application → Visa','applications','visas',$stages[2][2]],
        ['Level 3 → Level 4','Visa → Billing','visas','billing',$stages[3][2]],
    ];
@endphp
<section class="panel crm-conversion-panel" aria-labelledby="conversion-heading-{{$scope}}" data-conversion-scope="{{$scope}}">
    <header class="crm-conversion-heading">
        <div class="crm-conversion-heading__title">
            <span class="crm-conversion-period-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="{{$scope==='last12'?'M4 5h16v16H4V5Zm0 5h16M8 3v4M16 3v4M8 14h2m4 0h2M8 17h2':'M3 12a9 9 0 1 0 3-6M3 3v6h6M12 7v5l3 2'}}"/></svg>
            </span>
            <div><span class="crm-conversion-eyebrow">Cohort performance</span><h2 id="conversion-heading-{{$scope}}">{{$label}}</h2></div>
        </div>
        <span class="crm-conversion-period">{{$scope==='last12'?\Carbon\CarbonImmutable::now('Asia/Kolkata')->startOfMonth()->subMonths(11)->format('M Y').' – '.\Carbon\CarbonImmutable::now('Asia/Kolkata')->format('M Y'):'All recorded cohorts'}}</span>
    </header>

    <ol class="crm-stage-flow" aria-label="{{$label}} recorded lifecycle milestones">
        @foreach($stages as [$key,$name,$path])
        <li>
            <span class="crm-stage-flow__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="{{$path}}"/></svg></span>
            <span class="crm-stage-flow__text"><span>{{$name}}</span><strong>{{number_format($totals[$key])}}</strong></span>
            @unless($loop->last)<svg class="crm-stage-flow__connector" viewBox="0 0 60 20" aria-hidden="true"><path d="M2 10h52m-6-6 6 6-6 6"/></svg>@endunless
        </li>
        @endforeach
    </ol>

    <div class="crm-conversion-metrics">
        @foreach($metrics as [$metric,$journey,$from,$to,$path])
        @php
            $rate=$reports->percent($totals[$to],$totals[$from]);
            $arc=$rate===null?0:min(100,max(0,$rate));
        @endphp
        <article class="crm-conversion-metric {{$loop->first?'crm-conversion-metric--total':''}} {{$rate===null?'crm-conversion-metric--unavailable':''}}">
            <div class="crm-conversion-metric__top"><span class="crm-conversion-metric__label">{{$metric}}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{$path}}"/></svg></div>
            <div class="crm-conversion-gauge" data-conversion-rate="{{$rate===null?'unavailable':$rate}}">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                    <circle class="crm-conversion-gauge__track" cx="60" cy="60" r="49" fill="none" stroke-width="8"/>
                    @if($rate!==null && $rate>0)<circle class="crm-conversion-gauge__value" cx="60" cy="60" r="49" fill="none" stroke-width="8" stroke-linecap="round" pathLength="100" stroke-dasharray="{{$arc}} 100" transform="rotate(-90 60 60)"/>@endif
                    @if($rate===null)<path class="crm-conversion-gauge__empty" d="M48 60h24" stroke-width="3" stroke-linecap="round"/>@endif
                </svg>
                @if($rate!==null)<span class="crm-conversion-gauge__number"><strong>{{number_format($rate,$rate==floor($rate)?0:1)}}</strong><span>%</span></span>@endif
            </div>
            <h3>{{$journey}}</h3>
            <p class="crm-conversion-count"><strong>{{number_format($totals[$to])}}</strong><span>of {{number_format($totals[$from])}} profiles</span></p>
            @if($rate===null)
                <p class="crm-conversion-note">Not applicable — no denominator</p>
            @else
                <p class="crm-conversion-note"><span class="crm-conversion-exact">{{number_format($rate,2)}}%</span> recorded conversion</p>
                @if($rate>100)<p class="crm-conversion-note">Recorded milestones may include skipped stages; ring capped at 100%.</p>@endif
            @endif
        </article>
        @endforeach
    </div>
    <footer class="crm-conversion-footnote"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v1"/></svg><p>Unique student profiles · recorded milestones only. Missing stage history is not inferred.</p></footer>
</section>
