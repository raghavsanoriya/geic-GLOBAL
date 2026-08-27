@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/tests'), 'mobileBackLabel' => 'Back to test prep'])
@php($detailUrl = url()->current())
@php($testMedia = \App\Support\DetailPageAssets::testGallery($test))
@php($testUniversities = \App\Support\DetailPageAssets::universityNetwork())

<style>
    :root { --td-navy: #0e2145; --td-red: #e31e24; --td-soft: #f4f7fb; --td-ink: #15294d; --td-muted: #64748b; --td-line: #dfe7f0; }
    .td-page { overflow: clip; background: #fff; color: var(--td-ink); }
    .td-wrap { width: min(1280px, calc(100% - 48px)); margin-inline: auto; }
    .td-section { padding: 88px 0; scroll-margin-top: 92px; }
    .td-section--soft { background: var(--td-soft); }
    .td-kicker { display: inline-flex; align-items: center; gap: 10px; color: var(--td-red); font-size: 12px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .td-kicker::before { width: 28px; height: 2px; background: currentColor; content: ''; }
    .td-title { max-width: 760px; margin: 14px 0 0; color: var(--td-navy); font-size: clamp(34px, 4vw, 51px); line-height: 1.1; font-weight: 800; letter-spacing: -.045em; text-wrap: balance; }
    .td-lead { max-width: 690px; margin: 16px 0 0; color: var(--td-muted); font-size: 16px; line-height: 1.75; }
    .td-button { display: inline-flex; min-height: 52px; align-items: center; justify-content: center; gap: 9px; padding: 0 23px; border-radius: 14px; background: var(--td-red); color: #fff !important; font-size: 14px; font-weight: 800; transition: background .2s ease, transform .2s ease; }
    .td-button:hover, .td-button:focus-visible { background: #c81820; color: #fff; transform: translateY(-2px); }
    .td-button--outline { border: 1px solid rgba(255,255,255,.34); background: rgba(255,255,255,.08); }
    .td-button--outline:hover, .td-button--outline:focus-visible { background: #fff; color: var(--td-navy) !important; }
    .td-page :is(a, summary):focus-visible { outline: 3px solid rgba(227,30,36,.32); outline-offset: 3px; }

    .td-hero { padding: 128px 0 0; background: var(--td-soft); }
    .td-hero__shell { position: relative; min-height: 560px; display: flex; align-items: flex-end; overflow: hidden; border-radius: 34px; background: var(--td-navy); box-shadow: 0 26px 65px rgba(14,33,69,.2); }
    .td-hero__image, .td-hero__overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
    .td-hero__image { object-fit: cover; object-position: center; }
    .td-hero__overlay { background: linear-gradient(90deg, rgba(6,19,45,.98), rgba(7,22,49,.88) 44%, rgba(7,22,49,.2)); }
    .td-hero__content { position: relative; z-index: 1; width: min(660px, calc(100% - 380px)); padding: 62px; }
    .td-crumbs { display: flex; flex-wrap: wrap; gap: 8px; color: rgba(255,255,255,.64); font-size: 13px; }
    .td-crumbs a { color: #fff; font-weight: 700; }
    .td-hero__eyebrow { display: inline-flex; align-items: center; margin-top: 26px; padding: 8px 13px; border: 1px solid rgba(255,255,255,.22); border-radius: 999px; background: rgba(255,255,255,.09); color: #fff; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; backdrop-filter: blur(10px); }
    .td-hero h1 { max-width: 700px; margin: 17px 0 0; color: #fff; font-size: clamp(46px, 5.8vw, 72px); line-height: 1.01; font-weight: 800; letter-spacing: -.06em; text-wrap: balance; }
    .td-hero h1 span { color: #ff636a; }
    .td-hero__lead { max-width: 650px; margin: 19px 0 0; color: rgba(255,255,255,.84); font-size: 17px; line-height: 1.72; }
    .td-hero__actions { display: flex; flex-wrap: wrap; gap: 13px; margin-top: 28px; }
    .td-hero__number { position: absolute; z-index: 2; top: 34px; right: 40px; display: grid; width: 78px; height: 78px; place-items: center; border: 1px solid rgba(255,255,255,.24); border-radius: 50%; background: rgba(14,33,69,.47); color: #fff; font-size: 20px; font-weight: 800; backdrop-filter: blur(12px); }
    .td-facts { position: relative; z-index: 3; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); margin: -1px 30px 0; border: 1px solid var(--td-line); border-radius: 0 0 24px 24px; background: #fff; box-shadow: 0 18px 42px rgba(14,33,69,.08); }
    .td-fact { padding: 21px 25px; border-right: 1px solid var(--td-line); }
    .td-fact:last-child { border-right: 0; }
    .td-fact b { display: block; color: var(--td-red); font-size: 19px; line-height: 1.1; }
    .td-fact span { display: block; margin-top: 6px; color: var(--td-muted); font-size: 12px; font-weight: 700; }
    .td-anchor { position: sticky; z-index: 100; top: 0; padding: 12px 0; border-bottom: 1px solid rgba(223,231,240,.92); background: rgba(244,247,251,.94); box-shadow: 0 10px 26px rgba(14,33,69,.06); backdrop-filter: blur(16px); }
    .td-anchor__bar { display: flex; gap: 8px; overflow-x: auto; padding: 7px; border: 1px solid var(--td-line); border-radius: 17px; background: #fff; scrollbar-width: none; }
    .td-anchor__bar::-webkit-scrollbar { display: none; }
    .td-anchor a { flex: 0 0 auto; min-height: 40px; padding: 10px 15px; border-radius: 11px; color: #53657f; font-size: 13px; font-weight: 800; }
    .td-anchor a:hover, .td-anchor a:first-child { background: var(--td-navy); color: #fff; }

    .td-overview { display: grid; grid-template-columns: .94fr 1.06fr; align-items: center; gap: 70px; }
    .td-overview p { color: var(--td-muted); font-size: 16px; line-height: 1.82; }
    .td-overview p + p { margin-top: 15px; }
    .td-overview__visual { position: relative; min-height: 470px; }
    .td-overview__image { position: absolute; inset: 0 0 0 55px; overflow: hidden; border-radius: 30px; box-shadow: 0 22px 52px rgba(14,33,69,.16); }
    .td-overview__image img { width: 100%; height: 100%; object-fit: cover; }
    .td-overview__badge { position: absolute; left: 0; bottom: 38px; width: 165px; padding: 20px 17px; border: 7px solid #fff; border-radius: 22px; background: var(--td-red); color: #fff; box-shadow: 0 16px 35px rgba(227,30,36,.22); }
    .td-overview__badge b { display: block; font-size: 24px; line-height: 1; }
    .td-overview__badge span { display: block; margin-top: 7px; font-size: 11px; font-weight: 700; line-height: 1.45; }
    .td-note { margin-top: 22px; padding: 17px 19px; border-left: 3px solid var(--td-red); border-radius: 0 14px 14px 0; background: #fff4f4; color: #5d4a50 !important; font-size: 14px !important; line-height: 1.65 !important; }

    .td-centre { text-align: center; }
    .td-centre .td-title, .td-centre .td-lead { margin-inline: auto; }
    .td-modules { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-top: 40px; }
    .td-module { min-height: 235px; padding: 26px; border: 1px solid var(--td-line); border-radius: 22px; background: #fff; box-shadow: 0 12px 28px rgba(14,33,69,.05); }
    .td-module__number { display: grid; width: 43px; height: 43px; place-items: center; border-radius: 13px; background: #fdebed; color: var(--td-red); font-size: 12px; font-weight: 900; }
    .td-module h3 { margin: 19px 0 0; color: var(--td-navy); font-size: 19px; line-height: 1.25; }
    .td-module p { margin: 10px 0 0; color: var(--td-muted); font-size: 14px; line-height: 1.65; }
    .td-support-wrap { padding: 55px; background: var(--td-navy); background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 64px 64px; }
    .td-support-wrap .td-kicker { color: #ff7b80; }
    .td-support-wrap .td-title { color: #fff; }
    .td-support-wrap .td-lead { color: rgba(255,255,255,.7); }
    .td-support { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 40px; }
    .td-support__item { position: relative; min-height: 230px; padding: 28px; border: 1px solid rgba(255,255,255,.15); border-radius: 24px; background: rgba(255,255,255,.07); }
    .td-support__number { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 50%; background: var(--td-red); color: #fff; font-size: 12px; font-weight: 900; }
    .td-support h3 { margin: 18px 0 0; color: #fff; font-size: 20px; line-height: 1.2; }
    .td-support p { margin: 10px 0 0; color: rgba(255,255,255,.7); font-size: 14px; line-height: 1.68; }
    .td-faq-layout { display: grid; grid-template-columns: .72fr 1.28fr; align-items: start; gap: 58px; }
    .td-faq-intro { position: sticky; top: 100px; }
    .td-faq { display: grid; gap: 12px; }
    .td-faq details { overflow: hidden; border: 1px solid var(--td-line); border-radius: 17px; background: #fff; }
    .td-faq summary { position: relative; padding: 21px 56px 21px 23px; color: var(--td-navy); font-size: 16px; font-weight: 800; cursor: pointer; list-style: none; }
    .td-faq summary::-webkit-details-marker { display: none; }
    .td-faq summary::after { position: absolute; top: 17px; right: 22px; color: var(--td-red); font-size: 25px; font-weight: 400; content: '+'; }
    .td-faq details[open] summary::after { content: '−'; }
    .td-faq p { padding: 0 23px 22px; color: var(--td-muted); line-height: 1.72; }
    .td-section#enquire { padding-top: 56px; padding-bottom: 56px; }
    .td-cta { position: relative; overflow: hidden; padding: 23px 30px; border-radius: 20px; background: var(--td-red); box-shadow: 0 13px 28px rgba(227,30,36,.14); }
    .td-cta::after { position: absolute; top: -82px; right: -76px; width: 210px; height: 210px; border: 30px solid rgba(255,255,255,.12); border-radius: 50%; content: ''; }
    .td-cta__content { position: relative; z-index: 1; max-width: 680px; }
    .td-cta .td-kicker { color: rgba(255,255,255,.76); }
    .td-cta h2 { margin: 7px 0 0; color: #fff; font-size: clamp(23px, 2.25vw, 30px); line-height: 1.13; font-weight: 800; letter-spacing: -.04em; text-wrap: balance; }
    .td-cta p { max-width: 640px; margin: 8px 0 0; color: rgba(255,255,255,.85); font-size: 13px; line-height: 1.58; }
    .td-cta .td-button { min-height: 42px; margin-top: 14px; padding-inline: 18px; background: #fff; color: var(--td-navy) !important; }
    .td-cta .td-button:hover { color: var(--td-red) !important; }
    @media (max-width: 991px) { .td-hero { padding-top: 106px; } .td-overview, .td-faq-layout { grid-template-columns: 1fr; gap: 44px; } .td-faq-intro { position: static; } .td-overview__visual { min-height: 420px; max-width: 720px; } .td-modules { grid-template-columns: repeat(2, minmax(0, 1fr)); } .td-support { grid-template-columns: repeat(2, minmax(0, 1fr)); } .td-support__item:last-child { grid-column: span 2; } }
    @media (max-width: 900px) { .td-hero__shell { display:block; min-height:0; } .td-hero__content { width:100%; } }
    @media (max-width: 767px) { .td-page { padding-bottom: 76px; } .td-wrap { width: min(100% - 28px, 620px); } .td-section { padding: 58px 0; } .td-section#enquire { padding-top: 42px; padding-bottom: 42px; } .td-title { font-size: 32px; } .td-lead { font-size: 15px; } .td-hero { padding-top: 82px; } .td-hero__shell { min-height: 580px; border-radius: 25px; } .td-hero__overlay { background: linear-gradient(180deg, rgba(5,17,39,.2), rgba(5,17,39,.97) 72%); } .td-hero__content { padding: 28px 24px 32px; } .td-hero h1 { font-size: 42px; } .td-hero__lead { font-size: 15px; line-height: 1.62; } .td-hero__number { top: 18px; right: 18px; width: 58px; height: 58px; font-size: 15px; } .td-hero__actions { display: grid; grid-template-columns: 1fr; } .td-button { width: 100%; } .td-facts { display: flex; overflow-x: auto; margin: 0 10px; border-radius: 0 0 21px 21px; scroll-snap-type: x mandatory; scrollbar-width: none; } .td-facts::-webkit-scrollbar { display: none; } .td-fact { flex: 0 0 82%; border-right: 1px solid var(--td-line) !important; scroll-snap-align: start; } .td-anchor { padding: 8px 0; } .td-anchor .td-wrap { width: 100%; } .td-anchor__bar { padding-inline: 14px; border-inline: 0; border-radius: 0; } .td-overview__visual { min-height: 355px; } .td-overview__image { inset: 0 0 0 26px; border-radius: 22px; } .td-overview__badge { bottom: 17px; width: 128px; padding: 17px 14px; border-width: 5px; border-radius: 18px; } .td-overview__badge b { font-size: 21px; } .td-modules { display: flex; overflow-x: auto; gap: 14px; margin-top: 31px; padding: 0 2px 8px; scroll-snap-type: x mandatory; scrollbar-width: none; } .td-modules::-webkit-scrollbar { display: none; } .td-module { flex: 0 0 84%; min-height: 215px; scroll-snap-align: start; } .td-support-wrap { padding: 42px 14px; } .td-support { display: flex; overflow-x: auto; gap: 14px; margin-top: 31px; padding: 0 2px 8px; scroll-snap-type: x mandatory; scrollbar-width: none; } .td-support::-webkit-scrollbar { display: none; } .td-support__item, .td-support__item:last-child { flex: 0 0 84%; min-height: 218px; scroll-snap-align: start; } .td-cta { padding: 22px 20px; border-radius: 18px; } .td-cta h2 { font-size: 24px; } .td-cta p { font-size: 13px; } }
    @media (prefers-reduced-motion: reduce) { .td-page *, .td-page *::before, .td-page *::after { transition-duration: .01ms !important; scroll-behavior: auto !important; } }
</style>

<main class="td-page">
    <section class="td-hero" id="overview"><div class="td-wrap"><div class="td-hero__shell"><img class="td-hero__image" src="{{ asset($cms['hero_image'] ?? $test['image']) }}" alt="{{ $test['image_alt'] }}" fetchpriority="high" width="1366" height="685"><div class="td-hero__overlay"></div><span class="td-hero__number" aria-label="Test {{ $test['number'] }}">{{ $test['number'] }}</span><div class="td-hero__content"><nav class="td-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><a href="{{ url('/tests') }}">Test prep</a><span>/</span><span>{{ $test['title'] }}</span></nav><span class="td-hero__eyebrow">{{ $test['eyebrow'] }}</span><h1>{{ $cms['hero_title'] ?? $test['title'] }}</h1><p class="td-hero__lead">{{ $cms['hero_copy'] ?? $test['summary'] }}</p><div class="td-hero__actions"><a href="{{ url('/#contact') }}" class="td-button">Plan my preparation <span aria-hidden="true">→</span></a><a href="{{ $detailUrl }}#modules" class="td-button td-button--outline">See the test format</a></div></div>@include('mirror.partials.hero-enquiry', ['formId' => 'test-hero', 'sourceContext' => 'Test preparation — '.$test['title'], 'returnTo' => '/tests/'.$test['slug'].'#overview'])</div><div class="td-facts" aria-label="{{ $test['title'] }} key facts">@foreach($test['facts'] as [$value, $label])<div class="td-fact"><b>{{ $value }}</b><span>{{ $label }}</span></div>@endforeach</div></div></section>

    <nav class="td-anchor" aria-label="Test-page sections"><div class="td-wrap"><div class="td-anchor__bar"><a href="{{ $detailUrl }}#overview">Overview</a><a href="{{ $detailUrl }}#gallery">In focus</a><a href="{{ $detailUrl }}#modules">Test format</a><a href="{{ $detailUrl }}#support">Preparation</a><a href="{{ $detailUrl }}#universities">Universities</a><a href="{{ $detailUrl }}#faqs">FAQs</a><a href="{{ $detailUrl }}#enquire">Enquire</a></div></div></nav>

    <section class="td-section"><div class="td-wrap td-overview"><div><span class="td-kicker">Know your exam</span><h2 class="td-title">Understand the format before you start your preparation.</h2><p class="td-lead">{{ $test['overview'] }}</p><p class="td-note">{{ $test['facts_note'] }}</p><a href="{{ $detailUrl }}#support" class="td-button" style="margin-top: 26px">See how we help</a></div><div class="td-overview__visual"><div class="td-overview__image"><img src="{{ asset($test['image']) }}" alt="{{ $test['image_alt'] }}" loading="lazy" width="1366" height="685"></div><div class="td-overview__badge"><b>{{ $test['number'] }}</b><span>of 8 test-prep pathways for global education goals</span></div></div></div></section>

    <x-detail-media-gallery :images="$testMedia" id="gallery" eyebrow="Preparation in focus" title="Turn practice into a confident application milestone." lead="Your test result matters most when it is planned around your course, university requirements and chosen intake." />

    <section class="td-section td-section--soft" id="modules"><div class="td-wrap"><div class="td-centre"><span class="td-kicker">Test format</span><h2 class="td-title">The areas you will prepare for.</h2><p class="td-lead">Every test is different. Knowing the sections first helps you create a study plan that spends time where it will matter most.</p></div><div class="td-modules">@foreach($test['modules'] as $index => [$title, $copy])<article class="td-module"><span class="td-module__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div></div></section>

    <section class="td-section td-support-wrap" id="support"><div class="td-wrap"><div class="td-centre"><span class="td-kicker">Your preparation path</span><h2 class="td-title">Build the score plan around your actual application.</h2><p class="td-lead">Your test strategy should work with your university shortlist, document schedule and planned intake — not sit separately from them.</p></div><div class="td-support">@foreach($test['support'] as $index => [$title, $copy])<article class="td-support__item"><span class="td-support__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div></div></section>

    <section class="td-section" id="universities"><div class="td-wrap"><div class="td-centre"><span class="td-kicker">University network</span><h2 class="td-title">Plan for the institutions on your shortlist.</h2><p class="td-lead">We help you verify the current test and score expectations for each university before you book or submit your result.</p></div><x-university-network :universities="$testUniversities" country="Global" slug="test-{{ $test['slug'] }}" /></div></section>

    <section class="td-section" id="faqs"><div class="td-wrap td-faq-layout"><div class="td-faq-intro"><span class="td-kicker">Common questions</span><h2 class="td-title">Answers before you book.</h2><p class="td-lead">Requirements vary by university and programme. A counselling conversation can help you verify your test choice before you begin.</p><a href="{{ url('/#contact') }}" class="td-button" style="margin-top: 25px">Talk to a counsellor</a></div><div class="td-faq">@foreach($test['faqs'] as [$question, $answer])<details><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>@endforeach</div></div></section>

    <section class="td-section td-section--soft" id="enquire"><div class="td-wrap"><div class="td-cta"><div class="td-cta__content"><span class="td-kicker">Start with the right plan</span><h2>Get clear on your {{ $test['title'] }} preparation and application timeline.</h2><p>Tell us where you want to study, what you plan to study and your target intake. We’ll help you decide the most practical next step.</p><a href="{{ url('/#contact') }}" class="td-button">Book free counselling <span aria-hidden="true">→</span></a></div></div></div></section>
</main>

@include('mirror.partials.footer')
