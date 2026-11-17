@if ($page['photoItems'] !== [])
    <section class="team-section tg-strip fix" aria-label="Dubai photos">
        <div class="swiper team-slider">
            <div class="swiper-wrapper">
                @foreach ($page['photoItems'] as $photo)
                    <div class="swiper-slide">
                        <div class="team-box-items">
                            <div class="team-image"><img src="{{ $photo['image'] }}" alt="{{ $photo['caption'] }}" loading="lazy"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
