@php
    $popupEnabled = ! in_array(strtolower(trim((string) ($cms['popup_enabled'] ?? '1'))), ['0', 'false', 'no', 'off'], true);
    $popupCtaUrl = $cms['popup_cta_url'] ?? '/contact#enquiry';
    $popupEventDate = $cms['popup_event_date'] ?? 'Coming soon · 2026';
    $popupEventLocation = $cms['popup_event_location'] ?? 'Europe & Dubai opportunities';
    $popupEventFormat = $cms['popup_event_format'] ?? 'Admission summit · Free registration';
    $popupEventHighlights = $cms['popup_event_highlights'] ?? 'Meet university representatives, explore courses and intakes, discover scholarship routes, and connect with admissions and visa experts.';
@endphp

@if($popupEnabled)
    <style>
        .tg-home-offer { position:fixed; z-index:9999; inset:0; display:grid; visibility:hidden; place-items:center; padding:20px; opacity:0; transition:opacity .22s ease,visibility .22s ease; }
        .tg-home-offer.is-open { visibility:visible; opacity:1; }
        .tg-home-offer__backdrop { position:absolute; inset:0; border:0; background:rgba(4,15,36,.68); backdrop-filter:blur(8px); cursor:pointer; }
        .tg-home-offer__dialog { position:relative; display:grid; overflow:hidden; width:min(980px,100%); max-height:min(700px,calc(100vh - 40px)); grid-template-columns:.9fr 1.1fr; border:1px solid rgba(255,255,255,.6); border-radius:30px; background:#fff; box-shadow:0 32px 90px rgba(4,15,36,.34); transform:translateY(18px) scale(.98); transition:transform .24s ease; }
        .tg-home-offer.is-open .tg-home-offer__dialog { transform:translateY(0) scale(1); }
        .tg-home-offer__media { position:relative; min-height:0; overflow:hidden; background:#fff5f2; }
        .tg-home-offer__media img { display:block; width:100%; height:100%; object-fit:contain; }
        .tg-home-offer__proof { position:absolute; z-index:1; right:18px; bottom:18px; left:18px; display:flex; align-items:center; gap:12px; padding:12px 14px; border:1px solid rgba(255,255,255,.8); border-radius:16px; background:rgba(14,33,69,.88); color:#fff; box-shadow:0 10px 24px rgba(14,33,69,.18); backdrop-filter:blur(10px); }
        .tg-home-offer__proof > .tg-home-offer__proof-icon { display:grid; width:38px; height:38px; flex:0 0 38px; place-items:center; border-radius:12px; background:#e31e24; }
        .tg-home-offer__proof-icon svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
        .tg-home-offer__proof > span:last-child > strong { display:block; font-size:13px; }
        .tg-home-offer__proof > span:last-child > span { display:block; margin-top:2px; color:rgba(255,255,255,.76); font-size:11px; }
        .tg-home-offer__content { position:relative; display:flex; min-width:0; min-height:0; flex-direction:column; justify-content:center; overflow-x:hidden; overflow-y:auto; padding:36px 42px; }
        .tg-home-offer__content::before { position:absolute; top:-138px; right:-130px; width:330px; height:330px; border:58px solid rgba(227,30,36,.06); border-radius:50%; content:''; pointer-events:none; }
        .tg-home-offer__close { position:absolute; z-index:2; top:18px; right:18px; display:grid; width:44px; height:44px; place-items:center; border:1px solid #e5eaf1; border-radius:14px; background:#fff; color:#0e2145; cursor:pointer; transition:border-color .2s ease,color .2s ease,transform .2s ease; }
        .tg-home-offer__close:hover { border-color:#f3951e; color:#f3951e; transform:rotate(4deg); }
        .tg-home-offer__close svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-width:2; }
        .tg-home-offer__eyebrow { display:inline-flex; align-items:center; align-self:flex-start; gap:10px; color:#e31e24; font-size:12px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
        .tg-home-offer__eyebrow::before { width:28px; height:2px; background:currentColor; content:''; }
        .tg-home-offer h2 { max-width:520px; margin:14px 0 0; color:#0e2145; font-size:clamp(29px,3.1vw,40px); line-height:1.06; font-weight:800; letter-spacing:-.045em; text-wrap:balance; }
        .tg-home-offer__copy { max-width:520px; margin:14px 0 0; color:#64748b; font-size:14px; line-height:1.55; }
        .tg-home-offer__event { display:grid; gap:10px; margin-top:16px; }
        .tg-home-offer__event-date { display:flex; align-items:center; gap:12px; padding:13px 15px; border:1px solid rgba(227,30,36,.18); border-radius:16px; background:#fff4f1; }
        .tg-home-offer__event-date-icon { display:grid; width:38px; height:38px; flex:0 0 38px; place-items:center; border-radius:12px; background:#e31e24; color:#fff; }
        .tg-home-offer__event-date-icon svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; }
        .tg-home-offer__event-date > div { min-width:0; }
        .tg-home-offer__event-date > div > small { display:block; color:#b05454; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .tg-home-offer__event-date > div > strong { display:block; margin-top:2px; color:#e31e24; font-size:17px; }
        .tg-home-offer__event-date > div > span { display:block; margin-top:2px; color:#7b8799; font-size:11px; }
        .tg-home-offer__event-facts { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .tg-home-offer__event-fact { padding:12px 13px; border:1px solid #e8edf3; border-radius:14px; background:#fbfcfe; }
        .tg-home-offer__event-fact small { display:block; color:#8a98aa; font-size:10px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .tg-home-offer__event-fact strong { display:block; margin-top:4px; color:#0e2145; font-size:12px; line-height:1.35; }
        .tg-home-offer__event-highlights { margin:0; color:#64748b; font-size:13px; line-height:1.55; }
        .tg-home-offer__actions { display:flex; align-items:center; flex-wrap:wrap; gap:14px; margin-top:20px; }
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
            .tg-home-offer__event { margin-top:17px; }
            .tg-home-offer__event-date strong { font-size:16px; }
            .tg-home-offer__event-facts { gap:8px; }
            .tg-home-offer__event-fact { padding:10px; }
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
                <img src="{{ asset($cms['popup_image'] ?? 'assets/transglobe/events/global-uni-expo-2026.png') }}" alt="{{ $cms['popup_image_alt'] ?? 'Global Uni Expo 2026 Europe and Dubai Admission Summit coming soon poster' }}" width="1122" height="1402">
            </div>
            <div class="tg-home-offer__content">
                <button class="tg-home-offer__close" type="button" data-offer-close aria-label="Close popup"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                <span class="tg-home-offer__eyebrow">{{ $cms['popup_eyebrow'] ?? 'Coming soon · 2026' }}</span>
                <h2 id="tg-home-offer-title">{{ $cms['popup_title'] ?? 'Global Uni Expo 2026 — Europe & Dubai Admission Summit' }}</h2>
                <p class="tg-home-offer__copy" id="tg-home-offer-copy">{{ $cms['popup_copy'] ?? 'A focused admission summit for students and parents who want a clearer route to Europe and Dubai. Meet university representatives, compare intakes and get practical next-step advice from our specialists.' }}</p>
                <div class="tg-home-offer__event" aria-label="Global Uni Expo event details">
                    <div class="tg-home-offer__event-date">
                        <span class="tg-home-offer__event-date-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="M8 13h3M8 17h5"/></svg></span>
                        <div>
                            <small>Event timing</small>
                            <strong>{{ $popupEventDate }}</strong>
                            <span>We’ll announce the exact date and registration schedule soon.</span>
                        </div>
                    </div>
                    <div class="tg-home-offer__event-facts">
                        <div class="tg-home-offer__event-fact"><small>Focus</small><strong>{{ $popupEventLocation }}</strong></div>
                        <div class="tg-home-offer__event-fact"><small>Entry</small><strong>{{ $popupEventFormat }}</strong></div>
                    </div>
                    <p class="tg-home-offer__event-highlights">{{ $popupEventHighlights }}</p>
                </div>
                <div class="tg-home-offer__actions">
                    <a href="{{ str_starts_with($popupCtaUrl, 'http') ? $popupCtaUrl : url($popupCtaUrl) }}" class="btn-flip-effect btn btn-primary btn-lg gap-8 text-white" data-text="{{ $cms['popup_cta_label'] ?? 'Register interest' }}"><span class="btn-flip-effect__text text-white">{{ $cms['popup_cta_label'] ?? 'Register interest' }}</span></a>
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
