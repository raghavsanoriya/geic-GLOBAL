<section class="cta-newsletter-section fix blue-bg">
    <div class="girl-shape animation__arryLeftRight"><img src="{{ asset('assets/dubai-landing/img/girl-img.png') }}" alt="" loading="lazy"></div>
    <div class="shape-1"><img src="{{ asset('assets/dubai-landing/img/shape-1.png') }}" alt=""></div>
    <div class="shape-2"><img src="{{ asset('assets/dubai-landing/img/shape-2.png') }}" alt=""></div>
    <div class="container">
        <div class="cta-newsletter-wrapper">
            <div class="section-title text-center">
                <h6 class="text-white">{{ $page['consultation_eyebrow'] }}</h6>
                <h2 class="text-white">{{ $page['consultation_title'] }}</h2>
            </div>
            <form id="tg-cta-form" class="newsletter-input-items mt-4 mt-md-0">
                <input type="text" id="tg-cta-name" placeholder="Your name" required name="cta_name" autocomplete="name" aria-label="Your name">
                <div class="icon"><i class="fas fa-user"></i></div>
                <button class="theme-btn yellow-btn" type="submit">{{ $page['consultation_button'] }}</button>
            </form>
            @if ($page['consultationPoints'] !== [])
                <ul class="list-items">
                    @foreach ($page['consultationPoints'] as $point)
                        <li><i class="far fa-check-circle"></i>{{ $point }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="tg-cta-links">
                <a href="{{ $page['telUrl'] }}"><i class="fas fa-phone-alt"></i>Call {{ $page['phone'] }}</a>
                <a href="mailto:{{ $page['email'] }}"><i class="fas fa-envelope"></i>{{ $page['email'] }}</a>
            </div>
        </div>
    </div>
</section>
