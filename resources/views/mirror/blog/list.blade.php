@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav')

@php
    $categories = collect($posts)->pluck('category')->unique()->values();
@endphp

<style>
    .tg-blog-page { min-height: 100vh; overflow: clip; background: #f5f8fc; color: #15294d; }
    .tg-blog-wrap { width: min(1220px, calc(100% - 48px)); margin-inline: auto; }
    .tg-blog-hero { padding: 132px 0 74px; background: #0e2145; background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px); background-size: 15px 15px; }
    .tg-blog-hero__inner { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr); align-items: center; gap: 60px; }
    .tg-blog-eyebrow { display: inline-flex; align-items: center; gap: 10px; color: #ff7378; font-size: 12px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .tg-blog-eyebrow::before { width: 28px; height: 2px; background: currentColor; content: ''; }
    .tg-blog-hero h1 { max-width: 700px; margin-top: 17px; color: #fff; font-size: clamp(42px, 5.5vw, 68px); line-height: 1.02; font-weight: 800; letter-spacing: -.055em; text-wrap: balance; }
    .tg-blog-hero h1 span { color: #ff565d; }
    .tg-blog-hero p { max-width: 640px; margin-top: 20px; color: rgba(255,255,255,.74); font-size: 17px; line-height: 1.7; }
    .tg-blog-hero__note { position: relative; padding: 28px; border: 1px solid rgba(255,255,255,.15); border-radius: 24px; background: rgba(255,255,255,.08); box-shadow: 0 18px 42px rgba(0,0,0,.12); }
    .tg-blog-hero__note::after { position: absolute; right: -70px; bottom: -95px; width: 210px; height: 210px; border: 30px solid rgba(255,255,255,.08); border-radius: 50%; content: ''; }
    .tg-blog-hero__note strong { display: block; color: #fff; font-size: 22px; }
    .tg-blog-hero__note p { margin-top: 8px; color: rgba(255,255,255,.66); font-size: 14px; line-height: 1.6; }
    .tg-blog-hero__stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 20px; }
    .tg-blog-hero__stats div { padding: 14px; border-radius: 15px; background: rgba(255,255,255,.08); }
    .tg-blog-hero__stats b { display: block; color: #fff; font-size: 23px; }
    .tg-blog-hero__stats span { display: block; margin-top: 3px; color: rgba(255,255,255,.6); font-size: 11px; }
    .tg-blog-list { padding: 72px 0 96px; }
    .tg-blog-toolbar { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; }
    .tg-blog-toolbar h2 { color: #0e2145; font-size: 38px; line-height: 1.15; font-weight: 800; letter-spacing: -.04em; }
    .tg-blog-toolbar p { max-width: 580px; margin-top: 10px; color: #71819b; font-size: 15px; line-height: 1.65; }
    .tg-blog-search { position: relative; width: min(100%, 330px); flex: 0 0 auto; }
    .tg-blog-search svg { position: absolute; top: 50%; left: 16px; width: 19px; height: 19px; color: #71819b; transform: translateY(-50%); pointer-events: none; }
    .tg-blog-search input { width: 100%; height: 52px; padding: 0 15px 0 47px; border: 1px solid #dbe3ed; border-radius: 14px; color: #0e2145; background: #fff; font: inherit; font-size: 14px; box-shadow: 0 8px 24px rgba(14,33,69,.05); }
    .tg-blog-search input:focus { border-color: #e31e24; outline: 0; box-shadow: 0 0 0 4px rgba(227,30,36,.11); }
    .tg-blog-search .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    .tg-blog-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 25px; }
    .tg-blog-filter { min-height: 38px; padding: 8px 14px; border: 1px solid #dbe3ed; border-radius: 999px; color: #53657f; background: #fff; font: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
    .tg-blog-filter:hover, .tg-blog-filter.is-active { border-color: #e31e24; color: #fff; background: #e31e24; }
    .tg-blog-count { margin-top: 18px; color: #8a98aa; font-size: 12px; }
    .tg-blog-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; margin-top: 26px; }
    .tg-blog-card { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 24px; background: #fff; box-shadow: 0 12px 34px rgba(14,33,69,.07); transition: transform .22s ease, box-shadow .22s ease; }
    .tg-blog-card:hover { transform: translateY(-5px); box-shadow: 0 20px 45px rgba(14,33,69,.13); }
    .tg-blog-card[hidden] { display: none; }
    /* Keep list thumbnails compact and consistently 2:1 across every grid size. */
    .tg-blog-card__media { position: relative; width: 100%; aspect-ratio: 2 / 1; overflow: hidden; background: #edf2f7; }
    .tg-blog-card__image { position: absolute; inset: 0; display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .tg-blog-card:hover .tg-blog-card__image { transform: scale(1.04); }
    .tg-blog-card__body { display: block; padding: 20px 21px 22px; }
    .tg-blog-card__meta { display: flex; align-items: center; justify-content: space-between; gap: 10px; color: #e31e24; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .tg-blog-card__read { color: #8a98aa; font-size: 11px; font-weight: 600; letter-spacing: 0; text-transform: none; }
    .tg-blog-card h3 { margin-top: 11px; color: #0e2145; font-size: 22px; line-height: 1.2; font-weight: 800; letter-spacing: -.035em; }
    .tg-blog-card p { margin-top: 10px; color: #71819b; font-size: 13px; line-height: 1.65; }
    .tg-blog-card__link { display: inline-flex; align-items: center; gap: 8px; margin-top: 17px; color: #e31e24; font-size: 13px; font-weight: 800; }
    .tg-blog-card__link span { transition: transform .2s ease; }
    .tg-blog-card:hover .tg-blog-card__link span { transform: translateX(4px); }
    .tg-blog-empty { display: none; margin-top: 28px; padding: 34px; border: 1px dashed #cbd6e3; border-radius: 20px; color: #71819b; background: #fff; text-align: center; }
    .tg-blog-empty.is-visible { display: block; }
    @media (max-width: 991px) {
        .tg-blog-hero { padding: 112px 0 58px; }
        .tg-blog-hero__inner { grid-template-columns: 1fr; gap: 30px; }
        .tg-blog-hero__note { max-width: 560px; }
        .tg-blog-toolbar { align-items: flex-start; flex-direction: column; }
        .tg-blog-search { width: 100%; }
        .tg-blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .tg-blog-page { padding-bottom: 78px; }
        .tg-blog-wrap { width: min(100% - 28px, 620px); }
        .tg-blog-hero { padding: 22px 0 35px; background: transparent; }
        .tg-blog-hero__inner { gap: 20px; }
        .tg-blog-hero__content, .tg-blog-hero__note { padding: 25px 20px; border-radius: 24px; background: #0e2145; }
        .tg-blog-hero h1 { font-size: 38px; }
        .tg-blog-hero p { font-size: 14px; line-height: 1.6; }
        .tg-blog-hero__note { padding: 20px; }
        .tg-blog-hero__note::after { right: -110px; bottom: -120px; }
        .tg-blog-hero__stats b { font-size: 20px; }
        .tg-blog-list { margin: 12px 10px; padding: 32px 0 40px; border-radius: 28px; background: #fff; box-shadow: 0 10px 30px rgba(14,33,69,.055); }
        .tg-blog-toolbar h2 { font-size: 30px; }
        .tg-blog-toolbar p { font-size: 14px; }
        .tg-blog-filters { margin-top: 19px; }
        .tg-blog-grid { gap: 14px; margin-top: 20px; }
        .tg-blog-card { border-radius: 18px; }
        .tg-blog-card__body { padding: 14px; }
        .tg-blog-card h3 { font-size: 17px; }
        .tg-blog-card p { font-size: 12px; line-height: 1.55; }
        .tg-blog-card__link { font-size: 12px; }
    }
    @media (max-width: 390px) {
        .tg-blog-grid { grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) { .tg-blog-card, .tg-blog-card__image, .tg-blog-card__link span { transition: none; } .tg-blog-card:hover { transform: none; } }
</style>

<main class="tg-blog-page">
    <section class="tg-blog-hero">
        <div class="tg-blog-wrap tg-blog-hero__inner">
            <div class="tg-blog-hero__content">
                <div class="tg-blog-eyebrow">Trans Globe journal</div>
                <h1>{{ $cms['hero_title'] ?? 'Clearer answers for your global future.' }}</h1>
                <p>{{ $cms['hero_copy'] ?? 'Practical study-abroad guidance on destinations, admissions, scholarships, tests and student visas—written for Indian students and families.' }}</p>
            </div>
            <aside class="tg-blog-hero__note">
                <strong>Make your next decision with confidence.</strong>
                <p>Save the articles that help you compare options, prepare your documents and plan the right intake.</p>
                <div class="tg-blog-hero__stats"><div><b>{{ count($posts) }}</b><span>fresh guides</span></div><div><b>{{ $categories->count() }}</b><span>topics to explore</span></div></div>
            </aside>
        </div>
    </section>

    <section class="tg-blog-list" id="articles">
        <div class="tg-blog-wrap">
            <div class="tg-blog-toolbar">
                <div><h2>{{ $cms['list_title'] ?? 'Explore the latest articles' }}</h2><p>{{ $cms['list_copy'] ?? 'Useful context for choosing your destination, preparing a strong application and moving forward with a clear plan.' }}</p></div>
                <label class="tg-blog-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 5 5" stroke-linecap="round"/></svg><span class="sr-only">Search articles</span><input id="tgBlogSearch" type="search" placeholder="Search articles" autocomplete="off"></label>
            </div>
            <div class="tg-blog-filters" role="list" aria-label="Filter articles"><button class="tg-blog-filter is-active" type="button" data-blog-filter="all">All articles</button>@foreach($categories as $category)<button class="tg-blog-filter" type="button" data-blog-filter="{{ strtolower($category) }}">{{ $category }}</button>@endforeach</div>
            <div class="tg-blog-count" id="tgBlogCount">Showing {{ count($posts) }} articles</div>
            <div class="tg-blog-grid" id="tgBlogGrid">
                @foreach($posts as $post)
                    <article class="tg-blog-card" data-blog-card data-category="{{ strtolower($post['category']) }}" data-search="{{ strtolower($post['title'].' '.$post['excerpt'].' '.$post['category']) }}">
                        <a href="{{ url('/blog/'.$post['slug']) }}" aria-label="Read {{ $post['title'] }}">
                            <div class="tg-blog-card__media">
                                <img class="tg-blog-card__image" src="{{ asset($post['image']) }}" alt="{{ $post['title'] }}" loading="lazy" width="1200" height="800">
                            </div>
                            <div class="tg-blog-card__body">
                                <div class="tg-blog-card__meta"><span>{{ $post['category'] }}</span><span class="tg-blog-card__read">{{ $post['read_time'] }}</span></div>
                                <h3>{{ $post['title'] }}</h3>
                                <p>{{ $post['excerpt'] }}</p>
                                <span class="tg-blog-card__link">Read article <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
            <div class="tg-blog-empty" id="tgBlogEmpty">No articles match that search. Try another topic or clear the search.</div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('tgBlogSearch');
    const cards = Array.from(document.querySelectorAll('[data-blog-card]'));
    const filters = Array.from(document.querySelectorAll('[data-blog-filter]'));
    const count = document.getElementById('tgBlogCount');
    const empty = document.getElementById('tgBlogEmpty');
    let category = 'all';
    function update() {
        const term = (input?.value || '').trim().toLowerCase();
        let visible = 0;
        cards.forEach(card => {
            const matchesCategory = category === 'all' || card.dataset.category === category;
            const matchesTerm = !term || card.dataset.search.includes(term);
            const show = matchesCategory && matchesTerm;
            card.hidden = !show;
            if (show) visible++;
        });
        if (count) count.textContent = `Showing ${visible} article${visible === 1 ? '' : 's'}`;
        empty?.classList.toggle('is-visible', visible === 0);
    }
    filters.forEach(button => button.addEventListener('click', () => { category = button.dataset.blogFilter || 'all'; filters.forEach(item => item.classList.toggle('is-active', item === button)); update(); }));
    input?.addEventListener('input', update);
});
</script>

@include('mirror.partials.footer')
