<section id="internships" class="about-section tg-about fix section-padding pt-0">
    <div class="container">
        <div class="about-wrapper">
            <div class="row g-4 align-items-center">
                <div class="col-xl-4 col-lg-6">
                    <div class="about-image">
                        @if ($page['internshipImages']['main'] !== '')
                            <img src="{{ $page['internshipImages']['main'] }}" alt="Dubai landmark" loading="lazy">
                        @endif
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="about-content">
                        <div class="section-title mb-3">
                            <h6>{{ $page['internships_eyebrow'] }}</h6>
                            <h2>{{ $page['internships_title'] }}</h2>
                        </div>
                        <p>{{ $page['internships_copy'] }}</p>
                        @if (trim($page['internships_note']) !== '')
                            <p><em>{{ $page['internships_note'] }}</em></p>
                        @endif
                        <div class="counter-box-items">
                            <div class="counter-content">
                                <h2>{{ $page['internships_counter_value'] }}</h2>
                                <p>{{ $page['internships_counter_label'] }}</p>
                            </div>
                            <p class="text">{{ $page['internships_counter_text'] }}</p>
                        </div>
                        @if ($page['internshipPoints'] !== [])
                            <ul class="tg-points">
                                @foreach ($page['internshipPoints'] as $point)
                                    <li><i class="fas fa-check-circle"></i>{{ $point }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ $page['whatsappUrl'] }}" class="theme-btn">{{ $page['internships_cta'] }}</a>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-12">
                    <div class="about-image-items">
                        @if ($page['internshipImages']['side'] !== '')
                            <div class="about-image-2"><img src="{{ $page['internshipImages']['side'] }}" alt="Dubai skyline" loading="lazy"></div>
                        @endif
                        @if ($page['internshipImages']['small'] !== '')
                            <div class="about-image-3"><img src="{{ $page['internshipImages']['small'] }}" alt="Dubai landmark" loading="lazy"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
