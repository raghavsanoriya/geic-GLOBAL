@props(['images' => [], 'id' => 'gallery', 'eyebrow' => 'A closer look', 'title' => 'See the journey from another angle.', 'lead' => null])

@once
    <style>
        .detail-media { padding: 90px 0; background: #f4f7fb; scroll-margin-top: 96px; }
        .detail-media__wrap { width: min(1280px, calc(100% - 48px)); margin-inline: auto; }
        .detail-media__intro { max-width: 760px; }
        .detail-media__eyebrow { display: inline-flex; align-items: center; gap: 10px; color: #e31e24; font-size: 12px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .detail-media__eyebrow::before { width: 28px; height: 2px; background: currentColor; content: ''; }
        .detail-media h2 { max-width: 700px; margin: 14px 0 0; color: #0e2145; font-size: clamp(34px, 4vw, 50px); line-height: 1.1; letter-spacing: -.045em; }
        .detail-media__lead { max-width: 700px; margin: 16px 0 0; color: #64748b; font-size: 16px; line-height: 1.75; }
        .detail-media__grid { display: grid; grid-template-columns: 1.14fr .86fr .86fr; gap: 16px; margin-top: 42px; }
        .detail-media__card { position: relative; min-height: 280px; overflow: hidden; border-radius: 24px; background: #0e2145; box-shadow: 0 14px 34px rgba(14,33,69,.1); }
        .detail-media__card:first-child { min-height: 360px; }
        .detail-media__card img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s ease; }
        .detail-media__card:hover img { transform: scale(1.035); }
        .detail-media__card::after { position: absolute; inset: auto 0 0; height: 54%; background: linear-gradient(transparent, rgba(5,17,39,.83)); content: ''; }
        .detail-media__card figcaption { position: absolute; z-index: 1; right: 20px; bottom: 18px; left: 20px; color: #fff; font-size: 16px; font-weight: 800; }
        @media (max-width: 991px) { .detail-media__grid { grid-template-columns: 1fr 1fr; } .detail-media__card:first-child { grid-column: 1 / -1; } }
        @media (max-width: 767px) { .detail-media { padding: 58px 0; } .detail-media__wrap { width: min(100% - 28px, 620px); } .detail-media h2 { font-size: 32px; } .detail-media__lead { font-size: 15px; } .detail-media__grid { display: flex; overflow-x: auto; gap: 14px; margin: 31px -14px 0; padding: 0 14px 9px; scroll-snap-type: x mandatory; scrollbar-width: none; } .detail-media__grid::-webkit-scrollbar { display: none; } .detail-media__card, .detail-media__card:first-child { flex: 0 0 84%; min-height: 300px; scroll-snap-align: start; } }
        @media (prefers-reduced-motion: reduce) { .detail-media__card img { transition: none; } }
    </style>
@endonce

<section class="detail-media" id="{{ $id }}">
    <div class="detail-media__wrap">
        <div class="detail-media__intro">
            <span class="detail-media__eyebrow">{{ $eyebrow }}</span>
            <h2>{{ $title }}</h2>
            @if($lead)<p class="detail-media__lead">{{ $lead }}</p>@endif
        </div>
        <div class="detail-media__grid">
            @foreach($images as $image)
                <figure class="detail-media__card"><img src="{{ asset($image['src']) }}" alt="{{ $image['alt'] }}" loading="lazy" decoding="async" width="1600" height="1100"><figcaption>{{ $image['label'] }}</figcaption></figure>
            @endforeach
        </div>
    </div>
</section>
