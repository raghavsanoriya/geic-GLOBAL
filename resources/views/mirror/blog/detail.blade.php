@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav', ['mobileBackHref' => url('/blog'), 'mobileBackLabel' => 'Back to blog'])

@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post['title'],
        'description' => $post['excerpt'],
        'image' => [asset($cms['hero_image'] ?? $post['image'])],
        'datePublished' => \Carbon\Carbon::parse($post['date'])->toIso8601String(),
        'dateModified' => \Carbon\Carbon::parse($post['date'])->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $post['author']],
        'publisher' => ['@type' => 'Organization', 'name' => 'Trans Globe Indore | GEIC', 'url' => url('/')],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url('/blog/'.$post['slug'])],
    ];
@endphp
<script type="application/ld+json">@json($articleSchema, JSON_UNESCAPED_SLASHES)</script>

<style>
    .tg-article-page { overflow: clip; background: #f5f8fc; color: #15294d; }
    .tg-article-wrap { width: min(1120px, calc(100% - 48px)); margin-inline: auto; }
    .tg-article-hero { padding: 132px 0 0; }
    .tg-article-hero__shell { position: relative; display: grid; grid-template-columns: minmax(0, .96fr) minmax(320px, 1.04fr); overflow: hidden; min-height: 470px; border-radius: 34px; background: #0e2145; box-shadow: 0 25px 60px rgba(14,33,69,.18); }
    .tg-article-hero__copy { position: relative; z-index: 2; padding: 60px 54px; }
    .tg-article-crumbs { display: flex; flex-wrap: wrap; gap: 8px; color: rgba(255,255,255,.62); font-size: 12px; }
    .tg-article-crumbs a { color: #fff; font-weight: 700; }
    .tg-article-kicker { display: inline-flex; align-items: center; gap: 9px; margin-top: 30px; color: #ff7378; font-size: 11px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .tg-article-kicker::before { width: 27px; height: 2px; background: currentColor; content: ''; }
    .tg-article-hero h1 { max-width: 650px; margin-top: 15px; color: #fff; font-size: clamp(40px, 5vw, 62px); line-height: 1.03; font-weight: 800; letter-spacing: -.055em; text-wrap: balance; }
    .tg-article-hero__lead { max-width: 590px; margin-top: 19px; color: rgba(255,255,255,.78); font-size: 16px; line-height: 1.72; }
    .tg-article-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 25px; color: rgba(255,255,255,.65); font-size: 12px; }
    .tg-article-meta span { display: inline-flex; align-items: center; gap: 7px; }
    .tg-article-meta span + span::before { width: 4px; height: 4px; border-radius: 50%; background: #ff7378; content: ''; }
    .tg-article-hero__media { position: relative; min-height: 470px; }
    .tg-article-hero__media::after { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(14,33,69,.8), rgba(14,33,69,.04) 45%), linear-gradient(0deg, rgba(14,33,69,.45), transparent 45%); content: ''; }
    .tg-article-hero__media img { width: 100%; height: 100%; object-fit: cover; }
    .tg-article-body { padding: 72px 0 96px; }
    .tg-article-layout { display: grid; grid-template-columns: minmax(0, 1fr) 270px; align-items: start; gap: 50px; }
    .tg-article-content { padding: 40px 46px 48px; border: 1px solid #e2e8f0; border-radius: 27px; background: #fff; box-shadow: 0 12px 32px rgba(14,33,69,.06); }
    .tg-article-content > p:first-child { color: #0e2145; font-size: 19px; line-height: 1.72; font-weight: 600; }
    .tg-article-content p { margin-top: 18px; color: #5e718e; font-size: 16px; line-height: 1.82; }
    .tg-article-content h2 { margin-top: 38px; color: #0e2145; font-size: 27px; line-height: 1.2; font-weight: 800; letter-spacing: -.035em; }
    .tg-article-content h2 + p { margin-top: 11px; }
    .tg-article-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 31px; padding-top: 23px; border-top: 1px solid #edf1f5; }
    .tg-article-tags span { padding: 7px 11px; border-radius: 999px; color: #e31e24; background: #fdebed; font-size: 11px; font-weight: 800; }
    .tg-article-aside { position: sticky; top: 100px; display: grid; gap: 15px; }
    .tg-article-aside__card { padding: 22px; border: 1px solid #e2e8f0; border-radius: 21px; background: #fff; box-shadow: 0 10px 26px rgba(14,33,69,.055); }
    .tg-article-aside__card h2 { color: #0e2145; font-size: 18px; line-height: 1.25; font-weight: 800; }
    .tg-article-aside__card p { margin-top: 8px; color: #71819b; font-size: 13px; line-height: 1.6; }
    .tg-article-button { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 48px; margin-top: 17px; border-radius: 13px; color: #fff !important; background: #e31e24; font-size: 13px; font-weight: 800; transition: transform .2s ease, background .2s ease; }
    .tg-article-button:hover, .tg-article-button:focus-visible { background: #c81820; transform: translateY(-2px); }
    .tg-article-related { margin-top: 15px; }
    .tg-article-related a { display: block; padding: 13px 0; border-top: 1px solid #edf1f5; color: #0e2145; font-size: 13px; line-height: 1.35; font-weight: 700; }
    .tg-article-related a:first-of-type { margin-top: 12px; }
    .tg-article-related a:hover { color: #e31e24; }
    .tg-article-back { display: inline-flex; align-items: center; gap: 8px; margin-top: 25px; color: #e31e24; font-size: 13px; font-weight: 800; }
    @media (max-width: 991px) {
        .tg-article-hero { padding-top: 112px; }
        .tg-article-hero__shell { grid-template-columns: 1fr; }
        .tg-article-hero__media { min-height: 320px; order: -1; }
        .tg-article-layout { grid-template-columns: 1fr; }
        .tg-article-aside { position: static; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .tg-article-page { padding-bottom: 78px; }
        .tg-article-wrap { width: min(100% - 28px, 620px); }
        .tg-article-hero { padding-top: 22px; }
        .tg-article-hero__shell { border-radius: 27px; }
        .tg-article-hero__copy { padding: 28px 20px 32px; }
        .tg-article-hero__media { min-height: 235px; }
        .tg-article-hero h1 { font-size: 38px; }
        .tg-article-hero__lead { font-size: 14px; line-height: 1.6; }
        .tg-article-meta { gap: 9px; margin-top: 20px; }
        .tg-article-body { padding: 38px 0 52px; }
        .tg-article-layout { gap: 20px; }
        .tg-article-content { padding: 25px 19px 30px; border-radius: 22px; }
        .tg-article-content > p:first-child { font-size: 17px; }
        .tg-article-content p { font-size: 14px; line-height: 1.7; }
        .tg-article-content h2 { margin-top: 29px; font-size: 23px; }
        .tg-article-aside { display: grid; grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) { .tg-article-button { transition: none; } }
</style>

<main class="tg-article-page">
    <section class="tg-article-hero">
        <div class="tg-article-wrap">
            <div class="tg-article-hero__shell">
                <div class="tg-article-hero__copy">
                    <nav class="tg-article-crumbs" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><span>/</span><a href="{{ url('/blog') }}">Blog</a><span>/</span><span>{{ $post['category'] }}</span></nav>
                    <div class="tg-article-kicker">{{ $post['category'] }}</div>
                    <h1>{{ $cms['hero_title'] ?? $post['title'] }}</h1>
                    <p class="tg-article-hero__lead">{{ $cms['hero_copy'] ?? $post['excerpt'] }}</p>
                    <div class="tg-article-meta"><span>{{ $post['date'] }}</span><span>{{ $post['read_time'] }}</span><span>By {{ $post['author'] }}</span></div>
                </div>
                <div class="tg-article-hero__media"><img src="{{ asset($cms['hero_image'] ?? $post['image']) }}" alt="{{ $post['title'] }}" fetchpriority="high" width="1200" height="800"></div>
            </div>
        </div>
    </section>

    <section class="tg-article-body">
        <div class="tg-article-wrap tg-article-layout">
            <article class="tg-article-content">
                <p>{{ $post['intro'] }}</p>
                @foreach($post['sections'] as $section)
                    <h2>{{ $section['title'] }}</h2><p>{{ $section['copy'] }}</p>
                @endforeach
                <div class="tg-article-tags" aria-label="Article topics">@foreach($post['tags'] as $tag)<span>{{ $tag }}</span>@endforeach</div>
                <a class="tg-article-back" href="{{ url('/blog') }}">← Back to all articles</a>
            </article>
            <aside class="tg-article-aside">
                <div class="tg-article-aside__card"><h2>Need a plan for your profile?</h2><p>Bring your questions to a Trans Globe Indore counsellor and turn this guidance into a practical next step.</p><a class="tg-article-button" href="{{ url('/contact#enquiry') }}">Book free counselling</a></div>
                <div class="tg-article-aside__card tg-article-related"><h2>Keep exploring</h2>@foreach(array_slice($relatedPosts, 0, 3) as $related)<a href="{{ url('/blog/'.$related['slug']) }}">{{ $related['title'] }}</a>@endforeach</div>
            </aside>
        </div>
    </section>
</main>

@include('mirror.partials.footer')
