@php
    $popupEnabled = ! in_array(strtolower(trim((string) ($cms['popup_enabled'] ?? '1'))), ['0', 'false', 'no', 'off'], true);
    $popupCtaUrl = $cms['popup_cta_url'] ?? '/contact#enquiry';
@endphp

@if($popupEnabled)
    <style>
        .tg-home-offer { position:fixed; z-index:9999; inset:0; display:grid; visibility:hidden; place-items:center; padding:20px; opacity:0; transition:opacity .22s ease,visibility .22s ease; }
        .tg-home-offer.is-open { visibility:visible; opacity:1; }
        .tg-home-offer__backdrop { position:absolute; inset:0; border:0; background:rgba(4,15,36,.68); backdrop-filter:blur(8px); cursor:pointer; }
        .tg-home-offer__dialog { position:relative; display:grid; overflow:hidden; width:min(900px,100%); grid-template-columns:.92fr 1.08fr; border:1px solid rgba(255,255,255,.6); border-radius:30px; background:#fff; box-shadow:0 32px 90px rgba(4,15,36,.34); transform:translateY(18px) scale(.98); transition:transform .24s ease; }
        .tg-home-offer.is-open .tg-home-offer__dialog { transform:translateY(0) scale(1); }
        .tg-home-offer__media { position:relative; min-height:470px; overflow:hidden; background:#0e2145; }
        .tg-home-offer__media::after { position:absolute; inset:0; background:linear-gradient(180deg,rgba(14,33,69,.02),rgba(14,33,69,.72)); content:''; }
        .tg-home-offer__media img { display:block; width:100%; height:100%; object-fit:cover; }
        .tg-home-offer__proof { position:absolute; z-index:1; right:22px; bottom:22px; left:22px; display:flex; align-items:center; gap:12px; padding:15px 16px; border:1px solid rgba(255,255,255,.24); border-radius:16px; background:rgba(14,33,69,.72); color:#fff; backdrop-filter:blur(10px); }
        .tg-home-offer__proof > .tg-home-offer__proof-icon { display:grid; width:40px; height:40px; flex:0 0 40px; place-items:center; border-radius:12px; background:#e31e24; }
        .tg-home-offer__proof-icon svg { width:21px; height:21px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
        .tg-home-offer__proof > span:last-child > strong { display:block; font-size:14px; }
        .tg-home-offer__proof > span:last-child > span { display:block; margin-top:2px; color:rgba(255,255,255,.72); font-size:12px; }
        .tg-home-offer__content { position:relative; display:flex; min-width:0; flex-direction:column; justify-content:center; padding:54px 48px; }
        .tg-home-offer__content::before { position:absolute; top:-138px; right:-130px; width:330px; height:330px; border:58px solid rgba(227,30,36,.06); border-radius:50%; content:''; pointer-events:none; }
        .tg-home-offer__close { position:absolute; z-index:2; top:18px; right:18px; display:grid; width:44px; height:44px; place-items:center; border:1px solid #e5eaf1; border-radius:14px; background:#fff; color:#0e2145; cursor:pointer; transition:border-color .2s ease,color .2s ease,transform .2s ease; }
        .tg-home-offer__close:hover { border-color:#f3951e; color:#f3951e; transform:rotate(4deg); }
        .tg-home-offer__close svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-width:2; }
        .tg-home-offer__eyebrow { display:inline-flex; align-items:center; align-self:flex-start; gap:10px; color:#e31e24; font-size:12px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
        .tg-home-offer__eyebrow::before { width:28px; height:2px; background:currentColor; content:''; }
        .tg-home-offer h2 { max-width:470px; margin:17px 0 0; color:#0e2145; font-size:clamp(31px,3.4vw,45px); line-height:1.08; font-weight:800; letter-spacing:-.045em; text-wrap:balance; }
        .tg-home-offer__copy { max-width:490px; margin:18px 0 0; color:#64748b; font-size:15px; line-height:1.7; }
        .tg-home-offer__actions { display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin-top:28px; }
        .tg-home-offer__later { min-height:48px; padding:0 8px; border:0; background:transparent; color:#64748b; font-size:13px; font-weight:700; cursor:pointer; }
        .tg-home-offer__later:hover { color:#f3951e; }
        .tg-home-offer :focus-visible { outline:3px solid rgba(243,149,30,.45); outline-offset:3px; }
        body.tg-home-offer-lock { overflow:hidden; }
        @media (max-width:767px) {
            .tg-home-offer { align-items:end; padding:12px 12px calc(88px + env(safe-area-inset-bottom)); }
            .tg-home-offer__dialog { max-height:min(760px,calc(100vh - 112px)); grid-template-columns:1fr; overflow-y:auto; border-radius:25px; }
            .tg-home-offer__media { min-height:210px; max-height:260px; }
            .tg-home-offer__proof { right:14px; bottom:14px; left:14px; padding:12px; }
            .tg-home-offer__content { padding:31px 23px 27px; }
            .tg-home-offer__close { top:12px; right:12px; background:rgba(255,255,255,.92); }
            .tg-home-offer h2 { padding-right:12px; font-size:31px; }
            .tg-home-offer__copy { font-size:14px; }
            .tg-home-offer__actions { display:grid; grid-template-columns:1fr; gap:7px; margin-top:23px; }
            .tg-home-offer__actions .btn { width:100%; }
            .tg-home-offer__later { width:100%; }
        }
        @media (prefers-reduced-motion:reduce) { .tg-home-offer,.tg-home-offer__dialog,.tg-home-offer__close { transition:none!important; } }
    </style>

    <div class="tg-home-offer" id="tg-home-offer" aria-hidden="true">
        <button class="tg-home-offer__backdrop" type="button" data-offer-close aria-label="Close counselling offer"></button>
        <section class="tg-home-offer__dialog" role="dialog" aria-modal="true" aria-labelledby="tg-home-offer-title" aria-describedby="tg-home-offer-copy" tabindex="-1">
            <div class="tg-home-offer__media">
                <img src="{{ asset($cms['popup_image'] ?? 'assets/transglobe/services/services-team.avif') }}" alt="{{ $cms['popup_image_alt'] ?? 'Trans Globe Indore counsellor helping a student plan their study-abroad journey' }}" width="768" height="768">
                <div class="tg-home-offer__proof"><span class="tg-home-offer__proof-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span><span><strong>Free, no-pressure guidance</strong><span>Profile-first advice from our Indore team</span></span></div>
            </div>
            <div class="tg-home-offer__content">
                <button class="tg-home-offer__close" type="button" data-offer-close aria-label="Close popup"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                <span class="tg-home-offer__eyebrow">{{ $cms['popup_eyebrow'] ?? 'Free profile review' }}</span>
                <h2 id="tg-home-offer-title">{{ $cms['popup_title'] ?? 'Your global education plan starts with one clear conversation.' }}</h2>
                <p class="tg-home-offer__copy" id="tg-home-offer-copy">{{ $cms['popup_copy'] ?? 'Tell our Indore counsellors where you are today. We will help you compare destinations, courses, scholarships and visa pathways without pressure.' }}</p>
                <div class="tg-home-offer__actions">
                    <a href="{{ str_starts_with($popupCtaUrl, 'http') ? $popupCtaUrl : url($popupCtaUrl) }}" class="btn-flip-effect btn btn-primary btn-lg gap-8 text-white" data-text="{{ $cms['popup_cta_label'] ?? 'Book Free Counselling' }}"><span class="btn-flip-effect__text text-white">{{ $cms['popup_cta_label'] ?? 'Book Free Counselling' }}</span></a>
                    <button class="tg-home-offer__later" type="button" data-offer-close>{{ $cms['popup_close_label'] ?? 'Maybe later' }}</button>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const offer = document.getElementById('tg-home-offer');
            if (!offer) return;

            const dialog = offer.querySelector('[role="dialog"]');
            const closeButtons = Array.from(offer.querySelectorAll('[data-offer-close]'));
            const storageKey = 'tg-home-offer-seen-v1';
            const previewRequested = new URLSearchParams(window.location.search).get('preview-popup') === '1';
            let lastFocused = null;

            function hasSeenOffer() {
                try { return window.sessionStorage.getItem(storageKey) === '1'; } catch (error) { return false; }
            }

            function rememberOffer() {
                try { window.sessionStorage.setItem(storageKey, '1'); } catch (error) {}
            }

            function openOffer() {
                lastFocused = document.activeElement;
                offer.classList.add('is-open');
                offer.setAttribute('aria-hidden', 'false');
                document.body.classList.add('tg-home-offer-lock');
                window.setTimeout(function () { dialog.focus(); }, 20);
            }

            function closeOffer() {
                rememberOffer();
                offer.classList.remove('is-open');
                offer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('tg-home-offer-lock');
                if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
            }

            closeButtons.forEach(function (button) { button.addEventListener('click', closeOffer); });
            offer.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeOffer();
                if (event.key !== 'Tab') return;
                const focusable = Array.from(dialog.querySelectorAll('a[href],button:not([disabled]),[tabindex]:not([tabindex="-1"])'));
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            });

            if (previewRequested || !hasSeenOffer()) window.setTimeout(openOffer, previewRequested ? 0 : 1400);
        });
    </script>
@endif
