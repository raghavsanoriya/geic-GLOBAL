<section class="rocket-summary-grid" aria-label="Filtered summary">
@foreach($cards as [$label, $value, $path])
<article class="rocket-summary-card"><span class="rocket-summary-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg></span><div><strong>{{ number_format($value) }}</strong><p>{{ $label }}</p></div></article>
@endforeach
</section>
