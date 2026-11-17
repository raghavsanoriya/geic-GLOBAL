@if ($page['requirementItems'] !== [])
    <section id="requirements" class="brand-section tg-req fix section-padding pt-0">
        <div class="container">
            <div class="brand-wrapper">
                <div class="section-title text-center">
                    <h2 class="mb-3">{{ $page['requirements_title'] }}</h2>
                    <p>{{ $page['requirements_copy'] }}</p>
                </div>
                <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-6">
                    @foreach ($page['requirementItems'] as $item)
                        <div class="col">
                            <div class="req-item">
                                <i class="{{ $item['icon'] }}"></i>
                                <h6>{{ $item['title'] }}</h6>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
