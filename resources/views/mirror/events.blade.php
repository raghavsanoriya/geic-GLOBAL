@include('mirror.partials.header')
@include('mirror.partials.mobile-destination-nav')

@php
    $currentEvent = collect($events)->firstWhere('status', 'Upcoming');
    $pastEvents = collect($events)->where('status', 'Past event');
    $heroTitle = $cms['hero_title'] ?? 'Meet universities. Find your next move.';
    $heroCopy = $cms['hero_copy'] ?? 'Discover Trans Globe events, university visits and admission days that turn study-abroad research into useful conversations and clear next steps.';
    $heroImage = $cms['hero_image'] ?? $currentEvent['image'];
@endphp

<style>
@font-face{font-family:'Plus Jakarta Sans';src:url('{{ asset('assets/fonts/plus-jakarta-sans-latin.woff2') }}') format('woff2');font-style:normal;font-weight:200 800;font-display:swap}
.ev-page{--navy:#0e2145;--red:#e31e24;--orange:#f3951e;--soft:#f3f7fb;--line:#dfe7f1;color:var(--navy);font-family:'Plus Jakarta Sans',sans-serif;background:#fff}
.ev-page *{box-sizing:border-box}.ev-wrap{width:min(1180px,calc(100% - 40px));margin:auto}.ev-hero{padding:28px 0 72px;background:var(--soft)}
.ev-hero__panel{min-height:530px;border-radius:34px;overflow:hidden;position:relative;display:flex;align-items:flex-end;background:center/cover no-repeat;box-shadow:0 22px 55px rgba(14,33,69,.16)}
.ev-hero__panel:after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(5,18,46,.96) 0%,rgba(5,18,46,.78) 48%,rgba(5,18,46,.18) 100%)}
.ev-hero__content{position:relative;z-index:1;width:min(680px,100%);padding:64px;color:#fff}.ev-kicker{display:flex;align-items:center;gap:12px;color:#ff7378;text-transform:uppercase;letter-spacing:.14em;font-size:12px;font-weight:800}.ev-kicker:before{content:'';width:30px;height:2px;background:currentColor}
.ev-hero h1{font-size:clamp(40px,5.5vw,72px);line-height:1.02;letter-spacing:-.045em;margin:18px 0 20px;color:#fff}.ev-hero p{font-size:18px;line-height:1.75;color:rgba(255,255,255,.8);margin:0}.ev-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:30px}
.ev-button{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:52px;padding:0 24px;border-radius:13px;background:var(--red);color:#fff!important;font-weight:800;text-decoration:none;transition:.2s ease}.ev-button:hover,.ev-button:focus-visible{background:var(--orange);transform:translateY(-2px)}.ev-button--ghost{background:#fff;color:var(--navy)!important}.ev-button--ghost:hover{color:#fff!important}
.ev-current{margin-top:-38px;position:relative;z-index:2}.ev-current__card{display:grid;grid-template-columns:1.05fr .95fr;background:#fff;border:1px solid var(--line);border-radius:28px;overflow:hidden;box-shadow:0 18px 50px rgba(14,33,69,.1)}.ev-current__image{min-height:360px}.ev-current__image img{width:100%;height:100%;object-fit:cover}.ev-current__body{padding:42px}.ev-pill{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:9px 13px;background:#fff0f0;color:var(--red);font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.ev-pill:before{content:'';width:8px;height:8px;border-radius:50%;background:currentColor}.ev-current h2{font-size:clamp(28px,3vw,42px);line-height:1.1;letter-spacing:-.035em;margin:20px 0 14px}.ev-meta{display:flex;flex-wrap:wrap;gap:10px;margin:22px 0}.ev-meta span{padding:9px 12px;border-radius:10px;background:var(--soft);font-size:13px;font-weight:700}.ev-current p,.ev-card p{color:#667695;line-height:1.7}
.ev-section{padding:92px 0}.ev-section--soft{background:var(--soft)}.ev-head{display:flex;align-items:end;justify-content:space-between;gap:30px;margin-bottom:34px}.ev-head h2{font-size:clamp(32px,4vw,48px);letter-spacing:-.04em;line-height:1.1;margin:10px 0 0}.ev-head p{max-width:560px;color:#667695;line-height:1.7;margin:0}.ev-filter{display:flex;gap:12px;margin:0 0 30px}.ev-filter input{width:100%;border:1px solid var(--line);border-radius:14px;padding:16px 18px;color:var(--navy);font:inherit;outline:none}.ev-filter input:focus{border-color:var(--red);box-shadow:0 0 0 4px rgba(227,30,36,.1)}
.ev-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}.ev-card{background:#fff;border:1px solid var(--line);border-radius:24px;overflow:hidden;display:flex;flex-direction:column;transition:.25s ease}.ev-card:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(14,33,69,.1)}.ev-card__media{height:220px;position:relative;overflow:hidden}.ev-card__media img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}.ev-card:hover img{transform:scale(1.035)}.ev-card__badge{position:absolute;top:16px;left:16px;background:#fff;color:var(--red);font-size:11px;font-weight:800;text-transform:uppercase;padding:8px 11px;border-radius:999px}.ev-card__body{padding:24px;display:flex;flex-direction:column;flex:1}.ev-card h3{font-size:21px;line-height:1.3;margin:12px 0}.ev-card__date{font-size:12px;font-weight:800;color:var(--red);text-transform:uppercase;letter-spacing:.08em}.ev-card__link{margin-top:auto;padding-top:18px;border-top:1px solid var(--line);color:var(--red);font-weight:800;text-decoration:none;display:flex;justify-content:space-between}.ev-card__link:hover{color:var(--orange)}
.ev-trust{background:var(--navy);color:#fff;border-radius:32px;padding:50px}.ev-trust__grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:30px}.ev-trust__item{border:1px solid rgba(255,255,255,.16);border-radius:18px;padding:24px}.ev-trust__icon{width:46px;height:46px;border-radius:14px;background:var(--red);display:grid;place-items:center;font-weight:800}.ev-trust h2{color:#fff;font-size:38px;margin:12px 0}.ev-trust h3{color:#fff;font-size:17px;margin:18px 0 8px}.ev-trust p{color:rgba(255,255,255,.66);font-size:14px;line-height:1.6;margin:0}
.ev-cta{display:flex;align-items:center;justify-content:space-between;gap:30px;padding:46px;border-radius:28px;background:var(--red);color:#fff}.ev-cta h2{font-size:clamp(30px,4vw,48px);color:#fff;margin:0 0 10px}.ev-cta p{margin:0;color:rgba(255,255,255,.8)}
.ev-empty{display:none;padding:28px;border:1px dashed var(--line);border-radius:18px;text-align:center;color:#667695}
@media(max-width:991px){.ev-current__card{grid-template-columns:1fr}.ev-grid{grid-template-columns:repeat(2,1fr)}.ev-trust__grid{grid-template-columns:repeat(2,1fr)}.ev-head{align-items:start;flex-direction:column}.ev-hero__panel:after{background:linear-gradient(0deg,rgba(5,18,46,.96),rgba(5,18,46,.42))}}
@media(max-width:767px){.ev-wrap{width:min(100% - 24px,1180px)}.ev-hero{padding:12px 0 52px}.ev-hero__panel{min-height:580px;border-radius:26px}.ev-hero__content{padding:28px}.ev-hero h1{font-size:42px}.ev-hero p{font-size:15px}.ev-current{margin-top:-30px}.ev-current__body{padding:26px}.ev-current__image{min-height:250px}.ev-section{padding:64px 0}.ev-grid{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:14px;padding-bottom:12px}.ev-card{min-width:84vw;scroll-snap-align:start}.ev-trust{padding:28px;border-radius:26px}.ev-trust__grid{display:flex;overflow-x:auto;scroll-snap-type:x mandatory}.ev-trust__item{min-width:78vw;scroll-snap-align:start}.ev-cta{align-items:flex-start;flex-direction:column;padding:30px}.ev-button{width:100%}}
@media(prefers-reduced-motion:reduce){.ev-page *{scroll-behavior:auto!important;transition:none!important}.ev-card:hover,.ev-button:hover{transform:none}}
</style>

<main class="ev-page">
    <section class="ev-hero">
        <div class="ev-wrap">
            <div class="ev-hero__panel" style="background-image:url('{{ asset($heroImage) }}')">
                <div class="ev-hero__content"><span class="ev-kicker">Events & updates</span><h1>{{ $heroTitle }}</h1><p>{{ $heroCopy }}</p><div class="ev-actions"><a class="ev-button" href="#upcoming">See upcoming event <span aria-hidden="true">↓</span></a><a class="ev-button ev-button--ghost" href="{{ url('/contact#enquiry') }}">Ask our Indore team</a></div></div>
            </div>
        </div>
    </section>

    @if($currentEvent)
    <section class="ev-current" id="upcoming"><div class="ev-wrap"><article class="ev-current__card"><div class="ev-current__image"><img src="{{ asset($currentEvent['image']) }}" alt="{{ $currentEvent['image_alt'] }}"></div><div class="ev-current__body"><span class="ev-pill">{{ $currentEvent['status'] }}</span><h2>{{ $currentEvent['title'] }}</h2><div class="ev-meta"><span>{{ $currentEvent['date'] }}</span><span>{{ $currentEvent['time'] }}</span><span>{{ $currentEvent['destination'] }}</span></div><p>{{ $currentEvent['summary'] }}</p><a class="ev-button" href="{{ url('/events/'.$currentEvent['slug']) }}">View event details <span aria-hidden="true">→</span></a></div></article></div></section>
    @endif

    <section class="ev-section"><div class="ev-wrap"><div class="ev-head"><div><span class="ev-kicker">Event archive</span><h2>{{ $cms['archive_title'] ?? 'Event highlights from Indore and beyond' }}</h2></div><p>{{ $cms['archive_copy'] ?? 'Explore recent university visits, admission days and expos from the Trans Globe network.' }}</p></div><div class="ev-filter"><label class="sr-only" for="event-search">Search events</label><input id="event-search" type="search" placeholder="Search by event, country or university" data-event-search></div><div class="ev-grid" data-event-grid>
        @foreach($pastEvents as $item)
            <article class="ev-card" data-event-card data-search="{{ strtolower($item['title'].' '.$item['destination']) }}"><div class="ev-card__media"><img src="{{ asset($item['image']) }}" alt="{{ $item['image_alt'] }}" loading="lazy"><span class="ev-card__badge">{{ $item['destination'] }}</span></div><div class="ev-card__body"><span class="ev-card__date">{{ $item['date'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['summary'] }}</p><a class="ev-card__link" href="{{ url('/events/'.$item['slug']) }}"><span>Explore event</span><span aria-hidden="true">→</span></a></div></article>
        @endforeach
    </div><p class="ev-empty" data-event-empty>No events match your search.</p></div></section>

    <section class="ev-section ev-section--soft"><div class="ev-wrap"><div class="ev-trust"><span class="ev-kicker">Why attend with us</span><h2>Expert guidance around every conversation.</h2><div class="ev-trust__grid">
        @foreach([['10+','Expert Counsellors','An experienced education team helps you ask better questions.'],['800+','Partner Universities','Access to a broad international institution network.'],['98%','Visa Success Rate','Careful preparation supports stronger visa outcomes.'],['1:1','Personalised Guidance','Advice is tailored to your profile and career goals.']] as $proof)
            <article class="ev-trust__item"><span class="ev-trust__icon">{{ $proof[0] }}</span><h3>{{ $proof[1] }}</h3><p>{{ $proof[2] }}</p></article>
        @endforeach
    </div></div></div></section>

    <section class="ev-section"><div class="ev-wrap"><div class="ev-cta"><div><h2>Missed an event?</h2><p>Tell us what you wanted to explore. Our Indore counsellors can help you plan the next useful conversation.</p></div><a class="ev-button ev-button--ghost" href="{{ url('/contact#enquiry') }}">Speak to a counsellor <span aria-hidden="true">→</span></a></div></div></section>
</main>

<script>
document.addEventListener('DOMContentLoaded',function(){const input=document.querySelector('[data-event-search]');if(!input)return;const cards=[...document.querySelectorAll('[data-event-card]')];const empty=document.querySelector('[data-event-empty]');input.addEventListener('input',function(){const query=this.value.trim().toLowerCase();let shown=0;cards.forEach(card=>{const match=!query||card.dataset.search.includes(query);card.hidden=!match;if(match)shown++});empty.style.display=shown?'none':'block'})});
</script>

@include('mirror.partials.footer')
