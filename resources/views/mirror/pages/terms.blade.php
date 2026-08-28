@include('mirror.partials.header', ['siteCms' => $cms])
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/'), 'mobileBackLabel' => 'Back to home'])

@php
    $legalSections = [
        ['services', '01', $cms['services_title'] ?? 'Scope of our services', $cms['services_copy'] ?? 'Our support may include country, course and university counselling; application and document guidance; and information about admissions, visas, scholarships, funding and timelines. Guidance is advisory and delivered on a best-effort basis.'],
        ['outcomes', '02', $cms['outcomes_title'] ?? 'No guarantee of outcomes', $cms['outcomes_copy'] ?? 'Educational institutions and immigration authorities make their own admission, scholarship and visa decisions. Requirements, fees, policies and timelines can change. We cannot guarantee an admission, visa, scholarship, interview or any other particular result.'],
        ['responsibilities', '03', $cms['responsibilities_title'] ?? 'Your responsibilities', $cms['responsibilities_copy'] ?? 'You must provide information that is complete, accurate and current; submit genuine, verifiable documents; and meet the requirements and deadlines set by institutions and authorities. False or incomplete information can lead to rejection or other consequences.'],
        ['fees', '04', $cms['fees_title'] ?? 'Fees, payments and refunds', $cms['fees_copy'] ?? 'If a service has a fee, we will communicate it before you proceed. Payment and refund terms are governed by the relevant service agreement or policy. A payment may be non-refundable unless we confirm otherwise in writing.'],
        ['content', '05', $cms['content_title'] ?? 'Website content and intellectual property', $cms['content_copy'] ?? 'Website text, graphics, images, forms, logos, design and branding belong to Trans Globe or their respective rights holders. You may use them for personal information only and may not reproduce, modify or distribute them without permission.'],
        ['conduct', '06', $cms['conduct_title'] ?? 'Acceptable website use', $cms['conduct_copy'] ?? 'Do not use this website unlawfully or fraudulently, interfere with its security or availability, upload harmful software, impersonate another person, or misuse our forms and communication channels.'],
        ['third-party', '07', $cms['third_party_title'] ?? 'Third-party websites and services', $cms['third_party_copy'] ?? 'Links to universities, government portals and other third parties are provided for convenience. Their content, availability, privacy practices and terms are controlled by those organisations, not by Trans Globe Indore.'],
        ['liability', '08', $cms['liability_title'] ?? 'Limitations and service availability', $cms['liability_copy'] ?? 'To the extent permitted by law, Trans Globe Indore is not responsible for indirect losses, missed opportunities or delays caused by institutions, authorities, service providers, network failures or events outside our reasonable control.'],
        ['law', '09', $cms['law_title'] ?? 'Changes, termination and governing law', $cms['law_copy'] ?? 'We may update these terms or restrict access when the website is misused, these terms are breached, or the law requires it. Updated terms apply when posted. These terms are governed by the laws of India and disputes are subject to courts with applicable jurisdiction in India.'],
    ];
@endphp

<style>
    @font-face { font-family:'Plus Jakarta Sans'; font-style:normal; font-weight:400 800; font-display:swap; src:url('{{ asset('assets/fonts/plus-jakarta-sans-latin.woff2') }}') format('woff2'); }
    :root { --legal-navy:#0e2145; --legal-red:#e31e24; --legal-orange:#f3951e; --legal-ink:#182a4b; --legal-muted:#66758f; --legal-soft:#f4f7fb; --legal-line:#dfe7f1; }
    .legal-page { overflow:clip; background:#fff; color:var(--legal-ink); font-family:'Plus Jakarta Sans',sans-serif; }
    .legal-wrap { width:min(1240px,calc(100% - 48px)); margin-inline:auto; }
    .legal-page :is(a,button):focus-visible { outline:3px solid rgba(243,149,30,.5); outline-offset:3px; }
    .legal-hero { padding:126px 0 72px; background:var(--legal-soft); }
    .legal-hero__panel { position:relative; isolation:isolate; overflow:hidden; min-height:440px; padding:64px; border-radius:34px; background:var(--legal-navy); box-shadow:0 26px 64px rgba(14,33,69,.18); }
    .legal-hero__panel::before { position:absolute; z-index:-2; inset:0; opacity:.2; background-image:linear-gradient(rgba(255,255,255,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.12) 1px,transparent 1px); background-size:48px 48px; content:''; }
    .legal-hero__panel::after { position:absolute; z-index:-1; top:-220px; right:-125px; width:570px; height:570px; border:82px solid rgba(227,30,36,.32); border-radius:50%; content:''; }
    .legal-crumbs { display:flex; flex-wrap:wrap; gap:8px; color:rgba(255,255,255,.6); font-size:13px; }
    .legal-crumbs a { color:#fff; font-weight:700; }
    .legal-eyebrow { display:inline-flex; align-items:center; gap:10px; color:var(--legal-red); font-size:12px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .legal-eyebrow::before { width:29px; height:2px; background:currentColor; content:''; }
    .legal-hero .legal-eyebrow { margin-top:54px; color:#ff7379; }
    .legal-hero h1 { max-width:740px; margin:17px 0 0; color:#fff; font-size:clamp(44px,6vw,74px); line-height:1; font-weight:800; letter-spacing:-.055em; text-wrap:balance; }
    .legal-hero__copy { max-width:680px; margin:22px 0 0; color:rgba(255,255,255,.76); font-size:17px; line-height:1.75; }
    .legal-hero__note { display:inline-flex; align-items:center; gap:10px; margin-top:30px; padding:13px 16px; border:1px solid rgba(255,255,255,.18); border-radius:14px; background:rgba(255,255,255,.08); color:#fff; font-size:13px; font-weight:700; }
    .legal-hero__note svg { width:19px; height:19px; fill:none; stroke:#65d6b1; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
    .legal-main { padding:88px 0 104px; }
    .legal-layout { display:grid; grid-template-columns:280px minmax(0,1fr); align-items:start; gap:56px; }
    .legal-nav { position:sticky; top:112px; overflow:hidden; padding:10px; border:1px solid var(--legal-line); border-radius:23px; background:#fff; box-shadow:0 15px 38px rgba(14,33,69,.07); }
    .legal-nav__label { display:block; padding:16px 17px 10px; color:var(--legal-red); font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .legal-nav a { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 17px; border-radius:13px; color:var(--legal-ink); font-size:13px; font-weight:700; transition:background-color .18s ease,color .18s ease; }
    .legal-nav a span { color:#a7b3c6; font-size:11px; }
    .legal-nav a:hover { background:#fff5ed; color:var(--legal-orange); }
    .legal-intro { padding:36px; border:1px solid var(--legal-line); border-radius:26px; background:var(--legal-soft); }
    .legal-intro h2 { max-width:690px; margin:15px 0 0; color:var(--legal-navy); font-size:clamp(28px,3.3vw,42px); line-height:1.12; font-weight:800; letter-spacing:-.04em; text-wrap:balance; }
    .legal-intro p { max-width:760px; margin:17px 0 0; color:var(--legal-muted); font-size:15px; line-height:1.8; }
    .legal-sections { display:grid; gap:16px; margin-top:22px; }
    .legal-card { scroll-margin-top:116px; display:grid; grid-template-columns:58px minmax(0,1fr); gap:22px; padding:29px; border:1px solid var(--legal-line); border-radius:23px; background:#fff; box-shadow:0 10px 32px rgba(14,33,69,.045); transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease; }
    .legal-card:hover { border-color:rgba(243,149,30,.55); box-shadow:0 18px 42px rgba(14,33,69,.09); transform:translateY(-2px); }
    .legal-card__number { display:grid; width:48px; height:48px; place-items:center; border-radius:15px; background:#fdebed; color:var(--legal-red); font-size:12px; font-weight:800; }
    .legal-card h2 { margin:3px 0 0; color:var(--legal-navy); font-size:21px; line-height:1.3; font-weight:800; letter-spacing:-.025em; }
    .legal-card p { margin:10px 0 0; color:var(--legal-muted); font-size:15px; line-height:1.78; }
    .legal-contact { position:relative; overflow:hidden; display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:38px; margin-top:24px; padding:38px; border-radius:26px; background:var(--legal-red); color:#fff; }
    .legal-contact::after { position:absolute; right:-90px; bottom:-130px; width:260px; height:260px; border:45px solid rgba(255,255,255,.08); border-radius:50%; content:''; }
    .legal-contact h2 { position:relative; z-index:1; margin:0; color:#fff; font-size:30px; line-height:1.15; font-weight:800; letter-spacing:-.035em; }
    .legal-contact p { position:relative; z-index:1; max-width:700px; margin:10px 0 0; color:rgba(255,255,255,.8); font-size:15px; line-height:1.7; }
    .legal-contact__actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; justify-content:flex-end; gap:10px; }
    .legal-contact__button { display:inline-flex; min-height:50px; align-items:center; justify-content:center; padding:0 19px; border:1px solid #fff; border-radius:13px; background:#fff; color:var(--legal-navy)!important; font-size:13px; font-weight:800; transition:border-color .18s ease,background-color .18s ease,color .18s ease,transform .18s ease; }
    .legal-contact__button:hover { border-color:var(--legal-orange); background:var(--legal-orange); color:#fff!important; transform:translateY(-2px); }
    .legal-contact__meta { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:9px 18px; margin-top:18px; color:rgba(255,255,255,.8); font-size:12px; }
    .legal-contact__meta a { color:#fff; font-weight:700; }
    @media (max-width:991px) {
        .legal-hero { padding-top:102px; }
        .legal-hero__panel { min-height:400px; padding:46px; }
        .legal-layout { grid-template-columns:1fr; gap:24px; }
        .legal-nav { position:static; display:flex; overflow-x:auto; padding:9px; scrollbar-width:none; }
        .legal-nav::-webkit-scrollbar { display:none; }
        .legal-nav__label { display:none; }
        .legal-nav a { flex:0 0 auto; white-space:nowrap; }
        .legal-contact { grid-template-columns:1fr; }
        .legal-contact__actions { justify-content:flex-start; }
    }
    @media (max-width:767px) {
        .legal-wrap { width:min(100% - 28px,1240px); }
        .legal-hero { padding:14px 0 36px; }
        .legal-hero__panel { min-height:360px; padding:33px 24px; border-radius:26px; }
        .legal-hero__panel::after { top:-190px; right:-200px; width:430px; height:430px; border-width:58px; }
        .legal-hero .legal-eyebrow { margin-top:45px; }
        .legal-hero h1 { font-size:42px; }
        .legal-hero__copy { font-size:15px; line-height:1.7; }
        .legal-main { padding:46px 0 72px; }
        .legal-nav { margin-inline:-14px; border-right:0; border-left:0; border-radius:0; box-shadow:none; }
        .legal-intro { padding:26px 22px; border-radius:22px; }
        .legal-card { grid-template-columns:1fr; gap:15px; padding:24px 21px; border-radius:21px; }
        .legal-card__number { width:42px; height:42px; border-radius:13px; }
        .legal-card p,.legal-contact p { font-size:16px; }
        .legal-contact { padding:29px 22px; border-radius:22px; }
        .legal-contact h2 { font-size:27px; }
        .legal-contact__button { width:100%; }
    }
    @media (prefers-reduced-motion:reduce) { .legal-page * { scroll-behavior:auto!important; transition:none!important; } }
</style>

<main class="legal-page">
    <section class="legal-hero"><div class="legal-wrap"><div class="legal-hero__panel">
        <nav class="legal-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><span>Terms &amp; Conditions</span></nav>
        <span class="legal-eyebrow">{{ $cms['hero_eyebrow'] ?? 'Clear terms. Confident decisions.' }}</span>
        <h1>{{ $cms['hero_title'] ?? 'Terms & Conditions' }}</h1>
        <p class="legal-hero__copy">{{ $cms['hero_copy'] ?? 'These terms explain how you may use the Trans Globe Indore website and what to expect from our study-abroad guidance and support.' }}</p>
        <span class="legal-hero__note"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.7 8 7 10 4.3-2 7-5.4 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-5"/></svg> Trans Globe Indore · Managed by GEIC</span>
    </div></div></section>

    <section class="legal-main"><div class="legal-wrap legal-layout">
        <nav class="legal-nav" aria-label="Terms sections">
            <span class="legal-nav__label">On this page</span>
            @foreach($legalSections as [$id, $number, $title])
                <a href="#{{ $id }}"><span>{{ $number }}</span>{{ $title }}</a>
            @endforeach
        </nav>

        <div>
            <article class="legal-intro">
                <span class="legal-eyebrow">Introduction</span>
                <h2>{{ $cms['intro_title'] ?? 'An open, responsible working relationship' }}</h2>
                <p>{{ $cms['intro_copy'] ?? 'Trans Globe Indore, managed by Global Education and Immigration Consultants (GEIC), provides study-abroad information, counselling and application support. By using this website or submitting an enquiry, you agree to these terms.' }}</p>
            </article>

            <div class="legal-sections">
                @foreach($legalSections as [$id, $number, $title, $copy])
                    <article class="legal-card" id="{{ $id }}"><span class="legal-card__number">{{ $number }}</span><div><h2>{{ $title }}</h2><p>{{ $copy }}</p></div></article>
                @endforeach
            </div>

            <aside class="legal-contact">
                <div>
                    <h2>{{ $cms['contact_title'] ?? 'Questions about these terms?' }}</h2>
                    <p>{{ $cms['contact_copy'] ?? 'Speak with the Trans Globe Indore team if you need help understanding how these terms apply to your enquiry or counselling service.' }}</p>
                    <div class="legal-contact__meta"><a href="mailto:info@geic.in">info@geic.in</a><a href="tel:+919826666886">+91 98266 66886</a><span>Indore, Madhya Pradesh</span></div>
                </div>
                <div class="legal-contact__actions"><a class="legal-contact__button" href="{{ url('/contact') }}">{{ $cms['contact_cta'] ?? 'Contact the Indore office' }} <span aria-hidden="true">→</span></a></div>
            </aside>
        </div>
    </div></section>
</main>

@include('mirror.partials.footer', ['siteCms' => $cms])
