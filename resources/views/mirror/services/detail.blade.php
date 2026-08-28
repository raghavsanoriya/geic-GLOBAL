@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/services'), 'mobileBackLabel' => 'Back to services'])
@php
    $detailUrl = url()->current();
    $serviceMedia = \App\Support\DetailPageAssets::serviceGallery($service);
    $serviceUniversities = \App\Support\DetailPageAssets::universityNetwork();
@endphp

<style>
    :root { --sd-navy: #0e2145; --sd-red: #e31e24; --sd-soft: #f4f7fb; --sd-ink: #15294d; --sd-muted: #64748b; --sd-line: #dfe7f0; }
    .sd-page { overflow: clip; background: #fff; color: var(--sd-ink); }
    .sd-wrap { width: min(1280px, calc(100% - 48px)); margin-inline: auto; }
    .sd-section { padding: 92px 0; scroll-margin-top: 96px; }
    .sd-section--soft { background: var(--sd-soft); }
    .sd-kicker { display: inline-flex; align-items: center; gap: 10px; color: var(--sd-red); font-size: 12px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .sd-kicker::before { width: 28px; height: 2px; background: currentColor; content: ''; }
    .sd-title { max-width: 750px; margin: 14px 0 0; color: var(--sd-navy); font-size: clamp(34px, 4vw, 50px); line-height: 1.1; font-weight: 800; letter-spacing: -.045em; text-wrap: balance; }
    .sd-lead { max-width: 700px; margin: 16px 0 0; color: var(--sd-muted); font-size: 17px; line-height: 1.75; }
    .sd-center { text-align: center; }
    .sd-center .sd-title, .sd-center .sd-lead { margin-inline: auto; }
    .sd-button { display: inline-flex; min-height: 52px; align-items: center; justify-content: center; gap: 9px; padding: 0 23px; border: 1px solid transparent; border-radius: 14px; background: var(--sd-red); color: #fff !important; font-size: 14px; font-weight: 800; transition: background .2s ease, transform .2s ease, border-color .2s ease; }
    .sd-button:hover, .sd-button:focus-visible { background: #c81820; color: #fff; transform: translateY(-2px); }
    .sd-button--outline { border-color: rgba(255,255,255,.34); background: rgba(255,255,255,.07); }
    .sd-button--outline:hover, .sd-button--outline:focus-visible { border-color: #fff; background: #fff; color: var(--sd-navy) !important; }
    .sd-page :is(a, summary):focus-visible { outline: 3px solid rgba(227,30,36,.32); outline-offset: 3px; }

    .sd-hero { padding: 128px 0 0; background: var(--sd-soft); }
    .sd-hero__shell { position: relative; min-height: 560px; overflow: hidden; display: flex; align-items: flex-end; border-radius: 34px; background: var(--sd-navy); box-shadow: 0 26px 65px rgba(14,33,69,.2); }
    .sd-hero__image, .sd-hero__overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
    .sd-hero__image { object-fit: cover; object-position: center; }
    .sd-hero__overlay { background: linear-gradient(90deg, rgba(6,19,45,.97) 0%, rgba(7,22,49,.88) 44%, rgba(7,22,49,.2) 100%); }
    .sd-hero__content { position: relative; z-index: 1; width: min(660px, calc(100% - 380px)); padding: 64px; }
    .sd-crumbs { display: flex; flex-wrap: wrap; gap: 8px; color: rgba(255,255,255,.62); font-size: 13px; }
    .sd-crumbs a { color: #fff; font-weight: 700; }
    .sd-hero__eyebrow { display: inline-flex; align-items: center; margin-top: 28px; padding: 8px 13px; border: 1px solid rgba(255,255,255,.22); border-radius: 999px; color: #fff; background: rgba(255,255,255,.09); font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; backdrop-filter: blur(10px); }
    .sd-hero h1 { max-width: 700px; margin: 17px 0 0; color: #fff; font-size: clamp(45px, 5.6vw, 70px); line-height: 1.01; font-weight: 800; letter-spacing: -.055em; text-wrap: balance; }
    .sd-hero h1 span { color: #ff565d; }
    .sd-hero__lead { max-width: 660px; margin: 19px 0 0; color: rgba(255,255,255,.83); font-size: 17px; line-height: 1.72; }
    .sd-hero__actions { display: flex; flex-wrap: wrap; gap: 13px; margin-top: 29px; }
    .sd-hero__number { position: absolute; z-index: 2; top: 34px; right: 40px; display: grid; width: 78px; height: 78px; place-items: center; border: 1px solid rgba(255,255,255,.24); border-radius: 50%; color: #fff; background: rgba(14,33,69,.45); font-size: 20px; font-weight: 800; backdrop-filter: blur(12px); }
    .sd-quick { position: relative; z-index: 3; display: grid; grid-template-columns: repeat(3, 1fr); margin: -1px 30px 0; border: 1px solid var(--sd-line); border-radius: 0 0 24px 24px; background: #fff; box-shadow: 0 18px 42px rgba(14,33,69,.08); }
    .sd-quick__item { display: flex; align-items: center; gap: 13px; min-width: 0; padding: 20px 25px; border-right: 1px solid var(--sd-line); }
    .sd-quick__item:last-child { border-right: 0; }
    .sd-quick__mark { display: grid; width: 37px; height: 37px; flex: 0 0 37px; place-items: center; border-radius: 12px; color: var(--sd-red); background: #fdebed; font-weight: 900; }
    .sd-quick strong { display: block; color: var(--sd-navy); font-size: 14px; }
    .sd-quick span { display: block; margin-top: 3px; color: var(--sd-muted); font-size: 12px; line-height: 1.4; }
    .sd-quick .sd-quick__mark { display: grid; margin-top: 0; color: var(--sd-red); }
    .sd-quick__mark svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2.4; }

    .sd-anchor { position: sticky; z-index: 100; top: 0; padding: 12px 0; border-bottom: 1px solid rgba(223,231,240,.92); background: rgba(244,247,251,.94); box-shadow: 0 10px 26px rgba(14,33,69,.06); backdrop-filter: blur(16px); }
    .sd-anchor__bar { display: flex; gap: 8px; overflow-x: auto; padding: 7px; border: 1px solid var(--sd-line); border-radius: 17px; background: #fff; scrollbar-width: none; }
    .sd-anchor__bar::-webkit-scrollbar { display: none; }
    .sd-anchor a { flex: 0 0 auto; min-height: 40px; padding: 10px 15px; border-radius: 11px; color: #53657f; font-size: 13px; font-weight: 800; }
    .sd-anchor a:hover, .sd-anchor a:first-child { color: #fff; background: var(--sd-navy); }

    .sd-overview { display: grid; grid-template-columns: .95fr 1.05fr; align-items: center; gap: 70px; }
    .sd-overview__copy p { color: var(--sd-muted); font-size: 16px; line-height: 1.82; }
    .sd-overview__copy p + p { margin-top: 16px; }
    .sd-overview__image { position: relative; min-height: 490px; }
    .sd-overview__image-main { position: absolute; inset: 0 0 0 56px; overflow: hidden; border-radius: 30px; box-shadow: 0 22px 52px rgba(14,33,69,.16); }
    .sd-overview__image-main img { width: 100%; height: 100%; object-fit: cover; }
    .sd-overview__flag { position: absolute; left: 0; bottom: 42px; width: 154px; padding: 21px 18px; border: 7px solid #fff; border-radius: 22px; color: #fff; background: var(--sd-red); box-shadow: 0 16px 35px rgba(227,30,36,.22); }
    .sd-overview__flag strong { display: block; font-size: 25px; line-height: 1; }
    .sd-overview__flag span { display: block; margin-top: 7px; font-size: 11px; font-weight: 700; line-height: 1.45; }

    .sd-process { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 42px; }
    .sd-process__item { position: relative; min-height: 246px; padding: 28px; border: 1px solid rgba(255,255,255,.15); border-radius: 24px; background: rgba(255,255,255,.07); }
    .sd-process__item::after { position: absolute; top: 49px; right: -19px; width: 20px; border-top: 2px dashed rgba(255,255,255,.45); content: ''; }
    .sd-process__item:last-child::after { display: none; }
    .sd-process__number { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 50%; color: #fff; background: var(--sd-red); font-size: 12px; font-weight: 900; }
    .sd-process h3 { margin: 19px 0 0; color: #fff; font-size: 20px; line-height: 1.2; }
    .sd-process p { margin: 10px 0 0; color: rgba(255,255,255,.69); font-size: 14px; line-height: 1.7; }
    .sd-process-wrap { padding: 56px; background: var(--sd-navy); background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 64px 64px; }
    .sd-process-wrap .sd-title { color: #fff; }
    .sd-process-wrap .sd-lead { color: rgba(255,255,255,.7); }
    .sd-process-wrap .sd-kicker { color: #ff7b80; }

    .sd-results { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 42px; }
    .sd-result { min-height: 190px; padding: 28px; border: 1px solid var(--sd-line); border-radius: 22px; background: #fff; box-shadow: 0 12px 28px rgba(14,33,69,.05); }
    .sd-result__icon { display: grid; width: 43px; height: 43px; place-items: center; border-radius: 13px; color: var(--sd-red); background: #fdebed; }
    .sd-result__icon svg { width: 23px; height: 23px; fill: none; stroke: currentColor; stroke-width: 2; }
    .sd-result h3 { margin: 18px 0 0; color: var(--sd-navy); font-size: 18px; line-height: 1.3; }
    .sd-result p { margin: 9px 0 0; color: var(--sd-muted); font-size: 14px; line-height: 1.6; }

    .sd-faq-layout { display: grid; grid-template-columns: .72fr 1.28fr; align-items: start; gap: 58px; }
    .sd-faq-intro { position: sticky; top: 100px; }
    .sd-faq { display: grid; gap: 12px; }
    .sd-faq details { overflow: hidden; border: 1px solid var(--sd-line); border-radius: 17px; background: #fff; }
    .sd-faq summary { position: relative; padding: 21px 56px 21px 23px; color: var(--sd-navy); font-size: 16px; font-weight: 800; cursor: pointer; list-style: none; }
    .sd-faq summary::-webkit-details-marker { display: none; }
    .sd-faq summary::after { position: absolute; top: 18px; right: 22px; color: var(--sd-red); content: '+'; font-size: 25px; font-weight: 400; }
    .sd-faq details[open] summary::after { content: '−'; }
    .sd-faq p { padding: 0 23px 22px; color: var(--sd-muted); line-height: 1.72; }
    .sd-section#enquire { padding-top: 56px; padding-bottom: 56px; }
    .sd-cta { position: relative; overflow: hidden; padding: 23px 30px; border-radius: 20px; background: var(--sd-red); box-shadow: 0 13px 28px rgba(227,30,36,.14); }
    .sd-cta::after { position: absolute; top: -82px; right: -76px; width: 210px; height: 210px; border: 30px solid rgba(255,255,255,.12); border-radius: 50%; content: ''; }
    .sd-cta__content { position: relative; z-index: 1; max-width: 680px; }
    .sd-cta .sd-kicker { color: rgba(255,255,255,.75); }
    .sd-cta h2 { margin: 7px 0 0; color: #fff; font-size: clamp(23px, 2.25vw, 30px); line-height: 1.13; font-weight: 800; letter-spacing: -.04em; text-wrap: balance; }
    .sd-cta p { max-width: 640px; margin: 8px 0 0; color: rgba(255,255,255,.85); font-size: 13px; line-height: 1.58; }
    .sd-cta .sd-button { min-height: 42px; margin-top: 14px; padding-inline: 18px; background: #fff; color: var(--sd-navy) !important; }
    .sd-cta .sd-button:hover { color: var(--sd-red) !important; background: #fff; }

    @media (max-width: 991px) {
        .sd-hero { padding-top: 105px; }
        .sd-overview, .sd-faq-layout { grid-template-columns: 1fr; gap: 44px; }
        .sd-faq-intro { position: static; }
        .sd-overview__image { min-height: 420px; max-width: 720px; }
        .sd-process, .sd-results { grid-template-columns: repeat(2, 1fr); }
        .sd-process__item:last-child { grid-column: span 2; }
        .sd-process__item:nth-child(2)::after { display: none; }
    }
    @media (max-width: 900px) {
        .sd-hero__shell { display:block; min-height:0; }
        .sd-hero__content { width:100%; }
    }
    @media (max-width: 767px) {
        .sd-page { padding-bottom: 76px; }
        .sd-wrap { width: min(100% - 28px, 620px); }
        .sd-section { padding: 58px 0; }
        .sd-title { font-size: 32px; }
        .sd-lead { font-size: 15px; }
        .sd-hero { padding-top: 82px; }
        .sd-hero__shell { min-height: 580px; border-radius: 25px; }
        .sd-hero__overlay { background: linear-gradient(180deg, rgba(5,17,39,.2), rgba(5,17,39,.97) 72%); }
        .sd-hero__content { padding: 28px 24px 32px; }
        .sd-hero h1 { font-size: 42px; }
        .sd-hero__lead { font-size: 15px; line-height: 1.62; }
        .sd-hero__number { top: 18px; right: 18px; width: 58px; height: 58px; font-size: 15px; }
        .sd-hero__actions { display: grid; grid-template-columns: 1fr; }
        .sd-button { width: 100%; }
        .sd-quick { display: flex; overflow-x: auto; margin: 0 10px; border-radius: 0 0 21px 21px; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .sd-quick::-webkit-scrollbar { display: none; }
        .sd-quick__item { flex: 0 0 83%; border-right: 1px solid var(--sd-line) !important; scroll-snap-align: start; }
        .sd-anchor { padding: 8px 0; }
        .sd-anchor .sd-wrap { width: 100%; }
        .sd-anchor__bar { padding-inline: 14px; border-inline: 0; border-radius: 0; }
        .sd-overview__image { min-height: 355px; }
        .sd-overview__image-main { inset: 0 0 0 26px; border-radius: 22px; }
        .sd-overview__flag { bottom: 17px; width: 126px; padding: 17px 14px; border-width: 5px; border-radius: 18px; }
        .sd-overview__flag strong { font-size: 21px; }
        .sd-process-wrap { padding: 42px 14px; }
        .sd-process { display: flex; overflow-x: auto; gap: 14px; margin-top: 31px; padding: 0 2px 8px; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .sd-process::-webkit-scrollbar { display: none; }
        .sd-process__item, .sd-process__item:last-child { flex: 0 0 84%; min-height: 232px; scroll-snap-align: start; }
        .sd-process__item::after { display: none; }
        .sd-results { display: flex; overflow-x: auto; gap: 14px; margin-inline: -14px; padding: 0 14px 8px; scroll-snap-type: x mandatory; scrollbar-width: none; }
        .sd-results::-webkit-scrollbar { display: none; }
        .sd-result { flex: 0 0 84%; min-height: 178px; scroll-snap-align: start; }
        .sd-section#enquire { padding-top: 42px; padding-bottom: 42px; }
        .sd-cta { padding: 22px 20px; border-radius: 18px; }
        .sd-cta h2 { font-size: 24px; }
        .sd-cta p { font-size: 13px; }
    }
    @media (prefers-reduced-motion: reduce) { .sd-page *, .sd-page *::before, .sd-page *::after { transition-duration: .01ms !important; scroll-behavior: auto !important; } }
</style>

<main class="sd-page">
    <section class="sd-hero" id="overview">
        <div class="sd-wrap">
            <div class="sd-hero__shell">
                <img class="sd-hero__image" src="{{ asset($cms['hero_image'] ?? $service['image']) }}" alt="{{ $service['image_alt'] }}" fetchpriority="high" width="1366" height="685">
                <div class="sd-hero__overlay"></div>
                <span class="sd-hero__number" aria-label="Service {{ $service['number'] }}">{{ $service['number'] }}</span>
                <div class="sd-hero__content">
                    <nav class="sd-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><a href="{{ url('/services') }}">Services</a><span>/</span><span>{{ $service['title'] }}</span></nav>
                    <span class="sd-hero__eyebrow">{{ $cms['hero_eyebrow'] ?? $service['eyebrow'] }}</span>
                    <h1>{{ $cms['hero_title'] ?? $service['title'] }}</h1>
                    <p class="sd-hero__lead">{{ $cms['hero_copy'] ?? $service['summary'] }}</p>
                    <div class="sd-hero__actions"><a href="{{ url('/#contact') }}" class="sd-button">Book free counselling <span aria-hidden="true">→</span></a><a href="{{ $detailUrl }}#process" class="sd-button sd-button--outline">See how it works</a></div>
                </div>
                @include('mirror.partials.hero-enquiry', ['formId' => 'service-hero', 'sourceContext' => 'Service — '.$service['title'], 'returnTo' => '/services/'.$service['slug'].'#overview'])
            </div>
            <div class="sd-quick" aria-label="Service support highlights">
                <div class="sd-quick__item"><span class="sd-quick__mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4.2 4.2L19 6.5"/></svg></span><span><strong>Profile-led advice</strong><span>Guidance shaped around your goals.</span></span></div>
                <div class="sd-quick__item"><span class="sd-quick__mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4.2 4.2L19 6.5"/></svg></span><span><strong>Clear next actions</strong><span>Know what matters at every stage.</span></span></div>
                <div class="sd-quick__item"><span class="sd-quick__mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4.2 4.2L19 6.5"/></svg></span><span><strong>One connected team</strong><span>Support from Indore to your destination.</span></span></div>
            </div>
        </div>
    </section>

    <nav class="sd-anchor" aria-label="Service page sections"><div class="sd-wrap"><div class="sd-anchor__bar"><a href="{{ $detailUrl }}#overview">Overview</a><a href="{{ $detailUrl }}#gallery">In focus</a><a href="{{ $detailUrl }}#process">How it works</a><a href="{{ $detailUrl }}#outcomes">What you get</a><a href="{{ $detailUrl }}#universities">Universities</a><a href="{{ $detailUrl }}#faqs">FAQs</a><a href="{{ $detailUrl }}#enquire">Enquire</a></div></div></nav>

    <section class="sd-section" id="overview">
        <div class="sd-wrap sd-overview">
            <div class="sd-overview__copy"><div class="sd-kicker">{{ $cms['overview_eyebrow'] ?? $service['eyebrow'] }}</div><h2 class="sd-title">{{ $cms['overview_title'] ?? 'A specialist service with your whole journey in mind.' }}</h2><p>{{ $cms['overview_copy'] ?? $service['overview'] }}</p><p>{{ $cms['overview_copy_two'] ?? 'Our team keeps the advice practical, the process easy to follow and your next decision clear.' }}</p><a href="{{ $detailUrl }}#process" class="sd-button" style="margin-top: 26px">{{ $cms['overview_cta_label'] ?? 'See the process' }}</a></div>
            <div class="sd-overview__image"><div class="sd-overview__image-main"><img src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}" loading="lazy" width="1366" height="685"></div><div class="sd-overview__flag"><strong>{{ $service['number'] }}</strong><span>of 10 integrated services for your global education journey</span></div></div>
        </div>
    </section>

    <x-detail-media-gallery :images="$serviceMedia" id="gallery" eyebrow="Your study journey" title="Guidance that connects every important decision." lead="A clearer path from your current profile to a university application that feels ready to submit." />

    <section class="sd-section sd-process-wrap" id="process">
        <div class="sd-wrap"><div class="sd-center"><div class="sd-kicker">{{ $cms['process_eyebrow'] ?? 'A simple, supported process' }}</div><h2 class="sd-title">{{ $cms['process_title'] ?? 'Move forward with a plan, not a guessing game.' }}</h2><p class="sd-lead">{{ $cms['process_copy'] ?? 'The exact details differ by student and destination, but every service begins by understanding your profile and ends with a confident next step.' }}</p></div>
            <div class="sd-process">
                @foreach(array_slice($service['process'], 0, 3) as $index => [$title, $copy])
                    @php($n = $index + 1)<article class="sd-process__item"><span class="sd-process__number">{{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $cms["process_{$n}_title"] ?? $title }}</h3><p>{{ $cms["process_{$n}_copy"] ?? $copy }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sd-section sd-section--soft" id="outcomes">
        <div class="sd-wrap"><div class="sd-center"><div class="sd-kicker">{{ $cms['results_eyebrow'] ?? 'What you get' }}</div><h2 class="sd-title">{{ $cms['results_title'] ?? 'Practical help at the point it matters most.' }}</h2><p class="sd-lead">{{ $cms['results_copy'] ?? 'Focused support, clear information and a well-prepared next stage for your overseas education plan.' }}</p></div>
            <div class="sd-results">
                @foreach(array_slice($service['results'], 0, 6) as $index => $result)
                    @php($result = $cms['result_'.($index + 1)] ?? $result)
                    <article class="sd-result"><span class="sd-result__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4.2 4.2L19 6.5"/><path d="M12 3a9 9 0 1 0 9 9"/></svg></span><h3>{{ $result }}</h3><p>Guidance from a team that understands the next decision in your study-abroad journey.</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sd-section" id="universities"><div class="sd-wrap"><div class="sd-center"><div class="sd-kicker">University network</div><h2 class="sd-title">Institutions students often consider with this service.</h2><p class="sd-lead">A selection of recognised universities across the destinations we support. Your shortlist is always matched to your profile, course and intake.</p></div><x-university-network :universities="$serviceUniversities" country="Global" slug="service-{{ $service['slug'] }}" /></div></section>

    <section class="sd-section" id="faqs">
        <div class="sd-wrap sd-faq-layout"><div class="sd-faq-intro"><div class="sd-kicker">{{ $cms['faq_eyebrow'] ?? 'Common questions' }}</div><h2 class="sd-title">{{ $cms['faq_title'] ?? 'Answers before you begin.' }}</h2><p class="sd-lead">{{ $cms['faq_copy'] ?? 'Still deciding? A free counselling conversation is the easiest way to discuss your profile and next steps.' }}</p><a href="{{ url('/#contact') }}" class="sd-button" style="margin-top: 25px">{{ $cms['faq_cta_label'] ?? 'Talk to a counsellor' }}</a></div>
            <div class="sd-faq">
                @foreach(array_slice($service['faqs'], 0, 3) as $index => [$question, $answer])
                    @php($n = $index + 1)<details><summary>{{ $cms["faq_{$n}_question"] ?? $question }}</summary><p>{{ $cms["faq_{$n}_answer"] ?? $answer }}</p></details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sd-section sd-section--soft" id="enquire">
        <div class="sd-wrap"><div class="sd-cta"><div class="sd-cta__content"><div class="sd-kicker">{{ $cms['cta_eyebrow'] ?? 'Start with one conversation' }}</div><h2>{{ $cms['cta_title'] ?? 'Let’s make your '.strtolower($service['title']).' plan feel clear.' }}</h2><p>{{ $cms['cta_copy'] ?? 'Tell us your current stage, destination ideas and preferred intake. We’ll help you decide the most practical next step.' }}</p><a href="{{ url('/#contact') }}" class="sd-button">{{ $cms['cta_label'] ?? 'Book my free counselling session' }} <span aria-hidden="true">→</span></a></div></div></div>
    </section>
</main>

@include('mirror.partials.footer')
