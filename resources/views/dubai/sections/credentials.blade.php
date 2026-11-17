@if ($page['credentialItems'] !== [])
    <section class="feature-section tg-feature fix">
        <div class="container-fluid p-0">
            <div class="row g-0 row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5">
                @foreach ($page['credentialItems'] as $item)
                    <div class="col">
                        <div class="feature-box-items">
                            <div class="icon"><i class="{{ $item['icon'] }}"></i></div>
                            <div class="content">
                                <h3>{{ $item['title'] }}</h3>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
