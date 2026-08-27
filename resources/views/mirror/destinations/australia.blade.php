@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => 'destinations', 'mobileBackLabel' => 'Back to destinations'])

@php
    $australiaBenefits = [
        ['title' => 'World-class universities', 'copy' => 'Research-intensive institutions, including the Group of Eight, with globally respected qualifications.'],
        ['title' => 'Work while studying', 'copy' => 'Eligible international students can work during teaching periods and more during scheduled breaks.'],
        ['title' => 'Safe and welcoming', 'copy' => 'Strong student protections, multicultural cities and a high standard of living support international students.'],
        ['title' => 'Wide course choice', 'copy' => 'Flexible pathways across business, engineering, IT, health, hospitality, creative arts and more.'],
        ['title' => 'Practical learning', 'copy' => 'Internships, industry projects, placements and work-integrated learning build career-ready experience.'],
        ['title' => 'Strong value', 'copy' => 'Internationally recognised education with scholarships and practical pathways that support long-term outcomes.'],
    ];

    $australiaJourney = [
        ['stage' => 'Discover', 'title' => 'Free counselling', 'copy' => 'Map your profile, preferred course, budget and career direction.'],
        ['stage' => 'Match', 'title' => 'Shortlist and apply', 'copy' => 'Choose suitable universities, prepare documents and lodge applications.'],
        ['stage' => 'Qualify', 'title' => 'Offer and interview', 'copy' => 'Receive an offer and complete any Genuine Student interview.'],
        ['stage' => 'Confirm', 'title' => 'Accept and receive CoE', 'copy' => 'Pay the required deposit, arrange OSHC and obtain your eCoE.'],
        ['stage' => 'Prepare', 'title' => 'Financials and GS', 'copy' => 'Prepare financial evidence and your Genuine Student statement.'],
        ['stage' => 'Submit', 'title' => 'Lodge student visa', 'copy' => 'Submit the Subclass 500 application with the required evidence.'],
        ['stage' => 'Decide', 'title' => 'Visa decision', 'copy' => 'Receive the outcome and complete your pre-departure preparation.'],
        ['stage' => 'Arrive', 'title' => 'Fly to Australia', 'copy' => 'Use accommodation and arrival support to settle in confidently.'],
    ];

    $australiaRequirements = [
        'Valid passport and an updated resume for postgraduate applicants',
        'Class 10, Class 12 and previous degree transcripts and certificates',
        'IELTS, PTE or TOEFL results required by the chosen institution',
        'Academic or professional recommendation letters where applicable',
        'Work experience evidence for selected postgraduate and MBA programs',
    ];

    $australiaCareers = ['Commerce & Analytics', 'Machine Learning & AI', 'Nursing & Paramedical', 'Accounting & Finance', 'Hospitality & Tourism', 'Education & Teaching', 'Psychology & Social Sciences', 'Environmental Science'];

    $australiaFaqs = [
        ['Which Australian universities are highly regarded?', 'Australia has internationally recognised institutions including the University of Melbourne, Australian National University and the University of Sydney. The Group of Eight represents leading research-intensive universities.'],
        ['How many hours can international students work?', 'Eligible students can generally work up to 48 hours per fortnight during academic sessions and full-time during scheduled study breaks.'],
        ['What is the Genuine Student statement?', 'The GS statement explains your genuine intention to study in Australia and is an important part of the Subclass 500 student visa process.'],
        ['Are scholarships available?', 'Yes. Australian universities and government programs provide merit-based and other scholarships for eligible international students.'],
        ['What are the main Australian intakes?', 'February is the primary intake, July is the main mid-year option, and selected institutions also offer an October intake.'],
        ['Can graduates work in Australia after studying?', 'Eligible graduates may explore post-study work options. Duration and eligibility depend on the qualification, location and current immigration rules.'],
    ];
@endphp

<style>
    .au-page { background: #fff; color: #0e2145; }
    .au-container-narrow { max-width: 1080px; margin: 0 auto; }
    .country-detail-page #themeHeaderSticky.sticky { position: relative !important; top: -42px !important; width: auto !important; animation: none !important; }
    .au-section { padding: 92px 0; scroll-margin-top: 92px; }
    .au-section-soft { background: #f5f8fc; }
    .au-kicker { display: inline-flex; align-items: center; gap: 10px; color: #E31E24; font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .au-kicker::before { content: ''; width: 28px; height: 2px; background: currentColor; }
    .au-heading { max-width: 780px; margin-top: 14px; color: #0e2145; font-size: 42px; line-height: 1.15; font-weight: 700; text-wrap: balance; }
    .au-lead { max-width: 760px; margin-top: 16px; color: #697b96; font-size: 16px; line-height: 1.75; }

    .au-hero { padding: 132px 0 28px; background: #f5f8fc; }
    .au-hero__shell { position: relative; min-height: 560px; overflow: hidden; display: flex; align-items: flex-end; border-radius: 36px; background: #0e2145; box-shadow: 0 24px 60px rgba(14,33,69,.2); }
    .au-hero__image, .au-hero__overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
    .au-hero__image { object-fit: cover; }
    .au-hero__overlay { background: linear-gradient(90deg, rgba(5,17,39,.94) 0%, rgba(5,17,39,.76) 48%, rgba(5,17,39,.18) 100%); }
    .au-hero__content { position: relative; z-index: 2; width: min(660px, calc(100% - 380px)); padding: 64px; }
    .au-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; color: rgba(255,255,255,.68); font-size: 13px; }
    .au-breadcrumb a { color: #fff; }
    .au-country-label { display: inline-flex; align-items: center; gap: 10px; margin-top: 32px; padding: 8px 13px; border: 1px solid rgba(255,255,255,.24); border-radius: 999px; color: #fff; background: rgba(14,33,69,.42); backdrop-filter: blur(10px); }
    .au-country-label img { width: auto !important; height: 22px !important; border-radius: 3px; }
    .au-hero h1 { margin-top: 18px; color: #fff; font-size: 60px; line-height: 1.03; font-weight: 700; }
    .au-hero h1 span { display: block; color: #E31E24; }
    .au-hero__copy { max-width: 640px; margin-top: 18px; color: rgba(255,255,255,.8); font-size: 17px; line-height: 1.7; }
    .au-hero__actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 28px; }
    .au-outline-button { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 24px; border: 1px solid rgba(255,255,255,.38); border-radius: 12px; color: #fff; font-weight: 700; }
    .au-outline-button:hover, .au-outline-button:focus { background: rgba(255,255,255,.1); border-color: #fff; color: #fff; }

    .au-quick-facts { position: relative; z-index: 3; display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: -2px 34px 0; padding: 18px; border: 1px solid #e2e8f0; border-radius: 0 0 26px 26px; background: #fff; box-shadow: 0 18px 45px rgba(14,33,69,.08); }
    .au-quick-fact { min-width: 0; padding: 16px 18px; border-right: 1px solid #e8edf4; }
    .au-quick-fact:last-child { border-right: 0; }
    .au-quick-fact strong { display: block; color: #0e2145; font-size: 23px; }
    .au-quick-fact span { display: block; margin-top: 4px; color: #7b8ba2; font-size: 12px; line-height: 1.45; }

    .au-anchor-nav { position: sticky; z-index: 100; top: 0; margin: 0; padding: 12px 0; border-bottom: 1px solid rgba(221,229,239,.9); background: rgba(245,248,252,.9); box-shadow: 0 12px 28px rgba(14,33,69,.07); backdrop-filter: blur(16px); }
    .au-anchor-nav__inner { display: flex; gap: 8px; overflow-x: auto; padding: 8px; border: 1px solid #e4eaf2; border-radius: 18px; background: #fff; box-shadow: 0 10px 30px rgba(14,33,69,.05); scrollbar-width: none; }
    .au-anchor-nav__inner::-webkit-scrollbar { display: none; }
    .au-anchor-nav a { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; flex: 1 0 auto; padding: 0 16px; border-radius: 11px; color: #526580; font-size: 13px; font-weight: 700; transition: color .2s ease, background-color .2s ease, box-shadow .2s ease; }
    .au-anchor-nav a:hover, .au-anchor-nav a:focus-visible { background: rgba(227,30,36,.09); color: #E31E24; }
    .au-anchor-nav a.is-active { background: #0e2145; color: #fff; box-shadow: 0 8px 20px rgba(14,33,69,.18); }

    .au-overview { display: grid; grid-template-columns: 1.05fr .95fr; align-items: center; gap: 54px; }
    .au-overview__media { position: relative; overflow: hidden; min-height: 500px; border-radius: 28px; }
    .au-overview__media img { width: 100%; height: 100%; object-fit: cover; }
    .au-overview__badge { position: absolute; right: 20px; bottom: 20px; max-width: 220px; padding: 18px; border-radius: 18px; background: #fff; color: #0e2145; box-shadow: 0 14px 36px rgba(14,33,69,.2); }
    .au-overview__badge strong { display: block; font-size: 24px; }
    .au-overview__badge span { display: block; margin-top: 4px; color: #71819b; font-size: 12px; }
    .au-overview__copy p { color: #697b96; font-size: 16px; line-height: 1.8; }
    .au-overview__copy p + p { margin-top: 18px; }

    .au-benefit-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 20px; margin-top: 34px; }
    .au-benefit { height: 100%; padding: 26px; border: 1px solid #e3e9f1; border-radius: 22px; background: #fff; box-shadow: 0 12px 34px rgba(14,33,69,.06); transition: transform .2s ease, box-shadow .2s ease; }
    .au-benefit:hover { transform: translateY(-4px); box-shadow: 0 18px 42px rgba(14,33,69,.11); }
    .au-benefit__icon { display: inline-flex; align-items: center; justify-content: center; width: 46px; height: 46px; border-radius: 14px; background: rgba(227,30,36,.1); color: #E31E24; }
    .au-benefit__icon svg { width: 22px; height: 22px; }
    .au-benefit h3 { margin-top: 18px; color: #0e2145; font-size: 18px; font-weight: 700; }
    .au-benefit p { margin-top: 10px; color: #71819b; font-size: 14px; line-height: 1.65; }
    .au-life-gallery { display: grid; grid-template-columns: 1.25fr .75fr; grid-template-rows: repeat(2, 210px); gap: 18px; margin-top: 38px; }
    .au-life-gallery__item { position: relative; overflow: hidden; min-width: 0; border-radius: 24px; background: #0e2145; box-shadow: 0 16px 38px rgba(14,33,69,.12); }
    .au-life-gallery__item:first-child { grid-row: 1 / 3; }
    .au-life-gallery__item img { width: 100%; height: 100%; object-fit: cover; transition: transform .55s cubic-bezier(.2,.75,.25,1); }
    .au-life-gallery__item::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 42%, rgba(5,17,39,.86) 100%); }
    .au-life-gallery__item:hover img { transform: scale(1.035); }
    .au-life-gallery__caption { position: absolute; z-index: 2; left: 24px; right: 24px; bottom: 22px; color: #fff; }
    .au-life-gallery__caption span { display: block; color: #ff8a8e; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .au-life-gallery__caption strong { display: block; margin-top: 5px; font-size: 19px; line-height: 1.3; }

    .au-dark-band { position: relative; overflow: hidden; padding: 44px 48px; border-radius: 28px; background-color: #0e2145; background-image: radial-gradient(rgba(255,255,255,.09) 1px, transparent 1px); background-size: 13px 13px; }
    .au-dark-band::after { content: ''; position: absolute; right: -85px; bottom: -125px; width: 290px; height: 290px; border: 56px solid rgba(255,255,255,.05); border-radius: 50%; }
    .au-dark-band__content { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 30px; }
    .au-dark-band h2 { color: #fff; font-size: 30px; line-height: 1.2; }
    .au-dark-band p { max-width: 650px; margin-top: 10px; color: rgba(255,255,255,.7); line-height: 1.65; }

    .au-journey { position: relative; overflow: hidden; background-color: #0e2145; background-image: radial-gradient(rgba(255,255,255,.065) 1px, transparent 1px), radial-gradient(circle at 8% 16%, rgba(227,30,36,.14), transparent 24%), radial-gradient(circle at 94% 82%, rgba(243,149,30,.11), transparent 26%); background-size: 18px 18px, auto, auto; color: #fff; }
    .au-journey::before, .au-journey::after { content: ''; position: absolute; border: 1px solid rgba(255,255,255,.06); border-radius: 50%; pointer-events: none; }
    .au-journey::before { width: 420px; height: 420px; left: -250px; top: -220px; box-shadow: 0 0 0 58px rgba(255,255,255,.018); }
    .au-journey::after { width: 360px; height: 360px; right: -190px; bottom: -210px; box-shadow: 0 0 0 72px rgba(255,255,255,.018); }
    .au-journey .container { position: relative; z-index: 1; }
    .au-journey .au-kicker { color: #ff5a60; }
    .au-journey .au-heading { color: #fff; }
    .au-journey .au-lead { color: rgba(255,255,255,.68); }
    .au-journey__intro { display: flex; align-items: flex-end; justify-content: space-between; gap: 40px; }
    .au-journey__intro-copy { max-width: 780px; }
    .au-journey__count { flex: 0 0 auto; display: flex; align-items: center; gap: 14px; padding: 15px 18px; border: 1px solid rgba(255,255,255,.14); border-radius: 18px; background: rgba(255,255,255,.07); backdrop-filter: blur(10px); }
    .au-journey__count strong { display: grid; place-items: center; width: 48px; height: 48px; border-radius: 15px; background: #E31E24; color: #fff; font-size: 22px; box-shadow: 0 10px 25px rgba(227,30,36,.28); }
    .au-journey__count span { max-width: 95px; color: rgba(255,255,255,.74); font-size: 12px; font-weight: 700; line-height: 1.35; text-transform: uppercase; letter-spacing: .08em; }
    .au-journey-grid { position: relative; display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); grid-auto-rows: 1fr; gap: 56px 44px; margin-top: 52px; }
    .au-journey-step { position: relative; z-index: 2; min-width: 0; min-height: 228px; padding: 24px; border: 1px solid rgba(255,255,255,.14); border-radius: 24px; background: linear-gradient(145deg, rgba(255,255,255,.105), rgba(255,255,255,.052)); box-shadow: inset 0 1px 0 rgba(255,255,255,.08), 0 18px 40px rgba(3,13,32,.15); backdrop-filter: blur(8px); transition: transform .22s ease, border-color .22s ease, background-color .22s ease; }
    .au-journey-step:hover { transform: translateY(-5px); border-color: rgba(255,255,255,.27); background-color: rgba(255,255,255,.05); }
    .au-journey-step:nth-child(5) { grid-column: 4; grid-row: 2; }
    .au-journey-step:nth-child(6) { grid-column: 3; grid-row: 2; }
    .au-journey-step:nth-child(7) { grid-column: 2; grid-row: 2; }
    .au-journey-step:nth-child(8) { grid-column: 1; grid-row: 2; }
    .au-journey-step__top { display: flex; align-items: center; justify-content: space-between; gap: 14px; }
    .au-journey-step__number { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 16px; background: linear-gradient(145deg, #f32930, #c90f15); color: #fff; font-size: 13px; font-weight: 800; box-shadow: 0 0 0 7px rgba(14,33,69,.72), 0 12px 24px rgba(227,30,36,.25); }
    .au-journey-step__stage { display: inline-flex; align-items: center; gap: 7px; color: #ffb4b7; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .au-journey-step__stage::before { content: ''; width: 7px; height: 7px; border: 2px solid currentColor; border-radius: 50%; }
    .au-journey-step h3 { margin-top: 25px; color: #fff; font-size: 18px; line-height: 1.35; }
    .au-journey-step p { margin-top: 10px; color: rgba(255,255,255,.67); font-size: 13px; line-height: 1.68; }
    .au-journey-step__connector { position: absolute; z-index: 3; top: 37px; right: -45px; width: 45px; height: 22px; pointer-events: none; }
    .au-journey-step__connector::before { content: ''; position: absolute; left: 0; right: 8px; top: 10px; border-top: 2px dashed rgba(255,255,255,.34); }
    .au-journey-step__connector::after { content: ''; position: absolute; right: 3px; top: 6px; width: 9px; height: 9px; border-top: 2px solid #ff5a60; border-right: 2px solid #ff5a60; transform: rotate(45deg); }
    .au-journey-step:nth-child(4) .au-journey-step__connector { top: 100%; right: 50%; width: 22px; height: 57px; transform: translateX(50%); }
    .au-journey-step:nth-child(4) .au-journey-step__connector::before { left: 10px; right: auto; top: 0; bottom: 8px; border-top: 0; border-left: 2px dashed rgba(255,255,255,.34); }
    .au-journey-step:nth-child(4) .au-journey-step__connector::after { right: 6px; top: auto; bottom: 3px; transform: rotate(135deg); }
    .au-journey-step:nth-child(n+5):nth-child(-n+7) .au-journey-step__connector { left: -45px; right: auto; transform: rotate(180deg); }
    .au-journey-step:nth-child(8) .au-journey-step__connector { display: none; }
    .au-journey-outcome { display: flex; align-items: center; gap: 18px; width: min(760px, 100%); margin: 48px auto 0; padding: 18px 20px; border: 1px solid rgba(255,255,255,.15); border-radius: 22px; background: rgba(255,255,255,.075); box-shadow: 0 18px 40px rgba(3,13,32,.18); }
    .au-journey-outcome__icon { display: grid; place-items: center; width: 48px; height: 48px; flex: 0 0 48px; border-radius: 50%; background: #fff; color: #159657; font-size: 21px; font-weight: 900; box-shadow: 0 0 0 7px rgba(255,255,255,.08); }
    .au-journey-outcome__copy { min-width: 0; flex: 1; }
    .au-journey-outcome small { display: block; color: #ffb4b7; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .au-journey-outcome strong { display: block; margin-top: 4px; color: #fff; font-size: 17px; }
    .au-journey-outcome a { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 17px; border-radius: 12px; background: #E31E24; color: #fff; font-size: 13px; font-weight: 700; white-space: nowrap; }

    .au-two-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 34px; }
    .au-panel { padding: 32px; border: 1px solid #e3e9f1; border-radius: 24px; background: #fff; box-shadow: 0 14px 38px rgba(14,33,69,.07); }
    .au-panel h3 { color: #0e2145; font-size: 24px; }
    .au-check-list { margin-top: 20px; }
    .au-check-list li { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #edf1f5; color: #637691; font-size: 14px; line-height: 1.55; }
    .au-check-list li:last-child { border-bottom: 0; }
    .au-check { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; flex: 0 0 24px; border-radius: 50%; background: rgba(63,205,130,.13); color: #159657; font-size: 13px; font-weight: 800; }
    .au-visa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
    .au-visa-item { padding: 16px; border-radius: 15px; background: #f5f8fc; }
    .au-visa-item strong { display: block; color: #0e2145; font-size: 14px; }
    .au-visa-item span { display: block; margin-top: 5px; color: #71819b; font-size: 12px; line-height: 1.45; }

    .au-budget-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 16px; margin-top: 34px; }
    .au-budget-card { padding: 24px; border: 1px solid #e2e8f0; border-radius: 20px; background: #fff; box-shadow: 0 12px 30px rgba(14,33,69,.06); }
    .au-budget-card span { display: block; color: #71819b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
    .au-budget-card strong { display: block; margin-top: 12px; color: #0e2145; font-size: 20px; line-height: 1.3; }
    .au-budget-card small { display: block; margin-top: 8px; color: #8997aa; line-height: 1.45; }
    .au-note { margin-top: 18px; color: #8290a5; font-size: 12px; line-height: 1.55; }

    .au-future-grid { display: grid; grid-template-columns: 1.15fr .85fr; gap: 24px; margin-top: 34px; }
    .au-careers, .au-intakes { padding: 32px; border-radius: 24px; }
    .au-careers { background: #0e2145; }
    .au-intakes { border: 1px solid #e2e8f0; background: #fff; }
    .au-careers h3 { color: #fff; font-size: 25px; }
    .au-intakes h3 { color: #0e2145; font-size: 25px; }
    .au-career-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .au-career-list span { padding: 10px 13px; border: 1px solid rgba(255,255,255,.15); border-radius: 999px; color: rgba(255,255,255,.82); background: rgba(255,255,255,.07); font-size: 12px; }
    .au-intake { display: grid; grid-template-columns: 88px 1fr; gap: 14px; padding: 16px 0; border-bottom: 1px solid #edf1f5; }
    .au-intake:last-child { border-bottom: 0; }
    .au-intake strong { color: #E31E24; }
    .au-intake span { color: #71819b; font-size: 13px; line-height: 1.5; }

    .au-university-grid { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 18px; margin-top: 34px; }
    .au-university { display: flex; align-items: center; justify-content: center; min-height: 142px; padding: 22px; border: 1px solid #e2e8f0; border-radius: 20px; background: #fff; box-shadow: 0 10px 28px rgba(14,33,69,.06); }
    .au-university img { width: auto !important; height: auto !important; max-width: 135px; max-height: 70px; object-fit: contain; }

    .au-faq { margin-top: 30px; }
    .au-faq details { margin-top: 12px; border: 1px solid #e2e8f0; border-radius: 17px; background: #fff; padding: 20px 22px; }
    .au-faq summary { display: flex; align-items: center; justify-content: space-between; gap: 18px; color: #0e2145; font-size: 16px; font-weight: 700; cursor: pointer; list-style: none; }
    .au-faq summary::-webkit-details-marker { display: none; }
    .au-faq summary::after { content: '+'; color: #E31E24; font-size: 23px; font-weight: 400; }
    .au-faq details[open] summary::after { content: '−'; }
    .au-faq p { max-width: 900px; margin-top: 14px; color: #71819b; font-size: 14px; line-height: 1.7; }

    @media (max-width: 991px) {
        .au-section { padding: 68px 0; scroll-margin-top: 90px; }
        .au-hero { padding-top: 112px; }
        .au-hero__shell { min-height: 520px; border-radius: 28px; }
        .au-hero__content { padding: 40px 30px; }
        .au-hero h1 { font-size: 46px; }
        .au-quick-facts { grid-template-columns: 1fr 1fr; margin: 0 18px; }
        .au-quick-fact:nth-child(2) { border-right: 0; }
        .au-overview { grid-template-columns: 1fr; }
        .au-overview__media { min-height: 420px; }
        .au-benefit-grid { grid-template-columns: 1fr 1fr; }
        .au-life-gallery { grid-template-rows: repeat(2, 180px); }
        .au-journey__intro { align-items: flex-start; flex-direction: column; }
        .au-journey-grid { grid-template-columns: 1fr; gap: 0; width: min(720px, 100%); margin: 42px auto 0; padding-left: 30px; }
        .au-journey-grid::before { content: ''; position: absolute; left: 9px; top: 30px; bottom: 30px; border-left: 2px dashed rgba(255,255,255,.32); }
        .au-journey-step:nth-child(n) { grid-column: auto; grid-row: auto; }
        .au-journey-step { min-height: 0; margin-bottom: 24px; padding: 24px; }
        .au-journey-step::before { content: ''; position: absolute; left: -27px; top: 34px; width: 15px; height: 15px; border: 4px solid #0e2145; border-radius: 50%; background: #ff5a60; box-shadow: 0 0 0 2px rgba(255,255,255,.32); }
        .au-journey-step:last-child { margin-bottom: 0; }
        .au-journey-step__connector { display: none; }
        .au-journey-outcome { width: min(690px, calc(100% - 30px)); margin-left: auto; margin-right: auto; }
        .au-budget-grid { grid-template-columns: 1fr 1fr; }
        .au-future-grid { grid-template-columns: 1fr; }
        .au-university-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
    }
    @media (max-width: 575px) {
        .au-heading { font-size: 32px; }
        .au-hero__shell { min-height: 560px; }
        .au-hero__overlay { background: linear-gradient(180deg, rgba(5,17,39,.42), rgba(5,17,39,.96)); }
        .au-hero__content { padding: 30px 22px; }
        .au-hero h1 { font-size: 38px; }
        .au-hero__copy { font-size: 15px; }
        .au-quick-facts { grid-template-columns: 1fr 1fr; gap: 0; padding: 10px; }
        .au-quick-fact { padding: 14px 12px; }
        .au-quick-fact:nth-child(odd) { border-right: 1px solid #e8edf4; }
        .au-benefit-grid, .au-two-panel, .au-journey-grid, .au-budget-grid { grid-template-columns: 1fr; }
        .au-life-gallery { grid-template-columns: 1fr; grid-template-rows: none; }
        .au-life-gallery__item, .au-life-gallery__item:first-child { grid-row: auto; min-height: 230px; }
        .au-journey-grid { margin-top: 38px; padding-left: 26px; }
        .au-journey-grid::before { left: 8px; top: 24px; bottom: 24px; }
        .au-journey-step { margin-bottom: 22px; padding: 22px; }
        .au-journey-step::before { left: -24px; top: 31px; width: 14px; height: 14px; }
        .au-journey__count { width: 100%; }
        .au-journey-outcome { align-items: flex-start; flex-wrap: wrap; }
        .au-journey-outcome a { width: 100%; }
        .au-dark-band { padding: 32px 24px; }
        .au-dark-band__content { align-items: flex-start; flex-direction: column; }
        .au-visa-grid { grid-template-columns: 1fr; }
        .au-university-grid { grid-template-columns: 1fr 1fr; }
        .au-university { min-height: 118px; }
    }
    @media (max-width: 767px), (max-width: 991px) and (max-height: 500px) {
        .au-page { padding: 10px 0 2px; background: #f3f6f9; }
        .au-page .container { padding-right: 14px; padding-left: 14px; }
        .au-section { margin: 12px 10px; padding: 32px 0; overflow: hidden; border-radius: 28px; background: #fff; scroll-margin-top: 142px; box-shadow: 0 10px 30px rgba(14,33,69,.055); }
        .au-section-soft { background: #fff; }
        .au-kicker { font-size: 10px; letter-spacing: .1em; }
        .au-heading { margin-top: 11px; font-size: 30px; line-height: 1.15; }
        .au-lead { margin-top: 12px; font-size: 14px; line-height: 1.65; }

        .au-hero { padding: 0; background: transparent; }
        .au-hero__shell { min-height: 510px; border-radius: 28px; box-shadow: 0 15px 36px rgba(14,33,69,.16); }
        .au-hero__overlay { background: linear-gradient(180deg, rgba(5,17,39,.32), rgba(5,17,39,.96)); }
        .au-hero__content { padding: 27px 20px; }
        .au-breadcrumb { display: none; }
        .au-country-label { margin-top: 0; padding: 7px 11px; font-size: 12px; }
        .au-hero h1 { margin-top: 14px; font-size: 42px; line-height: 1.02; }
        .au-hero__copy { margin-top: 14px; font-size: 14px; line-height: 1.6; }
        .au-hero__actions { display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 22px; }
        .au-hero__actions a { width: 100%; min-height: 50px; }
        .au-quick-facts { display: flex; gap: 10px; margin: 10px 0 0; padding: 10px; overflow-x: auto; border-radius: 22px; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .au-quick-facts::-webkit-scrollbar { display: none; }
        .au-quick-fact { min-width: 154px; flex: 0 0 154px; padding: 13px 14px; border: 1px solid #e8edf4 !important; border-radius: 15px; scroll-snap-align: start; }
        .au-quick-fact strong { font-size: 19px; }

        .au-anchor-nav { top: 72px; padding: 7px 0; background: rgba(243,246,249,.94); }
        .au-anchor-nav .container { padding-right: 10px; padding-left: 10px; }
        .au-anchor-nav__inner { gap: 6px; padding: 6px; border-radius: 17px; }
        .au-anchor-nav a { min-height: 44px; padding: 0 14px; font-size: 12px; }

        .au-overview { gap: 24px; }
        .au-overview__media { min-height: 300px; border-radius: 22px; }
        .au-overview__badge { right: 13px; bottom: 13px; max-width: 190px; padding: 13px; border-radius: 15px; }
        .au-overview__badge strong { font-size: 18px; }
        .au-overview__copy p { font-size: 14px; line-height: 1.7; }
        .au-overview__copy .btn { width: 100%; min-height: 50px; }

        #why-australia .text-center,
        #universities .text-center,
        #faqs .text-center { text-align: left !important; }
        .au-benefit-grid { display: flex; gap: 12px; margin: 24px -14px 0; padding: 0 14px 12px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .au-benefit-grid::-webkit-scrollbar { display: none; }
        .au-benefit { min-width: 82vw; flex: 0 0 82vw; padding: 22px; border-radius: 21px; scroll-snap-align: start; }
        .au-life-gallery { display: flex; gap: 12px; margin: 24px -14px 0; padding: 0 14px 12px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .au-life-gallery::-webkit-scrollbar { display: none; }
        .au-life-gallery__item,
        .au-life-gallery__item:first-child { min-width: 84vw; min-height: 270px; flex: 0 0 84vw; border-radius: 21px; scroll-snap-align: start; }
        .au-life-gallery__caption { left: 18px; right: 18px; bottom: 17px; }

        .au-dark-band { padding: 27px 20px; border-radius: 24px; }
        .au-dark-band h2 { font-size: 27px; }
        .au-dark-band p { font-size: 13px; }
        .au-dark-band .btn { width: 100%; min-height: 51px; }

        .au-journey { background-color: #0e2145; }
        .au-journey__intro { gap: 22px; }
        .au-journey__count { padding: 11px 13px; border-radius: 16px; }
        .au-journey__count strong { width: 42px; height: 42px; font-size: 18px; }
        .au-journey-grid { margin-top: 28px; padding-left: 25px; }
        .au-journey-step { margin-bottom: 14px; padding: 19px; border-radius: 20px; }
        .au-journey-step::before { left: -23px; top: 27px; }
        .au-journey-step__number { width: 42px; height: 42px; border-radius: 14px; }
        .au-journey-step h3 { margin-top: 19px; font-size: 17px; }
        .au-journey-outcome { width: 100%; margin-top: 28px; padding: 16px; border-radius: 19px; }

        .au-two-panel { gap: 12px; margin-top: 24px; }
        .au-panel { padding: 21px; border-radius: 21px; }
        .au-panel h3 { font-size: 21px; }
        .au-check-list li { font-size: 13px; }

        .au-budget-grid { display: flex; gap: 12px; margin: 24px -14px 0; padding: 0 14px 12px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .au-budget-grid::-webkit-scrollbar { display: none; }
        .au-budget-card { min-width: 76vw; flex: 0 0 76vw; padding: 21px; border-radius: 20px; scroll-snap-align: start; }
        .au-future-grid { gap: 12px; margin-top: 24px; }
        .au-careers, .au-intakes { padding: 22px; border-radius: 21px; }
        .au-careers h3, .au-intakes h3 { font-size: 22px; }
        .au-intake { grid-template-columns: 78px 1fr; }

        .au-university-grid { display: flex; gap: 12px; margin: 24px -14px 0; padding: 0 14px 12px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .au-university-grid::-webkit-scrollbar { display: none; }
        .au-university { min-width: 46vw; min-height: 122px; flex: 0 0 46vw; border-radius: 19px; scroll-snap-align: start; }
        .au-faq { margin-top: 22px; }
        .au-faq details { padding: 17px; border-radius: 16px; }
        .au-faq summary { font-size: 14px; line-height: 1.45; }
        #contact .au-dark-band__content { gap: 22px; }
    }
    @media (max-width:900px) {
        .au-hero__shell { display:block; min-height:0; }
        .au-hero__content { width:100%; }
    }
    @media (max-width:575px) { .au-hero__shell { min-height:0; } }
    @media (prefers-reduced-motion: reduce) {
        .au-benefit, .au-benefit__icon, .au-journey-step, .au-life-gallery__item img { transition: none; }
        .au-benefit:hover, .au-journey-step:hover, .au-life-gallery__item:hover img { transform: none; }
    }
</style>

<main class="au-page">
    <section class="au-hero">
        <div class="container">
            <div class="au-hero__shell">
                <img src="assets/transglobe/destinations/australia-detail-hero.jpg" alt="Sydney Harbour and Opera House in Australia" class="au-hero__image">
                <div class="au-hero__overlay"></div>
                <div class="au-hero__content">
                    <nav class="au-breadcrumb" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><a href="{{ url('/destinations') }}">Destinations</a><span>/</span><span>Australia</span></nav>
                    <div class="au-country-label"><img src="assets/transglobe/destinations/flags/au.png" alt="Australia flag"><span>Expert study guidance</span></div>
                    <h1>Study in <span>Australia</span></h1>
                    <p class="au-hero__copy">Build a globally respected education and career path through research-led universities, practical learning and welcoming student cities.</p>
                    <div class="au-hero__actions">
                        <a href="{{ url('/destinations/australia') }}#contact" class="btn btn-primary btn-xlg text-white">Free consultation</a>
                        <a href="{{ url('/destinations/australia') }}#journey" class="au-outline-button">See the complete process</a>
                    </div>
                </div>
                @include('mirror.partials.hero-enquiry', ['formId' => 'australia-hero', 'sourceContext' => 'Study in Australia', 'returnTo' => '/destinations/australia#overview'])
            </div>

            <div class="au-quick-facts" aria-label="Australia study facts">
                <div class="au-quick-fact"><strong>42</strong><span>Universities nationwide</span></div>
                <div class="au-quick-fact"><strong>Go8</strong><span>Plus regional, ATN and IRU networks</span></div>
                <div class="au-quick-fact"><strong>2–3 years</strong><span>Common post-study work range</span></div>
                <div class="au-quick-fact"><strong>160+</strong><span>Nationalities represented</span></div>
            </div>

        </div>
    </section>

    <nav class="au-anchor-nav" aria-label="Australia page sections">
        <div class="container">
            <div class="au-anchor-nav__inner">
                <a class="is-active" href="{{ url('/destinations/australia') }}#overview">Overview</a><a href="{{ url('/destinations/australia') }}#why-australia">Why Australia</a><a href="{{ url('/destinations/australia') }}#journey">Study journey</a><a href="{{ url('/destinations/australia') }}#requirements">Requirements</a><a href="{{ url('/destinations/australia') }}#budget">Costs</a><a href="{{ url('/destinations/australia') }}#intakes">Intakes & careers</a><a href="{{ url('/destinations/australia') }}#universities">Universities</a><a href="{{ url('/destinations/australia') }}#faqs">FAQs</a>
            </div>
        </div>
    </nav>

    <section id="overview" class="au-section">
        <div class="container">
            <div class="au-overview">
                <div class="au-overview__media">
                    <img src="assets/transglobe/destinations/australia.jpg" alt="Australian city and waterfront" loading="lazy">
                    <div class="au-overview__badge"><strong>Practical by design</strong><span>Industry projects, capstones, placements and internships.</span></div>
                </div>
                <div class="au-overview__copy">
                    <div class="au-kicker">Australia at a glance</div>
                    <h2 class="au-heading">Academic prestige meets real-world learning</h2>
                    <p>Australia combines research-focused universities with teaching designed around industry. Students can study across the Group of Eight, technology-focused institutions and strong regional university networks.</p>
                    <p>Sydney, Melbourne, Brisbane, Perth, Adelaide, Canberra and other student cities offer reliable infrastructure, healthcare, public transport and multicultural communities.</p>
                    <a href="{{ url('/destinations/australia') }}#requirements" class="btn btn-primary btn-lg text-white mt-24">Check admission requirements</a>
                </div>
            </div>
        </div>
    </section>

    <section id="why-australia" class="au-section au-section-soft">
        <div class="container">
            <div class="text-center mx-auto" style="max-width:820px">
                <div class="au-kicker">Why Australia</div>
                <h2 class="au-heading mx-auto">A study destination built for ambitious students</h2>
                <p class="au-lead mx-auto">Strong academics, practical experience and an inclusive student environment work together to support both education and employability.</p>
            </div>
            <div class="au-benefit-grid">
                @foreach ($australiaBenefits as $benefit)
                    <article class="au-benefit">
                        <span class="au-benefit__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 3 3 8l9 5 9-5-9-5Z" stroke-width="1.7" stroke-linejoin="round"/><path d="M6 11v5c2.8 2.5 9.2 2.5 12 0v-5" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                        <h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['copy'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="au-life-gallery" aria-label="Student life in Australia">
                <figure class="au-life-gallery__item">
                    <img src="assets/transglobe/destinations/australia/campus-students.jpg" alt="International students walking together on an Australian campus" loading="lazy" width="1200" height="800">
                    <figcaption class="au-life-gallery__caption"><span>Campus community</span><strong>Learn alongside students from around the world</strong></figcaption>
                </figure>
                <figure class="au-life-gallery__item">
                    <img src="assets/transglobe/destinations/australia/campus-life.webp" alt="Students spending time outside a modern Australian university building" loading="lazy" width="1280" height="853">
                    <figcaption class="au-life-gallery__caption"><span>Everyday experience</span><strong>A lively, welcoming campus culture</strong></figcaption>
                </figure>
                <figure class="au-life-gallery__item">
                    <img src="assets/transglobe/destinations/australia/student-community.webp" alt="Students meeting and socialising on an Australian university campus" loading="lazy" width="960" height="539">
                    <figcaption class="au-life-gallery__caption"><span>Belong from day one</span><strong>Build friendships and a global network</strong></figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="au-section">
        <div class="container">
            <div class="au-dark-band">
                <div class="au-dark-band__content">
                    <div><h2>Your Australia plan starts with one clear conversation</h2><p>Understand your eligible courses, realistic budget, intake timeline and visa pathway before you make a decision.</p></div>
                    <a href="{{ url('/destinations/australia') }}#contact" class="btn btn-primary btn-xlg text-white">Plan my journey</a>
                </div>
            </div>
        </div>
    </section>

    <section id="journey" class="au-section au-journey">
        <div class="container">
            <div class="au-journey__intro">
                <div class="au-journey__intro-copy">
                    <div class="au-kicker">The complete journey</div>
                    <h2 class="au-heading">One connected path from counselling to Australia</h2>
                    <p class="au-lead">Follow one clear route through every milestone, document and decision, with Trans Globe Indore supporting you all the way.</p>
                </div>
                <div class="au-journey__count" aria-label="Eight guided milestones"><strong>8</strong><span>Guided milestones</span></div>
            </div>
            <div class="au-journey-grid">
                @foreach ($australiaJourney as $index => $step)
                    <article class="au-journey-step">
                        <div class="au-journey-step__top">
                            <span class="au-journey-step__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="au-journey-step__stage">{{ $step['stage'] }}</span>
                        </div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['copy'] }}</p>
                        <span class="au-journey-step__connector" aria-hidden="true"></span>
                    </article>
                @endforeach
            </div>
            <div class="au-journey-outcome">
                <span class="au-journey-outcome__icon" aria-hidden="true">✓</span>
                <div class="au-journey-outcome__copy"><small>Destination reached</small><strong>Arrive informed, prepared and ready for student life.</strong></div>
                <a href="{{ url('/destinations/australia') }}#contact">Start my journey →</a>
            </div>
        </div>
    </section>

    <section id="requirements" class="au-section au-section-soft">
        <div class="container">
            <div class="au-kicker">Prepare with confidence</div>
            <h2 class="au-heading">Admission and student visa essentials</h2>
            <div class="au-two-panel">
                <article class="au-panel">
                    <h3>Admission requirements</h3>
                    <ul class="au-check-list">
                        @foreach ($australiaRequirements as $requirement)<li><span class="au-check">✓</span><span>{{ $requirement }}</span></li>@endforeach
                    </ul>
                </article>
                <article class="au-panel">
                    <h3>Subclass 500 essentials</h3>
                    <div class="au-visa-grid">
                        <div class="au-visa-item"><strong>English proficiency</strong><span>IELTS, PTE or TOEFL as required.</span></div>
                        <div class="au-visa-item"><strong>Genuine Student</strong><span>A clear GS statement explaining your study intent.</span></div>
                        <div class="au-visa-item"><strong>Financial capacity</strong><span>Evidence covering tuition and living costs.</span></div>
                        <div class="au-visa-item"><strong>CoE and OSHC</strong><span>Confirmation of Enrolment and health cover.</span></div>
                    </div>
                    <a href="{{ url('/destinations/australia') }}#contact" class="btn btn-primary btn-lg text-white mt-24">Discuss my eligibility</a>
                </article>
            </div>
        </div>
    </section>

    <section id="budget" class="au-section">
        <div class="container">
            <div class="au-kicker">Plan your budget</div>
            <h2 class="au-heading">Indicative financial planning</h2>
            <p class="au-lead">Use these planning ranges as a starting point. Your course, city, lifestyle and current government charges determine the final amount.</p>
            <div class="au-budget-grid">
                <article class="au-budget-card"><span>Undergraduate tuition</span><strong>AUD 24K–40K</strong><small>Indicative annual range</small></article>
                <article class="au-budget-card"><span>Postgraduate tuition</span><strong>AUD 25K–45K</strong><small>Indicative annual range</small></article>
                <article class="au-budget-card"><span>Living & accommodation</span><strong>AUD 29,710</strong><small>Indicative yearly planning figure</small></article>
                <article class="au-budget-card"><span>Airfare planning</span><strong>AUD 2,000</strong><small>Route and season dependent</small></article>
            </div>
            <p class="au-note">Figures are indicative and can change. Confirm current tuition, living-cost evidence and visa charges with your counsellor before applying.</p>
        </div>
    </section>

    <section id="intakes" class="au-section au-section-soft">
        <div class="container">
            <div class="au-kicker">Future ready</div>
            <h2 class="au-heading">Careers and Australian intakes</h2>
            <div class="au-future-grid">
                <article class="au-careers"><h3>High-demand study and career fields</h3><div class="au-career-list">@foreach ($australiaCareers as $career)<span>{{ $career }}</span>@endforeach</div></article>
                <article class="au-intakes"><h3>When to apply</h3>
                    <div class="au-intake"><strong>February</strong><span>Main intake with the widest course selection.</span></div>
                    <div class="au-intake"><strong>July</strong><span>Strong mid-year intake across many institutions.</span></div>
                    <div class="au-intake"><strong>October</strong><span>Selected programs at participating universities.</span></div>
                </article>
            </div>
        </div>
    </section>

    <section id="universities" class="au-section">
        <div class="container">
            <div class="text-center mx-auto" style="max-width:800px"><div class="au-kicker">Globally recognised</div><h2 class="au-heading mx-auto">Explore our Australian university network</h2><p class="au-lead mx-auto">From research-intensive universities to technology and industry-led institutions, we help you shortlist the right academic fit.</p></div>
            <div class="au-university-grid">
                @foreach ([
                    ['australian-national-university.png', 'Australian National University'],
                    ['monash-university.png', 'Monash University'],
                    ['adelaide-university.png', 'Adelaide University'],
                    ['university-of-queensland.png', 'University of Queensland'],
                    ['queensland-university-of-technology.png', 'Queensland University of Technology'],
                ] as [$logo, $name])
                    <div class="au-university"><img src="assets/transglobe/universities/{{ $logo }}" alt="{{ $name }}" loading="lazy"></div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faqs" class="au-section au-section-soft">
        <div class="container au-container-narrow">
            <div class="text-center"><div class="au-kicker">Questions, answered</div><h2 class="au-heading mx-auto">Study in Australia FAQs</h2></div>
            <div class="au-faq">
                @foreach ($australiaFaqs as $index => [$question, $answer])<details @if($index === 0) open @endif><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>@endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="au-section">
        <div class="container">
            <div class="au-dark-band">
                <div class="au-dark-band__content">
                    <div><div class="au-kicker" style="color:#ff5a60">Speak with GEIC Indore</div><h2 class="mt-12">Ready to build your Australia shortlist?</h2><p>Bring your academic history, preferred course and budget. We’ll help you understand realistic university, intake and visa options.</p></div>
                    <a href="tel:+919826666886" class="btn btn-primary btn-xlg text-white">Call +91 98266 66886</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nav = document.querySelector('.au-anchor-nav');
        const links = Array.from(document.querySelectorAll('.au-anchor-nav a'));
        const sections = links.map(function (link) {
            return document.getElementById(link.hash.slice(1));
        }).filter(Boolean);
        let ticking = false;

        function setActiveLink() {
            const responsiveOffset = window.matchMedia('(max-width: 767px), (max-width: 991px) and (max-height: 500px)').matches ? 92 : 28;
            const offset = (nav ? nav.offsetHeight : 0) + responsiveOffset;
            let current = sections[0] ? sections[0].id : '';

            sections.forEach(function (section) {
                if (window.scrollY >= section.offsetTop - offset) current = section.id;
            });

            links.forEach(function (link) {
                const active = link.hash === '#' + current;
                link.classList.toggle('is-active', active);
                if (active) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(setActiveLink);
                ticking = true;
            }
        }, { passive: true });

        links.forEach(function (link) {
            link.addEventListener('click', function () {
                links.forEach(function (item) { item.classList.remove('is-active'); });
                link.classList.add('is-active');
            });
        });

        setActiveLink();
    });
</script>

@include('mirror.partials.footer')
