@extends('admin.layout')

@section('title', 'Website content | Trans Globe Indore LMS')
@section('crumb', 'Website content')
@section('backUrl', route('admin.dashboard'))
@section('backLabel', 'Back to dashboard')

@push('styles')
    <style>
        .content-groups{display:flex;gap:9px;margin-bottom:20px;padding:7px;overflow-x:auto;border:1px solid var(--admin-line);border-radius:15px;background:#fff;box-shadow:0 4px 14px rgba(42,53,71,.025);scrollbar-width:thin}
        .content-group{display:flex;min-width:max-content;min-height:48px;align-items:center;gap:10px;padding:0 15px;border-radius:11px;color:#697791;font-size:11px;font-weight:800;transition:.2s ease}
        .content-group:hover{background:var(--admin-primary-soft);color:var(--admin-primary-dark)}
        .content-group[aria-current=page]{background:var(--admin-primary);color:#fff;box-shadow:0 8px 16px rgba(229,36,46,.2)}
        .content-group__count{display:grid;min-width:24px;height:24px;place-items:center;padding:0 6px;border-radius:99px;background:#f1f4f8;color:#72809a;font-size:9px}
        .content-group[aria-current=page] .content-group__count{background:rgba(255,255,255,.2);color:#fff}
        .content-group-summary{display:flex;align-items:center;justify-content:space-between;margin-bottom:15px;gap:16px}
        .content-group-summary h2{margin:0;color:var(--admin-ink);font-size:15px;letter-spacing:-.035em}
        .content-group-summary p{margin:3px 0 0;color:#8b98af;font-size:10px}
        .content-group-summary__actions{display:flex;align-items:center;gap:12px}.content-group-summary__actions>span{color:#98a5b8;font-size:10px;font-weight:800;white-space:nowrap}.content-add-button svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-width:2}
        .content-pages .content-card{min-height:0;overflow:hidden;padding:0}
        .content-card__thumbnail{position:relative;display:block;overflow:hidden;aspect-ratio:16/8.5;background:#e9eef5}
        .content-card__thumbnail img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .22s ease}
        .content-card__thumbnail:after{position:absolute;inset:auto 0 0;height:42%;background:linear-gradient(transparent,rgba(14,33,69,.55));content:'';pointer-events:none}
        .content-card__thumbnail:hover img{transform:scale(1.025)}
        .content-card__type--overlay{position:absolute;z-index:1;left:16px;bottom:14px;padding:5px 8px;border-radius:7px;background:rgba(255,255,255,.92);color:var(--admin-primary);box-shadow:0 4px 12px rgba(14,33,69,.12);backdrop-filter:blur(8px)}
        .content-card__body{display:flex;min-height:190px;flex:1;flex-direction:column;padding:18px 20px 20px}
        .content-card__body h2{margin:0;font-size:17px}
        .content-card__body p{display:-webkit-box;overflow:hidden;margin:8px 0 18px;-webkit-box-orient:vertical;-webkit-line-clamp:2}
        .content-card__body .content-card__foot{align-items:flex-end}
        .content-card__actions{display:flex;align-items:center;gap:8px}.content-card__actions form{margin:0}.button--duplicate{min-height:42px;padding:0 13px;border:1px solid var(--admin-line);background:#fff;color:var(--admin-ink);box-shadow:none}.button--duplicate:hover{border-color:var(--admin-accent);background:var(--admin-accent);color:#fff}
        @media(prefers-reduced-motion:reduce){.content-card__thumbnail img{transition:none}.content-card__thumbnail:hover img{transform:none}}
        @media(max-width:620px){.content-groups{margin-right:-14px;margin-left:-14px;padding-right:14px;padding-left:14px;border-right:0;border-left:0;border-radius:0}.content-group-summary{align-items:flex-start;flex-direction:column}.content-group-summary__actions{width:100%;justify-content:space-between}.content-group-summary__actions>span{white-space:normal}}
    </style>
@endpush

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Trans Globe Indore LMS</span><h1>Manage website content</h1></div>
        <p>Open a page to edit it in guided steps, inspect its image usage and control draft, publish and unpublish states.</p>
    </section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif

    <nav class="content-groups" aria-label="Website content groups">
        @foreach($groups as $group)
            <a class="content-group" href="{{ route('admin.pages.index', ['group' => $group['key']]) }}" @if($group['key'] === $activeGroup) aria-current="page" @endif>
                <span>{{ $group['label'] }}</span><span class="content-group__count">{{ $group['count'] }}</span>
            </a>
        @endforeach
    </nav>

    @php($selectedGroup = $groups->get($activeGroup))
    <div class="content-group-summary">
        <div><h2>{{ $selectedGroup['label'] }}</h2><p>{{ $selectedGroup['description'] }}</p></div>
        <div class="content-group-summary__actions"><span>Showing {{ $pages->count() }} page{{ $pages->count() === 1 ? '' : 's' }}</span><a class="button content-add-button" href="{{ route('admin.pages.create', ['group' => $activeGroup]) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add new page</a></div>
    </div>

    <section class="grid content-pages" aria-label="{{ $selectedGroup['label'] }}">
        @foreach($pages as $page)
            @php($pageState = $states->get($page['key']))
            @php($heroField = collect($page['fields'])->firstWhere('key', 'thumbnail_image') ?: collect($page['fields'])->firstWhere('key', 'hero_image'))
            @php($thumbnailPath = $heroImages->get($page['key']) ?: ($heroField['default'] ?? ''))
            @php($thumbnailUrl = str_starts_with($thumbnailPath, 'http://') || str_starts_with($thumbnailPath, 'https://') ? $thumbnailPath : asset(ltrim($thumbnailPath, '/')))
            <article class="content-card">
                <a class="content-card__thumbnail" href="{{ route('admin.pages.edit', $page['key']) }}" aria-label="Edit {{ $page['name'] }}">
                    @if($thumbnailPath)<img src="{{ $thumbnailUrl }}" alt="Preview of {{ $page['name'] }}" width="640" height="340" loading="lazy">@endif
                    <span class="content-card__type content-card__type--overlay">{{ str_starts_with($page['key'], 'promotion.') ? 'Promotional page' : (str_starts_with($page['key'], 'destination.') ? 'Destination' : (str_starts_with($page['key'], 'service.') ? 'Service' : (str_starts_with($page['key'], 'event.') ? 'Event' : (str_starts_with($page['key'], 'scholarship.') ? 'Scholarship' : (str_starts_with($page['key'], 'test.') ? 'Test prep' : 'Landing page'))))) }}</span>
                </a>
                <div class="content-card__body">
                    <h2>{{ $page['name'] }}</h2><p>{{ $page['description'] }}</p>
                    <div class="content-card__foot"><span>{{ $saved[$page['key']] ?? 0 }} saved field{{ ($saved[$page['key']] ?? 0) === 1 ? '' : 's' }} · {{ $pageState?->status ? ucfirst($pageState->status) : 'Published baseline' }}</span><div class="content-card__actions"><a class="button" href="{{ route('admin.pages.edit', $page['key']) }}">Edit <span aria-hidden="true">→</span></a>@if(str_starts_with($page['key'], 'promotion.'))<form method="post" action="{{ route('admin.pages.duplicate', $page['key']) }}">@csrf<button class="button button--duplicate" type="submit" aria-label="Duplicate {{ $page['name'] }}">Duplicate</button></form>@endif</div></div>
                </div>
            </article>
        @endforeach
    </section>
@endsection
