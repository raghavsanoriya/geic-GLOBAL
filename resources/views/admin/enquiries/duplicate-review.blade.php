@extends('admin.layout')

@section('title', 'Duplicate review | GEIC')
@section('crumb', 'Duplicate review')

@section('content')
    <div data-duplicate-auto-fetch>
    <section class="page-head">
        <div><span class="eyebrow">Lead quality</span><h1>Duplicate review</h1></div>
        <p>Review matching contact details. Every submission is kept; nothing is merged or deleted automatically.</p>
    </section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="panel review-help"><h2>How matching works</h2><p>A matching phone number or email flags a possible duplicate, not proof that two students are the same person. Confirm the details before deciding. Phone checks validate the format, not ownership or whether a number is active.</p><p>Foris sync and Google Sheets continue independently. Historical incomplete records are not changed.</p></section>
    <form class="panel review-filters" method="get" action="{{ route('admin.duplicates.index') }}">
        <input type="hidden" name="status" value="{{ $status }}">
        <label for="review-search">Search this queue</label>
        <input class="input" type="search" id="review-search" name="q" placeholder="Name, email or phone" value="{{ request('q') }}">
        <button class="button button--quiet" type="submit">Search</button>
    </form>
    <section class="panel review-auto-fetch" aria-label="Automatic queue updates">
        <p data-fetch-status role="status" aria-atomic="true">Auto fetch is on. Checking for updates every 15 seconds.</p>
        <div><button class="button button--quiet" type="button" data-fetch-now>Fetch now</button><button class="button button--quiet" type="button" data-fetch-toggle aria-pressed="false">Pause auto fetch</button></div>
    </section>
    @include('admin.enquiries.duplicate-review-feed')
    </div>
@endsection

@push('styles')
<style>
.review-counts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px}.review-count{display:grid;gap:8px;padding:22px;color:var(--admin-ink);text-decoration:none}.review-count strong{font-size:28px}.review-count[aria-current=page]{outline:2px solid var(--admin-primary);outline-offset:-2px}.review-count span,.review-help p,.review-footnote{color:var(--admin-muted);line-height:1.6}.review-help{padding:22px;margin-bottom:20px}.review-help h2,.review-card h2{margin:0;font-size:18px}.review-help p{margin:8px 0 0;font-size:13px}.review-filters{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:14px;padding:18px 22px;margin-bottom:20px}.review-filters label{font-size:13px}.review-list{display:grid;gap:20px}.review-card{padding:24px;min-width:0}.review-head,.review-match{display:flex;justify-content:space-between;align-items:flex-start;gap:18px}.review-head h2{margin-top:8px}.review-head p,.review-match p,.review-source{font-size:13px;line-height:1.7;overflow-wrap:anywhere}.review-head .pill,.review-match .pill{flex:none}.review-source{padding-bottom:16px;border-bottom:1px solid var(--admin-line);color:var(--admin-muted)}.review-card h3{font-size:14px}.review-matches{display:grid;gap:10px}.review-match{padding:16px;border:1px solid var(--admin-line);border-radius:12px}.review-match strong{font-size:13px}.review-match p{margin:4px 0 0}.review-decision{margin-top:22px;padding-top:20px;border-top:1px solid var(--admin-line)}.review-fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}.review-fields label{display:grid;gap:8px;font-size:12px;min-width:0}.review-fields select,.review-fields input{width:100%;min-width:0}.review-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:18px}.review-footnote{margin:14px 0 0;font-size:12px}.review-count:focus-visible{outline:3px solid var(--admin-primary);outline-offset:3px}.review-actions button{min-height:44px}@media(max-width:700px){.review-counts{grid-template-columns:1fr;gap:10px}.review-count{grid-template-columns:auto 1fr;align-items:center;padding:16px}.review-filters,.review-fields{grid-template-columns:1fr}.review-filters{gap:10px}.review-head,.review-match{flex-direction:column;gap:8px}.review-card{padding:18px}.review-actions{display:grid}.review-actions .button{white-space:normal}}
.review-auto-fetch{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px;margin-bottom:20px}.review-auto-fetch p{margin:0;color:var(--admin-muted);font-size:13px;line-height:1.6}.review-auto-fetch>div{display:flex;flex-wrap:wrap;gap:10px}.review-auto-fetch .button{min-height:44px}@media(max-width:700px){.review-auto-fetch{align-items:stretch;flex-direction:column}.review-auto-fetch>div{display:grid;grid-template-columns:1fr 1fr}.review-auto-fetch .button{padding:10px;white-space:normal}}
</style>
@endpush


@push('scripts')
<script type="module" src="{{ asset('assets/transglobe/js/duplicate-review.js') }}?v=20261020-1"></script>
@endpush
