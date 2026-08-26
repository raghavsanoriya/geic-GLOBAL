@props(['universities' => [], 'country', 'slug'])

@php
    $networkId = 'university-network-'.$slug;
    $hasMore = count($universities) > 8;
@endphp

@once
    <style>
        .university-network { margin-top:42px; }
        .university-network__grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
        .university-network__card { min-width:0; min-height:188px; padding:22px 18px 18px; border:1px solid #dfe7f1; border-radius:20px; background:#fff; box-shadow:0 12px 32px rgba(14,33,69,.06); }
        .university-network__logo { display:grid; height:104px; place-items:center; overflow:hidden; border-radius:14px; background:#f7f9fc; }
        .university-network__logo img { display:block; width:100%; height:100%; object-fit:contain; padding:14px; }
        .university-network__card h3 { margin:14px 0 0; color:#0e2145; font-size:15px; line-height:1.35; text-align:center; }
        .university-network__more { display:flex; min-height:48px; align-items:center; justify-content:center; gap:9px; margin:26px auto 0; padding:0 22px; border:0; border-radius:13px; color:#fff; background:#E31E24; font-weight:800; cursor:pointer; transition:background-color .2s ease,transform .2s ease; }
        .university-network__more:hover,.university-network__more:focus-visible { background:#F3951E; transform:translateY(-2px); }
        .university-network__more:focus-visible { outline:3px solid rgba(243,149,30,.4); outline-offset:3px; }
        .university-network__more svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-width:2; transition:transform .2s ease; }
        .university-network__more[aria-expanded="true"] svg { transform:rotate(180deg); }
        @media (max-width:991px) { .university-network__grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
        @media (max-width:767px) {
            .university-network { margin-inline:-14px; }
            .university-network__grid { display:grid; grid-auto-columns:74%; grid-template-columns:none; grid-auto-flow:column; overflow-x:auto; gap:14px; padding:0 14px 10px; scroll-snap-type:x mandatory; scrollbar-width:none; }
            .university-network__grid::-webkit-scrollbar { display:none; }
            .university-network__card { min-height:180px; scroll-snap-align:start; }
        }
        @media (prefers-reduced-motion:reduce) { .university-network__more,.university-network__more svg { transition:none; } }
    </style>
@endonce

<div class="university-network">
    <div class="university-network__grid" id="{{ $networkId }}">
        @foreach($universities as $index => $university)
            <article class="university-network__card" @if($index >= 8) hidden @endif>
                <div class="university-network__logo">
                    <img src="{{ asset($university['logo']) }}" alt="{{ $university['name'] }} logo" width="360" height="160" loading="lazy" decoding="async">
                </div>
                <h3>{{ $university['name'] }}</h3>
            </article>
        @endforeach
    </div>

    @if($hasMore)
        <button class="university-network__more" type="button" aria-expanded="false" aria-controls="{{ $networkId }}" data-university-toggle="{{ $networkId }}">
            <span>View all universities</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </button>
    @endif
</div>

@once
    <script>
        document.addEventListener('click',function(event){
            const button=event.target.closest('[data-university-toggle]');
            if(!button)return;
            const network=document.getElementById(button.dataset.universityToggle);
            const expanded=button.getAttribute('aria-expanded')==='true';
            network?.querySelectorAll('[hidden]').forEach(card=>{if(!expanded)card.removeAttribute('hidden');});
            if(expanded)Array.from(network?.children||[]).slice(8).forEach(card=>card.setAttribute('hidden',''));
            button.setAttribute('aria-expanded',String(!expanded));
            button.querySelector('span').textContent=expanded?'View all universities':'Show fewer universities';
        });
    </script>
@endonce
