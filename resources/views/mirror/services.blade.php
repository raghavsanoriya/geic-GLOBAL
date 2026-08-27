@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav')

@php
    $services = [
        [
            'slug' => 'expert-counselling',
            'number' => '01',
            'title' => 'Expert Counselling',
            'label' => 'Start with clarity',
            'copy' => 'Get a practical study-abroad plan shaped around your academic background, goals, budget and preferred destinations.',
            'icon' => 'compass',
            'image' => 'assets/services/expert-counselling.jpg',
            'image_alt' => 'Students and advisers planning their education journey',
        ],
        [
            'slug' => 'sop-documentation',
            'number' => '02',
            'title' => 'SOP & Documentation',
            'label' => 'Tell your story well',
            'copy' => 'Create a persuasive SOP, organise academic documents and prepare letters of recommendation that meet each university’s standards.',
            'icon' => 'document',
            'image' => 'assets/services/sop-documentation.jpg',
            'image_alt' => 'Writing an academic statement and supporting documents',
        ],
        [
            'slug' => 'university-admissions',
            'number' => '03',
            'title' => 'University Admissions',
            'label' => 'Apply with confidence',
            'copy' => 'Shortlist the right universities, manage deadlines and submit complete, accurate applications without the usual stress.',
            'icon' => 'university',
            'image' => 'assets/services/university-admissions.jpg',
            'image_alt' => 'Students reviewing university options together',
        ],
        [
            'slug' => 'scholarship-guidance',
            'number' => '04',
            'title' => 'Scholarship Guidance',
            'label' => 'Make your ambition affordable',
            'copy' => 'Find merit, need-based and external scholarship opportunities, then build a strong application for each one.',
            'icon' => 'award',
            'image' => 'assets/services/scholarship-guidance.jpg',
            'image_alt' => 'University library resources for scholarship research',
        ],
        [
            'slug' => 'test-preparation',
            'number' => '05',
            'title' => 'Test Preparation',
            'label' => 'Score for your next step',
            'copy' => 'Structured coaching for IELTS, PTE, TOEFL, GRE, GMAT, SAT and German language exams with expert trainers.',
            'icon' => 'chart',
            'image' => 'assets/services/test-preparation.jpg',
            'image_alt' => 'Classroom prepared for English and entrance test coaching',
        ],
        [
            'slug' => 'visa-assistance',
            'number' => '06',
            'title' => 'Visa Assistance',
            'label' => 'Prepare every detail',
            'copy' => 'Receive up-to-date guidance for documentation, financial proofing and interview preparation for a meticulous visa application.',
            'icon' => 'passport',
            'image' => 'assets/services/visa-assistance.jpg',
            'image_alt' => 'Passport and travel documents at an airport',
        ],
        [
            'slug' => 'health-insurance',
            'number' => '07',
            'title' => 'Health Insurance',
            'label' => 'Travel covered',
            'copy' => 'Arrange the student health cover and international insurance required by your destination and education provider.',
            'icon' => 'shield',
            'image' => 'assets/services/health-insurance.jpg',
            'image_alt' => 'Healthcare professional representing student health cover',
        ],
        [
            'slug' => 'loans-financial-guide',
            'number' => '08',
            'title' => 'Loans & Financial Guide',
            'label' => 'Plan your finances',
            'copy' => 'Understand education loans, forex, blocked accounts and international banking before you begin your journey.',
            'icon' => 'wallet',
            'image' => 'assets/services/loans-financial-guide.jpg',
            'image_alt' => 'Education finance and budget planning',
        ],
        [
            'slug' => 'accommodation-assistance',
            'number' => '09',
            'title' => 'Accommodation Assistance',
            'label' => 'Arrive feeling at home',
            'copy' => 'Explore safe on-campus, private, shared and homestay options that work for your location and budget.',
            'icon' => 'home',
            'image' => 'assets/services/accommodation-assistance.jpg',
            'image_alt' => 'Students together in a shared residence',
        ],
        [
            'slug' => 'pre-post-departure',
            'number' => '10',
            'title' => 'Pre & Post Departure',
            'label' => 'Stay supported after the offer',
            'copy' => 'Get help with packing, cultural preparation, airport arrival, SIM cards and settling into your new city.',
            'icon' => 'flight',
            'image' => 'assets/services/pre-post-departure.jpg',
            'image_alt' => 'World map and aeroplane for an overseas journey',
        ],
    ];
@endphp

<style>
    .tg-services-page { overflow: hidden; background: #f6f8fb; color: #0e2145; }
    .tg-services-page * { box-sizing: border-box; }
    .tg-services-hero { padding: 128px 0 72px; background: #f6f8fb; }
    .tg-services-hero__shell { position: relative; overflow: hidden; padding: 62px; border-radius: 36px; background: #0e2145; box-shadow: 0 26px 65px rgba(10, 29, 62, .2); }
    .tg-services-hero__shell::before { content: ''; position: absolute; inset: 0; opacity: .2; background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px; mask-image: linear-gradient(90deg, #000 0%, transparent 88%); }
    .tg-services-hero__shell::after { content: ''; position: absolute; z-index: 0; top: -210px; right: -140px; width: 600px; height: 600px; border: 85px solid rgba(227,30,36,.42); border-radius: 50%; }
    .tg-services-hero__copy, .tg-services-hero__visual { position: relative; z-index: 1; }
    .tg-services-eyebrow { display: inline-flex; align-items: center; gap: 10px; color: rgba(255,255,255,.78); font-size: 13px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .tg-services-eyebrow::before { width: 30px; height: 2px; background: #e31e24; content: ''; }
    .tg-services-hero h1 { max-width: 640px; margin: 18px 0 0; color: #fff; font-size: clamp(42px, 4.7vw, 66px); line-height: 1.06; font-weight: 700; letter-spacing: -.045em; text-wrap: balance; }
    .tg-services-hero h1 span { color: #ef3b42; }
    .tg-services-hero__lead { max-width: 600px; margin: 22px 0 0; color: rgba(255,255,255,.76); font-size: 17px; line-height: 1.72; }
    .tg-services-hero__actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 30px; }
    .tg-services-hero__secondary { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 22px; border: 1px solid rgba(255,255,255,.3); border-radius: 14px; color: #fff; font-weight: 700; transition: background .2s ease, border-color .2s ease; }
    .tg-services-hero__secondary:hover, .tg-services-hero__secondary:focus-visible { border-color: #fff; background: rgba(255,255,255,.1); color: #fff; }
    .tg-services-proof { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 34px; }
    .tg-services-proof__item { padding: 15px 16px; border: 1px solid rgba(255,255,255,.14); border-radius: 15px; background: rgba(255,255,255,.07); }
    .tg-services-proof__item strong { display: block; color: #fff; font-size: 22px; line-height: 1.1; }
    .tg-services-proof__item span { display: block; margin-top: 5px; color: rgba(255,255,255,.64); font-size: 12px; line-height: 1.35; }
    .tg-services-hero__visual { display: grid; grid-template-columns: 1.08fr .92fr; gap: 14px; min-height: 415px; }
    .tg-services-hero__picture { position: relative; overflow: hidden; min-height: 190px; border: 1px solid rgba(255,255,255,.15); border-radius: 22px; background: #263b5c; }
    .tg-services-hero__picture--large { grid-row: span 2; }
    .tg-services-hero__picture img { width: 100%; height: 100%; object-fit: cover; }
    .tg-services-hero__picture::after { content: ''; position: absolute; inset: 0; background: linear-gradient(150deg, rgba(14,33,69,.04), rgba(14,33,69,.7)); }
    .tg-services-hero__picture span { position: absolute; z-index: 2; left: 15px; bottom: 14px; padding: 7px 11px; border-radius: 999px; background: rgba(14,33,69,.78); color: #fff; font-size: 12px; font-weight: 700; backdrop-filter: blur(8px); }
    .tg-services-hero__stamp { display: flex; align-items: center; gap: 10px; padding: 14px; border-radius: 20px; background: #fff; color: #0e2145; box-shadow: 0 14px 30px rgba(0,0,0,.18); }
    .tg-services-hero__stamp b { display: block; font-size: 18px; line-height: 1; }
    .tg-services-hero__stamp small { display: block; margin-top: 4px; color: #6d7b8d; font-size: 11px; line-height: 1.25; }
    .tg-services-hero__stamp-icon { display: grid; width: 38px; height: 38px; flex: 0 0 38px; place-items: center; border-radius: 12px; background: rgba(227,30,36,.1); color: #e31e24; }
    .tg-services-main { padding: 88px 0 96px; }
    .tg-services-intro { display: flex; align-items: end; justify-content: space-between; gap: 28px; }
    .tg-services-kicker { display: inline-flex; align-items: center; gap: 10px; color: #e31e24; font-size: 13px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .tg-services-kicker::before { width: 30px; height: 2px; background: currentColor; content: ''; }
    .tg-services-intro h2 { max-width: 750px; margin: 15px 0 0; color: #0e2145; font-size: clamp(34px, 3.6vw, 50px); line-height: 1.12; font-weight: 700; letter-spacing: -.04em; text-wrap: balance; }
    .tg-services-intro h2 span { color: #e31e24; }
    .tg-services-intro p { max-width: 405px; margin: 0; color: #66758b; font-size: 16px; line-height: 1.7; }
    .tg-services-filter { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 32px; }
    .tg-services-filter span { display: inline-flex; min-height: 36px; align-items: center; padding: 0 14px; border: 1px solid #dfe6ef; border-radius: 999px; background: #fff; color: #5d6d83; font-size: 13px; font-weight: 700; }
    .tg-services-filter span:first-child { border-color: #0e2145; background: #0e2145; color: #fff; }
    .tg-services-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; margin-top: 28px; }
    .tg-service-card { position: relative; display: flex; min-height: 412px; min-width: 0; overflow: hidden; padding: 26px; flex-direction: column; border: 1px solid #e0e7ef; border-radius: 24px; background: #fff; box-shadow: 0 12px 32px rgba(14,33,69,.065); transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
    .tg-service-card:hover { z-index: 2; border-color: rgba(227,30,36,.45); box-shadow: 0 22px 46px rgba(14,33,69,.13); transform: translateY(-5px); }
    .tg-service-card--featured { grid-column: span 2; min-height: 378px; border: 0; background: #0e2145; }
    .tg-service-card--featured::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, #0e2145 14%, rgba(14,33,69,.91) 48%, rgba(14,33,69,.23) 100%); }
    .tg-service-card--featured > * { position: relative; z-index: 1; }
    .tg-service-card--featured .tg-service-card__image { position: absolute; z-index: 0; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .75; }
    .tg-service-card--featured .tg-service-card__number { color: rgba(255,255,255,.5); }
    .tg-service-card--featured .tg-service-card__icon { background: rgba(255,255,255,.12); color: #fff; }
    .tg-service-card--featured h3 { max-width: 440px; color: #fff; font-size: 32px; }
    .tg-service-card--featured .tg-service-card__label, .tg-service-card--featured .tg-service-card__copy { color: rgba(255,255,255,.72); }
    .tg-service-card--featured .tg-service-card__link { color: #fff; }
    .tg-service-card__thumb { height: 128px; overflow: hidden; margin: -8px -8px 20px; border-radius: 16px; background: #dbe4ee; }
    .tg-service-card__thumb img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-service-card:hover .tg-service-card__thumb img { transform: scale(1.05); }
    .tg-service-card__top { display: flex; align-items: start; justify-content: space-between; gap: 18px; }
    .tg-service-card__icon { display: grid; width: 48px; height: 48px; flex: 0 0 48px; place-items: center; border-radius: 15px; background: rgba(227,30,36,.1); color: #e31e24; }
    .tg-service-card__icon svg { width: 24px; height: 24px; }
    .tg-service-card__number { color: #9aa8ba; font-size: 13px; font-weight: 800; letter-spacing: .1em; }
    .tg-service-card h3 { margin: 23px 0 0; color: #0e2145; font-size: 22px; line-height: 1.22; font-weight: 700; letter-spacing: -.025em; }
    .tg-service-card__label { margin: 8px 0 0; color: #e31e24; font-size: 13px; font-weight: 700; }
    .tg-service-card__copy { margin: 14px 0 0; color: #69788d; font-size: 14px; line-height: 1.68; }
    .tg-service-card__link { display: inline-flex; align-items: center; gap: 9px; margin-top: auto; padding-top: 20px; color: #0e2145; font-size: 14px; font-weight: 800; }
    .tg-service-card__link svg { width: 19px; height: 19px; transition: transform .2s ease; }
    .tg-service-card:hover .tg-service-card__link svg { transform: translateX(4px); }
    .tg-services-cta { position: relative; overflow: hidden; margin-top: 54px; padding: 32px 38px; border-radius: 24px; background: #e31e24; box-shadow: 0 16px 34px rgba(227,30,36,.16); }
    .tg-services-cta::after { content: ''; position: absolute; width: 280px; height: 280px; right: -92px; top: -104px; border: 40px solid rgba(255,255,255,.12); border-radius: 50%; }
    .tg-services-cta__content { position: relative; z-index: 2; }
    .tg-services-cta__content h2 { max-width: 820px; margin: 9px 0 0; color: #fff; font-size: clamp(26px, 2.7vw, 36px); line-height: 1.14; font-weight: 700; letter-spacing: -.035em; }
    .tg-services-cta__content p { max-width: 730px; margin: 10px 0 0; color: rgba(255,255,255,.84); font-size: 14px; line-height: 1.65; }
    .tg-services-cta__button { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; margin-top: 18px; padding: 0 22px; border-radius: 13px; background: #fff; color: #0e2145; font-weight: 800; box-shadow: 0 10px 20px rgba(78,0,0,.15); }
    .tg-services-cta__button:hover { color: #e31e24; transform: translateY(-2px); }
    .tg-services-cta__image { position: absolute; z-index: 1; top: 0; right: 0; width: 41%; height: 100%; object-fit: cover; opacity: .6; mix-blend-mode: multiply; }
    .tg-services-cta__image-overlay { position: absolute; z-index: 1; inset: 0; background: linear-gradient(90deg, #e31e24 43%, rgba(227,30,36,.12) 83%); }
    .tg-services-page :is(a, button):focus-visible { outline: 3px solid #f7b4b7; outline-offset: 3px; }
    @media (max-width: 991px) {
        .tg-services-hero { padding: 106px 0 58px; }
        .tg-services-hero__shell { padding: 42px; }
        .tg-services-hero__visual { min-height: 330px; margin-top: 34px; }
        .tg-services-intro { align-items: start; flex-direction: column; }
        .tg-services-intro p { max-width: 660px; }
        .tg-services-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .tg-services-page { padding-bottom: calc(90px + env(safe-area-inset-bottom)); }
        .tg-services-hero { padding: 84px 0 42px; }
        .tg-services-hero .container, .tg-services-main .container { padding-right: 14px; padding-left: 14px; }
        .tg-services-hero__shell { padding: 30px 22px; border-radius: 25px; }
        .tg-services-hero h1 { font-size: 40px; }
        .tg-services-hero__lead { font-size: 15px; }
        .tg-services-proof { grid-template-columns: 1fr; gap: 9px; }
        .tg-services-proof__item { display: flex; align-items: baseline; gap: 9px; padding: 12px 14px; }
        .tg-services-proof__item span { margin: 0; }
        .tg-services-hero__visual { min-height: 280px; grid-template-columns: 1.1fr .9fr; gap: 9px; }
        .tg-services-hero__picture { min-height: 130px; border-radius: 16px; }
        .tg-services-main { padding: 60px 0 70px; }
        .tg-services-intro h2 { font-size: 34px; }
        .tg-services-intro p { font-size: 15px; }
        .tg-services-filter { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; scrollbar-width: none; }
        .tg-services-filter::-webkit-scrollbar { display: none; }
        .tg-services-filter span { flex: 0 0 auto; }
        .tg-services-grid { grid-template-columns: 1fr; gap: 14px; }
        .tg-service-card, .tg-service-card--featured { min-height: 370px; padding: 22px; }
        .tg-service-card--featured { grid-column: span 1; }
        .tg-service-card--featured h3 { font-size: 28px; }
        .tg-service-card__thumb { height: 142px; margin: -6px -6px 18px; }
        .tg-services-cta { margin-top: 42px; padding: 27px 22px 138px; border-radius: 21px; }
        .tg-services-cta__content h2 { font-size: 27px; }
        .tg-services-cta__content p { font-size: 14px; }
        .tg-services-cta__image { width: 100%; height: 164px; top: auto; bottom: 0; opacity: .48; }
        .tg-services-cta__image-overlay { background: linear-gradient(180deg, #e31e24 18%, rgba(227,30,36,.12) 100%); }
    }
    @media (prefers-reduced-motion: reduce) { .tg-services-page *, .tg-services-page *::before, .tg-services-page *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
</style>

<main class="tg-services-page">
    <section class="tg-services-hero">
        <div class="container">
            <div class="tg-services-hero__shell">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7">
                        <div class="tg-services-hero__copy">
                            <div class="tg-services-eyebrow">Trans Globe Indore services</div>
                            <h1>Every expert step for your <span>global future.</span></h1>
                            <p class="tg-services-hero__lead">From your first shortlist to your first day abroad, get one joined-up team for every important decision, document and deadline.</p>
                            <div class="tg-services-hero__actions">
                                <a href="{{ url('/#contact') }}" class="btn-flip-effect btn btn-primary btn-lg gap-8 text-white" data-text="Book Free Counselling"><span class="btn-flip-effect__text text-white">Book Free Counselling</span></a>
                                <a href="#services-grid" class="tg-services-hero__secondary">Explore every service</a>
                            </div>
                            <div class="tg-services-proof" aria-label="Trans Globe Indore service highlights">
                                <div class="tg-services-proof__item"><strong>800+</strong><span>partner universities worldwide</span></div>
                                <div class="tg-services-proof__item"><strong>98%</strong><span>visa success rate</span></div>
                                <div class="tg-services-proof__item"><strong>32+ yrs</strong><span>of study-abroad expertise</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="tg-services-hero__visual" aria-label="Students supported at every step of their overseas education journey">
                            <div class="tg-services-hero__picture tg-services-hero__picture--large"><img src="{{ asset('assets/transglobe/services/services-team.avif') }}" alt="Trans Globe student support team" width="768" height="768"><span>Profile-first guidance</span></div>
                            <div class="tg-services-hero__picture"><img src="{{ asset('assets/transglobe/services/services-hero.avif') }}" alt="International education journey" width="1366" height="685"><span>One connected plan</span></div>
                            <div class="tg-services-hero__stamp"><span class="tg-services-hero__stamp-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6 9 17l-5-5"/></svg></span><span><b>10 services</b><small>One trusted team</small></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tg-services-main" id="services-grid">
        <div class="container">
            <div class="tg-services-intro">
                <div>
                    <div class="tg-services-kicker">A complete support system</div>
                    <h2>One plan. Every service. <span>Nothing left to chance.</span></h2>
                </div>
                <p>Trans Globe’s service framework is designed around the decisions students actually have to make — before, during and after their application.</p>
            </div>
            <div class="tg-services-filter" aria-label="Service categories"><span>All services</span><span>Applications</span><span>Test prep</span><span>Funding</span><span>Visa & arrival</span></div>

            <div class="tg-services-grid">
                @foreach ($services as $service)
                    <article class="tg-service-card {{ $loop->first ? 'tg-service-card--featured' : '' }}">
                        @if ($loop->first)
                            <img class="tg-service-card__image" src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}" width="1200" height="800">
                        @else
                            <div class="tg-service-card__thumb"><img src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}" loading="lazy" width="740" height="390"></div>
                        @endif
                        <div class="tg-service-card__top">
                            <span class="tg-service-card__icon" aria-hidden="true">
                                @if ($service['icon'] === 'compass')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="m14.7 9.3-1.8 3.6-3.6 1.8 1.8-3.6 3.6-1.8Z"/></svg>
                                @elseif ($service['icon'] === 'document')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg>
                                @elseif ($service['icon'] === 'university')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 10 9-5 9 5-9 5-9-5Z"/><path d="M5 12v6m4-4v4m6-4v4m4-6v6M3 20h18"/></svg>
                                @elseif ($service['icon'] === 'award')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="m8.5 11.5-1 8 4.5-2 4.5 2-1-8"/></svg>
                                @elseif ($service['icon'] === 'chart')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5m0 14h16M8 15l3-3 3 2 5-6"/></svg>
                                @elseif ($service['icon'] === 'passport')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="11" r="3"/><path d="M8 17c1.1-1.4 2.4-2 4-2s2.9.6 4 2"/></svg>
                                @elseif ($service['icon'] === 'shield')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 19 6v5c0 4.8-3 8.3-7 10-4-1.7-7-5.2-7-10V6z"/><path d="m9 12 2 2 4-4"/></svg>
                                @elseif ($service['icon'] === 'wallet')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7a3 3 0 0 1 3-3h11v16H6a2 2 0 0 1-2-2z"/><path d="M4 7h14v5h-4a2 2 0 0 0 0 4h4"/><circle cx="14" cy="14" r=".5" fill="currentColor"/></svg>
                                @elseif ($service['icon'] === 'home')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 11 9-7 9 7v9H3z"/><path d="M9 20v-6h6v6"/></svg>
                                @else <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 16.5 21 8l-7 13-2.5-6z"/><path d="m11.5 15 2.5-2.5"/></svg>@endif
                            </span>
                            <span class="tg-service-card__number">{{ $service['number'] }}</span>
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p class="tg-service-card__label">{{ $service['label'] }}</p>
                        <p class="tg-service-card__copy">{{ $service['copy'] }}</p>
                        <a class="tg-service-card__link" href="{{ url('/services/'.$service['slug']) }}">View service details <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h13M13 6l6 6-6 6"/></svg></a>
                    </article>
                @endforeach
            </div>

            <section class="tg-services-cta" aria-labelledby="services-cta-title">
                <img class="tg-services-cta__image" src="{{ asset('assets/transglobe/services/services-hero.avif') }}" alt="Students beginning their study-abroad journey" width="1366" height="685">
                <div class="tg-services-cta__image-overlay"></div>
                <div class="tg-services-cta__content">
                    <div class="tg-services-eyebrow">Start where you are</div>
                    <h2 id="services-cta-title">Your first conversation can change the whole journey.</h2>
                    <p>You do not need every answer today. Tell us where you are now, and our team will help you map the next practical step.</p>
                    <a href="{{ url('/#contact') }}" class="tg-services-cta__button">Book my free counselling session</a>
                </div>
            </section>
        </div>
    </section>
</main>

@include('mirror.partials.footer')
