@if ($page['reviewItems'] !== [])
    <section id="reviews" class="testimonial-section tg-reviews fix section-padding">
        <div class="container">
            <div class="section-title text-center">
                <h6>{{ $page['reviews_eyebrow'] }}</h6>
                <h2>{{ $page['reviews_title'] }}</h2>
                <div class="tg-rating-line">
                    <i class="fab fa-google"></i> {{ $page['reviews_rating'] }}
                    <span class="star" aria-hidden="true"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    {{ $page['reviews_count'] }} Google reviews
                </div>
            </div>
            <div class="swiper testimonial-slider">
                <div class="swiper-wrapper">
                    @foreach ($page['reviewItems'] as $review)
                        <div class="swiper-slide">
                            <article class="testimonial-box-items tg-review {{ $loop->even ? 'bg-2' : '' }}">
                                <div class="tg-review-head">
                                    <div class="tg-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($review['name'], 0, 1)) }}</div>
                                    <div class="tg-review-who">
                                        <h4>{{ $review['name'] }}</h4>
                                        <span><i class="fab fa-google"></i>Google review</span>
                                    </div>
                                    <i class="fas fa-quote-right tg-review-quote" aria-hidden="true"></i>
                                </div>
                                <div class="star" aria-label="Rated 5 out of 5"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                <p>&ldquo;{{ $review['text'] }}&rdquo;</p>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-dot text-center mt-5"><div class="dot"></div></div>
            </div>
        </div>
    </section>
@endif
