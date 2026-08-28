@include('mirror.partials.header', ['siteCms' => $cms])
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/'), 'mobileBackLabel' => 'Back to home'])

<style>
    @font-face { font-family:'Plus Jakarta Sans'; font-style:normal; font-weight:700 800; font-display:swap; src:url('{{ asset('assets/fonts/plus-jakarta-sans-latin.woff2') }}') format('woff2'); }
    :root { --ab-navy:#0e2145; --ab-red:#e31e24; --ab-orange:#f3951e; --ab-ink:#15294d; --ab-muted:#64748b; --ab-soft:#f4f7fb; --ab-line:#dfe7f0; }
    .ab-page { overflow:clip; background:#fff; color:var(--ab-ink); }
    .ab-wrap { width:min(1280px,calc(100% - 48px)); margin-inline:auto; }
    .ab-section { padding:92px 0; scroll-margin-top:96px; }
    .ab-kicker { display:inline-flex; align-items:center; gap:11px; color:var(--ab-red); font-size:12px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
    .ab-kicker::before { width:29px; height:2px; background:currentColor; content:''; }
    .ab-title { max-width:760px; margin:15px 0 0; color:var(--ab-navy); font-size:clamp(34px,4vw,52px); line-height:1.07; font-weight:800; letter-spacing:-.048em; text-wrap:balance; }
    .ab-title em { color:var(--ab-red); font-style:normal; }
    .ab-lead { max-width:680px; margin:18px 0 0; color:var(--ab-muted); font-size:16px; line-height:1.75; }
    .ab-page :is(a,button,summary):focus-visible { outline:3px solid rgba(243,149,30,.48); outline-offset:3px; }
    .ab-link { display:inline-flex; align-items:center; gap:9px; color:var(--ab-navy); font-size:14px; font-weight:800; transition:color .2s ease,gap .2s ease; }
    .ab-link:hover { gap:13px; color:var(--ab-orange); }
    .ab-hero { padding:126px 0 0; background:var(--ab-soft); }
    .ab-hero__shell { position:relative; display:grid; overflow:hidden; min-height:560px; grid-template-columns:1.08fr .92fr; align-items:center; gap:48px; padding:66px; border-radius:36px; background:var(--ab-navy); box-shadow:0 28px 70px rgba(14,33,69,.2); }
    .ab-hero__shell::before { position:absolute; inset:0; opacity:.22; background-image:linear-gradient(rgba(255,255,255,.11) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.11) 1px,transparent 1px); background-size:48px 48px; mask-image:linear-gradient(90deg,#000,transparent 90%); content:''; }
    .ab-hero__ring { position:absolute; top:-235px; right:-120px; width:610px; height:610px; border:82px solid rgba(227,30,36,.42); border-radius:50%; }
    .ab-hero__copy { position:relative; z-index:1; min-width:0; }
    .ab-crumbs { display:flex; flex-wrap:wrap; gap:8px; color:rgba(255,255,255,.62); font-size:13px; }
    .ab-crumbs a { color:#fff; font-weight:700; }
    .ab-hero .ab-kicker { margin-top:34px; color:#ff7379; }
    .ab-hero h1 { max-width:720px; margin:17px 0 0; color:#fff; font-size:clamp(44px,5.15vw,68px); line-height:1.01; font-weight:800; letter-spacing:-.058em; text-wrap:balance; }
    .ab-hero__lead { max-width:640px; margin:20px 0 0; color:rgba(255,255,255,.8); font-size:17px; line-height:1.7; }
    .ab-hero__actions { display:flex; flex-wrap:wrap; gap:13px; margin-top:29px; }
    .ab-button-secondary { display:inline-flex; min-height:52px; align-items:center; justify-content:center; gap:9px; padding:0 21px; border:1px solid rgba(255,255,255,.32); border-radius:14px; color:#fff!important; font-size:14px; font-weight:800; transition:border-color .2s ease,background .2s ease,transform .2s ease; }
    .ab-button-secondary:hover { border-color:var(--ab-orange); background:var(--ab-orange); transform:translateY(-2px); }
    .ab-hero__visual { position:relative; z-index:1; min-width:0; }
    .ab-hero__image { position:relative; overflow:hidden; min-height:430px; border:1px solid rgba(255,255,255,.18); border-radius:27px; background:#20375e; box-shadow:0 24px 54px rgba(4,15,36,.34); }
    .ab-hero__image::after { position:absolute; inset:0; background:linear-gradient(150deg,rgba(14,33,69,.08),transparent 44%,rgba(227,30,36,.2)); content:''; }
    .ab-hero__image img { display:block; width:100%; height:430px; object-fit:cover; }
    .ab-hero__badge { position:absolute; z-index:2; right:-18px; bottom:28px; display:flex; width:min(285px,82%); align-items:center; gap:13px; padding:17px; border:1px solid rgba(255,255,255,.74); border-radius:18px; background:rgba(255,255,255,.94); box-shadow:0 18px 40px rgba(4,15,36,.2); backdrop-filter:blur(12px); }
    .ab-hero__badge-icon { display:grid; width:43px; height:43px; flex:0 0 43px; place-items:center; border-radius:13px; background:#eaf8f2; color:#20a36b; }
    .ab-hero__badge-icon svg { width:22px; height:22px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
    .ab-hero__badge strong { display:block; color:var(--ab-navy); font-size:14px; }
    .ab-hero__badge span { display:block; margin-top:3px; color:var(--ab-muted); font-size:12px; line-height:1.4; }
    .ab-page-header { position:relative; z-index:3; margin:-1px 34px 0; padding:26px 29px; border:1px solid var(--ab-line); border-radius:0 0 25px 25px; background:#fff; box-shadow:0 18px 42px rgba(14,33,69,.08); }
    .ab-page-header__inner { display:flex; align-items:center; justify-content:space-between; gap:28px; }
    .ab-page-header__identity { display:flex; align-items:center; gap:15px; }
    .ab-page-header__icon { display:grid; width:54px; height:54px; flex:0 0 54px; place-items:center; border-radius:16px; background:#fdebed; color:var(--ab-red); }
    .ab-page-header__icon svg { width:28px; height:28px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; }
    .ab-page-header h2 { margin:0; color:var(--ab-navy); font-size:20px; font-weight:800; }
    .ab-page-header p { max-width:650px; margin:4px 0 0; color:var(--ab-muted); font-size:13px; line-height:1.55; }
    .ab-story { display:grid; grid-template-columns:.92fr 1.08fr; align-items:center; gap:72px; }
    .ab-story__media { position:relative; min-height:550px; }
    .ab-story__main { position:absolute; inset:0 70px 58px 0; overflow:hidden; border-radius:30px; background:#dce5ef; box-shadow:0 22px 52px rgba(14,33,69,.14); }
    .ab-story__main img,.ab-story__aside img { display:block; width:100%; height:100%; object-fit:cover; }
    .ab-story__main img { object-position:center top; }
    .ab-story__aside { position:absolute; right:0; bottom:0; width:47%; overflow:hidden; aspect-ratio:1 / 1.1; border:9px solid #fff; border-radius:25px; background:#dce5ef; box-shadow:0 18px 40px rgba(14,33,69,.18); }
    .ab-story__seal { position:absolute; top:28px; right:27px; display:grid; width:105px; height:105px; place-items:center; border-radius:50%; background:var(--ab-red); color:#fff; text-align:center; box-shadow:0 16px 32px rgba(227,30,36,.28); }
    .ab-story__seal strong { display:block; font-size:24px; line-height:1; }
    .ab-story__seal span { display:block; margin-top:4px; font-size:10px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .ab-story__copy p { margin:18px 0 0; color:var(--ab-muted); font-size:16px; line-height:1.78; }
    .ab-story__copy .ab-link { margin-top:25px; }
    .ab-purpose { background:var(--ab-soft); }
    .ab-purpose__head,.ab-services__head { display:flex; align-items:end; justify-content:space-between; gap:40px; }
    .ab-purpose__grid { display:grid; grid-template-columns:repeat(12,minmax(0,1fr)); gap:20px; margin-top:42px; }
    .ab-purpose-card { position:relative; overflow:hidden; min-height:290px; grid-column:span 4; padding:31px; border:1px solid var(--ab-line); border-radius:26px; background:#fff; box-shadow:0 14px 36px rgba(14,33,69,.06); }
    .ab-purpose-card--mission { grid-column:span 5; background:var(--ab-navy); }
    .ab-purpose-card--promise { grid-column:span 3; background:var(--ab-red); }
    .ab-purpose-card::after { position:absolute; right:-74px; bottom:-82px; width:210px; height:210px; border:38px solid rgba(227,30,36,.05); border-radius:50%; content:''; }
    .ab-purpose-card--mission::after,.ab-purpose-card--promise::after { border-color:rgba(255,255,255,.06); }
    .ab-purpose-card__number { position:relative; z-index:1; display:grid; width:48px; height:48px; place-items:center; border-radius:15px; background:#fdebed; color:var(--ab-red); font-size:13px; font-weight:800; }
    .ab-purpose-card--mission .ab-purpose-card__number,.ab-purpose-card--promise .ab-purpose-card__number { background:rgba(255,255,255,.14); color:#fff; }
    .ab-purpose-card h3 { position:relative; z-index:1; margin:37px 0 0; color:var(--ab-navy); font-size:25px; font-weight:800; }
    .ab-purpose-card p { position:relative; z-index:1; margin:13px 0 0; color:var(--ab-muted); font-size:14px; line-height:1.72; }
    .ab-purpose-card--mission h3,.ab-purpose-card--mission p,.ab-purpose-card--promise h3,.ab-purpose-card--promise p { color:#fff; }
    .ab-purpose-card--mission p,.ab-purpose-card--promise p { color:rgba(255,255,255,.76); }
    .ab-team { background:#fff; }
    .ab-team__head { display:flex; align-items:end; justify-content:space-between; gap:40px; }
    .ab-team__head-copy { max-width:620px; }
    .ab-team__controls { display:flex; align-items:center; gap:9px; flex:0 0 auto; }
    .ab-team__control { display:inline-grid; width:46px; height:46px; place-items:center; border:1px solid var(--ab-line); border-radius:50%; background:#fff; color:var(--ab-navy); cursor:pointer; transition:border-color .16s ease,background-color .16s ease,color .16s ease; }
    .ab-team__control:hover,.ab-team__control:focus-visible { border-color:var(--ab-orange); background:var(--ab-orange); color:#fff; outline:0; }
    .ab-team__control svg { width:19px; height:19px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
    .ab-team__viewport { overflow-x:auto; margin-top:42px; scroll-behavior:smooth; scroll-snap-type:x mandatory; scrollbar-width:none; overscroll-behavior-inline:contain; }
    .ab-team__viewport::-webkit-scrollbar { display:none; }
    .ab-team__grid { display:flex; gap:22px; width:max-content; margin:0; }
    .ab-team-card { position:relative; width:calc((min(1280px,100vw - 48px) - 44px) / 3); min-width:0; flex:0 0 calc((min(1280px,100vw - 48px) - 44px) / 3); overflow:hidden; border:1px solid var(--ab-line); border-radius:27px; background:#fff; box-shadow:0 15px 40px rgba(14,33,69,.07); scroll-snap-align:start; transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease; }
    .ab-team-card:hover { border-color:rgba(243,149,30,.58); box-shadow:0 24px 52px rgba(14,33,69,.14); transform:translateY(-5px); }
    .ab-team-card__media { position:relative; overflow:hidden; aspect-ratio:4 / 4.35; margin:11px 11px 0; border-radius:20px; background:#edf1f5; }
    .ab-team-card__media::after { position:absolute; inset:auto 0 0; height:34%; background:linear-gradient(180deg,transparent,rgba(14,33,69,.2)); content:''; }
    .ab-team-card__media img { display:block; width:100%; height:100%; object-fit:cover; object-position:center top; transition:transform .35s ease; }
    .ab-team-card:hover .ab-team-card__media img { transform:scale(1.025); }
    .ab-team-card__role { position:absolute; z-index:1; right:16px; bottom:16px; left:16px; display:flex; align-items:center; gap:8px; color:#fff; font-size:12px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .ab-team-card__role::before { width:9px; height:9px; flex:0 0 9px; border:3px solid rgba(255,255,255,.5); border-radius:50%; background:var(--ab-red); content:''; }
    .ab-team-card__body { padding:23px 25px 27px; }
    .ab-team-card h3 { margin:0; color:var(--ab-navy); font-size:24px; line-height:1.2; font-weight:800; }
    .ab-team-card p { margin:10px 0 0; color:var(--ab-muted); font-size:14px; line-height:1.68; }
    .ab-gallery { overflow:hidden; background:var(--ab-navy); }
    .ab-gallery__head { display:flex; align-items:end; justify-content:space-between; gap:40px; }
    .ab-gallery .ab-kicker { color:#ff7379; }
    .ab-gallery .ab-title { color:#fff; }
    .ab-gallery .ab-lead { color:rgba(255,255,255,.68); }
    .ab-gallery__viewport { overflow-x:auto; margin:42px calc((100vw - min(1280px,100vw - 48px)) / -2) 0; padding:0 max(24px,calc((100vw - 1280px) / 2)) 14px; scroll-snap-type:x proximity; scrollbar-width:none; }
    .ab-gallery__viewport::-webkit-scrollbar { display:none; }
    .ab-gallery__grid { display:grid; width:max-content; grid-auto-flow:column; grid-auto-columns:310px; grid-template-rows:repeat(2,215px); gap:14px; }
    .ab-gallery-card { position:relative; overflow:hidden; border:1px solid rgba(255,255,255,.13); border-radius:22px; background:#20375e; scroll-snap-align:start; }
    .ab-gallery-card--tall { grid-row:span 2; }
    .ab-gallery-card img { display:block; width:100%; height:100%; object-fit:cover; transition:transform .35s ease; }
    .ab-gallery-card::after { position:absolute; inset:0; opacity:0; background:linear-gradient(180deg,transparent 55%,rgba(7,24,52,.55)); content:''; transition:opacity .25s ease; }
    .ab-gallery-card:hover img { transform:scale(1.035); }
    .ab-gallery-card:hover::after { opacity:1; }
    .ab-gallery__hint { display:flex; align-items:center; gap:9px; margin-top:20px; color:rgba(255,255,255,.62); font-size:12px; font-weight:700; }
    .ab-gallery__hint::before { width:38px; height:1px; background:rgba(255,255,255,.35); content:''; }
    .ab-services__grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:19px; margin-top:42px; }
    .ab-service { position:relative; min-height:280px; overflow:hidden; padding:29px; border:1px solid var(--ab-line); border-radius:24px; background:#fff; box-shadow:0 12px 34px rgba(14,33,69,.055); transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease; }
    .ab-service:hover { border-color:rgba(243,149,30,.55); box-shadow:0 20px 44px rgba(14,33,69,.11); transform:translateY(-4px); }
    .ab-service__icon { display:grid; width:50px; height:50px; place-items:center; border-radius:15px; background:#fdebed; color:var(--ab-red); }
    .ab-service__icon svg { width:25px; height:25px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; }
    .ab-service h3 { margin:42px 0 0; color:var(--ab-navy); font-size:20px; line-height:1.3; font-weight:800; }
    .ab-service p { margin:11px 0 0; color:var(--ab-muted); font-size:14px; line-height:1.68; }
    .ab-service__arrow { position:absolute; right:24px; bottom:23px; color:#cbd5e1; font-size:21px; transition:color .2s ease,transform .2s ease; }
    .ab-service:hover .ab-service__arrow { color:var(--ab-orange); transform:translateX(3px); }
    .ab-proof { position:relative; overflow:hidden; background:var(--ab-navy); }
    .ab-proof::before,.ab-proof::after { position:absolute; border:70px solid rgba(255,255,255,.035); border-radius:50%; content:''; }
    .ab-proof::before { top:-190px; left:-190px; width:440px; height:440px; }
    .ab-proof::after { right:-210px; bottom:-240px; width:520px; height:520px; }
    .ab-proof__layout { position:relative; z-index:1; display:grid; grid-template-columns:.75fr 1.25fr; align-items:center; gap:70px; }
    .ab-proof .ab-kicker { color:#ff7379; }
    .ab-proof .ab-title { color:#fff; }
    .ab-proof .ab-lead { color:rgba(255,255,255,.7); }
    .ab-proof__grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .ab-stat { min-height:178px; padding:25px; border:1px solid rgba(255,255,255,.14); border-radius:22px; background:rgba(255,255,255,.075); box-shadow:inset 0 1px 0 rgba(255,255,255,.07); }
    .ab-stat strong { display:block; color:#fff; font-size:36px; line-height:1; font-weight:800; letter-spacing:-.035em; }
    .ab-stat span { display:block; max-width:170px; margin-top:12px; color:rgba(255,255,255,.68); font-size:13px; line-height:1.5; }
    .ab-stat:nth-child(2) { background:var(--ab-red); }
    .ab-process { background:var(--ab-soft); }
    .ab-process__head { text-align:center; }
    .ab-process__head .ab-title,.ab-process__head .ab-lead { margin-right:auto; margin-left:auto; }
    .ab-process__track { position:relative; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; margin-top:47px; }
    .ab-process__track::before { position:absolute; top:31px; right:10%; left:10%; border-top:2px dashed #cfd9e5; content:''; }
    .ab-process-card { position:relative; z-index:1; padding:0 12px; text-align:center; }
    .ab-process-card__step { display:grid; width:64px; height:64px; margin-inline:auto; place-items:center; border:8px solid var(--ab-soft); border-radius:50%; background:var(--ab-red); color:#fff; font-size:13px; font-weight:800; box-shadow:0 0 0 1px #efc5c7; }
    .ab-process-card h3 { margin:21px 0 0; color:var(--ab-navy); font-size:18px; font-weight:800; }
    .ab-process-card p { margin:9px 0 0; color:var(--ab-muted); font-size:13px; line-height:1.65; }
    .ab-faq-cta { display:grid; grid-template-columns:1.05fr .95fr; align-items:start; gap:46px; }
    .ab-faq-list { display:grid; gap:12px; margin-top:32px; }
    .ab-faq { overflow:hidden; border:1px solid var(--ab-line); border-radius:18px; background:#fff; }
    .ab-faq summary { display:flex; min-height:72px; align-items:center; justify-content:space-between; gap:18px; padding:18px 21px; color:var(--ab-navy); font-size:15px; font-weight:800; cursor:pointer; list-style:none; }
    .ab-faq summary::-webkit-details-marker { display:none; }
    .ab-faq summary::after { display:grid; width:32px; height:32px; flex:0 0 32px; place-items:center; border-radius:10px; background:#fdebed; color:var(--ab-red); content:'+'; font-size:21px; }
    .ab-faq[open] summary::after { content:'−'; }
    .ab-faq p { margin:0; padding:0 21px 21px; color:var(--ab-muted); font-size:14px; line-height:1.7; }
    .ab-cta { position:relative; display:flex; min-height:520px; overflow:hidden; align-items:flex-end; padding:34px; border-radius:30px; background:var(--ab-navy); box-shadow:0 24px 55px rgba(14,33,69,.18); }
    .ab-cta__image { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center 28%; transition:transform .35s ease; }
    .ab-cta::after { position:absolute; inset:0; background:linear-gradient(180deg,rgba(7,24,52,.05) 12%,rgba(7,24,52,.3) 46%,rgba(7,24,52,.96) 100%); content:''; }
    .ab-cta:hover .ab-cta__image { transform:scale(1.035); }
    .ab-cta__content { position:relative; z-index:1; width:100%; padding-top:150px; }
    .ab-cta .ab-kicker { color:#fff; text-shadow:0 2px 8px rgba(0,0,0,.3); }
    .ab-cta h2 { max-width:520px; margin:14px 0 0; color:#fff; font-size:clamp(31px,3.4vw,46px); line-height:1.08; font-weight:800; letter-spacing:-.045em; text-wrap:balance; }
    .ab-cta p { max-width:520px; margin:15px 0 0; color:rgba(255,255,255,.82); font-size:15px; line-height:1.68; }
    .ab-cta .btn { margin-top:24px; background:#fff!important; color:var(--ab-navy)!important; }
    .ab-cta .btn:hover { background:var(--ab-orange)!important; color:#fff!important; }
    .ab-page h1,.ab-page h2,.ab-page h3,.ab-page h4,.ab-page h5,.ab-page h6 { font-family:'Plus Jakarta Sans','main-font-family',sans-serif; letter-spacing:0; }
    @media (max-width:991px) { .ab-hero { padding-top:106px; } .ab-hero__shell { min-height:500px; gap:31px; padding:47px; } .ab-story { gap:45px; } .ab-story__media { min-height:480px; } .ab-purpose__grid { grid-template-columns:1fr; } .ab-purpose-card,.ab-purpose-card--mission,.ab-purpose-card--promise { min-height:250px; grid-column:auto; } .ab-team-card { width:calc((100vw - 70px) / 2); flex-basis:calc((100vw - 70px) / 2); } .ab-gallery__head { align-items:flex-start; flex-direction:column; gap:16px; } .ab-services__grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .ab-proof__layout { grid-template-columns:1fr; gap:44px; } .ab-faq-cta { grid-template-columns:1fr; } }
    @media (max-width:767px) {
        .about-page #appHeaderArea { display:none; }
        .ab-page { padding-bottom:calc(82px + env(safe-area-inset-bottom)); } .ab-wrap { width:min(100% - 28px,620px); } .ab-section { padding:62px 0; } .ab-hero { padding-top:82px; } .ab-hero__shell { grid-template-columns:1fr; gap:29px; min-height:0; padding:30px 23px; border-radius:26px; } .ab-hero__ring { top:-110px; right:-138px; width:390px; height:390px; border-width:54px; } .ab-hero h1 { font-size:41px; } .ab-hero__lead { font-size:15px; } .ab-hero__actions { display:grid; grid-template-columns:1fr; } .ab-hero__actions .btn,.ab-button-secondary { width:100%; } .ab-hero__image,.ab-hero__image img { min-height:290px; height:290px; } .ab-hero__badge { right:13px; bottom:14px; left:13px; width:auto; } .ab-page-header { margin:0 10px; padding:20px; border-radius:0 0 21px 21px; } .ab-page-header__inner { align-items:flex-start; flex-direction:column; gap:17px; } .ab-page-header__identity { align-items:flex-start; } .ab-title { font-size:34px; } .ab-lead { font-size:15px; } .ab-story { grid-template-columns:1fr; gap:39px; } .ab-story__media { min-height:410px; } .ab-story__main { inset:0 46px 50px 0; border-radius:24px; } .ab-story__seal { top:17px; right:10px; width:88px; height:88px; } .ab-story__seal strong { font-size:20px; } .ab-purpose__head,.ab-services__head { align-items:flex-start; flex-direction:column; gap:16px; } .ab-purpose__grid { gap:14px; margin-top:30px; } .ab-purpose-card { min-height:250px; padding:25px; border-radius:22px; }
        .ab-kicker { max-width:100%; flex-wrap:wrap; line-height:1.5; }
        .ab-team__head { align-items:flex-start; flex-direction:column; gap:16px; } .ab-team__controls { align-self:flex-end; } .ab-team__viewport { margin-right:-14px; margin-left:-14px; padding:0 14px 9px; } .ab-team__grid,.ab-services__grid,.ab-proof__grid,.ab-process__track { display:flex; gap:14px; } .ab-services__grid,.ab-proof__grid,.ab-process__track { overflow-x:auto; margin-right:-14px; margin-left:-14px; padding:0 14px 9px; scroll-snap-type:x mandatory; scrollbar-width:none; } .ab-services__grid::-webkit-scrollbar,.ab-proof__grid::-webkit-scrollbar,.ab-process__track::-webkit-scrollbar { display:none; } .ab-team-card,.ab-team-card:first-child { width:82vw; min-width:0; flex:0 0 82vw; grid-column:auto; scroll-snap-align:start; } .ab-team-card:first-child .ab-team-card__media,.ab-team-card__media { aspect-ratio:4 / 4.35; } .ab-gallery__viewport { margin-right:-14px; margin-left:-14px; padding-right:14px; padding-left:14px; scroll-snap-type:x mandatory; } .ab-gallery__grid { display:flex; gap:14px; } .ab-gallery-card,.ab-gallery-card--tall { width:82vw; height:300px; flex:0 0 82vw; grid-row:auto; } .ab-service { min-height:286px; flex:0 0 84%; padding:25px; scroll-snap-align:start; } .ab-stat { min-height:165px; flex:0 0 76%; scroll-snap-align:start; } .ab-process__track::before { display:none; } .ab-process-card { flex:0 0 79%; padding:24px; border:1px solid var(--ab-line); border-radius:21px; background:#fff; text-align:left; scroll-snap-align:start; } .ab-process-card__step { margin:0; border-color:#fff; } .ab-cta { min-height:480px; padding:25px; border-radius:24px; } .ab-cta__content { padding-top:125px; }
    }
    @media (prefers-reduced-motion:reduce) { .ab-page *,.ab-page *::before,.ab-page *::after { scroll-behavior:auto!important; transition-duration:.01ms!important; } }
</style>

<main class="ab-page">
    <section class="ab-hero"><div class="ab-wrap"><div class="ab-hero__shell"><div class="ab-hero__ring" aria-hidden="true"></div><div class="ab-hero__copy"><nav class="ab-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><span>About us</span></nav><span class="ab-kicker">{{ $cms['hero_eyebrow'] ?? 'Global Education. Personal Guidance.' }}</span><h1>{{ $cms['hero_title'] ?? 'A trusted Indore team for your international education journey.' }}</h1><p class="ab-hero__lead">{{ $cms['hero_copy'] ?? 'Trans Globe Indore, managed by Global Education and Immigration Consultants, turns complex study-abroad decisions into a clear, supported plan built around your goals.' }}</p><div class="ab-hero__actions"><a href="{{ url($cms['hero_primary_cta_url'] ?? '/contact#enquiry') }}" class="btn-flip-effect btn btn-primary btn-lg gap-8 text-white" data-text="{{ $cms['hero_primary_cta_label'] ?? 'Book Free Counselling' }}"><span class="btn-flip-effect__text text-white">{{ $cms['hero_primary_cta_label'] ?? 'Book Free Counselling' }}</span></a><a href="{{ url($cms['hero_secondary_cta_url'] ?? '/services') }}" class="ab-button-secondary">{{ $cms['hero_secondary_cta_label'] ?? 'Explore Our Services' }} <span aria-hidden="true">→</span></a></div></div><div class="ab-hero__visual"><figure class="ab-hero__image"><img src="{{ asset($cms['hero_image'] ?? 'assets/transglobe/services/services-team.avif') }}" alt="{{ $cms['hero_image_alt'] ?? 'GEIC Indore education counsellors supporting international students' }}" width="768" height="768" fetchpriority="high"></figure><div class="ab-hero__badge"><span class="ab-hero__badge-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span><span><strong>Profile-first counselling</strong><span>Advice shaped around your goals, not a fixed shortlist</span></span></div></div></div><div class="ab-page-header"><div class="ab-page-header__inner"><div class="ab-page-header__identity"><span class="ab-page-header__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span><h2>{{ $cms['page_header_title'] ?? 'About Trans Globe Indore' }}</h2><p>{{ $cms['page_header_copy'] ?? 'Education choices are personal. Our role is to make every option, requirement and next step easier to understand.' }}</p></span></div><a class="ab-link" href="#our-story">Read our story <span aria-hidden="true">↓</span></a></div></div></div></section>

    <section class="ab-section" id="our-story"><div class="ab-wrap ab-story"><div class="ab-story__media"><figure class="ab-story__main"><img src="{{ asset($cms['story_image'] ?? 'assets/transglobe/about/international-business-award-2023.jpeg') }}" alt="{{ $cms['story_image_alt'] ?? 'Trans Globe Indore representatives receiving the 2023 International Business Award for Best Abroad Education Consultant in Central India' }}" width="1200" height="1200" loading="lazy"></figure><figure class="ab-story__aside"><img src="{{ asset('assets/transglobe/destinations/australia/campus-students.jpg') }}" alt="International students learning together on campus" width="1200" height="800" loading="lazy"></figure><div class="ab-story__seal"><span><strong>1992</strong><span>Guiding students since</span></span></div></div><div class="ab-story__copy"><span class="ab-kicker">{{ $cms['story_eyebrow'] ?? 'Welcome to Global Education' }}</span><h2 class="ab-title">{{ $cms['story_title'] ?? 'Big ambitions deserve informed decisions.' }}</h2><p>{{ $cms['story_copy'] ?? 'Choosing education in India or overseas is a major life decision. Our counsellors bring together accurate information, thoughtful profile assessment and practical support so students and families can move forward with confidence.' }}</p><p>{{ $cms['story_copy_2'] ?? 'For us, studying abroad is more than earning a degree. It is a chance to broaden perspective, develop independence and build skills and experiences that can change the direction of a life.' }}</p><a class="ab-link" href="{{ url('/services') }}">See how we support students <span aria-hidden="true">→</span></a></div></div></section>

    <section class="ab-section ab-purpose"><div class="ab-wrap"><div class="ab-purpose__head"><div><span class="ab-kicker">Our foundation</span><h2 class="ab-title">Knowledge, clarity and <em>student-first guidance.</em></h2></div><p class="ab-lead">Our work connects accurate destination knowledge with a personal understanding of the student behind every application.</p></div><div class="ab-purpose__grid">@foreach([['01',$cms['who_title'] ?? 'Who we are',$cms['who_copy'] ?? 'We are experienced study-abroad education consultants who help students understand universities, programs, admission requirements, scholarships, visas and the cultural realities of studying in another country.',''],['02',$cms['mission_title'] ?? 'Our mission',$cms['mission_copy'] ?? 'Our mission is to make studying abroad simpler, more transparent and less stressful—from choosing a university and preparing applications to test planning, visa documentation and departure.','ab-purpose-card--mission'],['03',$cms['promise_title'] ?? 'Our promise',$cms['promise_copy'] ?? 'We listen before we recommend. Every plan is shaped by the student’s academic profile, career direction, finances, preferred destination and readiness—not by a one-size-fits-all shortlist.','ab-purpose-card--promise']] as [$number,$title,$copy,$class])<article class="ab-purpose-card {{ $class }}"><span class="ab-purpose-card__number">{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div></div></section>

    <section class="ab-section ab-team" id="our-team"><div class="ab-wrap"><div class="ab-team__head"><div class="ab-team__head-copy"><span class="ab-kicker">{{ $cms['team_eyebrow'] ?? 'Professional people' }}</span><h2 class="ab-title">{{ $cms['team_title'] ?? 'Meet our expert education consultants.' }}</h2><p class="ab-lead">{{ $cms['team_copy'] ?? 'Meet the people who bring experience, careful listening and practical study-abroad guidance to every student conversation.' }}</p></div><div class="ab-team__controls" aria-label="Team slider controls"><button class="ab-team__control" type="button" data-team-previous aria-label="Show previous team member"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button><button class="ab-team__control" type="button" data-team-pause aria-label="Pause team slider" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5v14M15 5v14"/></svg></button><button class="ab-team__control" type="button" data-team-next aria-label="Show next team member"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button></div></div>@php
        $aboutTeam = [[$cms['team_one_name'] ?? 'Johar Ali',$cms['team_one_role'] ?? 'Leadership team',$cms['team_one_bio'] ?? '',$cms['team_one_image'] ?? 'assets/transglobe/about/johar-ali.webp',$cms['team_one_image_alt'] ?? 'Johar Ali from the Trans Globe Indore leadership team'],[$cms['team_two_name'] ?? 'Ali',$cms['team_two_role'] ?? 'Student counsellor',$cms['team_two_bio'] ?? '',$cms['team_two_image'] ?? 'assets/transglobe/about/ali.webp',$cms['team_two_image_alt'] ?? 'Ali, student counsellor at Trans Globe Indore'],[$cms['team_three_name'] ?? 'Husain',$cms['team_three_role'] ?? 'Student counsellor',$cms['team_three_bio'] ?? '',$cms['team_three_image'] ?? 'assets/transglobe/about/husain.webp',$cms['team_three_image_alt'] ?? 'Husain, student counsellor at Trans Globe Indore']];
        $aboutTeamLoop = array_merge($aboutTeam, $aboutTeam);
    @endphp<div class="ab-team__viewport" data-team-slider aria-label="Our education consultants"><div class="ab-team__grid">@foreach($aboutTeamLoop as [$name,$role,$bio,$image,$alt])<article class="ab-team-card" @if($loop->iteration > count($aboutTeam)) aria-hidden="true" @endif><figure class="ab-team-card__media"><img src="{{ asset($image) }}" alt="{{ $loop->iteration > count($aboutTeam) ? '' : $alt }}" width="1122" height="1402" loading="lazy"><span class="ab-team-card__role">{{ $role }}</span></figure><div class="ab-team-card__body"><h3>{{ $name }}</h3>@if(filled($bio))<p>{{ $bio }}</p>@endif</div></article>@endforeach</div></div></div></section>

    @php
        $galleryDefaults = [
            ['assets/transglobe/about/gallery/counselling-event-01.jpg', 'Students receiving one-to-one education guidance at a Trans Globe Indore counselling event'],
            ['assets/transglobe/about/gallery/counselling-event-02.jpg', 'A family speaking with an international university representative at an education event'],
            ['assets/transglobe/about/gallery/counselling-event-03.jpg', 'Students comparing international study options with university representatives'],
            ['assets/transglobe/about/gallery/counselling-event-04.jpg', 'Students and families discussing overseas university pathways'],
            ['assets/transglobe/about/gallery/counselling-event-05.jpg', 'Trans Globe Indore event registration and student support desk'],
            ['assets/transglobe/about/gallery/counselling-event-06.jpg', 'A Trans Globe Indore counsellor reviewing IELTS preparation with a student'],
            ['assets/transglobe/about/gallery/counselling-event-07.jpg', 'University of Suffolk representative counselling prospective students'],
            ['assets/transglobe/about/gallery/counselling-event-08.jpg', 'International education adviser meeting a prospective student'],
            ['assets/transglobe/about/gallery/counselling-event-09.jpg', 'University representatives explaining study options at the Trans Globe Indore event'],
            ['assets/transglobe/about/gallery/counselling-event-10.jpg', 'Students checking in at a Trans Globe Indore education fair'],
            ['assets/transglobe/about/gallery/counselling-event-11.jpg', 'A student discussing application information with an education adviser'],
            ['assets/transglobe/about/gallery/counselling-event-12.jpg', 'A family exploring international study opportunities with a university representative'],
            ['assets/transglobe/about/gallery/counselling-event-13.jpg', 'Students and families attending individual international education meetings'],
            ['assets/transglobe/about/gallery/counselling-event-14.jpg', 'GBS Malta representative discussing courses with prospective students'],
            ['assets/transglobe/about/gallery/counselling-event-15.jpg', 'Trans Globe Indore counsellors guiding students during an education event'],
            ['assets/transglobe/about/gallery/award-event-01.jpg', 'Trans Globe Indore representatives at the International Business Awards'],
            ['assets/transglobe/about/gallery/award-event-02.jpg', 'Trans Globe Indore representative greeting a guest at the International Business Awards'],
            ['assets/transglobe/about/gallery/award-event-03.jpg', 'Trans Globe Indore representatives on the International Business Awards red carpet'],
            ['assets/transglobe/about/gallery/award-event-04.jpg', 'Trans Globe Indore receiving the 2023 International Business Award for Best Abroad Education Consultant in Central India'],
        ];
        $aboutGallery = collect($galleryDefaults)->map(function (array $item, int $index) use ($cms): array {
            $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

            return [
                $cms['gallery_image_'.$number] ?? $item[0],
                $cms['gallery_alt_'.$number] ?? $item[1],
            ];
        });
    @endphp
    <section class="ab-section ab-gallery" id="gallery"><div class="ab-wrap"><div class="ab-gallery__head"><div><span class="ab-kicker">{{ $cms['gallery_eyebrow'] ?? 'Guidance in action' }}</span><h2 class="ab-title">{{ $cms['gallery_title'] ?? 'Real conversations. Practical next steps.' }}</h2></div><p class="ab-lead">{{ $cms['gallery_copy'] ?? 'A closer look at Trans Globe Indore counsellors, students and international university representatives connecting through personal guidance and education events.' }}</p></div><div class="ab-gallery__viewport" aria-label="Trans Globe Indore event photo gallery"><div class="ab-gallery__grid">@foreach($aboutGallery as [$image,$alt])<figure class="ab-gallery-card {{ in_array($loop->iteration, [1, 6, 11, 16], true) ? 'ab-gallery-card--tall' : '' }}"><img src="{{ asset($image) }}" alt="{{ $alt }}" width="1200" height="900" loading="lazy" decoding="async"></figure>@endforeach</div></div><p class="ab-gallery__hint">Scroll to explore the gallery</p></div></section>

    <section class="ab-section" id="what-we-do"><div class="ab-wrap"><div class="ab-services__head"><div><span class="ab-kicker">{{ $cms['services_eyebrow'] ?? 'What we do' }}</span><h2 class="ab-title">{{ $cms['services_title'] ?? 'One connected team for every important step.' }}</h2></div><p class="ab-lead">{{ $cms['services_copy'] ?? 'From the first question to arrival overseas, our specialists coordinate the details that make a strong international education plan possible.' }}</p></div>@php
        $aboutServices = [[$cms['service_one_title'] ?? 'University & program selection',$cms['service_one_copy'] ?? 'Compare suitable institutions and courses against your academic background, career goals, budget and preferred student experience.','<path d="M3 10.5 12 5l9 5.5-9 5.5-9-5.5Z"/><path d="M7 13v4.5c2.9 2 7.1 2 10 0V13M21 10.5V16"/>'],[$cms['service_two_title'] ?? 'Applications & documentation',$cms['service_two_copy'] ?? 'Prepare complete applications, supporting documents, statements and timelines with detailed checks before submission.','<path d="M6 3h9l3 3v15H6V3Z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>'],[$cms['service_three_title'] ?? 'Test preparation',$cms['service_three_copy'] ?? 'Build a practical score plan for IELTS, PTE, TOEFL, GRE, GMAT or SAT with focused training and realistic practice.','<path d="M4 5h16v14H4V5Z"/><path d="M8 9h8M8 13h5M8 17h3"/>'],[$cms['service_four_title'] ?? 'Scholarships & funding',$cms['service_four_copy'] ?? 'Identify relevant awards, understand eligibility and present your academic and financial information clearly.','<circle cx="12" cy="8" r="5"/><path d="m9 12-2 9 5-3 5 3-2-9"/>'],[$cms['service_five_title'] ?? 'Visa & immigration support',$cms['service_five_copy'] ?? 'Organise evidence, financial documents, forms and interview preparation for a consistent student-visa application.','<rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M7.5 18c1.3-2.4 7.7-2.4 9 0"/>'],[$cms['service_six_title'] ?? 'Pre-departure & ongoing support',$cms['service_six_copy'] ?? 'Prepare for travel, accommodation, banking, arrival and the practical realities of beginning student life abroad.','<path d="m3 11 18-7-7 18-3-8-8-3Z"/><path d="m11 14 4-4"/>']];
    @endphp<div class="ab-services__grid">@foreach($aboutServices as [$title,$copy,$icon])<article class="ab-service"><span class="ab-service__icon" aria-hidden="true"><svg viewBox="0 0 24 24">{!! $icon !!}</svg></span><h3>{{ $title }}</h3><p>{{ $copy }}</p><span class="ab-service__arrow" aria-hidden="true">→</span></article>@endforeach</div></div></section>

    <section class="ab-section ab-proof"><div class="ab-wrap ab-proof__layout"><div><span class="ab-kicker">{{ $cms['proof_eyebrow'] ?? 'Experience you can measure' }}</span><h2 class="ab-title">{{ $cms['proof_title'] ?? 'A global network, grounded in Indore.' }}</h2><p class="ab-lead">{{ $cms['proof_copy'] ?? 'Students receive local, accessible support backed by the reach and experience of the wider Trans Globe network.' }}</p></div><div class="ab-proof__grid">@foreach([[$cms['proof_students_value'] ?? '70,250+',$cms['proof_students_label'] ?? 'students placed worldwide'],[$cms['proof_universities_value'] ?? '800+',$cms['proof_universities_label'] ?? 'partner universities'],[$cms['proof_visas_value'] ?? '98.7%',$cms['proof_visas_label'] ?? 'reported visa success rate'],[$cms['proof_years_value'] ?? '32+ yrs',$cms['proof_years_label'] ?? 'of international education expertise']] as [$value,$label])<article class="ab-stat"><strong>{{ $value }}</strong><span>{{ $label }}</span></article>@endforeach</div></div></section>

    <section class="ab-section ab-process"><div class="ab-wrap"><div class="ab-process__head"><span class="ab-kicker">{{ $cms['process_eyebrow'] ?? 'How we work' }}</span><h2 class="ab-title">{{ $cms['process_title'] ?? 'Clear guidance, connected from start to finish.' }}</h2><p class="ab-lead">One plan, shared across the specialists supporting your admission, funding, visa and departure.</p></div><div class="ab-process__track">@foreach([['01',$cms['process_one_title'] ?? 'Listen & assess',$cms['process_one_copy'] ?? 'We understand your profile, ambitions, concerns and non-negotiables before suggesting a direction.'],['02',$cms['process_two_title'] ?? 'Compare & plan',$cms['process_two_copy'] ?? 'Together we compare destinations, universities, courses, costs, scholarships and timelines.'],['03',$cms['process_three_title'] ?? 'Prepare & apply',$cms['process_three_copy'] ?? 'Our specialists coordinate applications, supporting documents, test plans and visa preparation.'],['04',$cms['process_four_title'] ?? 'Depart with confidence',$cms['process_four_copy'] ?? 'You receive practical pre-departure guidance and support for a confident transition into student life.']] as [$step,$title,$copy])<article class="ab-process-card"><span class="ab-process-card__step">{{ $step }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div></div></section>

    <section class="ab-section"><div class="ab-wrap ab-faq-cta"><div><span class="ab-kicker">{{ $cms['faq_eyebrow'] ?? 'Questions students ask' }}</span><h2 class="ab-title">{{ $cms['faq_title'] ?? 'What to know before choosing a consultant.' }}</h2><div class="ab-faq-list">@foreach([[$cms['faq_one_question'] ?? 'How much do study-abroad consultants charge?',$cms['faq_one_answer'] ?? 'Fees vary with the services and destination. Trans Globe Indore offers a free initial counselling conversation so you can understand your options and exactly what support is included before making a commitment.'],[$cms['faq_two_question'] ?? 'Can I work with GEIC if I am not in Indore?',$cms['faq_two_answer'] ?? 'Yes. Our online counselling process gives students outside Indore access to the same profile review, document support and destination specialists as an in-office appointment.'],[$cms['faq_three_question'] ?? 'Why is a study-abroad consultant useful?',$cms['faq_three_answer'] ?? 'A good consultant connects admissions, scholarships, tests, visas and practical preparation into one plan. That reduces missed requirements and helps you make decisions using current, destination-specific information.']] as $index => [$question,$answer])<details class="ab-faq" @if($index === 0) open @endif><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>@endforeach</div></div><aside class="ab-cta"><img class="ab-cta__image" src="{{ asset($cms['cta_image'] ?? 'assets/transglobe/about/student-guidance-session-2023.jpg') }}" alt="{{ $cms['cta_image_alt'] ?? 'A Trans Globe Indore counsellor speaking with a student during a guidance session' }}" width="900" height="1200" loading="lazy"><div class="ab-cta__content"><span class="ab-kicker">{{ $cms['cta_eyebrow'] ?? 'Start your journey' }}</span><h2>{{ $cms['cta_title'] ?? 'A powerful collaboration for a prosperous tomorrow.' }}</h2><p>{{ $cms['cta_copy'] ?? 'Bring us your questions, your goals and your current profile. We will help you understand the strongest next step.' }}</p><a href="{{ url($cms['cta_url'] ?? '/contact#enquiry') }}" class="btn-flip-effect btn btn-lg gap-8" data-text="{{ $cms['cta_label'] ?? 'Speak to Our Indore Counsellor' }}"><span class="btn-flip-effect__text">{{ $cms['cta_label'] ?? 'Speak to Our Indore Counsellor' }}</span></a></div></aside></div></section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slider = document.querySelector('[data-team-slider]');
    const previous = document.querySelector('[data-team-previous]');
    const next = document.querySelector('[data-team-next]');
    const pause = document.querySelector('[data-team-pause]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!slider || !previous || !next || !pause) return;

    const cards = Array.from(slider.querySelectorAll('.ab-team-card'));
    const uniqueCount = cards.length / 2;
    let timer = null;
    let userPaused = reducedMotion.matches;
    let interactionPaused = false;

    function stepWidth() {
        if (cards.length < 2) return slider.clientWidth;

        return cards[1].offsetLeft - cards[0].offsetLeft;
    }

    function loopBoundary() {
        return cards[uniqueCount]?.offsetLeft ?? slider.scrollWidth / 2;
    }

    function normalizeLoop() {
        const boundary = loopBoundary();
        if (slider.scrollLeft >= boundary - 2) slider.scrollLeft -= boundary;
    }

    function move(direction) {
        const boundary = loopBoundary();
        if (direction < 0 && slider.scrollLeft <= 2) slider.scrollLeft = boundary;
        normalizeLoop();
        slider.scrollBy({ left: stepWidth() * direction, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
        window.setTimeout(normalizeLoop, 650);
    }

    function stopTimer() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }

    function startTimer() {
        stopTimer();
        if (userPaused || interactionPaused || reducedMotion.matches || document.hidden) return;
        timer = window.setInterval(function () { move(1); }, 3800);
    }

    function updatePauseControl() {
        pause.setAttribute('aria-pressed', String(userPaused));
        pause.setAttribute('aria-label', userPaused ? 'Play team slider' : 'Pause team slider');
        pause.innerHTML = userPaused
            ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7V5Z"/></svg>'
            : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5v14M15 5v14"/></svg>';
    }

    previous.addEventListener('click', function () { move(-1); startTimer(); });
    next.addEventListener('click', function () { move(1); startTimer(); });
    pause.addEventListener('click', function () {
        userPaused = !userPaused;
        updatePauseControl();
        startTimer();
    });
    slider.addEventListener('pointerenter', function () { interactionPaused = true; startTimer(); });
    slider.addEventListener('pointerleave', function () { interactionPaused = false; startTimer(); });
    slider.addEventListener('focusin', function () { interactionPaused = true; startTimer(); });
    slider.addEventListener('focusout', function () { interactionPaused = false; startTimer(); });
    slider.addEventListener('pointerdown', function () { interactionPaused = true; startTimer(); }, { passive: true });
    slider.addEventListener('pointerup', function () { interactionPaused = false; startTimer(); }, { passive: true });
    document.addEventListener('visibilitychange', startTimer);
    reducedMotion.addEventListener('change', function (event) {
        if (event.matches) userPaused = true;
        updatePauseControl();
        startTimer();
    });

    updatePauseControl();
    startTimer();
});
</script>

@include('mirror.partials.footer', ['siteCms' => $cms])
