<section id="why" class="top-category-section tg-category fix section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-xl-4 col-lg-5">
                <div class="section-title mb-0">
                    <h6>{{ $page['why_eyebrow'] }}</h6>
                    <h2>{{ $page['why_title'] }}</h2>
                    <p>{{ $page['why_copy'] }}</p>
                    <a href="{{ $page['whatsappUrl'] }}" class="theme-btn yellow-btn">{{ $page['why_cta'] }}</a>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="top-category-wrapper">
                    <div class="row g-0">
                        @foreach ($page['whyItems'] as $item)
                            <div class="col-md-4 col-sm-6">
                                <div class="top-category-box-items {{ $loop->iteration % 3 === 0 ? 'border-right-none' : '' }} {{ $loop->first ? 'active' : '' }}">
                                    <div class="icon"><i class="{{ $item['icon'] }}"></i></div>
                                    <h6>{{ $item['title'] }}</h6>
                                    <p>{{ $item['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
