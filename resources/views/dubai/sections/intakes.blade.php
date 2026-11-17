@if ($page['intakeItems'] !== [])
    <section id="intakes" class="news-section tg-intakes fix section-padding pt-0">
        <div class="container">
            <div class="section-title-area d-flex justify-content-between align-items-end flex-wrap gap-3">
                <div class="section-title mb-0">
                    <h6>{{ $page['intakes_eyebrow'] }}</h6>
                    <h2>{{ $page['intakes_title'] }}</h2>
                </div>
                <a href="{{ $page['whatsappUrl'] }}" class="theme-btn yellow-btn">{{ $page['intakes_cta'] }}</a>
            </div>
            <div class="row">
                @foreach ($page['intakeItems'] as $item)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="news-box-items">
                            <div class="news-image">
                                @if ($item['image'] !== '')
                                    <img src="{{ $item['image'] }}" alt="" loading="lazy">
                                    <img src="{{ $item['image'] }}" alt="" loading="lazy">
                                @endif
                            </div>
                            <div class="news-content">
                                <span><i class="far fa-calendar-alt"></i>{{ $item['tag'] }}</span>
                                <h3><a href="{{ $page['whatsappUrl'] }}">{{ $item['month'] }} Intake</a></h3>
                                <p>{{ $item['text'] }}</p>
                                <a href="{{ $page['whatsappUrl'] }}" class="link-btn">Ask about this intake <i class="far fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
