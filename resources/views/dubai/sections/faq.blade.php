@if ($page['faqItems'] !== [])
    <section id="faq" class="tg-faq fix section-padding pt-0">
        <div class="container" style="max-width: 900px">
            <div class="section-title text-center">
                <h6>{{ $page['faq_eyebrow'] }}</h6>
                <h2>{{ $page['faq_title'] }}</h2>
            </div>
            @foreach ($page['faqItems'] as $faq)
                <details class="faq-item" @if ($loop->first) open @endif>
                    <summary>{{ $faq['question'] }}</summary>
                    <p>{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endif
