@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav')

@php($tests = \App\Support\TestPrepCatalog::all())

<style>
    :root { --tp-navy: #0e2145; --tp-red: #e31e24; --tp-paper: #f5f7fb; --tp-ink: #15294d; --tp-muted: #627189; --tp-line: #dfe6ef; }
    .tp-page { overflow: clip; padding-bottom: 96px; background: var(--tp-paper); color: var(--tp-ink); }
    .tp-wrap { width: min(1280px, calc(100% - 48px)); margin-inline: auto; }
    .tp-kicker { display: inline-flex; align-items: center; gap: 10px; color: var(--tp-red); font-size: 12px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .tp-kicker::before { width: 28px; height: 2px; background: currentColor; content: ''; }
    .tp-button { display: inline-flex; min-height: 52px; align-items: center; justify-content: center; gap: 9px; padding: 0 22px; border-radius: 14px; background: var(--tp-red); color: #fff !important; font-size: 14px; font-weight: 800; transition: background .2s ease, transform .2s ease; }
    .tp-button:hover, .tp-button:focus-visible { background: #c91820; color: #fff; transform: translateY(-2px); }
    .tp-page :is(a, summary):focus-visible { outline: 3px solid rgba(227,30,36,.3); outline-offset: 3px; }

    .tp-hero { padding: 126px 0 74px; }
    .tp-hero__shell { position: relative; isolation: isolate; overflow: hidden; padding: 64px; border-radius: 34px; background: var(--tp-navy); box-shadow: 0 26px 65px rgba(14,33,69,.2); }
    .tp-hero__image, .tp-hero__overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
    .tp-hero__image { z-index: -2; object-fit: cover; object-position: center; }
    .tp-hero__overlay { z-index: -1; background: linear-gradient(90deg, rgba(7,23,51,.98) 0%, rgba(7,23,51,.9) 43%, rgba(7,23,51,.38) 74%, rgba(7,23,51,.18) 100%); }
    .tp-hero__shell::before { position: absolute; z-index: 0; inset: 0; opacity: .17; background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 48px 48px; mask-image: linear-gradient(90deg, #000, transparent 72%); content: ''; }
    .tp-hero__orb { position: absolute; z-index: 1; top: -188px; right: -140px; width: 560px; height: 560px; border: 76px solid rgba(227,30,36,.42); border-radius: 50%; }
    .tp-hero__content { position: relative; z-index: 2; max-width: 760px; }
    .tp-hero__eyebrow { color: rgba(255,255,255,.78); }
    .tp-hero__eyebrow::before { background: var(--tp-red); }
    .tp-hero h1 { max-width: 700px; margin: 18px 0 0; color: #fff; font-size: clamp(44px, 5vw, 68px); line-height: 1.04; font-weight: 800; letter-spacing: -.055em; text-wrap: balance; }
    .tp-hero h1 span { color: #ff6268; }
    .tp-hero p { max-width: 610px; margin: 20px 0 0; color: rgba(255,255,255,.77); font-size: 17px; line-height: 1.72; }
    .tp-hero__actions { display: flex; flex-wrap: wrap; gap: 13px; margin-top: 30px; }
    .tp-hero__alt { display: inline-flex; min-height: 52px; align-items: center; justify-content: center; padding: 0 21px; border: 1px solid rgba(255,255,255,.3); border-radius: 14px; color: #fff; font-size: 14px; font-weight: 800; transition: background .2s ease, border-color .2s ease; }
    .tp-hero__alt:hover, .tp-hero__alt:focus-visible { border-color: #fff; background: rgba(255,255,255,.1); color: #fff; }
    .tp-proof { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; max-width: 720px; margin-top: 34px; }
    .tp-proof__item { padding: 15px 16px; border: 1px solid rgba(255,255,255,.14); border-radius: 15px; background: rgba(255,255,255,.07); }
    .tp-proof__item b { display: block; color: #fff; font-size: 20px; line-height: 1.1; }
    .tp-proof__item span { display: block; margin-top: 4px; color: rgba(255,255,255,.65); font-size: 12px; line-height: 1.35; }

    .tp-main { padding: 10px 0 0; }
    .tp-intro { display: flex; align-items: end; justify-content: space-between; gap: 30px; }
    .tp-intro h2 { max-width: 760px; margin: 14px 0 0; color: var(--tp-navy); font-size: clamp(34px, 4vw, 52px); line-height: 1.1; font-weight: 800; letter-spacing: -.045em; text-wrap: balance; }
    .tp-intro h2 span { color: var(--tp-red); }
    .tp-intro p { max-width: 420px; margin: 0; color: var(--tp-muted); font-size: 16px; line-height: 1.72; }
    .tp-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; margin-top: 36px; }
    .tp-card { position: relative; display: flex; min-height: 434px; min-width: 0; padding: 26px; flex-direction: column; overflow: hidden; border: 1px solid #e0e7ef; border-radius: 24px; background: #fff; box-shadow: 0 12px 32px rgba(14,33,69,.065); color: var(--tp-navy); transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease; }
    .tp-card:hover { z-index: 2; border-color: rgba(227,30,36,.45); box-shadow: 0 22px 46px rgba(14,33,69,.13); color: var(--tp-navy); transform: translateY(-5px); }
    .tp-card__thumb { height: 148px; overflow: hidden; margin: -8px -8px 22px; border-radius: 16px; background: #dbe4ee; }
    .tp-card__image { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform .35s ease; }
    .tp-card:hover .tp-card__image { transform: scale(1.05); }
    .tp-card__head { display: flex; align-items: start; justify-content: space-between; gap: 12px; }
    .tp-card__icon { display: grid; width: 48px; height: 48px; place-items: center; border-radius: 15px; color: var(--tp-red); background: rgba(227,30,36,.1); }
    .tp-card__icon svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.8; }
    .tp-card__number { color: #9aa8ba; font-size: 13px; font-weight: 800; letter-spacing: .1em; }
    .tp-card__content { display: flex; min-height: 0; flex: 1; flex-direction: column; }
    .tp-card h3 { margin: 21px 0 0; color: var(--tp-navy); font-size: clamp(25px, 2.1vw, 31px); line-height: 1.14; font-weight: 800; letter-spacing: -.04em; }
    .tp-card__detail { display: block; max-width: 390px; margin: 8px 0 0; color: var(--tp-red); font-size: 13px; font-weight: 800; line-height: 1.42; }
    .tp-card__copy { display: -webkit-box; overflow: hidden; margin: 14px 0 0; color: var(--tp-muted); font-size: 14px; line-height: 1.58; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .tp-card__link { display: inline-flex; align-items: center; gap: 9px; margin-top: auto; padding-top: 18px; color: var(--tp-navy); font-size: 14px; font-weight: 800; }
    .tp-card__link svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2.2; transition: transform .2s ease; }
    .tp-card:hover .tp-card__link svg { transform: translateX(4px); }

    .tp-note { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 19px; margin-top: 30px; padding: 22px 25px; border: 1px solid var(--tp-line); border-radius: 20px; background: #fff; box-shadow: 0 10px 24px rgba(14,33,69,.035); }
    .tp-note__icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 13px; color: #fff; background: var(--tp-navy); }
    .tp-note__icon svg { width: 21px; height: 21px; fill: none; stroke: currentColor; stroke-width: 2; }
    .tp-note strong { display: block; color: var(--tp-navy); font-size: 15px; }
    .tp-note p { margin: 4px 0 0; color: var(--tp-muted); font-size: 13px; line-height: 1.55; }
    .tp-note a { color: var(--tp-red); font-size: 13px; font-weight: 800; white-space: nowrap; }
    .tp-cta { position: relative; overflow: hidden; margin-top: 38px; padding: 23px 30px; border-radius: 20px; background: var(--tp-red); box-shadow: 0 13px 28px rgba(227,30,36,.14); }
    .tp-cta::after { position: absolute; top: -82px; right: -76px; width: 210px; height: 210px; border: 30px solid rgba(255,255,255,.12); border-radius: 50%; content: ''; }
    .tp-cta__content { position: relative; z-index: 1; max-width: 680px; }
    .tp-cta .tp-kicker { color: rgba(255,255,255,.78); }
    .tp-cta h2 { margin: 7px 0 0; color: #fff; font-size: clamp(23px, 2.25vw, 30px); line-height: 1.13; font-weight: 800; letter-spacing: -.04em; text-wrap: balance; }
    .tp-cta p { max-width: 640px; margin: 8px 0 0; color: rgba(255,255,255,.86); font-size: 13px; line-height: 1.58; }
    .tp-cta .tp-button { min-height: 42px; margin-top: 14px; padding-inline: 18px; background: #fff; color: var(--tp-navy) !important; }
    .tp-cta .tp-button:hover { color: var(--tp-red) !important; }
    @media (max-width: 991px) { .tp-hero { padding-top: 106px; } .tp-intro { align-items: start; flex-direction: column; } .tp-intro p { max-width: 670px; } .tp-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 767px) { .tp-page { padding-bottom: calc(92px + env(safe-area-inset-bottom)); } .tp-wrap { width: min(100% - 28px, 620px); } .tp-hero { padding: 84px 0 47px; } .tp-hero__shell { padding: 31px 23px; border-radius: 25px; } .tp-hero__orb { top: -117px; right: -115px; width: 330px; height: 330px; border-width: 49px; } .tp-hero h1 { font-size: 40px; } .tp-hero p { font-size: 15px; } .tp-hero__actions { display: grid; grid-template-columns: 1fr; } .tp-hero__actions > * { width: 100%; } .tp-proof { grid-template-columns: 1fr; gap: 8px; } .tp-proof__item { display: flex; gap: 9px; align-items: baseline; padding: 12px 14px; } .tp-proof__item span { margin: 0; } .tp-main { padding-top: 4px; } .tp-intro h2 { font-size: 34px; } .tp-intro p { font-size: 15px; } .tp-grid { grid-template-columns: 1fr; gap: 14px; margin-top: 28px; } .tp-card { min-height: 406px; padding: 24px; border-radius: 24px; } .tp-card__thumb { height: 154px; margin: -7px -7px 20px; } .tp-card h3 { font-size: 29px; } .tp-card__detail { font-size: 14px; } .tp-card__copy { font-size: 14px; } .tp-note { grid-template-columns: auto 1fr; gap: 14px; padding: 19px; } .tp-note a { grid-column: 1 / -1; } .tp-cta { margin-top: 32px; padding: 22px 20px; border-radius: 18px; } .tp-cta h2 { font-size: 24px; } .tp-cta p { font-size: 13px; } }
    @media (prefers-reduced-motion: reduce) { .tp-page *, .tp-page *::before, .tp-page *::after { transition-duration: .01ms !important; scroll-behavior: auto !important; } }
</style>

<main class="tp-page">
    <section class="tp-hero">
        <div class="tp-wrap">
            <div class="tp-hero__shell">
                <img class="tp-hero__image" src="{{ asset('assets/services/university-admissions.jpg') }}" alt="" fetchpriority="high" width="1200" height="800">
                <div class="tp-hero__overlay"></div>
                <div class="tp-hero__orb" aria-hidden="true"></div>
                <div class="tp-hero__content">
                    <span class="tp-kicker tp-hero__eyebrow">Test preparation</span>
                    <h1>Prepare with purpose. <span>Test with confidence.</span></h1>
                    <p>Choose the right test for your destination, build the skills it measures and move into your university application with a score plan that makes sense.</p>
                    <div class="tp-hero__actions"><a href="#test-grid" class="tp-button">Explore test options <span aria-hidden="true">↓</span></a><a href="{{ url('/#contact') }}" class="tp-hero__alt">Talk to a counsellor</a></div>
                    <div class="tp-proof" aria-label="Test preparation highlights"><div class="tp-proof__item"><b>8</b><span>test-prep pathways</span></div><div class="tp-proof__item"><b>1:1</b><span>test and score planning</span></div><div class="tp-proof__item"><b>Mock-led</b><span>readiness and feedback</span></div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="tp-main" id="test-grid">
        <div class="tp-wrap">
            <div class="tp-intro"><div><span class="tp-kicker">Choose your exam</span><h2>One clear place to find your <span>next test.</span></h2></div><p>Explore what each exam measures, then open a detailed page for the format, timing, score scale and preparation route.</p></div>
            <div class="tp-grid" aria-label="Test preparation options">
                @foreach($tests as $test)
                    <a class="tp-card" href="{{ url('/tests/'.$test['slug']) }}" aria-label="Explore {{ $test['title'] }} test preparation">
                        <span class="tp-card__thumb"><img class="tp-card__image" src="{{ asset($test['image']) }}" alt="{{ $test['image_alt'] }}" loading="lazy" width="1200" height="800"></span>
                        <div class="tp-card__head"><span class="tp-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v5h5M9 13h6M9 17h4"/></svg></span><span class="tp-card__number">{{ $test['number'] }}</span></div>
                        <div class="tp-card__content"><h3>{{ $test['card_title'] ?? $test['title'] }}</h3><span class="tp-card__detail">{{ $test['eyebrow'] }}</span><span class="tp-card__copy">{{ $test['summary'] }}</span><span class="tp-card__link">View test details <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg></span></div>
                    </a>
                @endforeach
            </div>
            <div class="tp-note"><span class="tp-note__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM12 8v4M12 16h.01"/></svg></span><div><strong>Not sure which test your university needs?</strong><p>Requirements can differ by institution, course and intake. Speak to our team before you invest time in preparation or book an exam.</p></div><a href="{{ url('/#contact') }}">Get test guidance <span aria-hidden="true">→</span></a></div>
            <div class="tp-cta"><div class="tp-cta__content"><span class="tp-kicker">Start with a score plan</span><h2>Turn your preferred university into a practical test-prep timeline.</h2><p>Tell us your destination, programme and planned intake. We’ll help you identify the test, score and preparation path that support your application.</p><a href="{{ url('/#contact') }}" class="tp-button">Book free counselling <span aria-hidden="true">→</span></a></div></div>
        </div>
    </section>
</main>

@include('mirror.partials.footer')
