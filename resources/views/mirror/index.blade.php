@include('mirror.partials.header')

<style>
    html { scroll-behavior: smooth; }
    .tg-section { padding: 88px 0; }
    .tg-section-soft { background: #f5f8fc; }
    .tg-eyebrow { color: var(--primary); font-size: 14px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .tg-title { color: var(--dark); font-size: 40px; line-height: 1.2; font-weight: 700; }
    .tg-copy { color: var(--gray-500); font-size: 16px; line-height: 1.75; }
    .tg-card { height: 100%; background: #fff; border: 1px solid #e8edf4; border-radius: 24px; padding: 28px; box-shadow: 0 14px 40px rgba(30, 55, 90, .06); transition: transform .2s ease, box-shadow .2s ease; }
    .tg-card:hover { transform: translateY(-4px); box-shadow: 0 18px 50px rgba(30, 55, 90, .11); }
    .tg-guidance-section { scroll-margin-top: 190px; }
    .tg-guidance-card { height: 100%; overflow: hidden; border: 1px solid #e3e9f1; border-radius: 24px; background: #fff; box-shadow: 0 14px 40px rgba(30,55,90,.08); }
    .tg-guidance-card__media { position: relative; aspect-ratio: 16 / 10; overflow: hidden; background: #e8edf4; }
    .tg-guidance-card__media img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .tg-guidance-card__tag { position: absolute; left: 16px; bottom: 16px; padding: 8px 12px; border-radius: 999px; background: #fff; color: var(--primary); font-size: 13px; font-weight: 700; box-shadow: 0 6px 18px rgba(14,33,69,.18); }
    .tg-guidance-card__body { padding: 22px; }
    .tg-guidance-card__body h3 { color: var(--dark); font-size: 19px; line-height: 1.35; font-weight: 700; }
    .tg-guidance-card__body p { color: var(--gray-500); font-size: 16px; line-height: 1.55; }
    .tg-icon { display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 16px; background: rgba(227, 30, 36, .1); color: var(--primary); font-size: 24px; font-weight: 700; }
    .tg-step { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: var(--primary); color: #fff; font-weight: 700; }
    .tg-stat-wrap { display: flex; align-items: center; gap: 16px; }
    .tg-stat-icon { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; flex: 0 0 64px; border-radius: 50%; color: #fff; }
    .tg-stat-icon svg { width: 28px; height: 28px; }
    .tg-destination { position: relative; min-height: 310px; height: 100%; overflow: hidden; border-radius: 24px; background: var(--secondary); box-shadow: 0 14px 40px rgba(30, 55, 90, .1); }
    .tg-destination__image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-destination__overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 24, 52, .08) 15%, rgba(7, 24, 52, .9) 100%); }
    .tg-destination__flag { position: absolute; z-index: 2; top: 20px; left: 20px; width: auto; height: 28px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    .tg-destination__content { position: absolute; z-index: 2; inset: 0; display: flex; flex-direction: column; justify-content: flex-end; padding: 24px; }
    .tg-destination__content h3 { color: #fff; }
    .tg-destination__content p { color: rgba(255,255,255,.82); line-height: 1.55; }
    .tg-destination:hover .tg-destination__image { transform: scale(1.07); }
    .tg-work-visa-shell { position: relative; overflow: hidden; padding: 48px; border-radius: 38px; background-color: var(--primary); background-image: radial-gradient(rgba(255,255,255,.15) 1.2px, transparent 1.2px); background-size: 12px 12px; box-shadow: 0 20px 50px rgba(227,30,36,.18); }
    .tg-work-visa-shell::after { content: ''; position: absolute; right: -90px; bottom: -120px; width: 320px; height: 320px; border: 60px solid rgba(255,255,255,.07); border-radius: 50%; pointer-events: none; }
    .tg-work-visa-intro { position: relative; z-index: 2; height: 100%; min-height: 410px; display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding-right: 20px; }
    .tg-work-visa-intro h2 { color: #fff; font-size: 34px; line-height: 1.18; font-weight: 700; }
    .tg-work-visa-intro p { color: rgba(255,255,255,.82); font-size: 15px; line-height: 1.6; }
    .tg-work-visa-link { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-size: 15px; font-weight: 700; }
    .tg-work-visa-link:hover { color: #fff; gap: 14px; }
    .tg-work-visa-art { position: absolute; left: 20px; bottom: 2px; width: 130px; color: rgba(255,255,255,.2); transform: rotate(-8deg); }
    .tg-work-visa-art svg { width: 100%; height: auto; }
    .tg-work-country { position: relative; z-index: 2; height: 100%; min-height: 410px; overflow: hidden; border-radius: 22px; background: #fff; box-shadow: 0 14px 34px rgba(13,35,74,.18); transition: transform .25s ease, box-shadow .25s ease; }
    .tg-work-country:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(13,35,74,.24); }
    .tg-work-country__media { position: relative; height: 205px; overflow: hidden; }
    .tg-work-country__media > img:first-child { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-work-country:hover .tg-work-country__media > img:first-child { transform: scale(1.06); }
    .tg-work-country__flag { position: absolute; top: 16px; left: 16px; width: auto !important; height: 30px !important; border-radius: 5px; box-shadow: 0 4px 14px rgba(0,0,0,.25); }
    .tg-work-country__body { padding: 18px 18px 14px; }
    .tg-work-country__title { color: var(--dark); font-size: 20px; font-weight: 700; }
    .tg-work-country__pathway { color: var(--primary); font-size: 13px; font-weight: 700; margin-top: 8px; }
    .tg-work-country__copy { color: var(--gray-500); font-size: 13px; line-height: 1.55; margin-top: 8px; }
    .tg-work-country__footer { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; border-top: 1px solid #edf0f5; color: var(--primary); font-size: 13px; font-weight: 700; }
    .tg-work-country__footer span:last-child { color: var(--gray-500); font-size: 11px; font-weight: 500; }
    .tg-universities { overflow: hidden; background: #f3f4ff; }
    .tg-university-kicker { display: inline-flex; align-items: center; gap: 12px; padding: 12px 20px; border-radius: 999px; background: var(--secondary); color: #fff; font-size: 14px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .tg-university-kicker::before { content: ''; width: 10px; height: 10px; border-radius: 3px; background: var(--primary); transform: rotate(45deg); }
    .tg-university-viewport { position: relative; width: 100%; margin-top: 36px; overflow: hidden; }
    .tg-university-viewport::before,
    .tg-university-viewport::after { content: ''; position: absolute; z-index: 2; top: 0; bottom: 0; width: 80px; pointer-events: none; }
    .tg-university-viewport::before { left: 0; background: linear-gradient(90deg, #f3f4ff, rgba(243,244,255,0)); }
    .tg-university-viewport::after { right: 0; background: linear-gradient(270deg, #f3f4ff, rgba(243,244,255,0)); }
    .tg-university-track { display: flex; width: max-content; animation: tg-university-scroll 38s linear infinite; will-change: transform; }
    .tg-university-viewport:hover .tg-university-track { animation-play-state: paused; }
    .tg-university-group { display: flex; gap: 24px; padding-right: 24px; }
    .tg-university-card { display: flex; align-items: center; justify-content: center; width: 190px; height: 154px; flex: 0 0 190px; padding: 24px; border: 1px solid #e3e8f1; border-radius: 24px; background: #fff; box-shadow: 0 10px 28px rgba(19, 42, 82, .09); transition: transform .2s ease, box-shadow .2s ease; }
    .tg-university-card:hover { transform: translateY(-4px); box-shadow: 0 16px 36px rgba(19, 42, 82, .15); }
    .tg-university-card img { display: block; width: auto !important; height: auto !important; max-width: 145px; max-height: 82px; object-fit: contain; }
    @keyframes tg-university-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .tg-proof { border-left: 4px solid var(--primary); padding-left: 20px; }
    .tg-why-matters { position: relative; overflow: hidden; scroll-margin-top: 190px; background-color: #0e2145; background-image: linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px); background-size: 64px 64px; }
    .tg-why-matters::before, .tg-why-matters::after { content: ''; position: absolute; border: 68px solid rgba(255,255,255,.035); border-radius: 50%; pointer-events: none; }
    .tg-why-matters::before { width: 360px; height: 360px; left: -230px; top: 70px; }
    .tg-why-matters::after { width: 430px; height: 430px; right: -280px; bottom: 40px; }
    .tg-why-matters .container { position: relative; z-index: 1; }
    .tg-why-eyebrow { display: inline-flex; align-items: center; gap: 12px; color: #fff; font-size: 14px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .tg-why-eyebrow::before { content: ''; width: 32px; height: 2px; background: var(--primary); }
    .tg-why-title { max-width: 900px; margin: 18px auto 0; color: #fff; font-size: 44px; line-height: 1.18; font-weight: 700; text-wrap: balance; }
    .tg-why-title__accent { display: block; color: var(--primary); font-style: italic; font-weight: 500; }
    .tg-why-card { height: 100%; padding: 30px; border: 1px solid rgba(255,255,255,.16); border-radius: 24px; background: #fff; box-shadow: 0 16px 40px rgba(3,13,32,.22); }
    .tg-why-card__heading, .tg-why-track__heading { display: flex; align-items: center; gap: 12px; color: #0e2145; font-size: 18px; line-height: 1.35; font-weight: 700; text-transform: uppercase; }
    .tg-why-card__marker { width: 16px; height: 16px; flex: 0 0 16px; border-radius: 5px; background: var(--primary); box-shadow: 0 0 0 6px rgba(227,30,36,.1); }
    .tg-why-card p { color: #5d6d87; font-size: 16px; line-height: 1.75; }
    .tg-why-card p + p { margin-top: 16px; }
    .tg-why-track { padding: 32px; border: 1px solid rgba(255,255,255,.22); border-radius: 24px; background: rgba(255,255,255,.08); box-shadow: inset 0 1px 0 rgba(255,255,255,.08); }
    .tg-why-track__heading { color: #fff; }
    .tg-why-track p { color: rgba(255,255,255,.8); font-size: 16px; line-height: 1.75; }
    .tg-why-track p + p { margin-top: 16px; }
    .tg-reviews { overflow: hidden; background: #f5f8fc; }
    .tg-google-score { display: inline-flex; align-items: center; gap: 12px; padding: 10px 18px; border: 1px solid #e2e8f0; border-radius: 999px; background: #fff; color: var(--dark); box-shadow: 0 8px 24px rgba(19,42,82,.07); }
    .tg-google-mark { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: #fff; color: #4285f4; border: 1px solid #e6eaf0; font-size: 18px; font-weight: 800; }
    .tg-review-board { margin-top: 40px; }
    .tg-review-row { position: relative; overflow: hidden; padding: 10px 0; }
    .tg-review-row::before, .tg-review-row::after { content: ''; position: absolute; z-index: 3; top: 0; bottom: 0; width: 90px; pointer-events: none; }
    .tg-review-row::before { left: 0; background: linear-gradient(90deg, #f5f8fc, rgba(245,248,252,0)); }
    .tg-review-row::after { right: 0; background: linear-gradient(270deg, #f5f8fc, rgba(245,248,252,0)); }
    .tg-review-track { display: flex; width: max-content; animation: tg-review-forward 46s linear infinite; will-change: transform; }
    .tg-review-row--reverse .tg-review-track { animation-name: tg-review-reverse; animation-duration: 52s; }
    .tg-review-row:hover .tg-review-track { animation-play-state: paused; }
    .tg-review-group { display: flex; gap: 24px; padding-right: 24px; }
    .tg-review-card { display: flex; flex-direction: column; width: 420px; height: 270px; flex: 0 0 420px; padding: 24px; border: 1px solid #e7ecf3; border-radius: 24px; background: #fff; box-shadow: 0 12px 32px rgba(19,42,82,.07); }
    .tg-review-card__quote { color: var(--gray-500); font-size: 15px; line-height: 1.55; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 6; overflow: hidden; }
    .tg-review-card__footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: auto; padding-top: 18px; border-top: 1px solid #eef1f5; }
    .tg-reviewer { display: flex; align-items: center; min-width: 0; gap: 12px; color: inherit; }
    .tg-reviewer:hover { color: inherit; }
    .tg-reviewer img { width: 48px !important; height: 48px !important; flex: 0 0 48px; border-radius: 50%; object-fit: cover; }
    .tg-reviewer__meta { min-width: 0; }
    .tg-reviewer__name { color: var(--dark); font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tg-reviewer__detail { color: var(--gray-500); font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tg-review-rating { flex: 0 0 auto; color: #f9a000; font-size: 16px; letter-spacing: 1px; white-space: nowrap; }
    .tg-review-time { color: var(--gray-500); font-size: 12px; margin-top: 5px; }
    @keyframes tg-review-forward { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    @keyframes tg-review-reverse { from { transform: translateX(-50%); } to { transform: translateX(0); } }
    .tg-faq details { background: #fff; border: 1px solid #e8edf4; border-radius: 16px; padding: 20px 24px; margin-top: 12px; }
    .tg-faq summary { color: var(--dark); font-size: 17px; font-weight: 600; cursor: pointer; }
    .tg-faq details p { margin-top: 14px; color: var(--gray-500); line-height: 1.7; }
    .tg-contact-card { background: var(--secondary); background-image: url('store/themes/footers/2/footer_background_7gn.png'); border-radius: 32px; padding: 56px; color: #fff; overflow: hidden; }
    .tg-contact-pill { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,.25); border-radius: 999px; padding: 10px 16px; color: #fff; }
    @media (max-width: 991px) {
        .tg-section { padding: 64px 0; }
        .tg-title { font-size: 32px; }
        .tg-contact-card { padding: 32px 24px; }
        .tg-work-visa-shell { padding: 28px; border-radius: 28px; }
        .tg-work-visa-intro { min-height: auto; padding: 8px 0 34px; }
        .tg-work-visa-intro h2 { font-size: 30px; }
        .tg-work-visa-art { display: none; }
        .tg-work-country { min-height: 400px; }
        .tg-university-card { width: 164px; height: 132px; flex-basis: 164px; padding: 20px; }
        .tg-university-card img { max-width: 126px; max-height: 70px; }
        .tg-university-viewport::before, .tg-university-viewport::after { width: 32px; }
        .tg-why-title { font-size: 34px; }
        .tg-why-card, .tg-why-track { padding: 24px; }
        .tg-why-matters::before { left: -280px; }
        .tg-why-matters::after { right: -340px; }
        .tg-review-card { width: 330px; height: 286px; flex-basis: 330px; padding: 20px; }
        .tg-review-row::before, .tg-review-row::after { width: 32px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .tg-university-viewport { overflow-x: auto; }
        .tg-university-track { animation: none; }
        .tg-university-group[aria-hidden="true"] { display: none; }
        .tg-review-row { overflow-x: auto; }
        .tg-review-track { animation: none; }
        .tg-review-group[aria-hidden="true"] { display: none; }
    }
</style>

<main>
    <div class="two-columns-hero-section" style="background-image: url(store/landing_builder/landing_1/1/hero_background_xfq.svg)">
        <div class="container h-100">
            <div class="row h-100 flex-column flex-lg-row">
                <div class="col-12 col-lg-5 two-columns-hero-section__content">
                    <div class="d-inline-flex align-items-center gap-8 p-8 pr-16 rounded-32 border-2 border-dark">
                        <span class="font-14 text-primary font-weight-bold">Since 1992</span>
                        <span class="font-14 text-dark">Built on trust. Driven by student success.</span>
                    </div>

                    <h1 class="d-inline-flex flex-column font-64 mt-24">
                        <span class="text-dark">Shape Your Ambition</span>
                        <span class="mt-4 text-primary">Into International Success</span>
                    </h1>

                    <div class="mt-16 font-16 text-gray-500">
                        At Trans Globe Indore, managed by GEIC, every student and every dream matters. From choosing the right course to securing your visa, our specialists guide you through every step of studying abroad.
                    </div>

                    <div class="d-flex align-items-lg-center flex-column flex-lg-row mt-32 gap-16">
                        <a href="#contact" class="btn-flip-effect btn btn-primary btn-xlg gap-8 text-white" data-text="Book Free Counselling">
                            <span class="btn-flip-effect__text text-white">Book Free Counselling</span>
                        </a>
                        <a href="destinations" class="btn-flip-effect btn-flip-effect__text-dark btn btn-xlg gap-8" data-text="Explore Destinations">
                            <span class="btn-flip-effect__text text-dark">Explore Destinations</span>
                        </a>
                    </div>

                    <div class="d-inline-flex align-items-center gap-12 mt-40 mt-lg-64 p-12 rounded-32 bg-gray-400-20 backdrop-filter-blur-2">
                        <span class="d-flex-center size-40 rounded-circle bg-primary text-white font-weight-bold">TG</span>
                        <div>
                            <div class="font-14 text-dark font-weight-bold">70,250+ students placed worldwide</div>
                            <div class="font-12 text-gray-500 mt-2">Across leading universities in 10+ countries</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-1"></div>
                <div class="col-12 col-lg-6">
                    <div class="two-columns-hero-section__images-side position-relative d-flex justify-content-end w-100 h-100 px-lg-24">
                        <div class="d-flex-center two-columns-hero-section__main-img">
                            <img src="store/landing_builder/landing_13/371/hero_image_j5t.png" alt="Students preparing to study abroad" class="img-cover">
                        </div>
                        <div class="d-flex-center two-columns-hero-section__spinning-img">
                            <img src="assets/transglobe/geic-revolver.svg?v=20260825b" alt="Global education" class="img-cover">
                        </div>
                        <div class="d-flex-center two-columns-hero-section__overlay-img">
                            <img src="store/landing_builder/landing_13/371/hero_overlay_UGc.png" alt="Start your overseas education journey" class="img-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="statistics-section">
            <div class="statistics-section__mask"></div>
            <div class="statistics-section__contents position-relative z-index-2" style="background-color: var(--secondary); background-image: url(store/landing_builder/landing_13/372/statistics_bg_T0k.png)">
                <div class="row">
                    <div class="statistic-col col-6 col-lg-3">
                        <div class="tg-stat-wrap">
                            <span class="tg-stat-icon" style="background:#E31E24"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm-6 9.18V16c0 2.21 2.69 4 6 4s6-1.79 6-4v-3.82l-6 3.27-6-3.27Z"/></svg></span>
                            <div><h4 class="font-28 text-white">70,250+</h4><p class="font-16 text-white mt-4">Students Placed</p></div>
                        </div>
                    </div>
                    <div class="statistic-col col-6 col-lg-3">
                        <div class="tg-stat-wrap">
                            <span class="tg-stat-icon" style="background:#3fcd82"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V3h8v4h10v14h-8v-4h-2v4H3Zm4-4h2v-2H7v2Zm0-4h2v-2H7v2Zm0-4h2V7H7v2Zm8 8h2v-2h-2v2Zm0-4h2v-2h-2v2Z"/></svg></span>
                            <div><h4 class="font-28 text-white">800+</h4><p class="font-16 text-white mt-4">Partner Universities</p></div>
                        </div>
                    </div>
                    <div class="statistic-col col-6 col-lg-3">
                        <div class="tg-stat-wrap">
                            <span class="tg-stat-icon" style="background:#ef6262"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4 5v6c0 5.05 3.41 9.74 8 11 4.59-1.26 8-5.95 8-11V5l-8-3Zm-1.1 14.2-3.6-3.6 1.4-1.4 2.2 2.19 4.4-4.39 1.4 1.4-5.8 5.8Z"/></svg></span>
                            <div><h4 class="font-28 text-white">98.7%</h4><p class="font-16 text-white mt-4">Visa Success Rate</p></div>
                        </div>
                    </div>
                    <div class="statistic-col col-6 col-lg-3">
                        <div class="tg-stat-wrap">
                            <span class="tg-stat-icon" style="background:#ffa200"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg></span>
                            <div><h4 class="font-28 text-white">16+</h4><p class="font-16 text-white mt-4">Branches in India &amp; Nepal</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="tg-section">
        <div class="container">
            <div class="text-center mx-auto" style="max-width: 760px">
                <div class="tg-eyebrow">How it works</div>
                <h2 class="tg-title mt-12">Four Simple Steps to Study Abroad</h2>
                <p class="tg-copy mt-16">From your first conversation to the day you board your flight, Trans Globe Indore makes the process clear, personal and manageable.</p>
            </div>
            <div class="row mt-32">
                <div class="col-12 col-md-6 col-lg-3 mt-24"><div class="tg-card"><span class="tg-step">01</span><h3 class="font-20 text-dark mt-20">Tell Us About Yourself</h3><p class="tg-copy mt-12">Share your academic background, interests, preferred countries and career goals. This first conversation helps us understand where you want to go.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3 mt-24"><div class="tg-card"><span class="tg-step">02</span><h3 class="font-20 text-dark mt-20">Meet a Specialist</h3><p class="tg-copy mt-12">Work with a country-and-course specialist to shortlist universities, explore scholarships and build a plan that suits your profile and budget.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3 mt-24"><div class="tg-card"><span class="tg-step">03</span><h3 class="font-20 text-dark mt-20">Apply With Confidence</h3><p class="tg-copy mt-12">We help with your SOP, recommendations, transcripts, documents and application forms so every submission is complete and compelling.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3 mt-24"><div class="tg-card"><span class="tg-step">04</span><h3 class="font-20 text-dark mt-20">Get Your Visa &amp; Go</h3><p class="tg-copy mt-12">Our visa team prepares your documents, finances and interview answers so you can travel knowing everything is in order.</p></div></div>
            </div>
        </div>
    </section>

    <section id="services" class="tg-section tg-section-soft">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-12 col-lg-7">
                    <div class="tg-eyebrow">Our services</div>
                    <h2 class="tg-title mt-12">Everything You Need, Under One Roof</h2>
                    <p class="tg-copy mt-16">Trans Globe Indore supports your complete journey—from your first exam and university application to your arrival in a new country.</p>
                </div>
                <div class="col-12 col-lg-5 mt-20 mt-lg-0 text-lg-right"><a href="#contact" class="btn btn-primary btn-lg text-white">Discuss Your Study Plan</a></div>
            </div>

            <div class="row mt-24">
                <div class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">01</span><h3 class="font-20 text-dark mt-20">Expert Counselling</h3><p class="tg-copy mt-12">Find the country, university and course that genuinely fit your goals—without being pushed toward a particular institution.</p></div></div>
                <div class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">02</span><h3 class="font-20 text-dark mt-20">University Admissions</h3><p class="tg-copy mt-12">Build a strong, error-free application with the right SOP, recommendation letters, documents and submission timeline.</p></div></div>
                <div id="scholarships" class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">03</span><h3 class="font-20 text-dark mt-20">Scholarship Guidance</h3><p class="tg-copy mt-12">Discover scholarships and bursaries you may not know you qualify for. More than 2,000 Trans Globe students receive awards each year.</p></div></div>
                <div id="test-prep" class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">04</span><h3 class="font-20 text-dark mt-20">IELTS, PTE, TOEFL &amp; More</h3><p class="tg-copy mt-12">Prepare for IELTS, PTE, TOEFL, GRE, GMAT and SAT with expert trainers, realistic practice and proven score-improvement support.</p></div></div>
                <div class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">05</span><h3 class="font-20 text-dark mt-20">Visa Assistance</h3><p class="tg-copy mt-12">Avoid incomplete or inconsistent applications with detailed document checks, financial guidance and interview preparation.</p></div></div>
                <div class="col-12 col-md-6 col-lg-4 mt-24"><div class="tg-card"><span class="tg-icon">06</span><h3 class="font-20 text-dark mt-20">Pre &amp; Post Departure Support</h3><p class="tg-copy mt-12">Get practical help with packing, banking, arrival and settling into your new university city—you are never doing this alone.</p></div></div>
            </div>
        </div>
    </section>

    <section id="destinations" class="tg-section">
        <div class="container">
            <div class="text-center mx-auto" style="max-width: 780px">
                <div class="tg-eyebrow">Where will you thrive?</div>
                <h2 class="tg-title mt-12">Explore the World’s Best Study Destinations</h2>
                <p class="tg-copy mt-16">From research-led universities to affordable public education and strong post-study pathways, discover the destination that best fits your future.</p>
            </div>
            <div class="row mt-24">
                @foreach ([
                    ['Australia', 'Research-led education, practical learning and the prestigious Group of Eight universities.', 'australia.jpg', 'au.png'],
                    ['New Zealand', 'Government-regulated education in a peaceful, welcoming environment.', 'new-zealand.jpg', 'nz.png'],
                    ['United Kingdom', 'Prestigious institutions with rigorous academic quality standards.', 'uk.jpg', 'gb.png'],
                    ['Ireland', 'English-medium education in one of Europe’s fastest-growing technology hubs.', 'ireland.jpg', 'ie.png'],
                    ['Germany', 'World-class engineering and innovation with low-cost public education options.', 'germany.webp', 'de.png'],
                    ['Europe', 'Diverse cultures, affordable tuition and outstanding international exposure.', 'europe-card.jpg', 'eu.png'],
                    ['United States', 'More than 4,000 accredited colleges and universities with diverse programs.', 'usa.jpg', 'us.png'],
                    ['Canada', 'High-quality education with attractive post-study work pathways.', 'canada.jpg', 'ca.png'],
                    ['Singapore', 'Asia’s education and business hub with globally ranked universities.', 'singapore.jpg', 'sg.png'],
                    ['Dubai & UAE', 'Globally recognised degrees in a fast-growing, cosmopolitan hub.', 'dubai-card.jpg', 'ae.png'],
                    ['Malaysia', 'Affordable study with international branch campuses and a multicultural lifestyle.', 'malaysia.webp', 'my.png'],
                    ['Switzerland', 'World-class education, research, innovation and exceptional quality of life.', 'switzerland.webp', 'ch.png'],
                ] as [$country, $description, $image, $flag])
                    <div class="col-12 col-md-6 col-lg-4 mt-20">
                        <div class="tg-destination">
                            <img src="assets/transglobe/destinations/{{ $image }}" alt="{{ $country }} study destination" class="tg-destination__image" loading="lazy">
                            <div class="tg-destination__overlay"></div>
                            <img src="assets/transglobe/destinations/flags/{{ $flag }}" alt="{{ $country }} flag" class="tg-destination__flag" loading="lazy">
                            <div class="tg-destination__content">
                                <h3 class="font-22">{{ $country }}</h3>
                                <p class="font-14 mt-10">{{ $description }}</p>
                                <span class="font-14 text-warning mt-12">Explore opportunities →</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="work-visas" class="tg-section tg-section-soft">
        <div class="container">
            <div class="tg-work-visa-shell">
                <div class="row align-items-stretch position-relative z-index-2">
                    <div class="col-12 col-lg-3">
                        <div class="tg-work-visa-intro">
                            <span class="tg-contact-pill mb-20">Work visa pathways</span>
                            <h2>Build Your Career Abroad</h2>
                            <p class="mt-16">Explore skilled-work opportunities with practical guidance on eligibility, documentation and the right pathway for your profile.</p>
                            <a href="#contact" class="tg-work-visa-link mt-22">Free Profile Assessment <span aria-hidden="true">→</span></a>
                            <span class="tg-work-visa-art" aria-hidden="true">
                                <svg viewBox="0 0 128 128" fill="currentColor"><path d="M71 9 54 44 16 53l-7 11 40 5-10 21-15 4-6 10 24 1 14 18 6-10-4-15 14-18 22 34 12-5-13-38 30-25 1-13-40 8L71 9Z"/></svg>
                            </span>
                        </div>
                    </div>

                    @foreach ([
                        ['Canada', 'Express Entry & Skilled Worker', 'PR-focused routes for qualified professionals across high-demand occupations.', 'canada.jpg', 'ca.png'],
                        ['Australia', 'General Skilled Migration', 'Occupation-led pathways for experienced professionals and skilled applicants.', 'australia.jpg', 'au.png'],
                        ['Germany', 'EU Blue Card & Opportunity Card', 'Career opportunities in engineering, technology, healthcare and other skilled fields.', 'germany.webp', 'de.png'],
                    ] as [$country, $pathway, $description, $image, $flag])
                        <div class="col-12 col-md-6 col-lg-3 mt-24 mt-lg-0">
                            <article class="tg-work-country">
                                <div class="tg-work-country__media">
                                    <img src="assets/transglobe/destinations/{{ $image }}" alt="Work visa opportunities in {{ $country }}" loading="lazy">
                                    <img src="assets/transglobe/destinations/flags/{{ $flag }}" alt="{{ $country }} flag" class="tg-work-country__flag" loading="lazy">
                                </div>
                                <div class="tg-work-country__body">
                                    <h3 class="tg-work-country__title">{{ $country }}</h3>
                                    <div class="tg-work-country__pathway">{{ $pathway }}</div>
                                    <p class="tg-work-country__copy">{{ $description }}</p>
                                </div>
                                <a href="#contact" class="tg-work-country__footer">
                                    <span>Explore pathway →</span>
                                    <span>Expert guidance</span>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="universities" class="tg-section tg-universities">
        @php
            $universityLogos = [
                ['Australian National University', 'australian-national-university.png'],
                ['Monash University', 'monash-university.png'],
                ['Adelaide University', 'adelaide-university.png'],
                ['The University of Queensland', 'university-of-queensland.png'],
                ['Queensland University of Technology', 'queensland-university-of-technology.png'],
                ['Arizona State University', 'arizona-state-university.jpg'],
                ['Brunel University London', 'brunel-university-london.png'],
                ['University of York', 'university-of-york.jpg'],
                ['Massey University', 'massey-university.jpg'],
                ['Auckland University of Technology', 'auckland-university-of-technology.png'],
            ];
        @endphp

        <div class="container text-center">
            <span class="tg-university-kicker">Global partner network</span>
            <h2 class="tg-title mt-20">800+ University Tie-Ups Worldwide</h2>
            <p class="tg-copy mt-12 mx-auto" style="max-width: 720px">Explore opportunities across a trusted global network of leading universities and find the institution that fits your ambitions.</p>
        </div>

        <div class="tg-university-viewport" aria-label="Partner universities">
            <div class="tg-university-track">
                @for ($copy = 0; $copy < 2; $copy++)
                    <div class="tg-university-group" @if ($copy === 1) aria-hidden="true" @endif>
                        @foreach ($universityLogos as [$university, $logo])
                            <div class="tg-university-card" title="{{ $university }}">
                                <img src="assets/transglobe/universities/{{ $logo }}" alt="{{ $copy === 0 ? $university : '' }}" loading="lazy">
                            </div>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>

        <div class="container text-center mt-40">
            <a href="#contact" class="btn btn-primary btn-lg text-white">View All Universities <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <section id="why-trans-globe" class="tg-section tg-why-matters" aria-labelledby="why-matters-title">
        <div class="container">
            <header class="text-center">
                <div class="tg-why-eyebrow">Why it matters</div>
                <h2 id="why-matters-title" class="tg-why-title">
                    Why the Right Consultants in India
                    <span class="tg-why-title__accent">Make All the Difference</span>
                </h2>
            </header>

            <div class="row mt-24">
                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>The Scale of the Opportunity</h3>
                        <p class="mt-20">Every year, over 1.3 million Indian students study abroad—to build genuinely global careers, access research facilities that don't exist in India, and earn degrees that open doors everywhere. The opportunity has never been greater. But navigating it without guidance has never been more complex.</p>
                        <p>Visa policies change. Deadlines shift. Scholarship requirements update every year. What worked three years ago may not work today. This is why working with experienced overseas education consultants isn't just convenient—it's genuinely important.</p>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>What Makes a Good Consultant</h3>
                        <p class="mt-20">Not all study-abroad consultants are the same. Some work with only a handful of universities and steer students regardless of fit. Some don't update their knowledge of visa policies. Some charge for services reputable consultancies include for free.</p>
                        <p>At Trans Globe, we've been doing this since 1992. Our counsellors are specialists, not generalists—each focuses on specific countries and knows those visa processes in detail. We're affiliated with 800+ universities, so we recommend what's right for you, not what's easiest for us.</p>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>Covering All of India</h3>
                        <p class="mt-20">Trans Globe has 16 offices across India—Rajkot, Ahmedabad, Surat, Anand, Gandhinagar, Vadodara, Jaipur, Delhi, Chandigarh, Pune, Indore, Jamnagar, Morbi and Kochi—plus an international office in Kathmandu, Nepal. We also offer online counselling.</p>
                        <p>Our reach means we understand students from different backgrounds, cities and academic systems. A student from a small town in Gujarat and one from a metro in Maharashtra have different needs—our counsellors understand both.</p>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>The Countries We Specialise In</h3>
                        <p class="mt-20">We help students study in Australia, Canada, the USA, the UK, Germany, New Zealand, Ireland, Singapore, Dubai &amp; the UAE, and across Europe. Each destination has its own visa requirements and post-study work opportunities, and our specialists track all of it continuously.</p>
                        <p>Germany is popular for its low public-university fees and strong engineering programs. Canada is sought after for its Post-Graduation Work Permit. Australia's Group of Eight universities rank among the world's best with clear pathways for skilled graduates.</p>
                    </article>
                </div>
            </div>

            <article class="tg-why-track mt-24">
                <h3 class="tg-why-track__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>Our Track Record</h3>
                <p class="mt-20">Since 1992, Trans Globe has placed more than 70,250 students at universities in over 10 countries. Our 98.7% visa success rate is built on three decades of understanding what visa officers look for. Our scholarship success—80% of our students receive some form of scholarship or bursary—comes from knowing which universities offer merit-based aid to Indian students.</p>
                <p>We're proud of these numbers. But we're prouder of the messages from students now working at top companies in London, Sydney, Toronto and Dubai, who tell us studying abroad was the best decision they ever made.</p>
            </article>
        </div>
    </section>

    <section id="guidance" class="tg-section tg-guidance-section">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <div class="tg-eyebrow">Stay informed</div>
                    <h2 class="tg-title mt-12">Study-Abroad Guidance in Plain Language</h2>
                    <p class="tg-copy mt-16">Follow Trans Globe Indore for visa updates, scholarship alerts, student stories and honest advice that helps families make better decisions.</p>
                </div>
                <div class="col-12 col-lg-7 mt-24 mt-lg-0">
                    <div class="row">
                        <div class="col-12 col-md-4 mt-16">
                            <article class="tg-guidance-card">
                                <div class="tg-guidance-card__media">
                                    <img src="store/1/default_images/blogs/blog2.jpg" alt="Student reviewing visa documents" width="1368" height="978" loading="lazy" decoding="async">
                                    <span class="tg-guidance-card__tag">Visa</span>
                                </div>
                                <div class="tg-guidance-card__body">
                                    <h3>Visa Updates</h3>
                                    <p class="mt-8">Understand current policies, documents and timelines.</p>
                                </div>
                            </article>
                        </div>
                        <div class="col-12 col-md-4 mt-16">
                            <article class="tg-guidance-card">
                                <div class="tg-guidance-card__media">
                                    <img src="store/1/default_images/blogs/blog3.jpg" alt="Student researching scholarship opportunities online" width="1368" height="978" loading="lazy" decoding="async">
                                    <span class="tg-guidance-card__tag">Scholarships</span>
                                </div>
                                <div class="tg-guidance-card__body">
                                    <h3>Scholarship Alerts</h3>
                                    <p class="mt-8">Find funding opportunities matched to your profile.</p>
                                </div>
                            </article>
                        </div>
                        <div class="col-12 col-md-4 mt-16">
                            <article class="tg-guidance-card">
                                <div class="tg-guidance-card__media">
                                    <img src="assets/transglobe/destinations/australia.jpg" alt="Sydney, Australia study destination" width="1920" height="1440" loading="lazy" decoding="async">
                                    <span class="tg-guidance-card__tag">Destinations</span>
                                </div>
                                <div class="tg-guidance-card__body">
                                    <h3>Country Guides</h3>
                                    <p class="mt-8">Compare study, work and lifestyle opportunities.</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="reviews" class="tg-section tg-reviews">
        @php
            $geicReviews = [
                ['name' => 'Arun Rawat', 'detail' => '2 reviews', 'time' => '3 weeks ago', 'rating' => 5, 'avatar' => 'arun-rawat.jpg', 'profile' => 'https://www.google.com/maps/contrib/101139747229314047176/reviews?hl=en-GB', 'text' => 'I had a great experience taking classes at TransGlobe. The teachers were knowledgeable, supportive, and always willing to help. The teaching methods were clear and practical, which made learning easy and enjoyable. The staff was friendly, and the overall environment was positive and motivating. I truly appreciate the guidance and support.'],
                ['name' => 'anuj joshi', 'detail' => 'Local Guide · 16 reviews · 8 photos', 'time' => 'a month ago', 'rating' => 5, 'avatar' => 'anuj-joshi.jpg', 'profile' => 'https://www.google.com/maps/contrib/114720059525458984507/reviews?hl=en-GB', 'text' => 'Excellent service and a very supportive team throughout my journey. They provided clear guidance for university admissions, documentation, and the visa process. They were always available to answer my questions and made the entire process smooth and stress-free. Their professionalism and genuine support gave me confidence at every step. Thank you for everything!!'],
                ['name' => 'Gurshan Singh', 'detail' => '1 review', 'time' => '3 weeks ago', 'rating' => 5, 'avatar' => 'gurshan-singh.jpg', 'profile' => 'https://www.google.com/maps/contrib/115340119879227058911/reviews?hl=en-GB', 'text' => 'had an incredible experience with Transglobe indore while preparing to move abroad. From our very first consultation, their team demonstrated deep expertise, complete transparency, and a genuine commitment to my goals.'],
                ['name' => 'Siddhi Janve', 'detail' => '2 reviews', 'time' => '6 months ago', 'rating' => 5, 'avatar' => 'siddhi-janve.jpg', 'profile' => 'https://www.google.com/maps/contrib/110281383945291485493/reviews?hl=en-GB', 'text' => 'A professional and supportive study abroad consultancy. The team offers clear guidance and takes time to explain each step of the process, making it easier to plan applications with confidence. Their transparent and student-focused approach makes them a reliable choice.'],
                ['name' => 'Mustafa Nalwala', 'detail' => '1 review', 'time' => '6 months ago', 'rating' => 5, 'avatar' => 'mustafa-nalwala.jpg', 'profile' => 'https://www.google.com/maps/contrib/113484357490209490434/reviews?hl=en-GB', 'text' => 'Trans globe managed by GEIC, Indore exceeded my expectations! Helpful and attentive counselling team made the whole process seamless. Quick responses and hassle-free experience. Highly recommend for anyone looking abroad!!!....'],
                ['name' => 'Kundan Sharma', 'detail' => '2 reviews', 'time' => '6 months ago', 'rating' => 5, 'avatar' => 'kundan-sharma.jpg', 'profile' => 'https://www.google.com/maps/contrib/118432767180589444725/reviews?hl=en-GB', 'text' => 'I had a really good experience with Trans Globe .The team is very supportive and gives genuine guidance at every step. From university selection to application processing, financial guidance, visa processing, and even pre-departure counselling, everything was handled smoothly and professionally. Thank you very much.'],
                ['name' => 'Aaniya Bhavsar', 'detail' => '1 review', 'time' => '4 months ago', 'rating' => 5, 'avatar' => 'aaniya-bhavsar.jpg', 'profile' => 'https://www.google.com/maps/contrib/103054574298616027180/reviews?hl=en-GB', 'text' => 'It was very nice talking to the consultant. I had so many doubts regarding study abroad and he cleared everything that was going on my mind. Very nice experience talking to him'],
                ['name' => 'Antar Singh', 'detail' => '1 review', 'time' => '4 months ago', 'rating' => 5, 'avatar' => 'antar-singh.jpg', 'profile' => 'https://www.google.com/maps/contrib/101137092209324147601/reviews?hl=en-GB', 'text' => 'It was great and they explained everything. You must visit once if you are planning for studying abroad. Nice behaviour and everything was nice'],
                ['name' => 'Mohammed Safdari', 'detail' => 'Local Guide · 26 reviews · 830 photos', 'time' => '9 months ago', 'rating' => 5, 'avatar' => 'mohammed-safdari.jpg', 'profile' => 'https://www.google.com/maps/contrib/106498896229567093791/reviews?hl=en-GB', 'text' => 'I availed their support for the Germany master’s admission process as well as the student visa services, and everything was managed smoothly and efficiently. They guided me through each step, resolved all my doubts, and handled the entire documentation with complete professionalism. The team was always responsive and supportive, which made both the admission and visa procedures stress-free. I truly appreciate their expertise and highly recommend their services to anyone planning to pursue a master’s program in Germany.'],
                ['name' => 'Rishika Katara', 'detail' => '1 review', 'time' => '7 months ago', 'rating' => 5, 'avatar' => 'rishika-katara.jpg', 'profile' => 'https://www.google.com/maps/contrib/103625125473541928424/reviews?hl=en-GB', 'text' => 'The abroad counselling session was very helpful and informative. The counselor was knowledgeable, patient, and clearly explained the process while addressing all my doubts. I feel more confident about my study abroad plans after the session.'],
            ];
            $geicReviewRows = array_chunk($geicReviews, 5);
        @endphp

        <div class="container text-center">
            <div class="tg-eyebrow">Student experiences</div>
            <h2 class="tg-title mt-12">Real Students. Real Google Reviews.</h2>
            <p class="tg-copy mt-16 mx-auto" style="max-width: 740px">Hear directly from students who trusted Trans Globe Indore, managed by GEIC, for counselling, admissions, visa support and test preparation.</p>
            <a href="https://www.google.com/search?q=geic+indore#lrd=0x3962fd400e5c61eb:0x6db8cf73bcf20625,1,,,," target="_blank" rel="noopener noreferrer" class="tg-google-score mt-24" aria-label="View GEIC Indore reviews on Google">
                <span class="tg-google-mark" aria-hidden="true">G</span>
                <span class="font-weight-bold">4.8</span>
                <span class="tg-review-rating" aria-hidden="true">★★★★★</span>
                <span class="font-13 text-gray-500">495 Google reviews</span>
            </a>
        </div>

        <div class="tg-review-board">
            @foreach ($geicReviewRows as $rowIndex => $reviewRow)
                <div class="tg-review-row {{ $rowIndex === 1 ? 'tg-review-row--reverse' : '' }}">
                    <div class="tg-review-track">
                        @for ($copy = 0; $copy < 2; $copy++)
                            <div class="tg-review-group" @if ($copy === 1) aria-hidden="true" @endif>
                                @foreach ($reviewRow as $review)
                                    <article class="tg-review-card">
                                        <p class="tg-review-card__quote">“{{ $review['text'] }}”</p>
                                        <div class="tg-review-card__footer">
                                            <a href="{{ $review['profile'] }}" target="_blank" rel="noopener noreferrer" class="tg-reviewer" aria-label="View {{ $review['name'] }} on Google Maps">
                                                <img src="assets/geic/reviewers/{{ $review['avatar'] }}" alt="{{ $review['name'] }}" loading="lazy">
                                                <span class="tg-reviewer__meta">
                                                    <span class="tg-reviewer__name d-block">{{ $review['name'] }}</span>
                                                    <span class="tg-reviewer__detail d-block">{{ $review['detail'] }}</span>
                                                </span>
                                            </a>
                                            <span>
                                                <span class="tg-review-rating d-block" aria-label="{{ $review['rating'] }} out of 5 stars">★★★★★</span>
                                                <span class="tg-review-time d-block text-right">{{ $review['time'] }}</span>
                                            </span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="faq" class="tg-section tg-section-soft">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <div class="tg-eyebrow">Questions, answered</div>
                    <h2 class="tg-title mt-12">Frequently Asked Questions</h2>
                    <p class="tg-copy mt-16">Clear answers to the questions students and families ask before beginning their international education journey.</p>
                </div>
                <div class="col-12 col-lg-7 mt-24 mt-lg-0 tg-faq">
                    <details open><summary>What does a study-abroad consultant do?</summary><p>A consultant helps you choose a country and course, shortlist universities, strengthen applications, find scholarships, prepare your student visa and get ready for life abroad. At Trans Globe Indore, this support is free for students.</p></details>
                    <details><summary>How early should I start the application process?</summary><p>For most destinations, begin 12 to 18 months before your intended intake. This leaves enough time for language tests, university applications, scholarships and visa processing.</p></details>
                    <details><summary>Can Indian students get scholarships?</summary><p>Yes. Universities, governments and private organisations offer awards based on merit, field of study, financial need and other criteria. Trans Globe Indore helps identify and apply for suitable options.</p></details>
                    <details><summary>What is Trans Globe’s visa success rate?</summary><p>The Trans Globe network reports a 98.7% visa success rate, built on decades of experience preparing complete, consistent applications for students across 10+ countries.</p></details>
                    <details><summary>Can I get counselling if I cannot visit the Indore office?</summary><p>Yes. Trans Globe Indore offers online counselling sessions with the same detailed, specialist guidance available at our Indore office.</p></details>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="tg-section">
        <div class="container">
            <div class="tg-contact-card">
                <div class="row align-items-center position-relative z-index-2">
                    <div class="col-12 col-lg-8">
                        <div class="tg-contact-pill">Free, no-pressure guidance</div>
                        <h2 class="font-44 text-white mt-20">Your Journey Starts With One Conversation</h2>
                        <p class="font-16 text-white opacity-70 mt-16">You do not need to have everything figured out. Tell us where you are today, and we will explain your options honestly and help you take the next step.</p>
                        <div class="d-flex flex-wrap gap-12 mt-28">
                            <a href="tel:+919826666886" class="tg-contact-pill">+91 98266 66886</a>
                            <a href="mailto:info@geic.in" class="tg-contact-pill">info@geic.in</a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-28 mt-lg-0 text-lg-right">
                        <a href="tel:+919826666886" class="btn btn-primary btn-xlg text-white">Speak to Our Indore Counsellor</a>
                        <p class="font-13 text-white opacity-70 mt-12">Office No. 503, THE VIEW Tower 1, Yeshwant Niwas Rd, Lad Colony, Indore, Madhya Pradesh 452001</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@include('mirror.partials.footer')
