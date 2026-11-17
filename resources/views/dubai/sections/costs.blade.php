@php
    $tones = ['blue', 'yellow', 'green'];
    $icons = ['far fa-graduation-cap', 'far fa-user-graduate', 'far fa-home'];
    $scale = max(1, $page['costScale']);
    $ticks = [0, $scale / 4, $scale / 2, $scale * 3 / 4, $scale];
    $formatK = fn (float $value): string => rtrim(rtrim(number_format($value, 1), '0'), '.');
@endphp
<section id="costs" class="tg-costs fix section-padding">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-xl-5">
                <div class="section-title mb-0">
                    <h6>{{ $page['costs_eyebrow'] }}</h6>
                    <h2>{{ $page['costs_title'] }}</h2>
                    <p>{{ $page['costs_copy'] }}</p>
                </div>
                @if ($page['costExtras'] !== [])
                    <ul class="tg-cost-list">
                        @foreach ($page['costExtras'] as $extra)
                            <li>{{ $extra['label'] }} <span>{{ $extra['value'] }}</span></li>
                        @endforeach
                    </ul>
                @endif
                <p class="tg-note">{{ $page['costs_note'] }}</p>
            </div>
            <div class="col-xl-7">
                <div class="tg-cost-card">
                    <div class="tg-cost-head">
                        <h3>Yearly costs at a glance</h3>
                        <span>AED, per year</span>
                    </div>
                    <ul class="tg-cost-rows">
                        @foreach ($page['costItems'] as $cost)
                            @php
                                $low = min($cost['min'], $cost['max']);
                                $high = max($cost['min'], $cost['max']);
                            @endphp
                            <li class="tg-cost-row tone-{{ $tones[$loop->index % 3] }}">
                                <div class="tg-cost-icon" aria-hidden="true"><i class="{{ $icons[$loop->index % 3] }}"></i></div>
                                <div class="tg-cost-body">
                                    <div class="tg-cost-top">
                                        <div>
                                            <h4>{{ $cost['label'] }}</h4>
                                            <p>{{ $cost['note'] }}</p>
                                        </div>
                                        <strong>AED {{ $formatK($low) }}K – {{ $formatK($high) }}K</strong>
                                    </div>
                                    <div class="tg-range" role="img" aria-label="{{ $cost['label'] }}: AED {{ $formatK($low) }},000 to {{ $formatK($high) }},000 per year">
                                        <span style="left: {{ round($low / $scale * 100, 2) }}%; width: {{ round(($high - $low) / $scale * 100, 2) }}%"></span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tg-scale" aria-hidden="true">
                        @foreach ($ticks as $tick)
                            <span>{{ $tick == 0 ? '0' : $formatK($tick).'K' }}</span>
                        @endforeach
                    </div>
                    @if (trim($page['costs_total']) !== '')
                        <div class="tg-cost-total">
                            <i class="far fa-calculator" aria-hidden="true"></i>
                            <p>{{ $page['costs_total'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
