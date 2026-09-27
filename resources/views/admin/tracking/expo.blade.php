@extends('admin.layout')

@section('title', 'Expo tracking | Trans Globe Indore LMS')
@section('crumb', 'Expo tracking')
@section('backUrl', route('admin.ads.index'))
@section('backLabel', 'Back to ads & attribution')

@push('styles')
<style>
    .expo-tracking{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(280px,.8fr);align-items:start;gap:20px}.expo-tracking__form{display:grid;gap:20px;padding:24px}.expo-tracking__form h2,.expo-tracking__info h2{margin:0;color:var(--admin-ink);font-size:17px;letter-spacing:-.035em}.expo-tracking__form p,.expo-tracking__info p{margin:7px 0 0;color:var(--admin-muted);font-size:12px;line-height:1.55}.expo-tracking__fields{display:grid;gap:16px;padding-top:2px}.expo-tracking__field{display:grid;gap:7px}.expo-tracking__field label{color:var(--admin-ink);font-size:12px;font-weight:800}.expo-tracking__field small{color:#8290a8;font-size:10px;line-height:1.45}.expo-tracking__error{color:#b01e2a!important;font-weight:700}.expo-tracking__status{display:flex;align-items:flex-start;gap:12px;padding:15px;border:1px solid #f0d1d4;border-radius:12px;background:#fff7f8}.expo-tracking__status.is-enabled{border-color:#bce8d5;background:#f2fcf7}.expo-tracking__status-icon{display:grid;width:29px;height:29px;place-items:center;flex:0 0 29px;border-radius:50%;background:#fee5e7;color:#bd2530;font-size:12px;font-weight:900}.expo-tracking__status.is-enabled .expo-tracking__status-icon{background:#d7f5e8;color:#16835c}.expo-tracking__status strong{display:block;color:var(--admin-ink);font-size:12px}.expo-tracking__status span{display:block;margin-top:2px;color:var(--admin-muted);font-size:10px;line-height:1.45}.expo-tracking__actions{display:flex;align-items:center;justify-content:space-between;gap:14px;padding-top:4px}.expo-tracking__actions small{max-width:370px;color:#8290a8;font-size:10px;line-height:1.45}.expo-tracking__info{display:grid;gap:15px;padding:22px}.expo-tracking__step{display:grid;grid-template-columns:27px 1fr;gap:10px}.expo-tracking__step span{display:grid;width:27px;height:27px;place-items:center;border-radius:9px;background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-size:11px;font-weight:900}.expo-tracking__step strong{display:block;color:var(--admin-ink);font-size:12px}.expo-tracking__step p{margin-top:2px}.expo-tracking__privacy{padding:13px;border-radius:10px;background:#f7f9fc;color:#687791;font-size:10px;line-height:1.55}.expo-tracking__switch{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding:16px;border:1px solid var(--admin-line);border-radius:12px;background:#fbfcff}.expo-tracking__switch strong{display:block;color:var(--admin-ink);font-size:12px}.expo-tracking__switch span{display:block;margin-top:3px;color:var(--admin-muted);font-size:10px;line-height:1.45}.expo-tracking__switch input{width:20px;height:20px;accent-color:var(--admin-primary)}@media(max-width:960px){.expo-tracking{grid-template-columns:1fr}.expo-tracking__info{order:-1}}@media(max-width:560px){.expo-tracking__form,.expo-tracking__info{padding:18px}.expo-tracking__actions{align-items:stretch;flex-direction:column}.expo-tracking__actions .button{width:100%}}
</style>
@endpush

@section('content')
<section class="page-head"><div><span class="eyebrow">Advertising connection</span><h1>Global Uni Expo tracking</h1></div><p>Let an authorised Ads Manager connect Meta or Google Ads directly, without changing page code or cPanel files.</p></section>

@if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif

<div class="expo-tracking">
    <form class="panel expo-tracking__form" method="post" action="{{ route('admin.expo-tracking.update') }}" novalidate>
        @csrf
        @method('PUT')
        <header><h2>Provider details</h2><p>Only public browser IDs are stored here. Do not enter access tokens, passwords, or a Google service-account key.</p></header>

        <div class="expo-tracking__status {{ old('enabled', $tracking['enabled']) === '1' ? 'is-enabled' : '' }}" aria-live="polite"><span class="expo-tracking__status-icon" aria-hidden="true">{{ old('enabled', $tracking['enabled']) === '1' ? '✓' : 'Ⅱ' }}</span><div><strong>{{ old('enabled', $tracking['enabled']) === '1' ? 'Tracking is enabled' : 'Tracking is paused' }}</strong><span>{{ old('enabled', $tracking['enabled']) === '1' ? 'Visitors can now send events to the configured provider(s).' : 'No Meta or Google requests are made from the Expo page.' }}</span></div></div>

        <div class="expo-tracking__fields">
            <label class="expo-tracking__switch" for="tracking-enabled"><span><strong>Enable advertising tracking</strong><span>Turn this on only after at least one provider ID has been verified by the Ads Manager.</span></span><input id="tracking-enabled" type="checkbox" name="enabled" value="1" @checked(old('enabled', $tracking['enabled']) === '1')></label>
            @error('enabled')<small class="expo-tracking__error" role="alert">{{ $message }}</small>@enderror

            <div class="expo-tracking__field"><label for="meta-pixel-id">Meta Pixel ID</label><input class="input" id="meta-pixel-id" name="meta_pixel_id" value="{{ old('meta_pixel_id', $tracking['meta_pixel_id']) }}" inputmode="numeric" autocomplete="off" placeholder="123456789012345"><small>Optional. Enter the numeric Pixel ID from Meta Events Manager.</small>@error('meta_pixel_id')<small class="expo-tracking__error" role="alert">{{ $message }}</small>@enderror</div>

            <div class="expo-tracking__field"><label for="google-ads-id">Google Ads tag ID</label><input class="input" id="google-ads-id" name="google_ads_id" value="{{ old('google_ads_id', $tracking['google_ads_id']) }}" autocapitalize="characters" autocomplete="off" placeholder="AW-123456789"><small>Optional. Use the Google tag ID shown in Google Ads, beginning with <strong>AW-</strong>.</small>@error('google_ads_id')<small class="expo-tracking__error" role="alert">{{ $message }}</small>@enderror</div>

            <div class="expo-tracking__field"><label for="google-ads-conversion-label">Google Ads conversion label</label><input class="input" id="google-ads-conversion-label" name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $tracking['google_ads_conversion_label']) }}" autocomplete="off" placeholder="Optional conversion label"><small>Optional. With a label, a completed registration is reported as a Google Ads conversion. Without one, it is reported as a <strong>generate_lead</strong> event.</small>@error('google_ads_conversion_label')<small class="expo-tracking__error" role="alert">{{ $message }}</small>@enderror</div>
        </div>

        <footer class="expo-tracking__actions"><small>Saving applies to <strong>Global Uni Expo 2026</strong> only: <code>/_geic_release/uniexpo-dubai-europe</code>.</small><button class="button" type="submit">Save tracking setup</button></footer>
    </form>

    <aside class="panel expo-tracking__info"><div><span class="eyebrow">What this sends</span><h2>Simple event flow</h2></div><div class="expo-tracking__step"><span>1</span><div><strong>Page view</strong><p>Sent when a visitor opens the Expo landing page after tracking is enabled.</p></div></div><div class="expo-tracking__step"><span>2</span><div><strong>Confirmed lead</strong><p>Sent only after the registration has been saved successfully to the GEIC dashboard.</p></div></div><div class="expo-tracking__step"><span>3</span><div><strong>Keep Google Sheets unchanged</strong><p>The existing Google Sheet submission continues separately and is not edited by this setup.</p></div></div><div class="expo-tracking__privacy"><strong>Privacy note:</strong> enabling this shares standard advertising events with the selected provider. It does not send the registrant’s name, email, phone number, or other form fields.</div></aside>
</div>
@endsection