@php
    $mobileBackHref = $mobileBackHref ?? null;
    $mobileBackLabel = $mobileBackLabel ?? 'Back';
@endphp

<style>
    .tg-page-mobile-bar,
    .tg-page-mobile-drawer,
    .tg-page-mobile-backdrop,
    .tg-page-mobile-bottom { display: none; }

    @media (max-width: 767px), (max-width: 991px) and (max-height: 500px) {
body.contact-page,
body.terms-page,
body.destination-page,
        body.country-detail-page,
        body.services-page,
        body.service-detail-page,
        body.events-page,
        body.event-detail-page,
        body.scholarships-page,
        body.scholarship-detail-page,
        body.tests-page,
        body.test-detail-page,
        body.planning-tool-page,
        body.blog-page,
        body.blog-detail-page { padding-top: 72px; padding-bottom: calc(92px + env(safe-area-inset-bottom)); background: #f3f6f9; }
body.contact-page #appHeaderArea,
body.terms-page #appHeaderArea,
body.destination-page #appHeaderArea,
        body.country-detail-page #appHeaderArea,
        body.services-page #appHeaderArea,
        body.service-detail-page #appHeaderArea,
        body.events-page #appHeaderArea,
        body.event-detail-page #appHeaderArea,
        body.scholarships-page #appHeaderArea,
        body.scholarship-detail-page #appHeaderArea,
        body.tests-page #appHeaderArea,
        body.test-detail-page #appHeaderArea,
        body.planning-tool-page #appHeaderArea,
        body.blog-page #appHeaderArea,
        body.blog-detail-page #appHeaderArea { display: none !important; }
        body.tg-page-menu-open { overflow: hidden; }

        .tg-page-mobile-bar { position: fixed; z-index: 1100; top: 0; right: 0; left: 0; display: grid; grid-template-columns: 48px minmax(0,1fr) 48px; align-items: center; gap: 8px; min-height: 72px; padding: 10px 14px; border-bottom: 1px solid rgba(14,33,69,.08); background: rgba(255,255,255,.97); box-shadow: 0 8px 24px rgba(14,33,69,.08); backdrop-filter: blur(18px); }
        .tg-page-mobile-bar__brand { display: flex; align-items: center; justify-content: {{ $mobileBackHref ? 'center' : 'flex-start' }}; min-width: 0; height: 48px; }
        .tg-page-mobile-bar__brand img { display: block; width: auto !important; height: auto !important; max-width: {{ $mobileBackHref ? '176px' : '210px' }}; max-height: 43px; object-fit: contain; }
        .tg-page-mobile-back,
        .tg-page-mobile-menu,
        .tg-page-mobile-drawer__close { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border: 0; border-radius: 50%; background: #f4f6f9; color: #0e2145; }
        .tg-page-mobile-back svg { width: 23px; height: 23px; fill: none; stroke: currentColor; }
        .tg-page-mobile-bar__spacer { width: 48px; height: 48px; }
        .tg-page-mobile-menu { flex-direction: column; gap: 5px; }
        .tg-page-mobile-menu span { width: 20px; height: 2px; border-radius: 3px; background: currentColor; transition: transform .24s cubic-bezier(.2,0,0,1), opacity .16s ease; }
        .tg-page-mobile-menu[aria-expanded="true"] span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .tg-page-mobile-menu[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .tg-page-mobile-menu[aria-expanded="true"] span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .tg-page-mobile-backdrop { position: fixed; z-index: 1110; inset: 0; display: block; background: rgba(5,17,39,.48); opacity: 0; pointer-events: none; transition: opacity .24s ease; }
        .tg-page-mobile-drawer { position: fixed; z-index: 1120; top: 0; right: 0; bottom: 0; display: none; width: min(88vw, 370px); padding: 20px 18px calc(22px + env(safe-area-inset-bottom)); flex-direction: column; background: #fff; box-shadow: -20px 0 55px rgba(5,17,39,.2); transform: translateX(105%); transition: transform .3s cubic-bezier(.2,0,0,1); }
        body.tg-page-menu-open .tg-page-mobile-backdrop { opacity: 1; pointer-events: auto; }
        body.tg-page-menu-open .tg-page-mobile-drawer { display: flex; transform: translateX(0); }
        .tg-page-mobile-drawer__head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding-bottom: 16px; border-bottom: 1px solid #edf1f5; }
        .tg-page-mobile-drawer__eyebrow { color: #E31E24; font-size: 10px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .tg-page-mobile-drawer__head strong { display: block; margin-top: 3px; color: #0e2145; font-size: 18px; }
        .tg-page-mobile-drawer nav { display: grid; gap: 7px; margin-top: 16px; }
        .tg-page-mobile-drawer nav a { display: flex; align-items: center; justify-content: space-between; min-height: 54px; padding: 10px 14px; border-radius: 16px; background: #f7f8fb; color: #0e2145; font-size: 14px; font-weight: 700; }
        .tg-page-mobile-drawer nav a::after { content: '›'; color: #E31E24; font-size: 22px; font-weight: 400; }
        .tg-page-mobile-drawer__cta { display: inline-flex; align-items: center; justify-content: center; min-height: 54px; margin-top: auto; border-radius: 17px; background: #E31E24; color: #fff !important; font-size: 14px; font-weight: 700; box-shadow: 0 12px 28px rgba(227,30,36,.24); }

        .tg-page-mobile-bottom { position: fixed; z-index: 1100; right: 10px; bottom: calc(8px + env(safe-area-inset-bottom)); left: 10px; display: grid; grid-template-columns: repeat(5,minmax(0,1fr)); min-height: 72px; padding: 7px 6px 6px; border: 1px solid rgba(14,33,69,.1); border-radius: 24px; background: rgba(255,255,255,.97); box-shadow: 0 15px 42px rgba(5,17,39,.18); backdrop-filter: blur(18px); }
        .tg-page-mobile-bottom a { position: relative; display: flex; align-items: center; justify-content: center; min-width: 0; min-height: 58px; flex-direction: column; gap: 4px; border-radius: 17px; color: #7e8b9d; }
        .tg-page-mobile-bottom svg { width: 22px; height: 22px; fill: currentColor; }
        .tg-page-mobile-bottom span { font-size: 10px; font-weight: 700; }
        .tg-page-mobile-bottom a::before { content: ''; position: absolute; top: 3px; left: 50%; width: 18px; height: 3px; border-radius: 4px; background: #F3951E; opacity: 0; transform: translateX(-50%) scaleX(.3); }
        .tg-page-mobile-bottom a.is-active { color: #E31E24; background: rgba(227,30,36,.07); }
        .tg-page-mobile-bottom a.is-active::before { opacity: 1; transform: translateX(-50%) scaleX(1); }
        .tg-page-mobile-bottom__action { color: #fff !important; background: #E31E24 !important; }
        .tg-page-mobile-bottom__action::before { background: #F3951E !important; }

        body.destination-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > img,
        body.country-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > img,
        body.services-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > img,
        body.service-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > img { width: 230px !important; max-width: 62vw !important; margin-bottom: 16px !important; }
        body.destination-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center,
        body.country-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center,
        body.services-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center,
        body.service-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > .d-inline-flex-center { gap: 6px !important; max-width: 100%; padding: 8px 12px !important; border-width: 1px !important; font-size: 12px; line-height: 1.35; }
        body.destination-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > h3,
        body.country-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > h3,
        body.services-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > h3,
        body.service-detail-page #appFooterArea .theme-footer-1__section > .position-relative.z-index-2 > .container > .row > .col-12.col-lg-5 > h3 { margin-top: 12px !important; font-size: 28px !important; line-height: 1.2 !important; }
    }

    @media (prefers-reduced-motion: reduce) {
        .tg-page-mobile-menu span,
        .tg-page-mobile-backdrop,
        .tg-page-mobile-drawer { transition: none; }
    }
</style>

<header class="tg-page-mobile-bar" aria-label="Mobile application header">
    @if($mobileBackHref)
        <a href="{{ $mobileBackHref }}" class="tg-page-mobile-back" aria-label="{{ $mobileBackLabel }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    @else
        <span class="tg-page-mobile-bar__spacer" aria-hidden="true"></span>
    @endif
    <a href="{{ url('/') }}" class="tg-page-mobile-bar__brand" aria-label="Trans Globe Indore home">
        <img src="assets/transglobe/trans-globe-logo.png" alt="Trans Globe Indore managed by GEIC">
    </a>
    <button type="button" class="tg-page-mobile-menu" aria-label="Open navigation menu" aria-controls="tgPageMobileDrawer" aria-expanded="false"><span></span><span></span><span></span></button>
</header>

<button type="button" class="tg-page-mobile-backdrop" data-page-menu-close aria-label="Close navigation menu"></button>
<aside id="tgPageMobileDrawer" class="tg-page-mobile-drawer" aria-hidden="true">
    <div class="tg-page-mobile-drawer__head">
        <div><span class="tg-page-mobile-drawer__eyebrow">Explore</span><strong>Trans Globe Indore</strong></div>
        <button type="button" class="tg-page-mobile-drawer__close" data-page-menu-close aria-label="Close navigation menu">×</button>
    </div>
    <nav aria-label="Mobile menu">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/destinations') }}">Study destinations</a>
        <a href="{{ url('/services') }}">Our services</a>
        <a href="{{ url('/events') }}">Events</a>
        <a href="{{ url('/scholarships') }}">Scholarships</a>
        <a href="{{ url('/tests') }}">Test preparation</a>
        <a href="{{ url('/blog') }}">Blog</a>
        <a href="{{ url('/#work-visas') }}">Work visa pathways</a>
    </nav>
    <a href="{{ url('/contact#enquiry') }}" class="tg-page-mobile-drawer__cta">Book free counselling</a>
</aside>

<nav class="tg-page-mobile-bottom" aria-label="Primary mobile navigation">
    <a href="{{ url('/') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3V10.5Z"/></svg><span>Home</span></a>
    <a href="{{ url('/destinations') }}" class="{{ ($mirrorPage ?? '') === 'destinations' ? 'is-active' : '' }}" @if(($mirrorPage ?? '') === 'destinations') aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-13a7 7 0 1 0-14 0c0 6.8 7 13 7 13Zm0-10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg><span>Explore</span></a>
    <a href="{{ url('/services') }}" class="{{ str_starts_with(($mirrorPage ?? ''), 'services') ? 'is-active' : '' }}" @if(str_starts_with(($mirrorPage ?? ''), 'services')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg><span>Services</span></a>
    <a href="{{ url('/#reviews') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8 14.8 8l5.8.8-4.2 4.1 1 5.8-5.4-2.8-5.4 2.8 1-5.8-4.2-4.1L9.2 8 12 2.8Z"/></svg><span>Reviews</span></a>
    <a href="{{ url('/contact#enquiry') }}" class="tg-page-mobile-bottom__action"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v12H8l-4 4V4Zm4 4v2h8V8H8Zm0 4v2h5v-2H8Z"/></svg><span>Consult</span></a>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.matchMedia('(max-width: 767px), (max-width: 991px) and (max-height: 500px)').matches) return;
        const body = document.body;
        const button = document.querySelector('.tg-page-mobile-menu');
        const drawer = document.getElementById('tgPageMobileDrawer');
        const closers = document.querySelectorAll('[data-page-menu-close], .tg-page-mobile-drawer a');
        if (!button || !drawer) return;

        function setMenu(open) {
            body.classList.toggle('tg-page-menu-open', open);
            button.setAttribute('aria-expanded', String(open));
            drawer.setAttribute('aria-hidden', String(!open));
            if (!open) button.focus({ preventScroll: true });
        }

        button.addEventListener('click', function () { setMenu(!body.classList.contains('tg-page-menu-open')); });
        closers.forEach(function (item) { item.addEventListener('click', function () { setMenu(false); }); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') setMenu(false); });
    });
</script>
