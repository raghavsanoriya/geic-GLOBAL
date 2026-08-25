@include('mirror.partials.header')

@php
    $destinations = [
        ['slug' => 'australia', 'name' => 'Australia', 'flag' => 'au.png', 'image' => 'australia.jpg', 'tagline' => 'Research-led learning', 'copy' => 'Practical education, globally respected degrees and the prestigious Group of Eight universities.'],
        ['slug' => 'new-zealand', 'name' => 'New Zealand', 'flag' => 'nz.png', 'image' => 'new-zealand.jpg', 'tagline' => 'Quality with balance', 'copy' => 'Government-regulated education in a safe, welcoming setting with an exceptional quality of life.'],
        ['slug' => 'united-kingdom', 'name' => 'United Kingdom', 'flag' => 'gb.png', 'image' => 'uk.jpg', 'tagline' => 'Prestigious education', 'copy' => 'Rigorous academic standards, globally recognised qualifications and a rich international student culture.'],
        ['slug' => 'ireland', 'name' => 'Ireland', 'flag' => 'ie.png', 'image' => 'ireland.jpg', 'tagline' => 'Europe’s technology hub', 'copy' => 'English-medium study, vibrant student life and strong post-study opportunities in a growing tech economy.'],
        ['slug' => 'germany', 'name' => 'Germany', 'flag' => 'de.png', 'image' => 'germany.webp', 'tagline' => 'Engineering excellence', 'copy' => 'World-class public universities, outstanding innovation and many affordable study pathways.'],
        ['slug' => 'europe', 'name' => 'Europe', 'flag' => 'eu.png', 'image' => 'europe-card.jpg', 'tagline' => 'Many cultures, one journey', 'copy' => 'Diverse countries, renowned universities and rewarding international exposure across the continent.'],
        ['slug' => 'united-states', 'name' => 'United States', 'flag' => 'us.png', 'image' => 'usa.jpg', 'tagline' => 'Limitless academic choice', 'copy' => 'More than 4,000 accredited colleges and universities with an extraordinary range of programs.'],
        ['slug' => 'canada', 'name' => 'Canada', 'flag' => 'ca.png', 'image' => 'canada.jpg', 'tagline' => 'Study, work and grow', 'copy' => 'High-quality education in a welcoming country with attractive post-study work and PR pathways.'],
        ['slug' => 'singapore', 'name' => 'Singapore', 'flag' => 'sg.png', 'image' => 'singapore.jpg', 'tagline' => 'Asia’s education hub', 'copy' => 'World-ranked institutions, a bilingual environment and close connections to global business.'],
        ['slug' => 'dubai', 'name' => 'Dubai & UAE', 'flag' => 'ae.png', 'image' => 'dubai-card.jpg', 'tagline' => 'Global degrees, global city', 'copy' => 'International qualifications in a fast-growing, tax-free and cosmopolitan education centre.'],
        ['slug' => 'malaysia', 'name' => 'Malaysia', 'flag' => 'my.png', 'image' => 'malaysia.webp', 'tagline' => 'Affordable international study', 'copy' => 'Multicultural living and leading UK and Australian university branch campuses at accessible costs.'],
        ['slug' => 'switzerland', 'name' => 'Switzerland', 'flag' => 'ch.png', 'image' => 'switzerland.webp', 'tagline' => 'Innovation and quality', 'copy' => 'Outstanding education, research and quality of life in one of Europe’s strongest economies.'],
    ];
@endphp

<style>
    .tg-dest-page { background: #fff; }
    .tg-dest-hero { padding: 132px 0 72px; background: #f5f8fc; }
    .tg-dest-hero__shell { position: relative; overflow: hidden; border-radius: 36px; padding: 64px; background-color: #0e2145; background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px); background-size: 14px 14px; box-shadow: 0 24px 60px rgba(14,33,69,.18); }
    .tg-dest-hero__shell::before { content: ''; position: absolute; width: 420px; height: 420px; right: -210px; top: -220px; border: 70px solid rgba(255,255,255,.04); border-radius: 50%; }
    .tg-dest-hero__content { position: relative; z-index: 2; }
    .tg-dest-eyebrow { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .tg-dest-eyebrow::before { content: ''; width: 30px; height: 2px; background: #E31E24; }
    .tg-dest-hero h1 { max-width: 680px; margin-top: 18px; color: #fff; font-size: 54px; line-height: 1.08; font-weight: 700; text-wrap: balance; }
    .tg-dest-hero h1 span { color: #E31E24; }
    .tg-dest-hero__copy { max-width: 640px; margin-top: 20px; color: rgba(255,255,255,.76); font-size: 17px; line-height: 1.7; }
    .tg-dest-hero__actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 30px; }
    .tg-dest-secondary-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 24px; border: 1px solid rgba(255,255,255,.35); border-radius: 12px; color: #fff; font-weight: 700; transition: background-color .2s ease, border-color .2s ease; }
    .tg-dest-secondary-btn:hover, .tg-dest-secondary-btn:focus { border-color: #fff; background: rgba(255,255,255,.1); color: #fff; }
    .tg-dest-hero__mosaic { position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1fr; gap: 14px; min-height: 400px; }
    .tg-dest-hero__tile { position: relative; overflow: hidden; min-height: 190px; border-radius: 22px; }
    .tg-dest-hero__tile:first-child { grid-row: span 2; }
    .tg-dest-hero__tile img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-dest-hero__tile:hover img { transform: scale(1.05); }
    .tg-dest-hero__tile span { position: absolute; left: 14px; bottom: 14px; padding: 7px 11px; border-radius: 999px; background: rgba(14,33,69,.86); color: #fff; font-size: 12px; font-weight: 700; backdrop-filter: blur(8px); }
    .tg-dest-proof { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 24px; }
    .tg-dest-proof__item { padding: 16px 18px; border: 1px solid rgba(255,255,255,.14); border-radius: 16px; background: rgba(255,255,255,.07); }
    .tg-dest-proof__item strong { display: block; color: #fff; font-size: 22px; }
    .tg-dest-proof__item span { display: block; margin-top: 2px; color: rgba(255,255,255,.64); font-size: 12px; }

    .tg-dest-list { padding: 88px 0 96px; scroll-margin-top: 180px; }
    .tg-dest-list:target { margin-top: -180px; padding-top: 268px; }
    .tg-dest-list__head { display: flex; align-items: flex-end; justify-content: space-between; gap: 28px; }
    .tg-dest-list__head h2 { color: #0e2145; font-size: 42px; line-height: 1.15; font-weight: 700; }
    .tg-dest-list__head h2 span { color: #E31E24; }
    .tg-dest-list__head p { max-width: 650px; margin-top: 14px; color: #71819b; font-size: 16px; line-height: 1.7; }
    .tg-dest-search { position: relative; width: min(100%, 360px); flex: 0 0 auto; }
    .tg-dest-search svg { position: absolute; left: 17px; top: 50%; width: 20px; height: 20px; color: #71819b; transform: translateY(-50%); pointer-events: none; }
    .tg-dest-search input { width: 100%; height: 54px; padding: 0 18px 0 50px; border: 1px solid #dbe3ed; border-radius: 14px; background: #fff; color: #0e2145; font-size: 15px; box-shadow: 0 8px 24px rgba(14,33,69,.05); }
    .tg-dest-search input:focus { border-color: #E31E24; box-shadow: 0 0 0 4px rgba(227,30,36,.12); outline: none; }
    .tg-dest-result-count { margin-top: 18px; color: #71819b; font-size: 14px; }
    .tg-dest-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px; margin-top: 30px; }
    .tg-country-card { min-width: 0; overflow: hidden; border: 1px solid #e1e7ef; border-radius: 24px; background: #fff; box-shadow: 0 12px 34px rgba(14,33,69,.08); transition: transform .22s ease, box-shadow .22s ease; }
    .tg-country-card:hover { transform: translateY(-5px); box-shadow: 0 20px 44px rgba(14,33,69,.14); }
    .tg-country-card[hidden] { display: none; }
    .tg-country-card__media { position: relative; aspect-ratio: 1 / 1; overflow: hidden; background: #dce4ee; }
    .tg-country-card__media::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(8,20,43,.03) 30%, rgba(8,20,43,.94) 100%); }
    .tg-country-card__media > img:first-child { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-country-card:hover .tg-country-card__media > img:first-child { transform: scale(1.06); }
    .tg-country-card__flag { position: absolute; z-index: 2; top: 16px; left: 16px; width: auto !important; height: 28px !important; border: 2px solid rgba(255,255,255,.9); border-radius: 5px; box-shadow: 0 5px 14px rgba(0,0,0,.25); }
    .tg-country-card__content { position: absolute; z-index: 2; left: 0; right: 0; bottom: 0; padding: 20px; }
    .tg-country-card__kicker { color: rgba(255,255,255,.7); font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .tg-country-card__content h3 { margin-top: 5px; color: #fff; font-size: 23px; line-height: 1.15; font-weight: 700; }
    .tg-country-card__tagline { margin-top: 8px; color: #fff; font-size: 13px; font-weight: 600; }
    .tg-country-card__body { padding: 18px 20px 20px; }
    .tg-country-card__body p { min-height: 66px; color: #71819b; font-size: 13px; line-height: 1.65; }
    .tg-country-card__link { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 44px; margin-top: 15px; padding-top: 14px; border-top: 1px solid #edf1f5; color: #E31E24; font-size: 13px; font-weight: 700; }
    .tg-country-card__link svg { width: 18px; height: 18px; transition: transform .2s ease; }
    .tg-country-card:hover .tg-country-card__link svg { transform: translateX(4px); }
    .tg-dest-empty { display: none; margin-top: 32px; padding: 34px; border: 1px dashed #ccd6e3; border-radius: 20px; background: #f8fafc; color: #71819b; text-align: center; }
    .tg-dest-empty.is-visible { display: block; }

    .tg-dest-cta { padding: 0 0 96px; }
    .tg-dest-cta__shell { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 32px; padding: 48px 54px; border-radius: 30px; background-color: #0e2145; background-image: radial-gradient(rgba(255,255,255,.09) 1px, transparent 1px); background-size: 13px 13px; }
    .tg-dest-cta__shell::after { content: ''; position: absolute; right: -85px; bottom: -130px; width: 300px; height: 300px; border: 58px solid rgba(255,255,255,.05); border-radius: 50%; }
    .tg-dest-cta__content, .tg-dest-cta__action { position: relative; z-index: 2; }
    .tg-dest-cta h2 { max-width: 720px; color: #fff; font-size: 36px; line-height: 1.2; font-weight: 700; }
    .tg-dest-cta p { max-width: 720px; margin-top: 12px; color: rgba(255,255,255,.7); font-size: 15px; line-height: 1.65; }

    @media (max-width: 991px) {
        .tg-dest-hero { padding: 112px 0 56px; }
        .tg-dest-hero__shell { padding: 38px 28px; border-radius: 28px; }
        .tg-dest-hero h1 { font-size: 42px; }
        .tg-dest-hero__mosaic { margin-top: 34px; min-height: 330px; }
        .tg-dest-list { padding: 68px 0 72px; }
        .tg-dest-list:target { margin-top: -180px; padding-top: 248px; }
        .tg-dest-list__head { align-items: flex-start; flex-direction: column; }
        .tg-dest-list__head h2 { font-size: 34px; }
        .tg-dest-search { width: 100%; }
        .tg-dest-cta { padding-bottom: 72px; }
        .tg-dest-cta__shell { align-items: flex-start; flex-direction: column; padding: 36px 30px; }
    }
    @media (max-width: 575px) {
        .tg-dest-hero { padding-top: 104px; }
        .tg-dest-hero__shell { padding: 30px 20px; border-radius: 24px; }
        .tg-dest-hero h1 { font-size: 34px; }
        .tg-dest-hero__copy { font-size: 15px; }
        .tg-dest-proof { grid-template-columns: 1fr; }
        .tg-dest-hero__mosaic { grid-template-columns: 1fr 1fr; min-height: 280px; }
        .tg-dest-hero__tile { min-height: 130px; border-radius: 17px; }
        .tg-dest-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .tg-country-card { border-radius: 18px; }
        .tg-country-card__flag { top: 11px; left: 11px; height: 22px !important; }
        .tg-country-card__content { padding: 14px; }
        .tg-country-card__content h3 { font-size: 18px; }
        .tg-country-card__tagline { font-size: 11px; }
        .tg-country-card__body { padding: 14px; }
        .tg-country-card__body p { min-height: 88px; font-size: 12px; line-height: 1.5; }
        .tg-country-card__link { font-size: 12px; }
        .tg-dest-cta h2 { font-size: 29px; }
    }
    @media (max-width: 390px) {
        .tg-dest-grid { grid-template-columns: 1fr; }
        .tg-country-card__body p { min-height: auto; }
    }
    @media (prefers-reduced-motion: reduce) {
        .tg-country-card, .tg-country-card__media > img:first-child, .tg-country-card__link svg, .tg-dest-hero__tile img { transition: none; }
        .tg-country-card:hover { transform: none; }
    }
</style>

<main class="tg-dest-page">
    <section class="tg-dest-hero">
        <div class="container">
            <div class="tg-dest-hero__shell">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7 pr-lg-48">
                        <div class="tg-dest-hero__content">
                            <div class="tg-dest-eyebrow">Study destinations</div>
                            <h1>Find the country that fits <span>your ambition</span></h1>
                            <p class="tg-dest-hero__copy">Compare leading study destinations, understand what makes each one different and choose your next step with guidance from Trans Globe Indore.</p>
                            <div class="tg-dest-hero__actions">
                                <a href="#explore-destinations" class="btn btn-primary btn-xlg text-white">Explore countries</a>
                                <a href="index.html#contact" class="tg-dest-secondary-btn">Book free counselling</a>
                            </div>
                            <div class="tg-dest-proof" aria-label="Trans Globe experience">
                                <div class="tg-dest-proof__item"><strong>98.7%</strong><span>Visa success rate</span></div>
                                <div class="tg-dest-proof__item"><strong>32+</strong><span>Years of experience</span></div>
                                <div class="tg-dest-proof__item"><strong>70,250+</strong><span>Students placed</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="tg-dest-hero__mosaic" aria-hidden="true">
                            <div class="tg-dest-hero__tile"><img src="assets/transglobe/destinations/australia.jpg" alt=""><span>Australia</span></div>
                            <div class="tg-dest-hero__tile"><img src="assets/transglobe/destinations/germany.webp" alt=""><span>Germany</span></div>
                            <div class="tg-dest-hero__tile"><img src="assets/transglobe/destinations/canada.jpg" alt=""><span>Canada</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="explore-destinations" class="tg-dest-list">
        <div class="container">
            <div class="tg-dest-list__head">
                <div>
                    <div class="tg-dest-eyebrow" style="color:#E31E24">Explore your options</div>
                    <h2 class="mt-14">Study abroad <span>destinations</span></h2>
                    <p>Explore top countries, compare the experience and find the study environment that matches your academic and career goals.</p>
                </div>
                <div class="tg-dest-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="1.8"/><path d="m16.2 16.2 4 4" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <input id="destinationSearch" type="search" placeholder="Search a country" aria-label="Search study destinations">
                </div>
            </div>

            <div id="destinationResultCount" class="tg-dest-result-count" aria-live="polite">Showing all {{ count($destinations) }} destinations</div>

            <div id="destinationGrid" class="tg-dest-grid">
                @foreach ($destinations as $destination)
                    <article id="country-{{ $destination['slug'] }}" class="tg-country-card" data-country="{{ strtolower($destination['name'].' '.$destination['tagline']) }}">
                        <div class="tg-country-card__media">
                            <img src="assets/transglobe/destinations/{{ $destination['image'] }}" alt="Study in {{ $destination['name'] }}" loading="lazy">
                            <img src="assets/transglobe/destinations/flags/{{ $destination['flag'] }}" alt="{{ $destination['name'] }} flag" class="tg-country-card__flag" loading="lazy">
                            <div class="tg-country-card__content">
                                <div class="tg-country-card__kicker">Study destination</div>
                                <h3>{{ $destination['name'] }}</h3>
                                <div class="tg-country-card__tagline">{{ $destination['tagline'] }}</div>
                            </div>
                        </div>
                        <div class="tg-country-card__body">
                            <p>{{ $destination['copy'] }}</p>
                            <a href="{{ $destination['slug'] === 'australia' ? 'destinations/australia' : 'index.html#contact' }}" class="tg-country-card__link" aria-label="Get more information about studying in {{ $destination['name'] }}">
                                <span>Get more info</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div id="destinationEmpty" class="tg-dest-empty">No destination matches your search. Try another country name.</div>
        </div>
    </section>

    <section id="contact" class="tg-dest-cta">
        <div class="container">
            <div class="tg-dest-cta__shell">
                <div class="tg-dest-cta__content">
                    <h2>Not sure which destination is right for you?</h2>
                    <p>Tell our Indore counsellors about your goals, academic background and budget. We’ll help you compare realistic options and plan your next step.</p>
                </div>
                <div class="tg-dest-cta__action">
                    <a href="tel:+919826666886" class="btn btn-primary btn-xlg text-white">Speak to an Indore counsellor</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('destinationSearch');
        const cards = Array.from(document.querySelectorAll('.tg-country-card'));
        const count = document.getElementById('destinationResultCount');
        const empty = document.getElementById('destinationEmpty');

        if (!input) return;

        input.addEventListener('input', function () {
            const query = input.value.trim().toLowerCase();
            let visible = 0;

            cards.forEach(function (card) {
                const matches = card.dataset.country.includes(query);
                card.hidden = !matches;
                if (matches) visible += 1;
            });

            count.textContent = query
                ? 'Showing ' + visible + ' matching destination' + (visible === 1 ? '' : 's')
                : 'Showing all ' + cards.length + ' destinations';
            empty.classList.toggle('is-visible', visible === 0);
        });
    });
</script>

@include('mirror.partials.footer')
