@include('mirror.partials.header', ['siteCms' => $cms])
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/'), 'mobileBackLabel' => 'Back to home'])

@php($isCompare = $tool['slug'] === 'compare-destinations')
@php($isEmi = $tool['slug'] === 'emi-calculator')

<style>
    :root{--tool-navy:#0e2145;--tool-red:#e31e24;--tool-orange:#f3951e;--tool-soft:#f4f7fb;--tool-muted:#667695;--tool-line:#dfe7f0}
    .tg-tool{overflow:clip;background:#fff;color:var(--tool-navy);font-family:'Plus Jakarta Sans',sans-serif}.tg-tool *{box-sizing:border-box}.tg-tool__wrap{width:min(1180px,calc(100% - 40px));margin-inline:auto}.tg-tool__section{padding:64px 0}.tg-tool__section--soft{background:var(--tool-soft)}
    .tg-tool__eyebrow{display:inline-flex;align-items:center;gap:10px;color:var(--tool-red);font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.tg-tool__eyebrow:before{width:26px;height:2px;background:currentColor;content:''}.tg-tool__title{max-width:650px;margin:12px 0 0;color:var(--tool-navy);font-size:clamp(30px,3.4vw,46px);line-height:1.06;letter-spacing:-.05em;font-weight:800;text-wrap:balance}.tg-tool__lead{max-width:600px;margin:15px 0 0;color:var(--tool-muted);font-size:14px;line-height:1.6}.tg-tool__button{display:inline-flex;min-height:44px;align-items:center;justify-content:center;gap:8px;padding:0 18px;border:0;border-radius:11px;background:var(--tool-red);color:#fff!important;font-size:12px;font-weight:800;text-decoration:none;box-shadow:0 9px 18px rgba(227,30,36,.17);transition:transform .2s ease,background .2s ease}.tg-tool__button:hover,.tg-tool__button:focus-visible{background:#c91820;color:#fff;transform:translateY(-2px)}.tg-tool__button--ghost{border:1px solid rgba(255,255,255,.32);background:transparent;box-shadow:none}.tg-tool__button--ghost:hover{background:#fff;color:var(--tool-navy)!important}
    .tg-tool__hero{padding:88px 0 18px;background:var(--tool-soft)}.tg-tool__hero-panel{position:relative;overflow:hidden;display:grid;grid-template-columns:minmax(0,1.1fr) minmax(280px,.9fr);align-items:center;gap:28px;padding:38px;border-radius:28px;background:var(--tool-navy);box-shadow:0 22px 50px rgba(14,33,69,.17)}.tg-tool__hero-panel:before{position:absolute;inset:0;opacity:.22;background-image:linear-gradient(rgba(255,255,255,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.12) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(90deg,#000 0%,transparent 90%);content:''}.tg-tool__hero-copy,.tg-tool__hero-art{position:relative;z-index:1}.tg-tool__hero .tg-tool__eyebrow{color:#ff858a}.tg-tool__hero .tg-tool__title{color:#fff}.tg-tool__hero .tg-tool__lead{color:rgba(255,255,255,.76)}.tg-tool__hero-actions{display:flex;flex-wrap:wrap;gap:9px;margin-top:19px}.tg-tool__hero-art{min-height:220px;border:1px solid rgba(255,255,255,.2);border-radius:21px;background-position:center;background-size:cover;background-repeat:no-repeat;display:grid;place-items:center;box-shadow:inset 0 0 0 999px rgba(8,25,57,.38)}.tg-tool__hero-art:before{width:145px;height:145px;border:27px solid rgba(255,255,255,.58);border-radius:50%;box-shadow:0 0 0 16px rgba(255,255,255,.08),0 0 0 34px rgba(255,255,255,.06);content:''}.tg-tool__hero-art--emi:before{border-color:rgba(243,149,30,.9);border-radius:22px;transform:rotate(8deg)}.tg-tool__hero-art--loan:before{border-color:rgba(255,255,255,.8);border-radius:22px 22px 60px 22px;transform:rotate(-7deg)}.tg-tool__hero-note{margin-top:15px;color:rgba(255,255,255,.56);font-size:11px;line-height:1.5}
    .tg-tool__stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:18px}.tg-tool__stat{padding:13px 14px;border:1px solid rgba(255,255,255,.15);border-radius:13px;background:rgba(255,255,255,.07)}.tg-tool__stat strong{display:block;color:#fff;font-size:20px;letter-spacing:-.03em}.tg-tool__stat span{display:block;margin-top:3px;color:rgba(255,255,255,.62);font-size:10px}.tg-tool__section-head{display:flex;align-items:end;justify-content:space-between;gap:24px;margin-bottom:22px}.tg-tool__section-head h2{margin:8px 0 0;color:var(--tool-navy);font-size:clamp(28px,3.4vw,40px);line-height:1.08;letter-spacing:-.045em}.tg-tool__section-head p{max-width:530px;margin:0;color:var(--tool-muted);font-size:14px;line-height:1.6}
    .tg-tool__gallery{padding:0 0 28px;background:var(--tool-soft)}.tg-tool__gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}.tg-tool__gallery-card{position:relative;min-height:145px;overflow:hidden;border-radius:18px;background:#dce4ee}.tg-tool__gallery-card img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.tg-tool__gallery-card:hover img{transform:scale(1.05)}.tg-tool__gallery-card:after{position:absolute;inset:35% 0 0;background:linear-gradient(180deg,transparent,rgba(7,20,44,.86));content:''}.tg-tool__gallery-card span{position:absolute;z-index:1;right:12px;bottom:11px;left:12px;color:#fff;font-size:12px;font-weight:800}
    .tg-tool__filter{width:min(100%,360px);margin:0 0 15px auto}.tg-tool__filter input{width:100%;height:48px;padding:0 15px;border:1px solid var(--tool-line);border-radius:12px;background:#fff;color:var(--tool-navy);font:inherit}.tg-tool__filter input:focus-visible{outline:3px solid rgba(227,30,36,.2);outline-offset:2px}
    .tg-tool__card{border:1px solid var(--tool-line);border-radius:24px;background:#fff;box-shadow:0 14px 36px rgba(14,33,69,.07)}.tg-tool__table-wrap{overflow:auto}.tg-tool__table{width:100%;min-width:760px;border-collapse:collapse}.tg-tool__table th,.tg-tool__table td{padding:18px 20px;border-bottom:1px solid var(--tool-line);text-align:left;white-space:nowrap}.tg-tool__table th{background:#f8fafc;color:#71819b;font-size:11px;letter-spacing:.1em;text-transform:uppercase}.tg-tool__table td{color:var(--tool-muted);font-size:14px}.tg-tool__table td:first-child{color:var(--tool-navy);font-weight:800}.tg-tool__table tr:last-child td{border-bottom:0}.tg-tool__fit{display:inline-flex;padding:7px 10px;border-radius:999px;background:#fff1ef;color:var(--tool-red);font-size:11px;font-weight:800}
    .tg-tool__factor-grid,.tg-tool__lender-grid,.tg-tool__tips-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}.tg-tool__factor,.tg-tool__lender,.tg-tool__tip{padding:24px;border:1px solid var(--tool-line);border-radius:20px;background:#fff}.tg-tool__factor h3,.tg-tool__lender h3,.tg-tool__tip h3{margin:14px 0 8px;color:var(--tool-navy);font-size:17px;line-height:1.3}.tg-tool__factor p,.tg-tool__lender p,.tg-tool__tip p{margin:0;color:var(--tool-muted);font-size:13px;line-height:1.65}.tg-tool__number{display:grid;width:40px;height:40px;place-items:center;border-radius:13px;background:#fff0f0;color:var(--tool-red);font-weight:800}.tg-tool__cta{display:flex;align-items:center;justify-content:space-between;gap:30px;padding:36px 43px;border-radius:28px;background:var(--tool-red);color:#fff}.tg-tool__cta h2{max-width:620px;margin:0 0 8px;color:#fff;font-size:clamp(24px,2.7vw,34px);line-height:1.1;letter-spacing:-.04em}.tg-tool__cta p{max-width:720px;margin:0;color:rgba(255,255,255,.78);font-size:14px;line-height:1.55}
    .tg-emi{display:grid;grid-template-columns:minmax(0,.95fr) minmax(300px,1.05fr);gap:22px}.tg-emi__form,.tg-emi__result{padding:30px}.tg-emi__form h2,.tg-emi__result h2{margin:0;color:var(--tool-navy);font-size:25px;letter-spacing:-.03em}.tg-field{margin-top:20px}.tg-field label{display:flex;justify-content:space-between;gap:10px;color:var(--tool-navy);font-size:13px;font-weight:700}.tg-field output{color:var(--tool-red);font-weight:800}.tg-field input{width:100%;margin-top:10px;border:1px solid var(--tool-line);border-radius:12px;background:#fff;color:var(--tool-navy);font:inherit;padding:14px 15px}.tg-field input:focus-visible,.tg-tool :is(a,button):focus-visible{outline:3px solid rgba(227,30,36,.28);outline-offset:3px}.tg-field input[type=range]{padding:0;accent-color:var(--tool-red)}.tg-emi__result{display:flex;flex-direction:column;justify-content:center;background:linear-gradient(145deg,#0e2145,#193564);color:#fff}.tg-emi__result h2{color:#fff}.tg-emi__value{margin-top:22px;color:#fff;font-size:clamp(44px,6vw,72px);line-height:1;font-weight:800;letter-spacing:-.06em}.tg-emi__sub{margin-top:10px;color:rgba(255,255,255,.66);font-size:13px}.tg-emi__breakdown{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:26px}.tg-emi__breakdown div{padding:15px;border:1px solid rgba(255,255,255,.15);border-radius:14px;background:rgba(255,255,255,.07)}.tg-emi__breakdown strong{display:block;color:#fff;font-size:18px}.tg-emi__breakdown span{display:block;margin-top:3px;color:rgba(255,255,255,.6);font-size:11px}.tg-emi__disclaimer{margin-top:20px;color:rgba(255,255,255,.52);font-size:11px;line-height:1.55}
    .tg-check-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.tg-check{padding:27px;border:1px solid var(--tool-line);border-radius:22px;background:#fff}.tg-check h3{margin:0;color:var(--tool-navy);font-size:19px}.tg-check ul{display:grid;gap:11px;margin:19px 0 0;padding:0;list-style:none}.tg-check li{display:flex;gap:10px;color:var(--tool-muted);font-size:13px;line-height:1.5}.tg-check li:before{flex:0 0 auto;width:19px;height:19px;border-radius:50%;background:#e9f8f0;color:#19925a;content:'✓';font-size:12px;font-weight:800;text-align:center;line-height:19px}.tg-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:15px}.tg-step{padding:24px;border:1px solid var(--tool-line);border-radius:20px;background:#fff}.tg-step__num{color:var(--tool-red);font-size:12px;font-weight:800;letter-spacing:.1em}.tg-step h3{margin:13px 0 8px;font-size:17px}.tg-step p{margin:0;color:var(--tool-muted);font-size:13px;line-height:1.65}.tg-factor-row{display:flex;flex-wrap:wrap;gap:10px}.tg-factor-pill{padding:11px 14px;border-radius:999px;background:#fff;border:1px solid var(--tool-line);color:var(--tool-navy);font-size:13px;font-weight:700}
    @media(max-width:991px){.tg-tool__hero-panel{grid-template-columns:1fr;padding:44px}.tg-tool__hero-art{min-height:230px}.tg-tool__factor-grid,.tg-tool__lender-grid,.tg-tool__tips-grid{grid-template-columns:repeat(2,1fr)}.tg-check-grid{grid-template-columns:1fr}.tg-steps{grid-template-columns:repeat(2,1fr)}.tg-tool__section-head{align-items:start;flex-direction:column}.tg-tool__cta{align-items:flex-start;flex-direction:column}}
    @media(max-width:767px){.tg-tool__wrap{width:min(100% - 24px,1180px)}.tg-tool__section{padding:52px 0}.tg-tool__hero{padding:76px 0 14px}.tg-tool__hero-panel{padding:25px;border-radius:23px}.tg-tool__title{font-size:30px}.tg-tool__lead{font-size:14px}.tg-tool__hero-art{min-height:155px}.tg-tool__hero-art:before{width:105px;height:105px;border-width:21px}.tg-tool__stats{gap:7px}.tg-tool__stat{padding:11px 9px}.tg-tool__stat strong{font-size:17px}.tg-tool__stat span{font-size:9px}.tg-tool__gallery{padding-bottom:18px}.tg-tool__gallery-grid{grid-template-columns:repeat(2,1fr);gap:9px}.tg-tool__gallery-card{min-height:108px}.tg-tool__factor-grid,.tg-tool__lender-grid,.tg-tool__tips-grid,.tg-steps{grid-template-columns:1fr}.tg-emi{grid-template-columns:1fr}.tg-emi__form,.tg-emi__result{padding:23px}.tg-emi__value{font-size:50px}.tg-tool__cta{gap:18px;padding:26px 22px}.tg-tool__cta h2{font-size:25px}.tg-tool__button{width:100%}}
    @media(prefers-reduced-motion:reduce){.tg-tool *{scroll-behavior:auto!important;transition:none!important}.tg-tool__button:hover{transform:none}}
    /* Foreground hero subject: layered above the destination image and ring for depth. */
    .tg-tool__hero-art{position:relative;overflow:visible;isolation:isolate;box-shadow:inset 0 0 0 999px rgba(8,25,57,.38),0 16px 30px rgba(3,13,32,.18)}
    .tg-tool__hero-art:before{position:relative;z-index:1}
    .tg-tool__hero-foreground{position:absolute;z-index:2;right:-5%;bottom:-32%;width:min(110%,500px);max-height:210%;object-fit:contain;object-position:center bottom;filter:drop-shadow(0 22px 22px rgba(3,13,32,.4));pointer-events:none;user-select:none}
    .tg-tool__hero-art--compare .tg-tool__hero-foreground{right:-7%;bottom:-75%;width:min(120%,550px);max-height:170%}
    .tg-tool__title{font-size:clamp(29px,3vw,42px);line-height:1.08;letter-spacing:-.045em}
    .tg-tool__hero-art--emi .tg-tool__hero-foreground{right:-1%;bottom:-56%;width:min(58%,345px);max-height:none}
    .tg-tool__hero-art--loan .tg-tool__hero-foreground{right:-7%;bottom:-67%;width:min(82%,400px);max-height:none}
    @media(max-width:767px){.tg-tool__title{font-size:28px}.tg-tool__hero-foreground{right:-8%;bottom:-18%;width:min(112%,340px);max-height:180%}.tg-tool__hero-art--compare .tg-tool__hero-foreground{right:-10%;bottom:-70%;width:min(124%,370px);max-height:170%}.tg-tool__hero-art--emi .tg-tool__hero-foreground{right:-4%;bottom:-23%;width:min(62%,220px);max-height:none}.tg-tool__hero-art--loan .tg-tool__hero-foreground{right:-7%;bottom:-42%;width:min(76%,260px);max-height:none}}
</style>

<main class="tg-tool">
    <section class="tg-tool__hero">
        <div class="tg-tool__wrap">
            <div class="tg-tool__hero-panel">
                <div class="tg-tool__hero-copy">
                    <span class="tg-tool__eyebrow">{{ $tool['eyebrow'] }}</span>
                    <h1 class="tg-tool__title">{{ $tool['title'] }}</h1>
                    <p class="tg-tool__lead">{{ $tool['copy'] }}</p>
                    <div class="tg-tool__hero-actions">
                        @if($isCompare)<a class="tg-tool__button" href="#comparison">Compare countries <span aria-hidden="true">↓</span></a>@elseif($isEmi)<a class="tg-tool__button" href="#calculator">Calculate my EMI <span aria-hidden="true">↓</span></a>@else<a class="tg-tool__button" href="{{ url('/contact#enquiry') }}">Talk to a loan advisor <span aria-hidden="true">→</span></a>@endif
                        <a class="tg-tool__button tg-tool__button--ghost" href="{{ url('/contact#enquiry') }}">Book free counselling</a>
                    </div>
                    @if(isset($tool['hero_note']))<p class="tg-tool__hero-note">{{ $tool['hero_note'] }}</p>@endif
                    <div class="tg-tool__stats">@foreach($tool['stats'] as $stat)<div class="tg-tool__stat"><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>@endforeach</div>
                </div>
                <div class="tg-tool__hero-art {{ $isCompare ? 'tg-tool__hero-art--compare' : ($isEmi ? 'tg-tool__hero-art--emi' : ($tool['slug'] === 'education-loans' ? 'tg-tool__hero-art--loan' : '')) }}" style="background-image:linear-gradient(145deg,rgba(14,33,69,.18),rgba(8,25,57,.66)),url('{{ asset($tool['hero_image']) }}')" aria-hidden="true">
                    @if(isset($tool['hero_foreground']))<img class="tg-tool__hero-foreground" src="{{ asset($tool['hero_foreground']) }}" alt="" aria-hidden="true">@endif
                </div>
            </div>
        </div>
    </section>

    <section class="tg-tool__gallery" aria-label="Study abroad planning moments">
        <div class="tg-tool__wrap">
            <div class="tg-tool__gallery-grid">
                @foreach($tool['gallery'] as $image)
                    <div class="tg-tool__gallery-card"><img src="{{ asset($image['image']) }}" alt="{{ $image['alt'] }}" loading="lazy"><span>{{ $image['label'] }}</span></div>
                @endforeach
            </div>
        </div>
    </section>

    @if($isCompare)
        <section class="tg-tool__section" id="comparison"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">At a glance</span><h2>Compare the numbers that shape your decision.</h2></div><p>Use these indicative yearly estimates as a starting point. Your actual budget depends on university, city, course level, lifestyle and currency movement.</p></div><div class="tg-tool__filter"><label class="sr-only" for="destination-filter">Filter destinations</label><input id="destination-filter" type="search" placeholder="Filter destinations" data-destination-filter></div><div class="tg-tool__card tg-tool__table-wrap"><table class="tg-tool__table"><thead><tr><th>Destination</th><th>Yearly total</th><th>Tuition</th><th>Living</th><th>Misc. p.a.</th><th>Known for</th></tr></thead><tbody>@foreach($tool['rows'] as $row)<tr data-destination-row data-destination-name="{{ strtolower($row['country'].' '.$row['fit']) }}"><td>{{ $row['country'] }}</td><td><strong>{{ $row['total'] }}</strong></td><td>{{ $row['tuition'] }}</td><td>{{ $row['living'] }}</td><td>{{ $row['misc'] }}</td><td><span class="tg-tool__fit">{{ $row['fit'] }}</span></td></tr>@endforeach</tbody></table></div><p data-destination-empty hidden style="margin:18px 0 0;color:var(--tool-muted)">No destination matches that search.</p></div></section>
        <section class="tg-tool__section tg-tool__section--soft"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">Choose beyond cost</span><h2>Four questions to ask before you shortlist.</h2></div></div><div class="tg-tool__factor-grid">@foreach($tool['factors'] as $factor)<article class="tg-tool__factor"><span class="tg-tool__number">{{ $loop->iteration }}</span><h3>{{ $factor['title'] }}</h3><p>{{ $factor['copy'] }}</p></article>@endforeach</div></div></section>
    @elseif($isEmi)
        <section class="tg-tool__section" id="calculator"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">Estimate in seconds</span><h2>Make the monthly number easier to understand.</h2></div><p>This calculator uses the standard reducing-balance EMI formula. Change any input and the estimate updates instantly.</p></div><div class="tg-emi"><form class="tg-tool__card tg-emi__form" data-emi-form onsubmit="return false"><h2>Loan assumptions</h2><div class="tg-field"><label for="emi-principal">Loan amount <output data-emi-principal-output>₹20,00,000</output></label><input id="emi-principal" type="range" min="100000" max="10000000" step="50000" value="2000000" data-emi-principal></div><div class="tg-field"><label for="emi-rate">Interest rate <output data-emi-rate-output>10.5%</output></label><input id="emi-rate" type="range" min="1" max="20" step="0.1" value="10.5" data-emi-rate></div><div class="tg-field"><label for="emi-tenure">Repayment period <output data-emi-tenure-output>10 years</output></label><input id="emi-tenure" type="range" min="1" max="20" step="1" value="10" data-emi-tenure></div><p style="margin:22px 0 0;color:var(--tool-muted);font-size:12px;line-height:1.6">Try a shorter tenure to reduce total interest, or a longer tenure to keep monthly repayment manageable.</p></form><div class="tg-tool__card tg-emi__result" aria-live="polite"><h2>Indicative monthly EMI</h2><div class="tg-emi__value" data-emi-value>₹26,987</div><div class="tg-emi__sub">per month for the selected assumptions</div><div class="tg-emi__breakdown"><div><strong data-emi-interest>₹12,38,441</strong><span>Total interest</span></div><div><strong data-emi-total>₹32,38,441</strong><span>Total repayment</span></div></div><p class="tg-emi__disclaimer">This is an estimate, not a sanction or offer. Lenders may calculate interest, moratorium and fees differently. Confirm the final schedule with your financial advisor.</p></div></div></div></section>
        <section class="tg-tool__section tg-tool__section--soft"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">Before you borrow</span><h2>Three habits that protect your budget.</h2></div></div><div class="tg-tool__tips-grid">@foreach($tool['tips'] as $tip)<article class="tg-tool__tip"><span class="tg-tool__number">{{ $loop->iteration }}</span><h3>{{ $tip['title'] }}</h3><p>{{ $tip['copy'] }}</p></article>@endforeach</div></div></section>
    @else
        <section class="tg-tool__section" id="lender-types"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">Lender landscape</span><h2>There is more than one way to fund your study plan.</h2></div><p>Our advisors help you compare the trade-offs between speed, eligibility, collateral, repayment flexibility and the total cost of borrowing.</p></div><div class="tg-tool__lender-grid">@foreach($tool['lender_types'] as $item)<article class="tg-tool__lender"><span class="tg-tool__number">{{ $loop->iteration }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['copy'] }}</p></article>@endforeach</div></div></section>
        <section class="tg-tool__section"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">What lenders assess</span><h2>Clarity now makes the application smoother later.</h2></div></div><div class="tg-tool__tips-grid">@foreach($tool['loan_notes'] as $note)<article class="tg-tool__tip"><span class="tg-tool__number">{{ $loop->iteration }}</span><h3>{{ $note['title'] }}</h3><p>{{ $note['copy'] }}</p></article>@endforeach</div></div></section>
        <section class="tg-tool__section tg-tool__section--soft" id="documents"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">Get your file ready</span><h2>A clean document checklist speeds up every conversation.</h2></div></div><div class="tg-check-grid">@foreach($tool['checklists'] as $check)<article class="tg-check"><h3>{{ $check['title'] }}</h3><ul>@foreach($check['items'] as $item)<li>{{ $item }}</li>@endforeach</ul></article>@endforeach</div></div></section>
        <section class="tg-tool__section" id="loan-process"><div class="tg-tool__wrap"><div class="tg-tool__section-head"><div><span class="tg-tool__eyebrow">The process</span><h2>From first conversation to fee-day disbursal.</h2></div></div><div class="tg-steps">@foreach($tool['steps'] as $step)<article class="tg-step"><span class="tg-step__num">{{ $step['number'] }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['copy'] }}</p></article>@endforeach</div><div style="margin-top:32px"><h3 style="margin:0 0 14px;font-size:18px">Compare these loan factors</h3><div class="tg-factor-row">@foreach($tool['factors'] as $factor)<span class="tg-factor-pill">{{ $factor }}</span>@endforeach</div></div></div></section>
    @endif

    <section class="tg-tool__section"><div class="tg-tool__wrap"><div class="tg-tool__cta"><div><h2>Make your next decision with a real plan.</h2><p>Share your destination, course and budget. Our Indore team will help you turn this estimate into a realistic shortlist.</p></div><a class="tg-tool__button tg-tool__button--ghost" href="{{ url('/contact#enquiry') }}">Start a conversation <span aria-hidden="true">→</span></a></div></div></section>
</main>

@if($isEmi)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-emi-form]');
    if (!form) return;
    const principal = form.querySelector('[data-emi-principal]');
    const rate = form.querySelector('[data-emi-rate]');
    const tenure = form.querySelector('[data-emi-tenure]');
    const money = value => '₹' + Math.round(value).toLocaleString('en-IN');
    const update = () => {
        const p = Number(principal.value), annual = Number(rate.value), years = Number(tenure.value);
        const months = years * 12, monthlyRate = annual / 1200;
        const emi = monthlyRate === 0 ? p / months : p * monthlyRate * Math.pow(1 + monthlyRate, months) / (Math.pow(1 + monthlyRate, months) - 1);
        const total = emi * months;
        form.querySelector('[data-emi-principal-output]').textContent = money(p);
        form.querySelector('[data-emi-rate-output]').textContent = annual.toFixed(1) + '%';
        form.querySelector('[data-emi-tenure-output]').textContent = years + (years === 1 ? ' year' : ' years');
        document.querySelector('[data-emi-value]').textContent = money(emi);
        document.querySelector('[data-emi-interest]').textContent = money(total - p);
        document.querySelector('[data-emi-total]').textContent = money(total);
    };
    [principal, rate, tenure].forEach(input => input.addEventListener('input', update));
    update();
});
</script>
@endif

@if($isCompare)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('[data-destination-filter]');
    if (!input) return;
    const rows = [...document.querySelectorAll('[data-destination-row]')];
    const empty = document.querySelector('[data-destination-empty]');
    input.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let shown = 0;
        rows.forEach(row => { const visible = !query || row.dataset.destinationName.includes(query); row.hidden = !visible; if (visible) shown++; });
        empty.hidden = shown !== 0;
    });
});
</script>
@endif

@include('mirror.partials.footer', ['siteCms' => $cms])
