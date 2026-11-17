@if ($page['industryList'] !== [])
    <div class="marquee-section">
        <div class="mycustom-marque header-marque theme-blue-bg">
            <div class="scrolling-wrap">
                @for ($i = 0; $i < 3; $i++)
                    <div class="comm"><div></div>@foreach ($page['industryList'] as $item)<div class="cmn-textslide"><i class="flaticon-mortarboard"></i> {{ $item }}</div>@endforeach</div>
                @endfor
            </div>
        </div>
    </div>
@endif
