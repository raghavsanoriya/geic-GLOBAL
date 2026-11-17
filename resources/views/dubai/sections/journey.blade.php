@php
    $journeyTotal = count($page['journeySteps']);
    $visaTotal = count($page['visaSteps']);
@endphp
<section id="journey" class="popular-courses-section tg-courses fix section-padding pt-0">
    <div class="container">
        <div class="section-title-area">
            <div class="section-title mb-0">
                <h6>{{ $page['journey_eyebrow'] }}</h6>
                <h2>{{ $page['journey_title'] }}</h2>
            </div>
            @if (count($page['journeyTabs']) > 0)
                <div class="tg-tabs" role="tablist" aria-label="Filter journey steps">
                    <button type="button" class="active" role="tab" aria-selected="true" data-filter="journey">Full Journey</button>
                    @foreach ($page['journeyTabs'] as $tabKey => $tabLabel)
                        <button type="button" role="tab" aria-selected="false" data-filter="{{ $tabKey }}">{{ $tabLabel }}</button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="row g-4">
            @foreach ($page['journeySteps'] as $item)
                <div class="col-xl-3 col-lg-4 col-md-6 tg-card-col" data-tabs="journey {{ $item['tabKey'] }}">
                    <div class="courses-card-main-items">
                        <div class="courses-card-items">
                            <div class="courses-image">
                                @if ($item['image'] !== '')<img src="{{ $item['image'] }}" alt="" loading="lazy">@endif
                                <h3 class="courses-title">{{ $item['short'] }}</h3>
                                <h4 class="topic-title">Step {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</h4>
                            </div>
                            <div class="courses-content">
                                <ul class="post-cat">
                                    <li><a href="#journey">{{ $item['tab'] }}</a></li>
                                    <li class="tg-step">Step {{ $loop->iteration }} of {{ $journeyTotal }}</li>
                                </ul>
                                <h3><a href="{{ $page['whatsappUrl'] }}">{{ $item['title'] }}</a></h3>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @foreach ($page['visaSteps'] as $item)
                <div class="col-xl-3 col-lg-4 col-md-6 tg-card-col" data-tabs="{{ $item['tabKey'] }}" hidden>
                    <div class="courses-card-main-items">
                        <div class="courses-card-items">
                            <div class="courses-image">
                                @if ($item['image'] !== '')<img src="{{ $item['image'] }}" alt="" loading="lazy">@endif
                                <h3 class="courses-title">{{ $item['short'] }}</h3>
                                <h4 class="topic-title">UAE Student Visa</h4>
                            </div>
                            <div class="courses-content">
                                <ul class="post-cat">
                                    <li><a href="#journey">Visa</a></li>
                                    <li class="tg-step">Visa step {{ $loop->iteration }} of {{ $visaTotal }}</li>
                                </ul>
                                <h3><a href="{{ $page['whatsappUrl'] }}">{{ $item['title'] }}</a></h3>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if (trim($page['journey_note']) !== '')
            <p class="mt-4 mb-0 text-center">{{ $page['journey_note'] }}</p>
        @endif
    </div>
</section>
