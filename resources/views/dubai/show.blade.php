@php
    $sections = $page['sections'];
    $has = fn (string $section): bool => in_array($section, $sections, true);
    $nav = array_values(array_filter([
        ['href' => '#top', 'icon' => 'fas fa-home-lg', 'label' => 'Home', 'show' => true],
        ['href' => '#why', 'icon' => 'fas fa-city', 'label' => 'Why Dubai', 'show' => $has('why')],
        ['href' => '#journey', 'icon' => 'fas fa-route', 'label' => 'Journey', 'show' => $has('journey')],
        ['href' => '#costs', 'icon' => 'fas fa-wallet', 'label' => 'Costs', 'show' => $has('costs')],
        ['href' => '#reviews', 'icon' => 'fas fa-star', 'label' => 'Reviews', 'show' => $has('reviews')],
        ['href' => $has('contact') ? '#contact' : '#footer', 'icon' => 'fas fa-phone-rotary', 'label' => 'Contact', 'show' => true],
    ], fn (array $link): bool => $link['show']));
    $leadSaved = (bool) session('dubai_lead');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page['meta_title'] }}</title>
    <meta name="description" content="{{ $page['meta_description'] }}">
    <link rel="canonical" href="{{ url('/dubai') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/static/css/d77c6267e29f4365.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/static/css/3040a4c1aba51956.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/static/css/6ce99338b40f581f.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/static/css/79c932325e17b2d9.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/css/dubai-home.css') }}?v={{ @filemtime(public_path('assets/dubai-landing/css/dubai-home.css')) }}">
    {{-- Phone-only app layer: never loads on tablet or desktop widths. --}}
    <link rel="stylesheet" href="{{ asset('assets/dubai-landing/css/dubai-mobile.css') }}?v={{ @filemtime(public_path('assets/dubai-landing/css/dubai-mobile.css')) }}" media="(max-width: 767.98px)">

    @if ($tracking['enabled'] && $tracking['metaPixelId'] !== '')
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', @json($tracking['metaPixelId']));
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id={{ urlencode($tracking['metaPixelId']) }}&ev=PageView&noscript=1"></noscript>
    @endif
    @if ($tracking['enabled'] && $tracking['googleAdsId'] !== '')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($tracking['googleAdsId']) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($tracking['googleAdsId']));
        </script>
    @endif
</head>
<body class="tg-app">

@if ($page['highlightList'] !== [])
    <div class="marquee-section style-header">
        <div class="mycustom-marque header-marque theme-blue-bg">
            <div class="scrolling-wrap">
                @for ($i = 0; $i < 3; $i++)
                    <div class="comm"><div></div>@foreach ($page['highlightList'] as $item)<div class="cmn-textslide"><i class="flaticon-mortarboard"></i> {{ $item }}</div>@endforeach</div>
                @endfor
            </div>
        </div>
    </div>
@endif

{{-- Sidebar menu (opened from the header on tablets and the tab bar on phones) --}}
<div class="fix-area">
    <div class="offcanvas__info">
        <div class="offcanvas__wrapper">
            <div class="offcanvas__content">
                <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                    <div class="offcanvas__logo tg-logo"><a href="#top"><img src="{{ $page['logo'] }}" alt="Trans Globe Indore, managed by GEIC"></a></div>
                    <div class="offcanvas__close"><button type="button" aria-label="Close menu"><i class="fas fa-times"></i></button></div>
                </div>
                <h3 class="offcanvas-title">Study in Dubai</h3>
                <p>Free counselling, admissions, scholarships and UAE student visa support.</p>
                <div class="mobile-menu fix mb-3">
                    <ul class="tg-mobile-nav">
                        @foreach ($nav as $link)
                            <li><a href="{{ $link['href'] }}"><span class="tg-nav-icon"><i class="{{ $link['icon'] }}"></i></span>{{ $link['label'] }}<i class="fas fa-chevron-right tg-nav-chevron" aria-hidden="true"></i></a></li>
                        @endforeach
                        @if ($has('faq'))
                            <li><a href="#faq"><span class="tg-nav-icon"><i class="fas fa-question-circle"></i></span>FAQs<i class="fas fa-chevron-right tg-nav-chevron" aria-hidden="true"></i></a></li>
                        @endif
                    </ul>
                </div>
                <div class="offcanvas__contact">
                    <ul>
                        <li><div class="offcanvas__contact-icon"><i class="fal fa-map-marker-alt"></i></div><div class="offcanvas__contact-text">Lad Colony, Indore</div></li>
                        <li><div class="offcanvas__contact-icon"><i class="fal fa-envelope"></i></div><div class="offcanvas__contact-text"><a href="mailto:{{ $page['email'] }}">{{ $page['email'] }}</a></div></li>
                        <li><div class="offcanvas__contact-icon"><i class="fal fa-clock"></i></div><div class="offcanvas__contact-text">{{ $page['office_hours'] }}</div></li>
                        <li><div class="offcanvas__contact-icon"><i class="far fa-phone"></i></div><div class="offcanvas__contact-text"><a href="{{ $page['telUrl'] }}">{{ $page['phone'] }}</a></div></li>
                    </ul>
                    <div class="header-button mt-4"><a class="theme-btn yellow-btn" href="{{ $page['whatsappUrl'] }}">{{ $page['header_cta'] }}</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas__overlay"></div>

<header id="header-sticky" class="header-1">
    <div class="container-fluid">
        <div class="mega-menu-wrapper">
            <div class="header-main">
                <div class="header-left">
                    <div class="logo"><a class="header-logo tg-logo" href="#top"><img src="{{ $page['logo'] }}" alt="Trans Globe Indore, managed by GEIC"></a></div>
                </div>
                <div class="header-right d-flex justify-content-end align-items-center">
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                    @foreach ($nav as $link)
                                        <li><a href="{{ $link['href'] }}"><span class="head-icon"><i class="{{ $link['icon'] }}"></i></span>{{ $link['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="header-button"><a class="theme-btn yellow-btn" href="{{ $page['whatsappUrl'] }}">{{ $page['header_cta'] }}</a></div>
                    <a class="tg-header-call tg-mobile-only" href="{{ $page['telUrl'] }}" aria-label="Call {{ $page['phone'] }}"><i class="fas fa-phone-alt"></i></a>
                    <div class="header__hamburger d-xl-none my-auto">
                        <div class="sidebar__toggle" role="button" tabindex="0" aria-label="Open menu"><div class="header-bar"><span></span><span></span><span></span></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main id="top">
    @foreach ($sections as $section)
        @include('dubai.sections.'.$section)
    @endforeach
</main>

<footer id="footer" class="footer-section fix footer-bg">
    <div class="container">
        <div class="footer-widget-wrapper">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="single-footer-widget">
                        <div class="widget-head tg-logo"><a href="#top"><img src="{{ $page['logoWhite'] }}" alt="Trans Globe Indore, managed by GEIC"></a></div>
                        <div class="footer-content">
                            <p>{{ $page['footer_copy'] }}</p>
                            <div class="social-icon">
                                <a href="https://www.facebook.com/GEICINDORE1" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/geicindore1/" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="https://www.youtube.com/@geicindore5721" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a href="https://linkedin.com/in/global-education-752796283" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 ps-lg-5">
                    <div class="single-footer-widget">
                        <div class="widget-head"><h3>Study in Dubai</h3></div>
                        <ul class="list-area">
                            @foreach ($nav as $link)
                                <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 ps-lg-5">
                    <div class="single-footer-widget">
                        <div class="widget-head"><h3>Quick Links</h3></div>
                        <ul class="list-area">
                            <li><a href="{{ url('/') }}">About Trans Globe Indore</a></li>
                            <li><a href="{{ url('/destinations/dubai') }}">Dubai destination guide</a></li>
                            <li><a href="{{ url('/contact') }}">Contact</a></li>
                            <li><a href="{{ $page['whatsappUrl'] }}">Book Free Counselling</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 ps-xl-5">
                    <div class="single-footer-widget">
                        <div class="widget-head"><h3>Contact Us</h3></div>
                        <div class="footer-content">
                            <ul class="contact-info">
                                <li>{{ $page['address'] }}</li>
                                <li>{{ $page['office_hours'] }}</li>
                                <li><a href="mailto:{{ $page['email'] }}" class="link">{{ $page['email'] }}</a></li>
                                <li><a href="{{ $page['telUrl'] }}">{{ $page['phone'] }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom"><p>Copyright © {{ date('Y') }} <a href="{{ url('/') }}">Trans Globe Indore</a>, managed by GEIC. All rights reserved.</p></div>
    </div>
    <div class="footer-name"><h2>{{ $page['footer_brand'] }}</h2></div>
</footer>

<div class="tg-sheet-backdrop" data-close-sheet hidden></div>

{{-- Phone-only bottom tab bar (hidden on tablet and desktop) --}}
<nav class="tg-tabbar" aria-label="Quick navigation">
    <a href="#top" class="tg-tab is-active" data-spy="top" aria-current="location"><i class="fas fa-home" aria-hidden="true"></i><span>Home</span></a>
    @if ($has('journey'))
        <a href="#journey" class="tg-tab" data-spy="journey"><i class="fas fa-route" aria-hidden="true"></i><span>Journey</span></a>
    @endif
    @if ($has('contact'))
        <button type="button" class="tg-tab tg-tab-main" data-open-sheet aria-haspopup="dialog" aria-controls="enquiry-sheet"><span class="tg-fab" aria-hidden="true"><i class="fas fa-paper-plane"></i></span><span>Apply</span></button>
    @else
        <a href="{{ $page['whatsappUrl'] }}" class="tg-tab tg-tab-main"><span class="tg-fab" aria-hidden="true"><i class="fas fa-paper-plane"></i></span><span>Apply</span></a>
    @endif
    <a href="{{ $page['whatsappUrl'] }}" class="tg-tab"><i class="fab fa-whatsapp" aria-hidden="true"></i><span>Chat</span></a>
    <button type="button" class="tg-tab tg-tab-menu" aria-haspopup="dialog" aria-label="Open menu"><i class="fas fa-bars" aria-hidden="true"></i><span>Menu</span></button>
</nav>

<button id="back-top" class="back-to-top" type="button" aria-label="Back to top"><i class="fas fa-long-arrow-up"></i></button>
<a class="tg-wa" href="{{ $page['whatsappUrl'] }}" aria-label="Chat on WhatsApp"><i class="fab fa-whatsapp"></i></a>

@include('mirror.partials.site-analytics')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>
<script>
(function () {
    var waUrl = @json($page['whatsappUrl']);
    var googleAdsId = @json($tracking['enabled'] ? $tracking['googleAdsId'] : '');
    var conversionLabel = @json($tracking['enabled'] ? $tracking['googleAdsConversionLabel'] : '');

    // Ad tracking: no-ops unless the Meta Pixel / Google tag is enabled in Dubai lead tracking.
    function track(metaEvent, googleEvent, params) {
        if (window.fbq) { fbq('track', metaEvent, params || {}); }
        if (window.gtag && googleEvent) { gtag('event', googleEvent, params || {}); }
    }
    @if ($leadSaved)
        if (window.fbq) { fbq('track', 'Lead', { content_name: 'Dubai enquiry form' }); }
        if (window.gtag && googleAdsId) {
            if (conversionLabel) { gtag('event', 'conversion', { send_to: googleAdsId + '/' + conversionLabel }); }
            else { gtag('event', 'generate_lead', { form_name: 'Dubai enquiry form' }); }
        }
    @endif
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href^="https://wa.me"], a[href^="tel:"]');
        if (link) { track('Contact', 'contact', { method: link.href.indexOf('tel:') === 0 ? 'phone' : 'whatsapp' }); }
    });

    // Sidebar menu
    var info = document.querySelector('.offcanvas__info');
    var overlay = document.querySelector('.offcanvas__overlay');
    function toggleMenu(open) { info.classList.toggle('info-open', open); overlay.classList.toggle('overlay-open', open); }
    document.querySelectorAll('.sidebar__toggle, .tg-tab-menu').forEach(function (el) { el.addEventListener('click', function () { toggleMenu(true); }); });
    document.querySelector('.offcanvas__close button').addEventListener('click', function () { toggleMenu(false); });
    overlay.addEventListener('click', function () { toggleMenu(false); });
    document.querySelectorAll('.tg-mobile-nav a').forEach(function (a) { a.addEventListener('click', function () { toggleMenu(false); }); });

    // Journey tabs
    var tabs = document.querySelectorAll('.tg-tabs button');
    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            tabs.forEach(function (b) { b.classList.toggle('active', b === btn); b.setAttribute('aria-selected', b === btn ? 'true' : 'false'); });
            var filter = btn.dataset.filter;
            document.querySelectorAll('.tg-card-col').forEach(function (col) { col.hidden = col.dataset.tabs.split(' ').indexOf(filter) === -1; });
        });
    });

    // Consultation banner opens WhatsApp with the visitor's name
    var ctaForm = document.getElementById('tg-cta-form');
    if (ctaForm) {
        ctaForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = document.getElementById('tg-cta-name').value.trim();
            window.open(waUrl.split('?')[0] + '?text=' + encodeURIComponent('Hi, I am ' + name + '. I want to book a consultation for Dubai'), '_blank', 'noopener');
        });
    }

    // Phone-only: enquiry form as a bottom sheet. On larger screens the form stays inline.
    var phone = window.matchMedia('(max-width: 767.98px)');
    var sheet = document.getElementById('enquiry-sheet');
    var backdrop = document.querySelector('.tg-sheet-backdrop');
    var lastFocus = null;
    function openSheet(pushHistory) {
        if (!sheet || document.body.classList.contains('tg-sheet-open')) { return; }
        lastFocus = document.activeElement;
        sheet.setAttribute('role', 'dialog');
        sheet.setAttribute('aria-modal', 'true');
        backdrop.hidden = false;
        document.body.classList.add('tg-sheet-open');
        if (pushHistory !== false) { history.pushState({ tgSheet: true }, ''); }
        setTimeout(function () { var first = sheet.querySelector('input:not([type=hidden]):not([tabindex="-1"])'); if (first) { first.focus({ preventScroll: true }); } }, 250);
    }
    function closeSheet(fromHistory) {
        if (!sheet || !document.body.classList.contains('tg-sheet-open')) { return; }
        document.body.classList.remove('tg-sheet-open');
        sheet.removeAttribute('role');
        sheet.removeAttribute('aria-modal');
        setTimeout(function () { backdrop.hidden = true; }, 300);
        if (!fromHistory && history.state && history.state.tgSheet) { history.back(); }
        if (lastFocus) { lastFocus.focus({ preventScroll: true }); }
    }
    document.querySelectorAll('[data-open-sheet]').forEach(function (el) { el.addEventListener('click', function () { openSheet(); }); });
    document.querySelectorAll('[data-close-sheet]').forEach(function (el) { el.addEventListener('click', function () { closeSheet(); }); });
    window.addEventListener('popstate', function () { closeSheet(true); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeSheet(); toggleMenu(false); } });
    // After a submit, show the result in the popup; otherwise invite new visitors once per visit.
    if (sheet && sheet.querySelector('[data-enquiry-done]')) { try { sessionStorage.setItem('tg_dubai_lead', '1'); } catch (e) {} }
    if (sheet && sheet.querySelector('.tg-alert')) {
        openSheet(false);
    } else if (sheet) {
        var seen = false;
        // Open on every landing, except straight after this visitor has submitted.
        try { seen = sessionStorage.getItem('tg_dubai_lead') === '1'; } catch (e) {}
        if (!seen) {
            setTimeout(function () {
                try { sessionStorage.setItem('tg_dubai_popup', '1'); } catch (e) {}
                var info = document.querySelector('.offcanvas__info.info-open');
                if (!info) { openSheet(); }
            }, 1000);
        }
    }

    // Tab bar highlight + header shadow + back-to-top
    var spyTabs = document.querySelectorAll('.tg-tab[data-spy]');
    var journey = document.getElementById('journey');
    var backTop = document.getElementById('back-top');
    var header = document.getElementById('header-sticky');
    window.addEventListener('scroll', function () {
        backTop.classList.toggle('show', window.scrollY > 600);
        header.classList.toggle('tg-scrolled', window.scrollY > 40);
        if (phone.matches && journey) {
            var r = journey.getBoundingClientRect();
            var inJourney = r.top < window.innerHeight * 0.5 && r.bottom > 0;
            spyTabs.forEach(function (t) { var on = (t.dataset.spy === 'journey') === inJourney; t.classList.toggle('is-active', on); if (on) { t.setAttribute('aria-current', 'location'); } else { t.removeAttribute('aria-current'); } });
        }
    }, { passive: true });
    backTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

    // Sliders
    if (window.Swiper) {
        if (document.querySelector('.team-slider')) {
            new Swiper('.team-slider', { loop: true, speed: 1500, autoplay: { delay: 2500, disableOnInteraction: false },
                breakpoints: { 0: { slidesPerView: 1 }, 475: { slidesPerView: 2 }, 991: { slidesPerView: 3 }, 1199: { slidesPerView: 4 }, 1399: { slidesPerView: 5 } } });
        }
        if (document.querySelector('.testimonial-slider')) {
            new Swiper('.testimonial-slider', { spaceBetween: 24, speed: 800, loop: true,
                autoplay: { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true },
                pagination: { el: '.tg-reviews .dot', clickable: true },
                breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } } });
        }
    }
})();
</script>
</body>
</html>
