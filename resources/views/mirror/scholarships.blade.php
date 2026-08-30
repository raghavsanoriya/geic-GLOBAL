@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav')

@php($scholarships = \App\Support\ScholarshipCatalog::all())

<style>
    :root { --sp-navy:#0e2145; --sp-red:#e31e24; --sp-soft:#f5f7fb; --sp-ink:#15294d; --sp-muted:#66768c; --sp-line:#dfe7f0; }
    .sp-page { overflow: clip; background: var(--sp-soft); color: var(--sp-ink); }
    .sp-wrap { width: min(1280px, calc(100% - 48px)); margin-inline: auto; }
    .sp-section { padding: 90px 0; }
    .sp-kicker { display: inline-flex; align-items: center; gap: 10px; color: var(--sp-red); font-size: 12px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .sp-kicker::before { width: 29px; height: 2px; background: currentColor; content: ''; }
    .sp-heading { max-width: 760px; margin: 15px 0 0; color: var(--sp-navy); font-size: clamp(35px,4vw,52px); line-height: 1.08; font-weight: 800; letter-spacing: -.048em; text-wrap: balance; }
    .sp-lead { max-width: 655px; margin: 17px 0 0; color: var(--sp-muted); font-size: 17px; line-height: 1.74; }
    .sp-page :is(a,button):focus-visible { outline: 3px solid rgba(227,30,36,.36); outline-offset: 3px; }
    .sp-button { display: inline-flex; min-height: 53px; align-items: center; justify-content: center; gap: 9px; padding: 0 23px; border: 1px solid transparent; border-radius: 14px; background: var(--sp-red); color: #fff !important; font-size: 14px; font-weight: 800; transition: transform .2s ease, background .2s ease; }
    .sp-button:hover { background: #c81920; transform: translateY(-2px); }
    .sp-button--ghost { border-color: rgba(255,255,255,.32); background: rgba(255,255,255,.08); }
    .sp-button--ghost:hover { border-color: #fff; background: #fff; color: var(--sp-navy) !important; }

    .sp-hero { padding: 128px 0 0; background: var(--sp-soft); }
    .sp-hero__shell { position: relative; overflow: hidden; min-height: 570px; display: flex; align-items: flex-end; border-radius: 36px; background: var(--sp-navy); box-shadow: 0 28px 68px rgba(14,33,69,.2); }
    .sp-hero__image, .sp-hero__overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
    .sp-hero__image { object-fit: cover; object-position: center; }
    .sp-hero__overlay { background: linear-gradient(90deg, rgba(5,17,39,.97), rgba(6,22,50,.86) 44%, rgba(6,22,50,.18)); }
    .sp-hero__content { position: relative; z-index: 1; width: min(760px,100%); padding: 65px; }
    .sp-hero__pill { display: inline-flex; padding: 8px 13px; border: 1px solid rgba(255,255,255,.23); border-radius: 999px; color: #fff; background: rgba(255,255,255,.08); font-size: 12px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; backdrop-filter: blur(12px); }
    .sp-hero h1 { margin: 18px 0 0; color: #fff; font-size: clamp(47px,5.6vw,70px); line-height: 1.01; font-weight: 800; letter-spacing: -.058em; text-wrap: balance; }
    .sp-hero h1 span { color: #ff555e; }
    .sp-hero p { max-width: 650px; margin: 19px 0 0; color: rgba(255,255,255,.83); font-size: 17px; line-height: 1.72; }
    .sp-hero__actions { display: flex; flex-wrap: wrap; gap: 13px; margin-top: 29px; }
    .sp-hero__stamp { position: absolute; z-index: 2; top: 34px; right: 38px; width: 165px; padding: 19px; border: 1px solid rgba(255,255,255,.22); border-radius: 21px; color: #fff; background: rgba(14,33,69,.55); backdrop-filter: blur(12px); }
    .sp-hero__stamp strong { display: block; color: #ff8085; font-size: 29px; line-height: 1; }
    .sp-hero__stamp span { display: block; margin-top: 7px; color: rgba(255,255,255,.76); font-size: 12px; line-height: 1.4; }
    .sp-proof { position: relative; z-index: 2; display: grid; grid-template-columns: repeat(4,1fr); margin: -1px 29px 0; border: 1px solid var(--sp-line); border-radius: 0 0 26px 26px; background: #fff; box-shadow: 0 18px 42px rgba(14,33,69,.08); }
    .sp-proof__item { padding: 23px 25px; border-right: 1px solid var(--sp-line); }
    .sp-proof__item:last-child { border-right: 0; }
    .sp-proof__item strong { display: block; color: var(--sp-navy); font-size: 24px; line-height: 1.1; }
    .sp-proof__item span { display: block; margin-top: 6px; color: var(--sp-muted); font-size: 12px; line-height: 1.4; }

    .sp-intro { display: flex; align-items: end; justify-content: space-between; gap: 40px; }
    .sp-intro p { max-width: 385px; margin: 0; color: var(--sp-muted); font-size: 16px; line-height: 1.7; }
    .sp-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 19px; margin-top: 40px; }
    .sp-card { position: relative; min-width: 0; overflow: hidden; display: flex; min-height: 372px; padding: 26px; flex-direction: column; border: 1px solid var(--sp-line); border-radius: 25px; background: #fff; box-shadow: 0 11px 30px rgba(14,33,69,.06); transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease; }
    .sp-card:hover { z-index: 1; border-color: rgba(227,30,36,.45); box-shadow: 0 21px 44px rgba(14,33,69,.13); transform: translateY(-5px); }
    .sp-card--featured { grid-column: span 2; min-height: 405px; border: 0; background: var(--sp-navy); }
    .sp-card__image { position: absolute; z-index: 0; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .75; }
    .sp-card__image-overlay { position: absolute; z-index: 0; inset: 0; background: linear-gradient(90deg, var(--sp-navy) 8%, rgba(14,33,69,.88) 50%, rgba(14,33,69,.28)); }
    .sp-card > *:not(.sp-card__image):not(.sp-card__image-overlay) { position: relative; z-index: 1; }
    .sp-card__top { display: flex; align-items: center; justify-content: space-between; gap: 15px; }
    .sp-card__flag { width: 43px; height: 31px; overflow: hidden; border-radius: 5px; box-shadow: 0 3px 9px rgba(14,33,69,.16); object-fit: cover; }
    .sp-card__number { color: #9ba8b8; font-size: 12px; font-weight: 800; letter-spacing: .1em; }
    .sp-card h3 { margin: 21px 0 0; color: var(--sp-navy); font-size: 25px; line-height: 1.15; font-weight: 800; letter-spacing: -.03em; }
    .sp-card p { margin: 12px 0 0; color: var(--sp-muted); font-size: 14px; line-height: 1.68; }
    .sp-card__tags { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 17px; }
    .sp-card__tags span { padding: 7px 9px; border-radius: 999px; color: #52657d; background: var(--sp-soft); font-size: 11px; font-weight: 800; }
    .sp-card__link { display: inline-flex; align-items: center; gap: 8px; margin-top: auto; padding-top: 20px; color: var(--sp-navy); font-size: 14px; font-weight: 800; }
    .sp-card__link svg { width: 19px; height: 19px; transition: transform .2s ease; }
    .sp-card:hover .sp-card__link svg { transform: translateX(4px); }
    .sp-card--featured h3, .sp-card--featured .sp-card__link { color: #fff; }
    .sp-card--featured p { max-width: 520px; color: rgba(255,255,255,.76); }
    .sp-card--featured .sp-card__number { color: rgba(255,255,255,.54); }
    .sp-card--featured .sp-card__tags span { color: #fff; background: rgba(255,255,255,.14); }

    .sp-support { position: relative; overflow: hidden; margin-top: 54px; padding: 32px 38px; border-radius: 24px; background: var(--sp-red); box-shadow: 0 16px 34px rgba(227,30,36,.16); }
    .sp-support::after { position: absolute; top: -104px; right: -92px; width: 280px; height: 280px; border: 40px solid rgba(255,255,255,.12); border-radius: 50%; content: ''; }
    .sp-support__inner { position: relative; z-index: 1; max-width: 820px; }
    .sp-support .sp-kicker { color: rgba(255,255,255,.75); }
    .sp-support h2 { margin: 9px 0 0; color: #fff; font-size: clamp(26px,2.7vw,36px); line-height: 1.14; font-weight: 800; letter-spacing: -.045em; text-wrap: balance; }
    .sp-support p { max-width: 730px; margin: 10px 0 0; color: rgba(255,255,255,.85); font-size: 14px; line-height: 1.65; }
    .sp-support .sp-button { min-height: 48px; margin-top: 18px; color: var(--sp-navy) !important; background: #fff; }
    .sp-support .sp-button:hover { color: var(--sp-red) !important; }
    @media (max-width: 991px) { .sp-hero { padding-top: 105px; } .sp-intro { align-items: start; flex-direction: column; } .sp-intro p { max-width: 650px; } .sp-grid { grid-template-columns: repeat(2,minmax(0,1fr)); } .sp-proof { grid-template-columns: repeat(2,1fr); } .sp-proof__item:nth-child(2) { border-right: 0; } .sp-proof__item:nth-child(-n+2) { border-bottom: 1px solid var(--sp-line); } }
    @media (max-width: 767px) { .sp-page { padding-bottom: 76px; } .sp-wrap { width: min(100% - 28px,620px); } .sp-section { padding: 58px 0; } .sp-heading { font-size: 33px; } .sp-lead { font-size: 15px; } .sp-hero { padding-top: 82px; } .sp-hero__shell { min-height: 580px; border-radius: 25px; } .sp-hero__overlay { background: linear-gradient(180deg,rgba(5,17,39,.2),rgba(5,17,39,.97) 72%); } .sp-hero__content { padding: 29px 24px 32px; } .sp-hero h1 { font-size: 43px; } .sp-hero p { font-size: 15px; line-height: 1.62; } .sp-hero__actions { display: grid; grid-template-columns: 1fr; } .sp-button { width: 100%; } .sp-hero__stamp { top: 17px; right: 17px; width: 124px; padding: 14px; border-radius: 17px; } .sp-hero__stamp strong { font-size: 22px; } .sp-proof { display: flex; overflow-x: auto; margin: 0 10px; scroll-snap-type:x mandatory; scrollbar-width:none; } .sp-proof::-webkit-scrollbar { display:none; } .sp-proof__item { flex: 0 0 78%; border-right: 1px solid var(--sp-line) !important; border-bottom: 0 !important; scroll-snap-align:start; } .sp-grid { grid-template-columns: 1fr; gap: 14px; margin-top: 29px; } .sp-card,.sp-card--featured { min-height: 307px; padding: 23px; } .sp-card--featured { grid-column: auto; } .sp-card--featured h3 { font-size: 29px; } .sp-support { margin-top: 42px; padding: 27px 22px; border-radius: 21px; } .sp-support h2 { font-size: 27px; } .sp-support p { font-size: 14px; } }
    @media (prefers-reduced-motion:reduce) { .sp-page *, .sp-page *::before, .sp-page *::after { transition-duration:.01ms !important; scroll-behavior:auto !important; } }
</style>

<main class="sp-page">
    <section class="sp-hero">
        <div class="sp-wrap">
            <div class="sp-hero__shell">
                <img class="sp-hero__image" src="{{ asset($cms['hero_image'] ?? 'assets/transglobe/destinations/australia-detail-hero.jpg') }}" alt="Students exploring overseas education opportunities" fetchpriority="high" width="1600" height="900">
                <div class="sp-hero__overlay"></div><div class="sp-hero__stamp"><strong>Up to 80%</strong><span>scholarship opportunity for eligible profiles</span></div>
                <div class="sp-hero__content"><span class="sp-hero__pill">Trans Globe Indore scholarships</span><h1>{{ $cms['hero_title'] ?? 'Fund your future, without the guesswork.' }}</h1><p>{{ $cms['hero_copy'] ?? 'Studying abroad does not have to feel out of reach. Discover scholarship opportunities that match your profile, then apply with a clear, well-prepared plan.' }}</p><div class="sp-hero__actions"><a href="{{ url('/contact#enquiry') }}" class="sp-button">Get free scholarship counselling <span aria-hidden="true">→</span></a><a href="#destinations" class="sp-button sp-button--ghost">Explore destinations</a></div></div>
            </div>
            <div class="sp-proof" aria-label="Trans Globe Indore scholarship highlights"><div class="sp-proof__item"><strong>70,250+</strong><span>students helped worldwide</span></div><div class="sp-proof__item"><strong>800+</strong><span>top-ranked university options</span></div><div class="sp-proof__item"><strong>98.3%</strong><span>acceptance rate</span></div><div class="sp-proof__item"><strong>9</strong><span>scholarship destinations to explore</span></div></div>
        </div>
    </section>

    <section class="sp-section" id="destinations"><div class="sp-wrap"><div class="sp-intro"><div><div class="sp-kicker">Scholarship destinations</div><h2 class="sp-heading">Find the scholarship routes that fit your destination.</h2></div><p>Explore funding possibilities by country, from university merit awards to government, research and specialist opportunities.</p></div>
        <div class="sp-grid">
            @foreach($scholarships as $scholarship)
                <article class="sp-card {{ $loop->first ? 'sp-card--featured' : '' }}">
                    @if($loop->first)<img class="sp-card__image" src="{{ asset($scholarship['image']) }}" alt="{{ $scholarship['image_alt'] }}" loading="lazy" width="1200" height="800"><div class="sp-card__image-overlay"></div>@endif
                    <div class="sp-card__top"><img class="sp-card__flag" src="{{ asset('assets/transglobe/destinations/flags/'.$scholarship['flag']) }}" alt="{{ $scholarship['name'] }} flag"><span class="sp-card__number">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span></div><h3>{{ $scholarship['name'] }}</h3><p>{{ $scholarship['tagline'] }}</p><div class="sp-card__tags"><span>{{ $scholarship['awards'][0][0] }}</span><span>{{ $scholarship['awards'][1][0] }}</span></div><a href="{{ url('/scholarships/'.$scholarship['slug']) }}" class="sp-card__link">View scholarship details <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h13M13 6l6 6-6 6"/></svg></a>
                </article>
            @endforeach
        </div>
        <section class="sp-support"><div class="sp-support__inner"><div class="sp-kicker">Scholarship-ready profile</div><h2>The right funding opportunity starts with the right preparation.</h2><p>We help you match your academic strengths, destination and course plan to credible scholarship options—then prepare each application with care.</p><a href="{{ url('/contact#enquiry') }}" class="sp-button">Book my free counselling session <span aria-hidden="true">→</span></a></div></section>
    </div></section>
</main>

@include('mirror.partials.footer')
