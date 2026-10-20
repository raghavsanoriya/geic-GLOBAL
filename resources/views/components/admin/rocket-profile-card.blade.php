@props(['name', 'subtitle' => null, 'reference' => null, 'status' => null, 'details' => [], 'links' => []])
{{-- Converted from Rocket's users/kate-williams/profile.html profile-card structure. --}}
<aside {{ $attributes->class(['rocket-profile-card', 'profile-card-has-mask']) }} aria-label="Record overview" data-crm-record-overview>
    <div class="rocket-profile-cover" aria-hidden="true"><svg viewBox="0 0 320 100" fill="none"><circle cx="275" cy="10" r="73" stroke="currentColor"/><circle cx="275" cy="10" r="48" stroke="currentColor"/><path d="M0 85 100 25l120 80" stroke="currentColor"/></svg></div>
    <div class="rocket-profile-inner">
        <div class="profile-avatar-card" aria-hidden="true" data-overview-avatar>{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</div>
        @if($reference)<span class="rocket-record-reference">{{ $reference }}</span>@endif
        <h2 data-overview-title>{{ $name }}</h2>
        @if($subtitle)<p class="crm-help" data-overview-subtitle>{{ $subtitle }}</p>@endif
        @if($status)<span class="rocket-state" data-overview-status>{{ $status }}</span>@endif
        <dl class="profile-followers-card">@foreach($details as $label => $value)<div><dt>{{ $label }}</dt><dd data-overview-field="{{ \Illuminate\Support\Str::slug($label) }}">{{ filled($value) ? $value : 'Not provided' }}</dd></div>@endforeach</dl>
        @if($links)<nav class="rocket-profile-links" aria-label="Record shortcuts">@foreach($links as $label => $url)<a href="{{ $url }}">{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</nav>@endif
    </div>
</aside>
@include('admin.crm.rocket-styles')
