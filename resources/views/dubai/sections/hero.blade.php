<section class="hero-section hero-2 tg-hero fix">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1>{{ $page['hero_title'] }} <span>{{ $page['hero_title_accent'] }}</span></h1>
                    <p>{{ $page['hero_copy'] }}</p>
                    <div class="hero-button">
                        <a href="{{ $page['whatsappUrl'] }}" class="theme-btn">{{ $page['hero_cta'] }}</a>
                        <span class="button-text">
                            <a href="{{ $page['telUrl'] }}" class="video-btn" aria-label="Call {{ $page['phone'] }}"><i class="fas fa-phone-alt"></i></a>
                            <span class="ms-3 d-line">Call {{ $page['phone'] }}</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-items">
                    <div class="hero-image">
                        @if ($page['heroImage'] !== '')
                            <img src="{{ $page['heroImage'] }}" alt="Student ready to study in Dubai">
                        @endif
                        <div class="hero-shape"></div>
                        <div class="counter-box float-bob-y">
                            <p>{{ $page['hero_counter_prefix'] }}</p>
                            <h2>{{ $page['hero_counter_value'] }}</h2>
                            <p>{{ $page['hero_counter_label'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
