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
    .tg-blog-section { overflow: hidden; scroll-margin-top: 190px; background: #f5f8fc; }
    .tg-blog-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 32px; }
    .tg-blog-header__copy { max-width: 760px; }
    .tg-blog-all { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; flex: 0 0 auto; gap: 10px; padding: 0 20px; border: 1px solid rgba(14,33,69,.14); border-radius: 14px; background: #fff; color: #0e2145; font-size: 14px; font-weight: 700; box-shadow: 0 8px 24px rgba(14,33,69,.06); }
    .tg-blog-all:hover { border-color: #F3951E; background: #F3951E; color: #fff; transform: translateY(-2px); }
    .tg-blog-grid { display: grid; grid-template-columns: 1.2fr 1fr 1fr; grid-template-rows: repeat(2, 232px); gap: 20px; margin-top: 38px; }
    .tg-blog-card { position: relative; min-width: 0; overflow: hidden; border-radius: 24px; background: #0e2145; box-shadow: 0 16px 38px rgba(14,33,69,.14); }
    .tg-blog-card--featured { grid-row: 1 / 3; }
    .tg-blog-card__link { position: absolute; inset: 0; display: flex; color: #fff; }
    .tg-blog-card__link:focus-visible { outline: 3px solid #F3951E; outline-offset: -5px; }
    .tg-blog-card__image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
    .tg-blog-card__overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5,17,39,.04) 18%, rgba(5,17,39,.96) 100%); }
    .tg-blog-card__content { position: relative; z-index: 2; display: flex; width: 100%; min-width: 0; padding: 22px; flex-direction: column; justify-content: flex-end; }
    .tg-blog-card--featured .tg-blog-card__content { padding: 34px; }
    .tg-blog-card__category { align-self: flex-start; padding: 7px 11px; border-radius: 999px; background: #fff; color: #E31E24; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; box-shadow: 0 8px 18px rgba(5,17,39,.16); }
    .tg-blog-card__title { max-width: 95%; margin-top: 14px; color: #fff; font-size: 18px; line-height: 1.3; font-weight: 700; text-wrap: balance; }
    .tg-blog-card--featured .tg-blog-card__title { max-width: 90%; font-size: 29px; line-height: 1.22; }
    .tg-blog-card__excerpt { display: -webkit-box; max-width: 92%; margin-top: 13px; overflow: hidden; color: rgba(255,255,255,.78); font-size: 14px; line-height: 1.55; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
    .tg-blog-card__meta { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 18px; color: rgba(255,255,255,.72); font-size: 12px; }
    .tg-blog-card__action { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; flex: 0 0 42px; border-radius: 50%; background: #E31E24; color: #fff; font-size: 20px; box-shadow: 0 8px 20px rgba(227,30,36,.28); transition: background-color .2s ease, transform .2s ease; }
    .tg-blog-card:hover .tg-blog-card__image { transform: scale(1.07); }
    .tg-blog-card:hover .tg-blog-card__action { background: #F3951E; transform: translateX(3px); }
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
    .tg-mobile-read-more { display: none; }
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
    .tg-mobile-app-bar, .tg-mobile-discovery, .tg-mobile-bottom-nav, .tg-mobile-drawer, .tg-mobile-drawer-backdrop { display: none; }
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
    @media (max-width: 767px) {
        html { scroll-padding-top: 86px; }
        body.home-page { overflow-x: hidden; padding-bottom: calc(90px + env(safe-area-inset-bottom)); background: #edf1f5; }
        .home-page #appHeaderArea { display: none !important; }
        .home-page main { padding-top: 78px; }
        .home-page main > .container { padding-right: 12px; padding-left: 12px; }

        .tg-mobile-app-bar { position: fixed; z-index: 1002; top: 0; left: 0; right: 0; display: flex; align-items: center; justify-content: space-between; height: 72px; padding: 10px 16px; border-bottom: 1px solid rgba(14,33,69,.08); background: rgba(255,255,255,.94); box-shadow: 0 8px 24px rgba(14,33,69,.08); backdrop-filter: blur(16px); }
        .tg-mobile-app-bar__brand { display: flex; align-items: center; height: 48px; min-width: 0; }
        .tg-mobile-app-bar__brand img { display: block; width: auto !important; height: auto !important; max-width: 210px; max-height: 44px; object-fit: contain; }
        .tg-mobile-menu-button, .tg-mobile-drawer__close { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; flex: 0 0 48px; border: 0; border-radius: 50%; background: #f5f7fa; color: #0e2145; }
        .tg-mobile-menu-button { flex-direction: column; gap: 5px; }
        .tg-mobile-menu-button span { width: 20px; height: 2px; border-radius: 2px; background: currentColor; transition: transform .24s cubic-bezier(.2,0,0,1), opacity .16s ease; }
        .tg-mobile-menu-button[aria-expanded="true"] span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .tg-mobile-menu-button[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .tg-mobile-menu-button[aria-expanded="true"] span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .tg-mobile-discovery { display: flex; align-items: center; gap: 10px; margin: 8px 14px 12px; padding: 8px; border: 1px solid #e3e8ef; border-radius: 22px; background: #fff; box-shadow: 0 10px 28px rgba(14,33,69,.07); }
        .tg-mobile-discovery__search { display: flex; align-items: center; min-width: 0; min-height: 48px; flex: 1; gap: 11px; padding: 0 14px; border-radius: 16px; background: #f6f8fb; color: #6f7f96; font-size: 14px; }
        .tg-mobile-discovery__search span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .tg-mobile-discovery svg { width: 21px; height: 21px; flex: 0 0 21px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .tg-mobile-discovery__filter { display: grid; place-items: center; width: 48px; height: 48px; flex: 0 0 48px; border-radius: 15px; background: #F3951E; color: #fff; box-shadow: 0 8px 18px rgba(243,149,30,.28); }

        .tg-mobile-drawer-backdrop { position: fixed; z-index: 1003; inset: 0; display: block; visibility: hidden; background: rgba(5,17,39,.48); opacity: 0; transition: opacity .22s ease, visibility 0s linear .22s; }
        .tg-mobile-drawer { position: fixed; z-index: 1004; top: 0; right: 0; bottom: 0; display: flex; width: min(88vw, 360px); padding: 20px 18px calc(24px + env(safe-area-inset-bottom)); flex-direction: column; background: #fff; box-shadow: -22px 0 60px rgba(5,17,39,.22); transform: translate3d(105%,0,0); transition: transform .3s cubic-bezier(.2,0,0,1); }
        .tg-mobile-menu-open { overflow: hidden; }
        .tg-mobile-menu-open .tg-mobile-drawer { transform: translate3d(0,0,0); }
        .tg-mobile-menu-open .tg-mobile-drawer-backdrop { visibility: visible; opacity: 1; transition-delay: 0s; }
        .tg-mobile-drawer__header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 16px; border-bottom: 1px solid #e8edf3; }
        .tg-mobile-drawer__header span, .tg-mobile-drawer__header strong { display: block; }
        .tg-mobile-drawer__header span { color: #8a98aa; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .tg-mobile-drawer__header strong { margin-top: 3px; color: #0e2145; font-size: 19px; }
        .tg-mobile-drawer__close { font-size: 28px; font-weight: 300; }
        .tg-mobile-drawer__nav { display: grid; gap: 7px; margin-top: 14px; overflow-y: auto; }
        .tg-mobile-drawer__nav a { min-height: 58px; padding: 11px 14px; border-radius: 16px; color: #0e2145; background: #f7f9fc; }
        .tg-mobile-drawer__nav span, .tg-mobile-drawer__nav small { display: block; }
        .tg-mobile-drawer__nav span { font-size: 15px; font-weight: 700; }
        .tg-mobile-drawer__nav small { margin-top: 2px; color: #7c8ca2; font-size: 11px; }
        .tg-mobile-drawer__cta { display: flex; align-items: center; justify-content: center; min-height: 52px; margin-top: auto; border-radius: 16px; background: #E31E24; color: #fff; font-weight: 700; box-shadow: 0 10px 24px rgba(227,30,36,.22); }

        .tg-mobile-bottom-nav { position: fixed; z-index: 1001; left: 10px; right: 10px; bottom: calc(8px + env(safe-area-inset-bottom)); display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); min-height: 72px; padding: 7px 6px 6px; border: 1px solid rgba(14,33,69,.1); border-radius: 24px; background: rgba(255,255,255,.96); box-shadow: 0 15px 42px rgba(5,17,39,.18); backdrop-filter: blur(18px); }
        .tg-mobile-bottom-nav a { position: relative; display: flex; align-items: center; justify-content: center; min-width: 0; min-height: 58px; flex-direction: column; gap: 4px; border-radius: 17px; color: #7e8b9d; }
        .tg-mobile-bottom-nav svg { width: 22px; height: 22px; fill: currentColor; transition: transform .18s cubic-bezier(.2,0,0,1); }
        .tg-mobile-bottom-nav span { font-size: 10px; font-weight: 700; }
        .tg-mobile-bottom-nav a::before { content: ''; position: absolute; top: 3px; left: 50%; width: 18px; height: 3px; border-radius: 4px; background: #F3951E; opacity: 0; transform: translateX(-50%) scaleX(.3); transition: opacity .18s ease, transform .22s cubic-bezier(.2,0,0,1); }
        .tg-mobile-bottom-nav a.is-active { color: #E31E24; background: rgba(227,30,36,.07); }
        .tg-mobile-bottom-nav a.is-active::before { opacity: 1; transform: translateX(-50%) scaleX(1); }
        .tg-mobile-bottom-nav a.is-active svg { transform: translateY(-2px); }
        .tg-mobile-bottom-nav__action { color: #fff !important; background: #E31E24 !important; }
        .tg-mobile-bottom-nav__action::before { background: #F3951E !important; }

        .home-page .two-columns-hero-section { min-height: auto; height: auto; margin: 0 10px 14px; padding: 18px 0 22px; overflow: hidden; border-radius: 30px; background-color: #fff; background-size: cover; box-shadow: 0 14px 36px rgba(14,33,69,.08); }
        .home-page .two-columns-hero-section__content { padding-top: 4px; padding-bottom: 10px; }
        .home-page .two-columns-hero-section__content > div:first-child { max-width: 100%; padding-right: 12px !important; white-space: normal; }
        .home-page .two-columns-hero-section h1 { font-size: 37px !important; line-height: 1.06; letter-spacing: -.025em; }
        .home-page .two-columns-hero-section .btn { width: 100%; min-height: 52px; }
        .home-page .two-columns-hero-section__images-side { min-height: 330px; margin-top: 8px; transform: scale(.92); transform-origin: center top; }

        .home-page .statistics-section { margin: 0; }
        .home-page .statistics-section__contents { padding: 14px 0 5px; border-radius: 28px; }
        .home-page .statistics-section__mask { border-radius: 28px; }
        .home-page .tg-section { margin: 12px 10px; padding: 34px 0; overflow: hidden; border-radius: 28px; background-color: #fff; box-shadow: 0 10px 30px rgba(14,33,69,.055); scroll-margin-top: 82px; }
        .home-page .tg-section-soft { background: #f7f9fc; }
        .home-page .tg-title { font-size: 29px; line-height: 1.15; letter-spacing: -.018em; }
        .home-page .tg-copy { font-size: 15px; line-height: 1.62; }
        .home-page .tg-eyebrow { font-size: 11px; }
        .home-page .text-center { text-align: left !important; }
        .home-page .mx-auto { margin-left: 0 !important; }

        .tg-mobile-slider { display: flex; flex-wrap: nowrap; gap: 0; margin-right: -18px; margin-left: -18px; padding: 0 10px 18px; overflow-x: auto; overscroll-behavior-inline: contain; scroll-padding-inline: 10px; scroll-snap-type: inline mandatory; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
        .tg-mobile-slider::-webkit-scrollbar { display: none; }
        .tg-mobile-slider > [class*="col-"] { width: 84vw; max-width: 84vw; flex: 0 0 84vw; padding-right: 8px; padding-left: 8px; scroll-snap-align: start; scroll-snap-stop: always; }
        .statistics-section .tg-mobile-slider { margin: 0; padding: 6px 12px 12px; }
        .statistics-section .tg-mobile-slider > [class*="col-"] { width: 100%; max-width: 100%; flex-basis: 100%; padding: 0; }
        .statistics-section .statistic-col { border-right: 0; }
        .statistics-section .tg-stat-wrap { min-height: 98px; padding: 18px; border: 1px solid rgba(255,255,255,.12); border-radius: 20px; background: rgba(255,255,255,.07); }
        .statistics-section .tg-stat-icon { width: 52px; height: 52px; flex-basis: 52px; }
        .statistics-section .tg-stat-icon svg { width: 23px; height: 23px; }
        .statistics-section h4 { font-size: 23px !important; }
        .statistics-section p { font-size: 14px !important; }
        .tg-mobile-slider > [class*="col-"] > .tg-card,
        .tg-mobile-slider > [class*="col-"] > .tg-destination,
        .tg-mobile-slider > [class*="col-"] > .tg-work-country { height: 100%; }
        .home-page .tg-card { padding: 24px; border-radius: 23px; }


        .home-page .tg-work-visa-shell { padding: 22px 18px 18px; border-radius: 26px; }
        .tg-mobile-slider--work { margin-right: -18px; margin-left: -18px; padding-left: 10px; }
        .tg-mobile-slider--work .tg-work-visa-intro { min-height: 350px; padding: 24px 12px; }
        .tg-mobile-slider--work .tg-work-country { min-height: 380px; }
        .home-page .tg-universities { background: #f3f4ff; }
        .home-page .tg-university-viewport { overflow: hidden; scroll-snap-type: none; }
        .home-page .tg-university-viewport::-webkit-scrollbar { display: none; }
        .home-page .tg-university-track { animation: tg-university-scroll 30s linear infinite; }
        .home-page .tg-university-viewport:active .tg-university-track { animation-play-state: paused; }
        .home-page .tg-university-group { padding-left: 18px; }
        .home-page .tg-university-group[aria-hidden="true"] { display: flex; }
        .home-page .tg-university-card { scroll-snap-align: none; }
        .home-page .tg-why-matters { background-color: #0e2145; }
        .home-page .tg-why-title { font-size: 31px; text-align: left; }
        .home-page .tg-why-card { min-height: 0; }
        .home-page .tg-why-track { margin: 20px 0 0 !important; }
        .home-page .tg-why-expandable__content { position: relative; max-height: 158px; overflow: hidden; }
        .home-page .tg-why-expandable__content:not(.is-expanded)::after { content: ''; position: absolute; right: 0; bottom: 0; left: 0; height: 52px; pointer-events: none; background: linear-gradient(180deg, rgba(255,255,255,0), #fff 88%); }
        .home-page .tg-why-track .tg-why-expandable__content:not(.is-expanded)::after { background: linear-gradient(180deg, rgba(35,59,98,0), #233b62 88%); }
        .home-page .tg-why-expandable__content.is-expanded { max-height: none; }
        .home-page .tg-mobile-read-more { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; margin-top: 12px; padding: 0 16px; border: 1px solid rgba(227,30,36,.22); border-radius: 999px; background: rgba(227,30,36,.08); color: #E31E24; font-size: 13px; font-weight: 700; }
        .home-page .tg-why-track .tg-mobile-read-more { border-color: rgba(255,255,255,.22); background: rgba(255,255,255,.09); color: #fff; }

        .home-page .tg-blog-header { display: block; }
        .home-page .tg-blog-all { width: 100%; margin-top: 20px; }
        .home-page .tg-blog-grid { display: flex; gap: 14px; margin: 26px -18px 0; padding: 0 10px 18px; overflow-x: auto; overscroll-behavior-inline: contain; scroll-padding-inline: 10px; scroll-snap-type: inline mandatory; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
        .home-page .tg-blog-grid::-webkit-scrollbar { display: none; }
        .home-page .tg-blog-card, .home-page .tg-blog-card--featured { width: 82vw; min-width: 82vw; height: 410px; flex: 0 0 82vw; scroll-snap-align: start; scroll-snap-stop: always; }
        .home-page .tg-blog-card__content, .home-page .tg-blog-card--featured .tg-blog-card__content { padding: 24px; }
        .home-page .tg-blog-card__title, .home-page .tg-blog-card--featured .tg-blog-card__title { max-width: 100%; font-size: 22px; line-height: 1.25; }
        .home-page .tg-blog-card__excerpt { max-width: 100%; -webkit-line-clamp: 3; }

        .home-page .tg-review-track { animation: none; }
        .home-page .tg-review-group[aria-hidden="true"] { display: none; }
        .home-page .tg-review-row { padding: 8px 14px 16px; overflow-x: auto; overscroll-behavior-inline: contain; scroll-padding-left: 14px; scroll-snap-type: x mandatory; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
        .home-page .tg-review-row::-webkit-scrollbar { display: none; }
        .home-page .tg-review-row::before, .home-page .tg-review-row::after { display: none; }
        .home-page .tg-review-card { width: 84vw; height: 300px; flex-basis: 84vw; scroll-snap-align: start; }
        .home-page .tg-faq details { padding: 18px; }
        .home-page .tg-faq summary { font-size: 15px; line-height: 1.4; }
        .home-page .tg-contact-card { padding: 30px 22px; border-radius: 25px; }
        .home-page .tg-contact-card h2 { font-size: 31px !important; line-height: 1.12; }
        .home-page .tg-contact-card .btn { width: 100%; min-height: 52px; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 { padding-top: 4px; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > img { width: 230px !important; max-width: 62vw !important; margin-bottom: 16px !important; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center { gap: 6px !important; max-width: 100%; padding: 8px 12px !important; border-width: 1px !important; font-size: 12px; line-height: 1.35; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center .size-24 { width: 18px !important; min-width: 18px !important; height: 18px !important; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > h3 { margin-top: 12px !important; font-size: 28px !important; line-height: 1.2 !important; }
        .home-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .btn { margin-top: 20px !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .tg-university-viewport { overflow-x: auto; }
        .tg-university-track { animation: none; }
        .tg-university-group[aria-hidden="true"] { display: none; }
        .tg-review-row { overflow-x: auto; }
        .tg-review-track { animation: none; }
        .tg-review-group[aria-hidden="true"] { display: none; }
        .tg-blog-card__image, .tg-blog-card__action, .tg-blog-all, .tg-mobile-menu-button span, .tg-mobile-drawer, .tg-mobile-drawer-backdrop, .tg-mobile-bottom-nav svg, .tg-mobile-bottom-nav a::before { transition: none; }
    }
</style>

<main id="home">
    <header class="tg-mobile-app-bar" aria-label="Mobile application header">
        <a href="#home" class="tg-mobile-app-bar__brand" aria-label="Trans Globe Indore home">
            <img src="assets/transglobe/trans-globe-logo.png" alt="Trans Globe Indore managed by GEIC">
        </a>
        <button type="button" class="tg-mobile-menu-button" aria-label="Open navigation menu" aria-controls="tgMobileDrawer" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </header>

    <div class="tg-mobile-drawer-backdrop" data-mobile-menu-close aria-hidden="true"></div>
    <aside id="tgMobileDrawer" class="tg-mobile-drawer" aria-label="Mobile menu" aria-hidden="true">
        <div class="tg-mobile-drawer__header">
            <div><span>Explore</span><strong>Trans Globe Indore</strong></div>
            <button type="button" class="tg-mobile-drawer__close" data-mobile-menu-close aria-label="Close navigation menu">×</button>
        </div>
        <nav class="tg-mobile-drawer__nav">
            <a href="destinations"><span>Study destinations</span><small>Compare countries</small></a>
            <a href="#work-visas"><span>Work visa pathways</span><small>Explore skilled routes</small></a>
            <a href="#universities"><span>Partner universities</span><small>800+ global institutions</small></a>
            <a href="#scholarships"><span>Scholarships</span><small>Funding guidance</small></a>
            <a href="#test-prep"><span>Test preparation</span><small>IELTS, PTE and more</small></a>
            <a href="#why-trans-globe"><span>Why choose us</span><small>Experience and results</small></a>
            <a href="#faq"><span>FAQs</span><small>Common questions answered</small></a>
        </nav>
        <a href="#contact" class="tg-mobile-drawer__cta">Book free counselling</a>
    </aside>

    <section class="tg-mobile-discovery" aria-label="Quick actions">
        <a href="destinations" class="tg-mobile-discovery__search">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
            <span>Search countries and study options</span>
        </a>
        <a href="#contact" class="tg-mobile-discovery__filter" aria-label="Open free counselling section">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M6 14v6"/></svg>
        </a>
    </section>

    <nav class="tg-mobile-bottom-nav" aria-label="Primary mobile navigation">
        <a href="#home" class="is-active" data-mobile-nav="home" aria-current="page">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3V10.5Z"/></svg><span>Home</span>
        </a>
        <a href="#destinations" data-mobile-nav="destinations">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-13a7 7 0 1 0-14 0c0 6.8 7 13 7 13Zm0-10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg><span>Explore</span>
        </a>
        <a href="#services" data-mobile-nav="services">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg><span>Services</span>
        </a>
        <a href="#reviews" data-mobile-nav="reviews">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8 14.8 8l5.8.8-4.2 4.1 1 5.8-5.4-2.8-5.4 2.8 1-5.8-4.2-4.1L9.2 8 12 2.8Z"/></svg><span>Reviews</span>
        </a>
        <a href="#contact" class="tg-mobile-bottom-nav__action" data-mobile-nav="contact">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v12H8l-4 4V4Zm4 4v2h8V8H8Zm0 4v2h5v-2H8Z"/></svg><span>Consult</span>
        </a>
    </nav>

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
                <div class="row tg-mobile-slider" data-slider-name="Highlights">
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

    <section id="how-it-works" class="tg-section">
        <div class="container">
            <div class="text-center mx-auto" style="max-width: 760px">
                <div class="tg-eyebrow">How it works</div>
                <h2 class="tg-title mt-12">Four Simple Steps to Study Abroad</h2>
                <p class="tg-copy mt-16">From your first conversation to the day you board your flight, Trans Globe Indore makes the process clear, personal and manageable.</p>
            </div>
            <div class="row mt-32 tg-mobile-slider" data-slider-name="Your journey">
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

            <div class="row mt-24 tg-mobile-slider" data-slider-name="Services">
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
            <div class="row mt-24 tg-mobile-slider" data-slider-name="Destinations">
                @foreach ([
                    ['Australia', 'Research-led education, practical learning and the prestigious Group of Eight universities.', 'australia.jpg', 'au.png', 'australia'],
                    ['New Zealand', 'Government-regulated education in a peaceful, welcoming environment.', 'new-zealand.jpg', 'nz.png', 'new-zealand'],
                    ['United Kingdom', 'Prestigious institutions with rigorous academic quality standards.', 'uk.jpg', 'gb.png', 'uk'],
                    ['Ireland', 'English-medium education in one of Europe’s fastest-growing technology hubs.', 'ireland.jpg', 'ie.png', 'ireland'],
                    ['Germany', 'World-class engineering and innovation with low-cost public education options.', 'germany.webp', 'de.png', 'germany'],
                    ['Europe', 'Diverse cultures, affordable tuition and outstanding international exposure.', 'europe-card.jpg', 'eu.png', 'europe'],
                    ['United States', 'More than 4,000 accredited colleges and universities with diverse programs.', 'usa.jpg', 'us.png', 'usa'],
                    ['Canada', 'High-quality education with attractive post-study work pathways.', 'canada.jpg', 'ca.png', 'canada'],
                    ['Singapore', 'Asia’s education and business hub with globally ranked universities.', 'singapore.jpg', 'sg.png', 'singapore'],
                    ['Dubai & UAE', 'Globally recognised degrees in a fast-growing, cosmopolitan hub.', 'dubai-card.jpg', 'ae.png', 'dubai'],
                    ['Malaysia', 'Affordable study with international branch campuses and a multicultural lifestyle.', 'malaysia.webp', 'my.png', 'malaysia'],
                    ['Switzerland', 'World-class education, research, innovation and exceptional quality of life.', 'switzerland.webp', 'ch.png', 'switzerland'],
                ] as [$country, $description, $image, $flag, $slug])
                    <div class="col-12 col-md-6 col-lg-4 mt-20">
                        <a href="{{ url('/destinations/'.$slug) }}" class="tg-destination d-block" aria-label="Explore studying in {{ $country }}">
                            <img src="assets/transglobe/destinations/{{ $image }}" alt="{{ $country }} study destination" class="tg-destination__image" loading="lazy">
                            <div class="tg-destination__overlay"></div>
                            <img src="assets/transglobe/destinations/flags/{{ $flag }}" alt="{{ $country }} flag" class="tg-destination__flag" loading="lazy">
                            <div class="tg-destination__content">
                                <h3 class="font-22">{{ $country }}</h3>
                                <p class="font-14 mt-10">{{ $description }}</p>
                                <span class="font-14 text-warning mt-12">Explore opportunities →</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="work-visas" class="tg-section tg-section-soft">
        <div class="container">
            <div class="tg-work-visa-shell">
                <div class="row align-items-stretch position-relative z-index-2 tg-mobile-slider tg-mobile-slider--work" data-slider-name="Work pathways">
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

            <div class="row mt-24 tg-mobile-slider" data-slider-name="Why Trans Globe">
                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>The Scale of the Opportunity</h3>
                        <div id="why-scale-copy" class="tg-why-expandable__content">
                            <p class="mt-20">Every year, over 1.3 million Indian students study abroad—to build genuinely global careers, access research facilities that don't exist in India, and earn degrees that open doors everywhere. The opportunity has never been greater. But navigating it without guidance has never been more complex.</p>
                            <p>Visa policies change. Deadlines shift. Scholarship requirements update every year. What worked three years ago may not work today. This is why working with experienced overseas education consultants isn't just convenient—it's genuinely important.</p>
                        </div>
                        <button type="button" class="tg-mobile-read-more" aria-expanded="false" aria-controls="why-scale-copy">View more</button>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>What Makes a Good Consultant</h3>
                        <div id="why-consultant-copy" class="tg-why-expandable__content">
                            <p class="mt-20">Not all study-abroad consultants are the same. Some work with only a handful of universities and steer students regardless of fit. Some don't update their knowledge of visa policies. Some charge for services reputable consultancies include for free.</p>
                            <p>At Trans Globe, we've been doing this since 1992. Our counsellors are specialists, not generalists—each focuses on specific countries and knows those visa processes in detail. We're affiliated with 800+ universities, so we recommend what's right for you, not what's easiest for us.</p>
                        </div>
                        <button type="button" class="tg-mobile-read-more" aria-expanded="false" aria-controls="why-consultant-copy">View more</button>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>Covering All of India</h3>
                        <div id="why-india-copy" class="tg-why-expandable__content">
                            <p class="mt-20">Trans Globe has 16 offices across India—Rajkot, Ahmedabad, Surat, Anand, Gandhinagar, Vadodara, Jaipur, Delhi, Chandigarh, Pune, Indore, Jamnagar, Morbi and Kochi—plus an international office in Kathmandu, Nepal. We also offer online counselling.</p>
                            <p>Our reach means we understand students from different backgrounds, cities and academic systems. A student from a small town in Gujarat and one from a metro in Maharashtra have different needs—our counsellors understand both.</p>
                        </div>
                        <button type="button" class="tg-mobile-read-more" aria-expanded="false" aria-controls="why-india-copy">View more</button>
                    </article>
                </div>

                <div class="col-12 col-lg-6 mt-24">
                    <article class="tg-why-card">
                        <h3 class="tg-why-card__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>The Countries We Specialise In</h3>
                        <div id="why-countries-copy" class="tg-why-expandable__content">
                            <p class="mt-20">We help students study in Australia, Canada, the USA, the UK, Germany, New Zealand, Ireland, Singapore, Dubai &amp; the UAE, and across Europe. Each destination has its own visa requirements and post-study work opportunities, and our specialists track all of it continuously.</p>
                            <p>Germany is popular for its low public-university fees and strong engineering programs. Canada is sought after for its Post-Graduation Work Permit. Australia's Group of Eight universities rank among the world's best with clear pathways for skilled graduates.</p>
                        </div>
                        <button type="button" class="tg-mobile-read-more" aria-expanded="false" aria-controls="why-countries-copy">View more</button>
                    </article>
                </div>
            </div>

            <article class="tg-why-track mt-24">
                <h3 class="tg-why-track__heading"><span class="tg-why-card__marker" aria-hidden="true"></span>Our Track Record</h3>
                <div id="why-track-copy" class="tg-why-expandable__content">
                    <p class="mt-20">Since 1992, Trans Globe has placed more than 70,250 students at universities in over 10 countries. Our 98.7% visa success rate is built on three decades of understanding what visa officers look for. Our scholarship success—80% of our students receive some form of scholarship or bursary—comes from knowing which universities offer merit-based aid to Indian students.</p>
                    <p>We're proud of these numbers. But we're prouder of the messages from students now working at top companies in London, Sydney, Toronto and Dubai, who tell us studying abroad was the best decision they ever made.</p>
                </div>
                <button type="button" class="tg-mobile-read-more" aria-expanded="false" aria-controls="why-track-copy">View more</button>
            </article>
        </div>
    </section>

    <section id="blogs" class="tg-section tg-blog-section" aria-labelledby="blog-section-title">
        @php
            $recentBlogs = [
                [
                    'title' => 'Is It Still Safe to Study Abroad in 2026?',
                    'category' => 'Student Guidance',
                    'excerpt' => 'A calm, factual guide for Indian students and parents navigating travel concerns, visa scrutiny and changing global conditions.',
                    'image' => 'assets/transglobe/destinations/australia/campus-students.jpg',
                    'url' => 'https://transglobeedu.com/blog/is-it-safe-to-study-abroad-2026-indian-students',
                ],
                [
                    'title' => 'What University Admissions Officers Look For',
                    'category' => 'Admissions',
                    'excerpt' => 'Understand what matters beyond grades, from a strong SOP to a well-rounded global profile.',
                    'image' => 'assets/transglobe/destinations/europe-card.jpg',
                    'url' => 'https://transglobeedu.com/blog/university-admissions-guide-for-international-students-2025',
                ],
                [
                    'title' => 'Common Study Visa Mistakes and How to Avoid Them',
                    'category' => 'Student Visa',
                    'excerpt' => 'Learn how weak SOPs, inconsistent finances and incomplete documents can affect a visa application.',
                    'image' => 'store/1/default_images/blogs/blog2.jpg',
                    'url' => 'https://transglobeedu.com/blog/student-visa-mistakes-2025',
                ],
                [
                    'title' => 'USA vs UK vs Canada: Which Visa Process Is Easiest?',
                    'category' => 'Visa Comparison',
                    'excerpt' => 'Compare student visa requirements, timelines and practical considerations for three popular destinations.',
                    'image' => 'store/1/default_images/blogs/blog4.jpg',
                    'url' => 'https://transglobeedu.com/blog/student-visa-2025-usa-vs-uk-vs-canada',
                ],
                [
                    'title' => 'Choose the Best Study Destination for Your Career Goals',
                    'category' => 'Career Planning',
                    'excerpt' => 'Match your country choice with your budget, career direction and post-study opportunities.',
                    'image' => 'assets/transglobe/destinations/dubai-card.jpg',
                    'url' => 'https://transglobeedu.com/blog/best-study-abroad-destination-career-goals',
                ],
            ];
        @endphp

        <div class="container">
            <header class="tg-blog-header">
                <div class="tg-blog-header__copy">
                    <div class="tg-eyebrow">From the blog</div>
                    <h2 id="blog-section-title" class="tg-title mt-12">Fresh Study-Abroad Insights, Without the Jargon</h2>
                    <p class="tg-copy mt-16">Recent guidance from Trans Globe on university admissions, student visas and choosing the right destination for your future.</p>
                </div>
                <a href="https://transglobeedu.com/blogs" target="_blank" rel="noopener noreferrer" class="tg-blog-all">
                    Explore all articles <span aria-hidden="true">→</span>
                </a>
            </header>

            <div class="tg-blog-grid" aria-label="Recent Trans Globe articles">
                @foreach ($recentBlogs as $index => $blog)
                    <article class="tg-blog-card blog-section__post-card {{ $index === 0 ? 'tg-blog-card--featured' : '' }}">
                        <a href="{{ $blog['url'] }}" target="_blank" rel="noopener noreferrer" class="tg-blog-card__link" aria-label="Read {{ $blog['title'] }} on Trans Globe">
                            <img src="{{ $blog['image'] }}" alt="{{ $blog['title'] }}" class="tg-blog-card__image blog-section__post-card-img" width="1200" height="800" loading="lazy" decoding="async">
                            <span class="tg-blog-card__overlay" aria-hidden="true"></span>
                            <span class="tg-blog-card__content">
                                <span class="tg-blog-card__category">{{ $blog['category'] }}</span>
                                <span class="tg-blog-card__title">{{ $blog['title'] }}</span>
                                @if ($index === 0)
                                    <span class="tg-blog-card__excerpt">{{ $blog['excerpt'] }}</span>
                                @endif
                                <span class="tg-blog-card__meta">
                                    <span>By Trans Globe</span>
                                    <span class="tg-blog-card__action" aria-hidden="true">→</span>
                                </span>
                            </span>
                        </a>
                    </article>
                @endforeach
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileQuery = window.matchMedia('(max-width: 767px)');
        if (!mobileQuery.matches) return;

        const body = document.body;
        const menuButton = document.querySelector('.tg-mobile-menu-button');
        const drawer = document.getElementById('tgMobileDrawer');
        const menuClosers = document.querySelectorAll('[data-mobile-menu-close], .tg-mobile-drawer a');
        let lastFocused = null;

        function openMenu() {
            lastFocused = document.activeElement;
            body.classList.add('tg-mobile-menu-open');
            menuButton.setAttribute('aria-expanded', 'true');
            drawer.setAttribute('aria-hidden', 'false');
            window.setTimeout(function () { drawer.querySelector('a')?.focus(); }, 180);
        }

        function closeMenu() {
            body.classList.remove('tg-mobile-menu-open');
            menuButton.setAttribute('aria-expanded', 'false');
            drawer.setAttribute('aria-hidden', 'true');
            lastFocused?.focus();
        }

        menuButton.addEventListener('click', function () {
            if (body.classList.contains('tg-mobile-menu-open')) closeMenu();
            else openMenu();
        });
        menuClosers.forEach(function (item) { item.addEventListener('click', closeMenu); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && body.classList.contains('tg-mobile-menu-open')) closeMenu();
        });

        document.querySelectorAll('.tg-mobile-read-more').forEach(function (button) {
            const content = document.getElementById(button.getAttribute('aria-controls'));
            if (!content) return;
            button.addEventListener('click', function () {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!expanded));
                button.textContent = expanded ? 'View more' : 'View less';
                content.classList.toggle('is-expanded', !expanded);
            });
        });

        const bottomLinks = Array.from(document.querySelectorAll('.tg-mobile-bottom-nav a[data-mobile-nav]'));
        const trackedSections = [
            ['home', document.querySelector('.two-columns-hero-section')],
            ['services', document.getElementById('services')],
            ['destinations', document.getElementById('destinations')],
            ['reviews', document.getElementById('reviews')],
            ['contact', document.getElementById('contact')]
        ].filter(function (entry) { return entry[1]; });
        let navTicking = false;

        function updateBottomNavigation() {
            const marker = window.scrollY + (window.innerHeight * .35);
            let current = 'home';
            trackedSections.forEach(function (entry) {
                if (marker >= entry[1].offsetTop) current = entry[0];
            });
            bottomLinks.forEach(function (link) {
                const active = link.dataset.mobileNav === current;
                link.classList.toggle('is-active', active);
                if (active) link.setAttribute('aria-current', 'page');
                else link.removeAttribute('aria-current');
            });
            navTicking = false;
        }

        window.addEventListener('scroll', function () {
            if (!navTicking) {
                window.requestAnimationFrame(updateBottomNavigation);
                navTicking = true;
            }
        }, { passive: true });
        updateBottomNavigation();
    });
</script>

@include('mirror.partials.footer')
