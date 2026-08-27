@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => 'destinations', 'mobileBackLabel' => 'Back to destinations'])

@php
    $slug = $destination['slug'];
    $name = $destination['name'];
    $detailUrl = url()->current();
@endphp

<style>
    :root { --cd-navy:#0e2145; --cd-red:#E31E24; --cd-orange:#F3951E; --cd-ink:#13274a; --cd-muted:#5b6d89; --cd-soft:#f4f7fb; --cd-border:#dfe7f1; }
    .cd-page { overflow: clip; background:#fff; color:var(--cd-ink); }
    .country-detail-page #themeHeaderSticky.sticky { position:relative!important; top:-42px!important; width:auto!important; animation:none!important; }
    .cd-container { width:min(1320px, calc(100% - 40px)); margin-inline:auto; }
    .cd-narrow { width:min(1060px, calc(100% - 40px)); margin-inline:auto; }
    .cd-section { padding:92px 0; scroll-margin-top:92px; }
    .cd-section--soft { background:var(--cd-soft); }
    .cd-kicker { display:inline-flex; align-items:center; gap:10px; color:var(--cd-red); font-size:13px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .cd-kicker::before { width:28px; height:2px; background:currentColor; content:''; }
    .cd-heading { max-width:780px; margin:14px 0 0; color:var(--cd-navy); font-size:clamp(34px,4vw,48px); line-height:1.12; font-weight:800; text-wrap:balance; }
    .cd-lead { max-width:760px; margin:16px 0 0; color:var(--cd-muted); font-size:17px; line-height:1.75; }
    .cd-center { text-align:center; }
    .cd-center .cd-heading,.cd-center .cd-lead { margin-inline:auto; }

    .cd-hero { padding:132px 0 0; background:var(--cd-soft); }
    .cd-hero__shell { position:relative; min-height:590px; overflow:hidden; display:flex; align-items:flex-end; border-radius:36px; background:var(--cd-navy); box-shadow:0 26px 70px rgba(14,33,69,.22); }
    .cd-hero__image,.cd-hero__overlay { position:absolute; inset:0; width:100%; height:100%; }
    .cd-hero__image { object-fit:cover; object-position:var(--hero-position, center); }
    .cd-hero__overlay { background:linear-gradient(90deg,rgba(5,17,39,.96) 0%,rgba(5,17,39,.82) 45%,rgba(5,17,39,.22) 100%); }
    .cd-hero__content { position:relative; z-index:2; width:min(660px,calc(100% - 380px)); padding:64px; }
    .cd-breadcrumb { display:flex; flex-wrap:wrap; align-items:center; gap:8px; color:rgba(255,255,255,.68); font-size:13px; }
    .cd-breadcrumb a { color:#fff; }
    .cd-label { display:inline-flex; align-items:center; gap:10px; margin-top:30px; padding:8px 13px; border:1px solid rgba(255,255,255,.25); border-radius:999px; color:#fff; background:rgba(14,33,69,.45); backdrop-filter:blur(12px); }
    .cd-label img { width:auto!important; height:22px!important; border-radius:3px; }
    .cd-hero h1 { margin:18px 0 0; color:#fff; font-size:clamp(44px,5vw,66px); line-height:1.02; font-weight:800; }
    .cd-hero h1 span { display:block; color:#ff5a60; }
    .cd-hero__copy { max-width:680px; margin:18px 0 0; color:rgba(255,255,255,.84); font-size:17px; line-height:1.7; }
    .cd-actions { display:flex; flex-wrap:wrap; gap:14px; margin-top:28px; }
    .cd-button { display:inline-flex; min-height:52px; align-items:center; justify-content:center; padding:0 24px; border:1px solid transparent; border-radius:13px; background:var(--cd-red); color:#fff!important; font-weight:800; transition:background-color .2s ease,transform .2s ease,border-color .2s ease; }
    .cd-button:hover,.cd-button:focus-visible { background:var(--cd-orange); transform:translateY(-2px); }
    .cd-button--ghost { border-color:rgba(255,255,255,.42); background:rgba(255,255,255,.06); }
    .cd-button--ghost:hover,.cd-button--ghost:focus-visible { border-color:var(--cd-orange); background:var(--cd-orange); }
    .cd-button:focus-visible,.cd-anchor a:focus-visible,.cd-faq summary:focus-visible,.cd-form :is(input,select,textarea):focus-visible { outline:3px solid rgba(243,149,30,.45); outline-offset:3px; }

    .cd-stats { position:relative; z-index:4; display:grid; grid-template-columns:repeat(4,1fr); gap:0; margin:-1px 32px 0; border:1px solid var(--cd-border); border-radius:0 0 26px 26px; background:#fff; box-shadow:0 18px 45px rgba(14,33,69,.08); }
    .cd-stat { min-width:0; padding:24px 26px; border-right:1px solid var(--cd-border); }
    .cd-stat:last-child { border-right:0; }
    .cd-stat strong { display:block; color:var(--cd-navy); font-size:24px; line-height:1.2; }
    .cd-stat span { display:block; margin-top:6px; color:var(--cd-muted); font-size:13px; line-height:1.45; }

    .cd-anchor { position:sticky; z-index:100; top:0; padding:12px 0; border-bottom:1px solid rgba(221,229,239,.9); background:rgba(244,247,251,.92); box-shadow:0 12px 28px rgba(14,33,69,.07); backdrop-filter:blur(16px); }
    .cd-anchor__inner { display:flex; gap:8px; overflow-x:auto; padding:8px; border:1px solid var(--cd-border); border-radius:18px; background:#fff; scrollbar-width:none; }
    .cd-anchor__inner::-webkit-scrollbar { display:none; }
    .cd-anchor a { flex:0 0 auto; min-height:44px; padding:12px 18px; border-radius:11px; color:#465a78; font-size:14px; font-weight:800; transition:color .2s ease,background-color .2s ease; }
    .cd-anchor a:hover,.cd-anchor a.is-active { color:#fff; background:var(--cd-orange); }

    .cd-overview { display:grid; grid-template-columns:1.05fr .95fr; align-items:center; gap:70px; }
    .cd-overview__copy p { color:var(--cd-muted); font-size:16px; line-height:1.8; }
    .cd-overview__copy p+p { margin-top:18px; }
    .cd-visual { position:relative; min-height:520px; }
    .cd-visual__main { position:absolute; inset:0 70px 50px 0; overflow:hidden; border-radius:30px; box-shadow:0 22px 52px rgba(14,33,69,.16); }
    .cd-visual__main img { width:100%; height:100%; object-fit:cover; }
    .cd-visual__card { position:absolute; right:0; bottom:0; width:52%; min-height:220px; overflow:hidden; border:9px solid #fff; border-radius:26px; background:var(--cd-navy); box-shadow:0 20px 44px rgba(14,33,69,.22); }
    .cd-visual__card img { width:100%; height:220px; object-fit:cover; opacity:.78; }
    .cd-visual__badge { position:absolute; left:22px; bottom:20px; padding:9px 13px; border-radius:999px; color:#fff; background:rgba(14,33,69,.88); font-size:12px; font-weight:800; }
    .cd-visual__mini { position:absolute; top:28px; right:0; width:42%; height:178px; overflow:hidden; border:8px solid #fff; border-radius:24px; box-shadow:0 18px 42px rgba(14,33,69,.18); }
    .cd-visual__mini img { width:100%; height:100%; object-fit:cover; }

    .cd-benefits { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-top:42px; }
    .cd-benefit { padding:28px; border:1px solid var(--cd-border); border-radius:22px; background:#fff; box-shadow:0 12px 32px rgba(14,33,69,.05); transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
    .cd-benefit:hover { transform:translateY(-4px); border-color:rgba(243,149,30,.5); box-shadow:0 18px 40px rgba(14,33,69,.1); }
    .cd-benefit__icon { display:grid; width:48px; height:48px; place-items:center; border-radius:15px; color:var(--cd-red); background:#fdebed; }
    .cd-benefit__icon svg { width:24px; height:24px; fill:none; stroke:currentColor; stroke-width:1.9; }
    .cd-benefit h3 { margin:18px 0 0; color:var(--cd-navy); font-size:19px; }
    .cd-benefit p { margin:10px 0 0; color:var(--cd-muted); line-height:1.65; }
    .cd-life-gallery { display:grid; grid-template-columns:1.15fr .85fr .85fr; gap:16px; margin-top:50px; }
    .cd-life-card { position:relative; min-height:280px; overflow:hidden; border-radius:24px; background:var(--cd-navy); }
    .cd-life-card:first-child { min-height:360px; }
    .cd-life-card img { width:100%; height:100%; object-fit:cover; transition:transform .35s ease; }
    .cd-life-card:hover img { transform:scale(1.035); }
    .cd-life-card::after { position:absolute; inset:auto 0 0; height:55%; background:linear-gradient(transparent,rgba(5,17,39,.82)); content:''; }
    .cd-life-card figcaption { position:absolute; z-index:2; right:20px; bottom:18px; left:20px; color:#fff; font-size:16px; font-weight:800; }

    .cd-facts { display:grid; grid-template-columns:.85fr 1.15fr; gap:24px; margin-top:42px; }
    .cd-fact-image { min-height:410px; overflow:hidden; border-radius:28px; }
    .cd-fact-image img { width:100%; height:100%; object-fit:cover; }
    .cd-fact-panel { display:grid; grid-template-columns:repeat(3,1fr); align-content:center; gap:14px; padding:34px; border-radius:28px; color:#fff; background-color:var(--cd-navy); background-image:radial-gradient(rgba(255,255,255,.08) 1px,transparent 1px); background-size:14px 14px; }
    .cd-fact-panel__intro { grid-column:1/-1; margin-bottom:14px; }
    .cd-fact-panel__intro h3 { color:#fff; font-size:30px; }
    .cd-fact-panel__intro p { margin-top:10px; color:rgba(255,255,255,.7); line-height:1.65; }
    .cd-fact { padding:20px; border:1px solid rgba(255,255,255,.14); border-radius:18px; background:rgba(255,255,255,.07); }
    .cd-fact span { display:block; color:rgba(255,255,255,.62); font-size:12px; text-transform:uppercase; letter-spacing:.08em; }
    .cd-fact strong { display:block; margin-top:8px; color:#fff; font-size:20px; line-height:1.3; }

    .cd-journey-wrap { margin-top:44px; padding:34px; border-radius:32px; background:var(--cd-navy); background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px); background-size:80px 80px; }
    .cd-journey { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
    .cd-step { position:relative; min-height:210px; padding:24px; border:1px solid rgba(255,255,255,.16); border-radius:20px; background:rgba(255,255,255,.07); }
    .cd-step:not(:nth-child(4n))::after { position:absolute; z-index:2; top:34px; right:-17px; width:18px; border-top:2px dashed rgba(243,149,30,.8); content:''; }
    .cd-step__top { display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .cd-step__number { display:grid; width:42px; height:42px; place-items:center; border:6px solid rgba(255,255,255,.09); border-radius:50%; color:#fff; background:var(--cd-red); font-size:12px; font-weight:800; background-clip:padding-box; }
    .cd-step__stage { color:#ffb45a; font-size:10px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .cd-step h3 { margin:18px 0 0; color:#fff; font-size:17px; }
    .cd-step p { margin:9px 0 0; color:rgba(255,255,255,.68); font-size:13px; line-height:1.65; }

    .cd-requirements { display:grid; grid-template-columns:1fr .86fr; gap:28px; margin-top:42px; }
    .cd-panel { padding:36px; border:1px solid var(--cd-border); border-radius:26px; background:#fff; box-shadow:0 14px 38px rgba(14,33,69,.06); }
    .cd-panel h3 { color:var(--cd-navy); font-size:26px; }
    .cd-checks { display:grid; grid-template-columns:1fr 1fr; gap:14px 22px; margin-top:24px; }
    .cd-checks li { display:flex; gap:10px; color:#526782; line-height:1.55; }
    .cd-check { flex:0 0 24px; display:grid; width:24px; height:24px; place-items:center; border-radius:50%; color:#fff; background:#2fbf87; font-size:12px; font-weight:900; }
    .cd-visa { position:relative; overflow:hidden; color:#fff; background:var(--cd-navy); }
    .cd-visa::after { position:absolute; right:-80px; bottom:-90px; width:240px; height:240px; border:46px solid rgba(255,255,255,.05); border-radius:50%; content:''; }
    .cd-visa h3 { color:#fff; }
    .cd-visa p { position:relative; z-index:2; margin-top:16px; color:rgba(255,255,255,.76); line-height:1.75; }
    .cd-note { margin-top:18px; padding:13px 15px; border-left:3px solid var(--cd-orange); border-radius:8px; color:rgba(255,255,255,.7); background:rgba(255,255,255,.07); font-size:12px; line-height:1.55; }

    .cd-costs { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-top:40px; }
    .cd-cost { min-height:164px; padding:25px; border:1px solid var(--cd-border); border-radius:21px; background:#fff; }
    .cd-cost span { color:var(--cd-muted); font-size:13px; font-weight:700; }
    .cd-cost strong { display:block; margin-top:14px; color:var(--cd-navy); font-size:21px; line-height:1.35; }
    .cd-cost small { display:block; margin-top:10px; color:#8896aa; line-height:1.5; }
    .cd-career-intakes { display:grid; grid-template-columns:1fr 1fr .9fr; gap:22px; margin-top:30px; }
    .cd-careers { display:flex; flex-wrap:wrap; gap:10px; margin-top:22px; }
    .cd-careers span { padding:11px 15px; border:1px solid #dce5ef; border-radius:999px; color:#425877; background:var(--cd-soft); font-size:13px; font-weight:700; }
    .cd-intakes { display:grid; gap:12px; margin-top:22px; }
    .cd-intake { padding:18px 20px; border-left:4px solid var(--cd-red); border-radius:12px; background:var(--cd-soft); }
    .cd-intake strong { display:block; color:var(--cd-navy); }
    .cd-intake span { display:block; margin-top:5px; color:var(--cd-muted); font-size:13px; line-height:1.5; }
    .cd-career-media { position:relative; min-height:360px; overflow:hidden; border-radius:26px; }
    .cd-career-media img { width:100%; height:100%; object-fit:cover; }
    .cd-career-media figcaption { position:absolute; right:18px; bottom:18px; left:18px; padding:13px 15px; border-radius:14px; color:#fff; background:rgba(14,33,69,.86); font-size:13px; font-weight:800; backdrop-filter:blur(10px); }

    .cd-faq { display:grid; gap:12px; margin-top:38px; }
    .cd-faq details { overflow:hidden; border:1px solid var(--cd-border); border-radius:17px; background:#fff; }
    .cd-faq summary { position:relative; padding:22px 56px 22px 24px; color:var(--cd-navy); font-weight:800; cursor:pointer; list-style:none; }
    .cd-faq summary::-webkit-details-marker { display:none; }
    .cd-faq summary::after { position:absolute; top:20px; right:22px; content:'+'; color:var(--cd-red); font-size:24px; }
    .cd-faq details[open] summary::after { content:'−'; }
    .cd-faq p { padding:0 24px 22px; color:var(--cd-muted); line-height:1.7; }

    .cd-contact { display:grid; grid-template-columns:.82fr 1.18fr; overflow:hidden; border-radius:32px; background:var(--cd-navy); box-shadow:0 24px 60px rgba(14,33,69,.2); }
    .cd-contact__intro { position:relative; padding:48px; color:#fff; background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px); background-size:14px 14px; }
    .cd-contact__intro h2 { margin-top:14px; color:#fff; font-size:38px; line-height:1.15; }
    .cd-contact__intro p { margin-top:16px; color:rgba(255,255,255,.72); line-height:1.7; }
    .cd-contact__meta { display:grid; gap:10px; margin-top:26px; }
    .cd-contact__meta a { color:#fff; font-weight:700; }
    .cd-form { padding:42px; background:#fff; }
    .cd-form__grid { display:grid; grid-template-columns:1fr 1fr; gap:17px; }
    .cd-field--full { grid-column:1/-1; }
    .cd-field label { display:block; margin-bottom:7px; color:var(--cd-navy); font-size:13px; font-weight:800; }
    .cd-field :is(input,select,textarea) { width:100%; min-height:50px; padding:12px 14px; border:1px solid #cfd9e6; border-radius:11px; color:var(--cd-navy); background:#fff; }
    .cd-field textarea { min-height:105px; resize:vertical; }
    .cd-consent { display:flex; align-items:flex-start; gap:10px; color:var(--cd-muted); font-size:12px; line-height:1.5; }
    .cd-consent input { flex:0 0 18px; width:18px; height:18px; margin-top:1px; accent-color:var(--cd-red); }
    .cd-error { display:block; margin-top:6px; color:#c71f27; font-size:12px; }
    .cd-alert { margin-bottom:18px; padding:14px 16px; border-radius:11px; color:#166343; background:#e8f8f1; }
    .cd-alert--error { color:#8d151a; background:#fff0f1; }
    .cd-alert--error ul { margin:8px 0 0 18px; }
    .cd-honeypot { position:absolute!important; left:-9999px!important; }
    .cd-disclaimer { margin-top:18px; color:#8391a5; font-size:12px; line-height:1.55; }

    @media (max-width:991px) {
        .cd-section { padding:72px 0; }
        .cd-hero { padding-top:104px; }
        .cd-hero__shell { min-height:540px; }
        .cd-stats { grid-template-columns:repeat(2,1fr); }
        .cd-stat:nth-child(2) { border-right:0; }
        .cd-stat:nth-child(-n+2) { border-bottom:1px solid var(--cd-border); }
        .cd-overview,.cd-requirements,.cd-career-intakes,.cd-contact { grid-template-columns:1fr; }
        .cd-benefits { grid-template-columns:repeat(2,1fr); }
        .cd-life-gallery { grid-template-columns:1fr 1fr; }
        .cd-life-card:first-child { grid-column:1/-1; }
        .cd-facts { grid-template-columns:1fr; }
        .cd-journey { grid-template-columns:repeat(2,1fr); }
        .cd-step:not(:nth-child(4n))::after { display:none; }
        .cd-costs { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:900px) {
        .cd-hero__shell { display:block; min-height:0; }
        .cd-hero__content { width:100%; }
    }
    @media (max-width:767px) {
        .cd-page { padding-bottom:76px; }
        .cd-container,.cd-narrow { width:min(100% - 28px, 620px); }
        .cd-section { padding:58px 0; scroll-margin-top:78px; }
        .cd-heading { font-size:31px; }
        .cd-lead { font-size:15px; }
        .cd-hero { padding:82px 0 0; }
        .cd-hero__shell { min-height:560px; align-items:flex-end; border-radius:26px; }
        .cd-hero__overlay { background:linear-gradient(180deg,rgba(5,17,39,.15) 0%,rgba(5,17,39,.96) 68%); }
        .cd-hero__content { padding:28px 24px 32px; }
        .cd-hero h1 { font-size:42px; }
        .cd-hero__copy { font-size:16px; line-height:1.6; }
        .cd-actions { display:grid; grid-template-columns:1fr; }
        .cd-button { width:100%; }
        .cd-stats { display:flex; overflow-x:auto; margin:0 12px; border-radius:0 0 22px 22px; scroll-snap-type:x mandatory; scrollbar-width:none; }
        .cd-stats::-webkit-scrollbar { display:none; }
        .cd-stat { flex:0 0 74%; border-right:1px solid var(--cd-border)!important; border-bottom:0!important; scroll-snap-align:start; }
        .cd-anchor { top:0; padding:8px 0; }
        .cd-anchor .cd-container { width:100%; }
        .cd-anchor__inner { border-radius:0; border-inline:0; padding-inline:14px; }
        .cd-anchor a { min-height:44px; padding:12px 14px; font-size:13px; }
        .cd-overview { gap:34px; }
        .cd-visual { min-height:390px; }
        .cd-visual__main { inset:0 34px 45px 0; border-radius:23px; }
        .cd-visual__card { width:58%; min-height:165px; border-width:6px; border-radius:20px; }
        .cd-visual__card img { height:165px; }
        .cd-visual__mini { top:18px; width:46%; height:128px; border-width:5px; border-radius:18px; }
        .cd-benefits { display:flex; overflow-x:auto; gap:14px; margin-inline:-14px; padding:0 14px 8px; scroll-snap-type:x mandatory; scrollbar-width:none; }
        .cd-benefits::-webkit-scrollbar { display:none; }
        .cd-benefit { flex:0 0 84%; scroll-snap-align:start; }
        .cd-life-gallery { display:flex; overflow-x:auto; gap:14px; margin-inline:-14px; padding:0 14px 8px; scroll-snap-type:x mandatory; scrollbar-width:none; }
        .cd-life-gallery::-webkit-scrollbar { display:none; }
        .cd-life-card,.cd-life-card:first-child { flex:0 0 84%; min-height:300px; scroll-snap-align:start; }
        .cd-fact-image { min-height:300px; }
        .cd-fact-panel { grid-template-columns:1fr; padding:24px; }
        .cd-journey-wrap { margin-inline:-14px; padding:24px 14px; border-radius:0; }
        .cd-journey { display:flex; overflow-x:auto; gap:14px; padding-bottom:8px; scroll-snap-type:x mandatory; scrollbar-width:none; }
        .cd-journey::-webkit-scrollbar { display:none; }
        .cd-step { flex:0 0 84%; min-height:230px; scroll-snap-align:center; }
        .cd-panel { padding:25px; }
        .cd-checks,.cd-costs,.cd-form__grid { grid-template-columns:1fr; }
        .cd-field--full { grid-column:auto; }
        .cd-costs { display:flex; overflow-x:auto; margin-inline:-14px; padding:0 14px 8px; scroll-snap-type:x mandatory; scrollbar-width:none; }
        .cd-cost { flex:0 0 82%; scroll-snap-align:start; }
        .cd-career-media { min-height:290px; }
        .cd-contact { border-radius:24px; }
        .cd-contact__intro,.cd-form { padding:28px 22px; }
        .cd-contact__intro h2 { font-size:31px; }
    }
    @media (prefers-reduced-motion:reduce) { .cd-button,.cd-benefit,.cd-life-card img { transition:none; } }
</style>

<main class="cd-page">
    <section class="cd-hero" id="overview">
        <div class="cd-container">
            <div class="cd-hero__shell" style="--hero-position:{{ $destination['hero_position'] }}">
                <img class="cd-hero__image" src="{{ asset($destination['hero']) }}" alt="Study in {{ $name }}" fetchpriority="high" width="1920" height="1200">
                <div class="cd-hero__overlay"></div>
                <div class="cd-hero__content">
                    <nav class="cd-breadcrumb" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><a href="{{ url('/destinations') }}">Destinations</a><span>/</span><span>{{ $name }}</span></nav>
                    <div class="cd-label"><img src="{{ asset('assets/transglobe/destinations/flags/'.$destination['flag']) }}" alt="{{ $name }} flag"><span>Expert guidance from GEIC Indore</span></div>
                    <h1>Study in <span>{{ $name }}</span></h1>
                    <p class="cd-hero__copy">{{ $destination['tagline'] }}</p>
                    <div class="cd-actions"><a class="cd-button" href="{{ $detailUrl }}#contact">Book free counselling</a><a class="cd-button cd-button--ghost" href="{{ $detailUrl }}#journey">See the complete journey</a></div>
                </div>
                @include('mirror.partials.hero-enquiry', ['formId' => 'destination-hero', 'sourceContext' => 'Study in '.$name, 'returnTo' => '/destinations/'.$slug.'#overview'])
            </div>
            <div class="cd-stats" aria-label="{{ $name }} study highlights">
                @foreach($destination['stats'] as [$value,$label])<div class="cd-stat"><strong>{{ $value }}</strong><span>{{ $label }}</span></div>@endforeach
            </div>
        </div>
    </section>

    <nav class="cd-anchor" aria-label="Page sections"><div class="cd-container"><div class="cd-anchor__inner">
        <a class="is-active" href="{{ $detailUrl }}#overview">Overview</a><a href="{{ $detailUrl }}#why">Why {{ $name }}</a><a href="{{ $detailUrl }}#journey">Study journey</a><a href="{{ $detailUrl }}#requirements">Requirements</a><a href="{{ $detailUrl }}#budget">Costs</a><a href="{{ $detailUrl }}#careers">Intakes & careers</a><a href="{{ $detailUrl }}#universities">Universities</a><a href="{{ $detailUrl }}#faqs">FAQs</a><a href="{{ $detailUrl }}#contact">Enquire</a>
    </div></div></nav>

    <section class="cd-section">
        <div class="cd-container cd-overview">
            <div class="cd-overview__copy"><div class="cd-kicker">Study in {{ $name }}</div><h2 class="cd-heading">A closer look at your study destination</h2><p>{{ $destination['overview'] }}</p><p>{{ $destination['overview_2'] }}</p><a class="cd-button" href="{{ $detailUrl }}#requirements" style="margin-top:26px">Check requirements</a></div>
            <div class="cd-visual" aria-label="{{ $name }} destination gallery">
                <div class="cd-visual__main"><img src="{{ asset($destination['gallery'][0]['src']) }}" alt="{{ $destination['gallery'][0]['alt'] }}" loading="lazy" width="1600" height="1100"></div>
                <div class="cd-visual__mini"><img src="{{ asset($destination['gallery'][1]['src']) }}" alt="{{ $destination['gallery'][1]['alt'] }}" loading="lazy" width="1600" height="1100"></div>
                <div class="cd-visual__card"><img src="{{ asset($destination['gallery'][2]['src']) }}" alt="{{ $destination['gallery'][2]['alt'] }}" loading="lazy" width="1600" height="1100"><span class="cd-visual__badge">Explore {{ $name }}</span></div>
            </div>
        </div>
    </section>

    <section class="cd-section cd-section--soft" id="why">
        <div class="cd-container"><div class="cd-center"><div class="cd-kicker">Why {{ $name }}</div><h2 class="cd-heading">What makes {{ $name }} stand out</h2><p class="cd-lead">Academic quality, practical learning and an international student experience designed for ambitious careers.</p></div>
            <div class="cd-benefits">
                @foreach($destination['benefits'] as [$title,$copy])
                    <article class="cd-benefit"><div class="cd-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m4 13 5 5L20 7"/><path d="M12 3a9 9 0 1 0 9 9"/></svg></div><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>
                @endforeach
            </div>
            <div class="cd-center" style="margin-top:62px"><div class="cd-kicker">Beyond the classroom</div><h2 class="cd-heading">Student life in {{ $name }}</h2></div>
            <div class="cd-life-gallery">
                @foreach($destination['gallery'] as $image)<figure class="cd-life-card"><img src="{{ asset($image['src']) }}" alt="{{ $image['alt'] }}" loading="lazy" width="1600" height="1100"><figcaption>{{ $image['label'] }}</figcaption></figure>@endforeach
            </div>
            <div class="cd-facts"><div class="cd-fact-image"><img src="{{ asset($destination['gallery'][1]['src']) }}" alt="{{ $destination['gallery'][1]['alt'] }}" loading="lazy" width="1600" height="1100"></div><div class="cd-fact-panel"><div class="cd-fact-panel__intro"><h3>{{ $name }} at a glance</h3><p>{{ $destination['facts_intro'] }}</p></div>@foreach($destination['facts'] as [$label,$value])<div class="cd-fact"><span>{{ $label }}</span><strong>{{ $value }}</strong></div>@endforeach</div></div>
        </div>
    </section>

    <section class="cd-section" id="journey">
        <div class="cd-container"><div class="cd-center"><div class="cd-kicker">The complete journey</div><h2 class="cd-heading">From counselling to arriving in {{ $name }}</h2><p class="cd-lead">A connected, step-by-step path with GEIC Indore supporting the details at every stage.</p></div>
            <div class="cd-journey-wrap"><div class="cd-journey">@foreach($destination['journey'] as $index => [$stage,$title,$copy])<article class="cd-step"><div class="cd-step__top"><span class="cd-step__number">{{ str_pad($index+1,2,'0',STR_PAD_LEFT) }}</span><span class="cd-step__stage">{{ $stage }}</span></div><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div></div>
        </div>
    </section>

    <section class="cd-section cd-section--soft" id="requirements">
        <div class="cd-container"><div class="cd-center"><div class="cd-kicker">Admission and visa</div><h2 class="cd-heading">Prepare the right documents with confidence</h2><p class="cd-lead">Exact requirements vary by program and institution; we help you build a complete, well-organised application.</p></div>
            <div class="cd-requirements"><article class="cd-panel"><h3>Typical admission documents</h3><ul class="cd-checks">@foreach($destination['requirements'] as $item)<li><span class="cd-check">✓</span><span>{{ $item }}</span></li>@endforeach</ul></article><article class="cd-panel cd-visa"><div class="cd-kicker" style="color:#ffb45a">Student visa</div><h3>{{ $destination['visa_title'] }}</h3><p>{{ $destination['visa_copy'] }}</p><div class="cd-note">Visa rules, work rights and financial thresholds can change. Confirm current requirements with the relevant government authority before applying.</div><a class="cd-button" href="{{ $detailUrl }}#contact" style="margin-top:24px">Discuss my eligibility</a></article></div>
        </div>
    </section>

    <section class="cd-section" id="budget">
        <div class="cd-container"><div class="cd-center"><div class="cd-kicker">Plan your budget</div><h2 class="cd-heading">Indicative study and living costs</h2><p class="cd-lead">Use these source-page estimates as a planning starting point. Actual costs vary by institution, city, lifestyle and exchange rate.</p></div>
            <div class="cd-costs">@foreach($destination['costs'] as [$label,$value])<article class="cd-cost"><span>{{ $label }}</span><strong>{{ $value }}</strong><small>Indicative estimate; confirm before applying.</small></article>@endforeach</div>
        </div>
    </section>

    <section class="cd-section cd-section--soft" id="careers">
        <div class="cd-container"><div class="cd-center"><div class="cd-kicker">Future ready</div><h2 class="cd-heading">Build your course and intake shortlist</h2></div><div class="cd-career-intakes"><article class="cd-panel"><h3>Popular career-focused fields</h3><div class="cd-careers">@foreach($destination['careers'] as $career)<span>{{ $career }}</span>@endforeach</div></article><article class="cd-panel"><h3>Common intakes</h3><div class="cd-intakes">@foreach($destination['intakes'] as [$intake,$copy])<div class="cd-intake"><strong>{{ $intake }}</strong><span>{{ $copy }}</span></div>@endforeach</div></article><figure class="cd-career-media"><img src="{{ asset($destination['support_image']['src']) }}" alt="{{ $destination['support_image']['alt'] }}" loading="lazy" width="1600" height="1100"><figcaption>Build experience, networks and career confidence in {{ $name }}.</figcaption></figure></div></div>
    </section>

    <section class="cd-section" id="universities"><div class="cd-container"><div class="cd-center"><div class="cd-kicker">University network</div><h2 class="cd-heading">Explore institutions in {{ $name }}</h2><p class="cd-lead">Country-specific university options sourced from the Trans Globe destination network.</p></div><x-university-network :universities="$destination['universities']" :country="$name" :slug="$slug" /></div></section>

    <section class="cd-section cd-section--soft" id="faqs"><div class="cd-narrow"><div class="cd-center"><div class="cd-kicker">Questions, answered</div><h2 class="cd-heading">Study in {{ $name }} FAQs</h2></div><div class="cd-faq">@foreach($destination['faqs'] as $index => [$question,$answer])<details @if($index===0) open @endif><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>@endforeach</div></div></section>

    <section class="cd-section cd-section--soft" id="contact">
        <div class="cd-container"><div class="cd-contact"><div class="cd-contact__intro"><div class="cd-kicker" style="color:#ffb45a">Free counselling</div><h2>Build your {{ $name }} shortlist with GEIC Indore</h2><p>Share your profile, course interests and preferred intake. Our destination specialist will help you understand realistic institution, budget and visa options.</p><div class="cd-contact__meta"><a href="tel:+919826666886">+91 98266 66886</a><a href="mailto:info@geic.in">info@geic.in</a><span>503, THE VIEW Tower 1, Yeshwant Niwas Rd, Indore 452001</span></div></div>
                <form class="cd-form" action="{{ route('destinations.enquire',['destination'=>$slug]) }}" method="post">@csrf
                    @if(session('enquiry_success'))<div class="cd-alert" role="status">{{ session('enquiry_success') }}</div>@endif
                    @if($errors->any())<div class="cd-alert cd-alert--error" id="form-errors" role="alert" tabindex="-1"><strong>Please check the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                    <div class="cd-form__grid">
                        <div class="cd-field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" autocomplete="name" required>@error('full_name')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="phone">Phone number</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" required>@error('phone')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="city">Current city</label><input id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2" required>@error('city')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="study_level">Preferred study level</label><select id="study_level" name="study_level" required><option value="">Select level</option>@foreach(['Undergraduate','Postgraduate','Diploma or pathway','Research','Not sure yet'] as $option)<option @selected(old('study_level')===$option)>{{ $option }}</option>@endforeach</select>@error('study_level')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="preferred_intake">Preferred intake</label><select id="preferred_intake" name="preferred_intake" required><option value="">Select intake</option>@foreach(['Next available intake','February intake','July intake','October intake','Not sure yet'] as $option)<option @selected(old('preferred_intake')===$option)>{{ $option }}</option>@endforeach</select>@error('preferred_intake')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="preferred_course">Preferred course</label><input id="preferred_course" name="preferred_course" value="{{ old('preferred_course') }}" placeholder="e.g. Data Science">@error('preferred_course')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field"><label for="english_test">English test status</label><select id="english_test" name="english_test" required><option value="">Select status</option>@foreach(['IELTS','PTE','TOEFL','Planning to take a test','Not sure yet'] as $option)<option @selected(old('english_test')===$option)>{{ $option }}</option>@endforeach</select>@error('english_test')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field cd-field--full"><label for="message">Anything else we should know?</label><textarea id="message" name="message">{{ old('message') }}</textarea>@error('message')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-field cd-field--full"><label class="cd-consent"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required><span>I agree that GEIC Indore may contact me about my study-abroad enquiry.</span></label>@error('consent')<span class="cd-error">{{ $message }}</span>@enderror</div>
                        <div class="cd-honeypot" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
                        <div class="cd-field cd-field--full"><button class="cd-button" type="submit">Request my free counselling call</button></div>
                    </div>
                    <p class="cd-disclaimer">The figures on this page are indicative and based on the supplied destination source. University, visa and work-right rules can change; our counsellors will help you verify current official requirements.</p>
                </form>
            </div></div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const nav=document.querySelector('.cd-anchor');
    const links=Array.from(document.querySelectorAll('.cd-anchor a'));
    const sections=links.map(link=>document.querySelector(link.hash)).filter(Boolean);
    let ticking=false;
    function sync(){
        const offset=(nav?.offsetHeight||0)+20;
        let current=sections[0]?.id||'';
        sections.forEach(section=>{if(window.scrollY>=section.offsetTop-offset)current=section.id;});
        links.forEach(link=>{const active=link.hash==='#'+current;link.classList.toggle('is-active',active);active?link.setAttribute('aria-current','location'):link.removeAttribute('aria-current');});
        ticking=false;
    }
    window.addEventListener('scroll',()=>{if(!ticking){requestAnimationFrame(sync);ticking=true;}},{passive:true});
    sync();
    document.getElementById('form-errors')?.focus();
});
</script>

@include('mirror.partials.footer')
