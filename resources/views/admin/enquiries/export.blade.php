@extends('admin.layout')

@section('title', 'Export leads')
@section('crumb', 'Export leads')
@section('backUrl', route('admin.enquiries.index'))
@section('backLabel', 'Back to student enquiries')

@push('styles')
<style>
    .export-layout{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(270px,.55fr);align-items:start;gap:20px}.export-card,.export-summary{padding:24px}.export-card__icon{display:grid;width:58px;height:58px;place-items:center;margin-bottom:18px;border-radius:18px;background:var(--admin-primary-soft);color:var(--admin-primary)}.export-card__icon svg{width:28px;height:28px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}.export-card h2,.export-summary h2{margin:0;color:var(--admin-ink);font-size:17px}.export-card>p,.export-summary>p{margin:6px 0 20px;color:var(--admin-muted);font-size:12px}.export-form{display:grid;gap:16px}.export-form__actions{display:flex;align-items:center;justify-content:flex-end;gap:10px}.export-summary__count{display:grid;min-height:128px;place-items:center;border-radius:18px;background:#fafcff;text-align:center}.export-summary__count strong{display:block;color:var(--admin-primary);font-size:42px;line-height:1}.export-summary__count span{display:block;margin-top:7px;color:var(--admin-muted);font-size:12px}.export-note{margin-top:16px;padding:14px 15px;border-radius:14px;background:#fff8ed;color:#885514;font-size:11px;line-height:1.55}@media(max-width:860px){.export-layout{grid-template-columns:1fr}}@media(max-width:560px){.export-card,.export-summary{padding:18px}.export-form__actions{align-items:stretch;flex-direction:column-reverse}.export-form__actions .button{width:100%}}
</style>
@endpush

@section('content')
    <section class="page-head"><div><span class="eyebrow">Lead management</span><h1>Export leads</h1></div><p>Choose the records you need, then download a clean CSV for follow-up or reporting.</p></section>

    <section class="export-layout">
        <article class="panel export-card">
            <span class="export-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v12M8 11l4 4 4-4M5 20h14"/></svg></span>
            <h2>Prepare your export</h2><p>Filters are optional. Leave them empty to include every enquiry.</p>
            <form class="export-form" method="get" action="{{ route('admin.export') }}">
                <div class="field"><label for="export-q">Search records</label><input class="input" id="export-q" type="search" name="q" value="{{ request('q') }}" placeholder="Name, email, phone or course"><small>Only records matching this text will be included.</small></div>
                <div class="field"><label for="export-destination">Destination</label><select class="select" id="export-destination" name="destination"><option value="">All destinations</option>@foreach($destinationOptions as $option)<option value="{{ $option }}" @selected(request('destination') === $option)>{{ $option }}</option>@endforeach</select></div>
                <div class="export-form__actions"><a class="button button--quiet" href="{{ route('admin.enquiries.export') }}">Reset filters</a><button class="button" type="submit">Download CSV <span aria-hidden="true">↓</span></button></div>
            </form>
        </article>
        <aside class="panel export-summary">
            <h2>Export summary</h2><p>The download reflects the filters currently shown.</p>
            <div class="export-summary__count"><div><strong>{{ $matchingCount }}</strong><span>of {{ $total }} total lead{{ $total === 1 ? '' : 's' }}</span></div></div>
            <div class="export-note"><strong>CSV format</strong><br>The file includes contact details, destination, study plan, message, source page, and submission time.</div>
        </aside>
    </section>
@endsection
