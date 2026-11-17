@extends('admin.layout')

@section('title', 'Dubai lead tracking | Trans Globe Indore LMS')
@section('crumb', 'Dubai lead tracking')
@section('backUrl', route('admin.ads.index'))
@section('backLabel', 'Back to ads & attribution')

@push('styles')
<style>
    .dubai-tracking{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(280px,.8fr);align-items:start;gap:20px}.dubai-tracking__form{display:grid;gap:20px;padding:24px}.dubai-tracking h2{margin:0;color:var(--admin-ink);font-size:17px;letter-spacing:-.035em}.dubai-tracking p{margin:7px 0 0;color:var(--admin-muted);font-size:12px;line-height:1.55}.dubai-tracking__fields{display:grid;gap:16px;padding-top:2px}.dubai-tracking__field{display:grid;gap:7px}.dubai-tracking__field label{color:var(--admin-ink);font-size:12px;font-weight:800}.dubai-tracking__field small{color:#8290a8;font-size:10px;line-height:1.45}.dubai-tracking__error{color:#b01e2a!important;font-weight:700}.dubai-tracking__status{display:flex;align-items:flex-start;gap:12px;padding:15px;border:1px solid #f0d1d4;border-radius:12px;background:#fff7f8}.dubai-tracking__status.is-enabled{border-color:#bce8d5;background:#f2fcf7}.dubai-tracking__status-icon{display:grid;width:29px;height:29px;place-items:center;flex:0 0 29px;border-radius:50%;background:#fee5e7;color:#bd2530;font-size:12px;font-weight:900}.dubai-tracking__status.is-enabled .dubai-tracking__status-icon{background:#d7f5e8;color:#16835c}.dubai-tracking__status strong{display:block;color:var(--admin-ink);font-size:12px}.dubai-tracking__status span{display:block;margin-top:2px;color:var(--admin-muted);font-size:10px;line-height:1.45}.dubai-tracking__switch{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding:16px;border:1px solid var(--admin-line);border-radius:12px;background:#fbfcff}.dubai-tracking__switch strong{display:block;color:var(--admin-ink);font-size:12px}.dubai-tracking__switch span{display:block;margin-top:3px;color:var(--admin-muted);font-size:10px;line-height:1.45}.dubai-tracking__switch input{width:20px;height:20px;accent-color:var(--admin-primary)}.dubai-tracking__actions{display:flex;align-items:center;justify-content:space-between;gap:14px;padding-top:4px}.dubai-tracking__actions small{max-width:370px;color:#8290a8;font-size:10px;line-height:1.45}.dubai-tracking__info{display:grid;gap:15px;padding:22px}.dubai-tracking__step{display:grid;grid-template-columns:27px 1fr;gap:10px}.dubai-tracking__step span{display:grid;width:27px;height:27px;place-items:center;border-radius:9px;background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-size:11px;font-weight:900}.dubai-tracking__step strong{display:block;color:var(--admin-ink);font-size:12px}.dubai-tracking__step p{margin-top:2px}.dubai-tracking__links{display:flex;flex-wrap:wrap;gap:10px}.dubai-tracking__hint{padding:12px 14px;border-radius:10px;background:#f4f8ff;color:#41516b;font-size:11px;line-height:1.5}.dubai-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px}.dubai-stats .panel{padding:18px 20px}.dubai-stats span{display:block;color:var(--admin-muted);font-size:11px;font-weight:700}.dubai-stats strong{display:block;margin-top:6px;color:var(--admin-ink);font-size:26px;letter-spacing:-.04em}.dubai-leads{margin-top:20px;padding:22px;overflow-x:auto}.dubai-leads table{width:100%;border-collapse:collapse;font-size:12px}.dubai-leads th{padding:10px 8px;border-bottom:1px solid var(--admin-line);color:var(--admin-muted);font-size:10px;letter-spacing:.06em;text-align:left;text-transform:uppercase}.dubai-leads td{padding:12px 8px;border-bottom:1px solid var(--admin-line);color:var(--admin-ink);vertical-align:top}.dubai-leads td small{display:block;color:var(--admin-muted)}@media(max-width:960px){.dubai-tracking{grid-template-columns:1fr}.dubai-tracking__info{order:-1}.dubai-stats{grid-template-columns:1fr}}@media(max-width:560px){.dubai-tracking__form,.dubai-tracking__info{padding:18px}.dubai-tracking__actions{align-items:stretch;flex-direction:column}.dubai-tracking__actions .button{width:100%}}
</style>
@endpush

@section('content')
<section class="page-head"><div><span class="eyebrow">Advertising connection</span><h1>Dubai lead tracking</h1></div><p>Connect Meta or Google Ads to the Study in Dubai page at <a href="{{ $publicUrl }}" target="_blank" rel="noopener">{{ $publicUrl }}</a> and follow the leads it brings in.</p></section>

@if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif

@if($canViewLeads && $stats)
    <div class="dubai-stats">
        <div class="panel"><span>Total Dubai enquiries</span><strong>{{ number_format($stats['total']) }}</strong></div>
        <div class="panel"><span>Last 7 days</span><strong>{{ number_format($stats['week']) }}</strong></div>
        <div class="panel"><span>Today</span><strong>{{ number_format($stats['today']) }}</strong></div>
    </div>
@endif

<div class="dubai-tracking">
    <form class="panel dubai-tracking__form" method="post" action="{{ route('admin.dubai-tracking.update') }}" novalidate>
        @csrf
        @method('PUT')
        <header><h2>Provider details</h2><p>Only public browser IDs are stored here. Do not enter access tokens, passwords, or a Google service-account key.</p></header>

        @php($isEnabled = old('enabled', $tracking['enabled']) === '1')
        <div class="dubai-tracking__status {{ $isEnabled ? 'is-enabled' : '' }}" aria-live="polite"><span class="dubai-tracking__status-icon" aria-hidden="true">{{ $isEnabled ? '✓' : 'Ⅱ' }}</span><div><strong>{{ $isEnabled ? 'Tracking is enabled' : 'Tracking is paused' }}</strong><span>{{ $isEnabled ? 'Visitors to /dubai now send events to the configured provider(s).' : 'No Meta or Google requests are made from the Dubai page.' }}</span></div></div>

        @if($prefilledFromExpo)
            <div class="dubai-tracking__hint" role="note">These IDs are pre-filled from <strong>Expo tracking</strong>. Review them and save to use the same accounts for Dubai.</div>
        @endif

        <div class="dubai-tracking__fields">
            <label class="dubai-tracking__switch" for="tracking-enabled"><span><strong>Enable advertising tracking</strong><span>Turn this on after at least one provider ID has been verified by the Ads Manager.</span></span><input id="tracking-enabled" type="checkbox" name="enabled" value="1" @checked($isEnabled)></label>
            @error('enabled')<small class="dubai-tracking__error" role="alert">{{ $message }}</small>@enderror

            <div class="dubai-tracking__field"><label for="meta-pixel-id">Meta Pixel ID</label><input class="input" id="meta-pixel-id" name="meta_pixel_id" value="{{ old('meta_pixel_id', $tracking['meta_pixel_id']) }}" inputmode="numeric" autocomplete="off" placeholder="123456789012345"><small>Optional. Enter the numeric Pixel ID from Meta Events Manager.</small>@error('meta_pixel_id')<small class="dubai-tracking__error" role="alert">{{ $message }}</small>@enderror</div>

            <div class="dubai-tracking__field"><label for="google-ads-id">Google Ads tag ID</label><input class="input" id="google-ads-id" name="google_ads_id" value="{{ old('google_ads_id', $tracking['google_ads_id']) }}" autocapitalize="characters" autocomplete="off" placeholder="AW-123456789"><small>Optional. Use the Google tag ID shown in Google Ads, beginning with <strong>AW-</strong>.</small>@error('google_ads_id')<small class="dubai-tracking__error" role="alert">{{ $message }}</small>@enderror</div>

            <div class="dubai-tracking__field"><label for="google-ads-conversion-label">Google Ads conversion label</label><input class="input" id="google-ads-conversion-label" name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $tracking['google_ads_conversion_label']) }}" autocomplete="off" placeholder="Optional conversion label"><small>Optional. With a label, a saved enquiry is reported as a Google Ads conversion. Without one, it is reported as a <strong>generate_lead</strong> event.</small>@error('google_ads_conversion_label')<small class="dubai-tracking__error" role="alert">{{ $message }}</small>@enderror</div>
        </div>

        <footer class="dubai-tracking__actions"><small>Saving applies to the <strong>Study in Dubai</strong> page only: <code>/dubai</code>.</small><button class="button" type="submit">Save tracking setup</button></footer>
    </form>

    <aside class="panel dubai-tracking__info">
        <div><span class="eyebrow">What this sends</span><h2>Simple event flow</h2></div>
        <div class="dubai-tracking__step"><span>1</span><div><strong>Page view</strong><p>Sent when a visitor opens /dubai after tracking is enabled.</p></div></div>
        <div class="dubai-tracking__step"><span>2</span><div><strong>Contact</strong><p>Sent when a visitor taps WhatsApp or the call button.</p></div></div>
        <div class="dubai-tracking__step"><span>3</span><div><strong>Confirmed lead</strong><p>Sent only after the enquiry has been saved to the GEIC dashboard and CRM.</p></div></div>
        <p>Enquiries are saved with destination <strong>Dubai</strong> and any UTM campaign values, so they also appear in Student enquiries and the CRM.</p>
        <div class="dubai-tracking__links">
            @can('content.manage')<a class="button" href="{{ route('admin.pages.edit', 'dubai') }}">Edit page content</a>@endcan
            <a class="button" href="{{ $publicUrl }}" target="_blank" rel="noopener">View live page</a>
        </div>
    </aside>
</div>

@if($canViewLeads)
    <section class="panel dubai-leads" aria-labelledby="dubai-leads-title">
        <h2 id="dubai-leads-title">Latest Dubai enquiries</h2>
        @if($leads->isEmpty())
            <p>No enquiries from /dubai yet.</p>
        @else
            <table>
                <thead><tr><th>Student</th><th>Study plan</th><th>Campaign</th><th>Received</th></tr></thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->full_name }}<small>{{ $lead->email }} · {{ $lead->phone }}</small></td>
                            <td>{{ $lead->study_level }}<small>{{ $lead->preferred_intake }} · {{ $lead->city }}</small></td>
                            <td>{{ $lead->utm_source ?: 'Direct' }}<small>{{ $lead->utm_campaign ?: '—' }}</small></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($lead->created_at)->format('d M Y') }}<small>{{ \Illuminate\Support\Carbon::parse($lead->created_at)->format('h:i A') }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endif
@endsection
