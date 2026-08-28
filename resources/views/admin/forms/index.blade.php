@extends('admin.layout')

@section('content')
<style>
    .forms-workspace{display:grid;gap:22px}.forms-toolbar{display:grid;grid-template-columns:minmax(220px,1.7fr) minmax(140px,1fr) minmax(125px,.8fr) minmax(135px,.9fr) auto auto auto;align-items:center;gap:10px;padding:14px 16px;border:1px solid var(--admin-line);border-radius:var(--admin-radius-card);background:#fff}.forms-search{position:relative;display:flex;align-items:center}.forms-search svg{position:absolute;left:14px;width:17px;height:17px;fill:none;stroke:var(--admin-muted);stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8;pointer-events:none}.forms-search input{padding-left:40px}.forms-toolbar select{min-width:0}.forms-toolbar__view{display:flex;align-items:center;justify-content:flex-end;gap:4px}.forms-toolbar__view a{display:grid;width:42px;height:42px;place-items:center;border:1px solid transparent;border-radius:10px;color:var(--admin-muted)}.forms-toolbar__view a:hover,.forms-toolbar__view a[aria-current=page]{border-color:#f6c2c6;background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-toolbar__view svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}.forms-toolbar__clear{color:var(--admin-muted);font-size:12px;font-weight:600;white-space:nowrap}.forms-toolbar__clear:hover{color:var(--admin-primary)}.forms-summary{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--admin-muted);font-size:12px}.forms-summary strong{color:var(--admin-ink)}.forms-groups{display:grid;gap:26px}.forms-group{display:grid;gap:12px}.forms-group__head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:0 2px}.forms-group__identity{display:flex;align-items:center;gap:12px;min-width:0}.forms-group__icon{display:grid;width:42px;height:42px;place-items:center;flex:0 0 42px;border-radius:13px;background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-group__icon--destinations{background:#edf4ff;color:#4a78d1}.forms-group__icon--services{background:#e9fbf7;color:#159a80}.forms-group__icon--events{background:#fff4e5;color:#e38b00}.forms-group__icon--scholarships{background:#f2edff;color:#7657c8}.forms-group__icon--tests{background:#fff0f1;color:var(--admin-primary)}.forms-group__icon svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.7}.forms-group__head h2{margin:0;color:var(--admin-ink);font-size:18px;letter-spacing:-.035em}.forms-group__head p{margin:3px 0 0;color:var(--admin-muted);font-size:12px}.forms-group__count{display:inline-flex;min-height:28px;align-items:center;padding:0 10px;border-radius:99px;background:#fff;color:var(--admin-muted);font-size:11px;font-weight:700}.forms-group .admin-card-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.forms-group .admin-card{min-width:0;overflow:visible;padding:0}.forms-card__thumb{display:block;aspect-ratio:2.8/1;overflow:hidden;border-radius:var(--admin-radius-card) var(--admin-radius-card) 0 0;background:#f1f5fa}.forms-card__thumb img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .25s ease}.forms-card__thumb:hover img{transform:scale(1.035)}.forms-card__body{display:flex;min-width:0;flex:1;flex-direction:column;padding:15px 18px 16px}.forms-card__body .admin-card__top{align-items:flex-start}.forms-card__type{display:block;margin-bottom:5px;color:var(--admin-primary);font-size:9px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.forms-card__body h3{margin:0;color:var(--admin-ink);font-size:16px;letter-spacing:-.035em;line-height:1.25}.forms-card__path{display:block;margin-top:4px;overflow-wrap:anywhere;color:var(--admin-muted);font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:10px}.forms-card__description{display:-webkit-box;overflow:hidden;margin:8px 0 0;color:var(--admin-muted);font-size:12px;line-height:1.45;-webkit-box-orient:vertical;-webkit-line-clamp:2}.forms-card__meta{display:flex;align-items:center;justify-content:space-between;margin-top:11px;padding-top:10px;border-top:1px solid var(--admin-line);gap:8px;color:#9aa7ba;font-size:10px}.forms-card__tools{display:flex;align-items:flex-end;flex-direction:column;flex:0 0 auto;gap:7px}.forms-card__menu{position:relative}.forms-card__menu summary{display:grid;width:31px;height:31px;place-items:center;border:1px solid var(--admin-line);border-radius:9px;background:#fff;color:var(--admin-muted);cursor:pointer;list-style:none}.forms-card__menu summary::-webkit-details-marker{display:none}.forms-card__menu summary:hover,.forms-card__menu[open] summary{border-color:#f5b6bb;background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-card__menu summary svg{width:16px;height:16px;fill:currentColor}.forms-card__menu nav{position:absolute;z-index:8;top:calc(100% + 6px);right:0;display:grid;min-width:145px;padding:5px;border:1px solid var(--admin-line);border-radius:11px;background:#fff;box-shadow:0 14px 30px rgba(27,42,75,.16)}.forms-card__menu nav a,.forms-card__menu nav button{display:flex;min-height:34px;align-items:center;width:100%;padding:0 10px;border:0;border-radius:7px;background:transparent;color:var(--admin-ink);font-size:11px;font-weight:600;text-align:left;cursor:pointer}.forms-card__menu nav a:hover,.forms-card__menu nav button:hover{background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-card__menu nav .is-danger{color:var(--admin-primary-dark)}.forms-card__menu nav form{margin:0}.forms-empty{padding:42px 24px;text-align:center}.forms-empty h2{margin:0;color:var(--admin-ink);font-size:17px}.forms-empty p{margin:7px 0 0;color:var(--admin-muted);font-size:12px}.forms-groups.is-list .forms-group .admin-card-grid{grid-template-columns:1fr}.forms-groups.is-list .forms-group .admin-card{display:grid;grid-template-columns:190px minmax(0,1fr);min-height:145px}.forms-groups.is-list .forms-card__thumb{height:100%;aspect-ratio:auto;border-radius:var(--admin-radius-card) 0 0 var(--admin-radius-card)}.forms-groups.is-list .forms-card__body{padding:18px}.forms-groups.is-list .forms-card__description{max-width:720px;-webkit-line-clamp:3}@media(max-width:1100px){.forms-toolbar{grid-template-columns:minmax(180px,1.5fr) minmax(110px,1fr) minmax(110px,.8fr) minmax(120px,.9fr) auto auto auto}.forms-group .admin-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.forms-toolbar{grid-template-columns:1fr 1fr;padding:12px}.forms-search,.forms-toolbar>.admin-button{grid-column:1/-1}.forms-toolbar__view{justify-content:flex-start}.forms-summary{align-items:flex-start;flex-direction:column}.forms-group .admin-card-grid,.forms-groups.is-list .forms-group .admin-card-grid{grid-template-columns:1fr}.forms-groups.is-list .forms-group .admin-card{display:flex;min-height:0}.forms-groups.is-list .forms-card__thumb{height:auto;aspect-ratio:2.8/1;border-radius:var(--admin-radius-card) var(--admin-radius-card) 0 0}.forms-card__menu nav{right:-4px}}
</style>

<style>
    .forms-toolbar .sr-only{position:absolute;width:1px;height:1px;overflow:hidden;margin:-1px;padding:0;border:0;clip:rect(0 0 0 0);white-space:nowrap}
    .forms-group .admin-card{overflow:visible}
    .forms-card__thumb{aspect-ratio:2.8/1;border-radius:var(--admin-radius-card) var(--admin-radius-card) 0 0}
    .forms-card__body{padding:15px 18px 16px}.forms-card__type{margin-bottom:5px}.forms-card__path{margin-top:4px}.forms-card__description{margin-top:8px;line-height:1.45}.forms-card__meta{margin-top:11px;padding-top:10px}
    .forms-card__thumb img[src$=".svg"]{object-fit:contain;padding:18px;background:#fff8f4}
    .forms-card__tools{display:flex;align-items:flex-end;flex-direction:column;flex:0 0 auto;gap:7px}.forms-card__menu{position:relative}.forms-card__menu summary{display:grid;width:31px;height:31px;place-items:center;border:1px solid var(--admin-line);border-radius:9px;background:#fff;color:var(--admin-muted);cursor:pointer;list-style:none}.forms-card__menu summary::-webkit-details-marker{display:none}.forms-card__menu summary:hover,.forms-card__menu[open] summary{border-color:#f5b6bb;background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-card__menu summary svg{width:16px;height:16px;fill:currentColor}.forms-card__menu nav{position:absolute;z-index:8;top:calc(100% + 6px);right:0;display:grid;min-width:145px;padding:5px;border:1px solid var(--admin-line);border-radius:11px;background:#fff;box-shadow:0 14px 30px rgba(27,42,75,.16)}.forms-card__menu nav a,.forms-card__menu nav button{display:flex;min-height:34px;align-items:center;width:100%;padding:0 10px;border:0;border-radius:7px;background:transparent;color:var(--admin-ink);font-size:11px;font-weight:600;text-align:left;cursor:pointer}.forms-card__menu nav a:hover,.forms-card__menu nav button:hover{background:var(--admin-primary-soft);color:var(--admin-primary)}.forms-card__menu nav .is-danger{color:var(--admin-primary-dark)}.forms-card__menu nav form{margin:0}
    .forms-groups.is-list .forms-group .admin-card{min-height:145px}.forms-groups.is-list .forms-group .forms-card__thumb{aspect-ratio:auto;border-radius:var(--admin-radius-card) 0 0 var(--admin-radius-card)}
    @media(max-width:760px){.forms-groups.is-list .forms-group .forms-card__thumb{aspect-ratio:2.8/1;border-radius:var(--admin-radius-card) var(--admin-radius-card) 0 0}.forms-card__menu nav{right:-4px}}
</style>

<div class="forms-workspace">
    <div class="admin-page-head">
        <div><span class="admin-kicker">Lead capture</span><h1>Forms workspace</h1><p>Manage enquiry forms for destinations, services, tests, events, scholarships and promotions.</p></div>
        <a class="admin-button admin-button--primary" href="{{ route('admin.forms.create') }}">Create form <span>→</span></a>
    </div>
    @if(session('status'))
        <div class="admin-alert admin-alert--success">{{ session('status') }}</div>
    @endif

    <form class="forms-toolbar" method="get" action="{{ route('admin.forms.index') }}">
        <label class="forms-search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></svg><input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search forms, page paths or descriptions" aria-label="Search forms"></label>
        <select class="select" name="group" aria-label="Filter by page group"><option value="">All page groups</option>
            @foreach($groups as $key => $group)<option value="{{ $key }}" @selected(request('group') === $key)>{{ $group['label'] }}</option>@endforeach
        </select>
        <select class="select" name="status" aria-label="Filter by status"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
        <select class="select" name="sort" aria-label="Sort forms"><option value="group" @selected($sort === 'group')>Group · A–Z</option><option value="name_asc" @selected($sort === 'name_asc')>Name · A–Z</option><option value="name_desc" @selected($sort === 'name_desc')>Name · Z–A</option><option value="newest" @selected($sort === 'newest')>Newest first</option><option value="oldest" @selected($sort === 'oldest')>Oldest first</option></select>
        <button class="admin-button" type="submit">Apply filters</button>
        <div class="forms-toolbar__view" aria-label="View mode">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" @if(request('view','grid') === 'grid') aria-current="page" @endif title="Grid view"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg><span class="sr-only">Grid view</span></a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" @if(request('view') === 'list') aria-current="page" @endif title="List view"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h12M8 12h12M8 18h12"/><path d="M4 6h.01M4 12h.01M4 18h.01"/></svg><span class="sr-only">List view</span></a>
        </div>
        @if(request()->hasAny(['q','group','status','sort']))<a class="forms-toolbar__clear" href="{{ route('admin.forms.index') }}">Clear</a>@endif
    </form>

    <div class="forms-summary"><span><strong>{{ $formGroups->sum(fn (array $group): int => $group['forms']->count()) }}</strong> forms across <strong>{{ $formGroups->count() }}</strong> page groups</span><span>Each card links to its public page and editable field set.</span></div>

    <div class="forms-groups @if(request('view') === 'list') is-list @endif">
        @forelse($formGroups as $groupKey => $group)
            <section class="forms-group">
                <div class="forms-group__head">
                    <div class="forms-group__identity">
                        <span class="forms-group__icon forms-group__icon--{{ $groupKey }}" aria-hidden="true">
                            @if($groupKey === 'destinations')<svg viewBox="0 0 24 24"><path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"/><circle cx="12" cy="10" r="2"/></svg>
                            @elseif($groupKey === 'services')<svg viewBox="0 0 24 24"><path d="M4 7.5h16M7 4v3.5M17 4v3.5M5 10h14v9H5z"/><path d="M9 14h2M13 14h2"/></svg>
                            @elseif($groupKey === 'events')<svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3M8 17h5"/></svg>
                            @elseif($groupKey === 'scholarships')<svg viewBox="0 0 24 24"><path d="m3 9 9-5 9 5-9 5-9-5Z"/><path d="M6 11v5c2.9 2.3 9.1 2.3 12 0v-5M21 9v6"/></svg>
                            @elseif($groupKey === 'tests')<svg viewBox="0 0 24 24"><path d="M7 3h10v18H7zM9.5 7h5M9.5 11h5M9.5 15h3"/></svg>
                            @else<svg viewBox="0 0 24 24"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg>
                            @endif
                        </span>
                        <div><h2>{{ $group['label'] }}</h2><p>{{ $group['description'] }}</p></div>
                    </div>
                    <span class="forms-group__count">{{ $group['forms']->count() }} {{ $group['forms']->count() === 1 ? 'form' : 'forms' }}</span>
                </div>
                <div class="admin-card-grid">
                    @foreach($group['forms'] as $form)
                        <article class="admin-card">
                            @if($form->public_path)
                                <a class="forms-card__thumb" href="{{ url($form->public_path) }}" title="Open {{ $form->name }}"><img src="{{ $form->thumbnail_url }}" alt="{{ $form->name }} thumbnail" loading="lazy"></a>
                            @else
                                <div class="forms-card__thumb"><img src="{{ $form->thumbnail_url }}" alt="{{ $form->name }} thumbnail" loading="lazy"></div>
                            @endif
                            <div class="forms-card__body">
                                <div class="admin-card__top">
                                    <div><span class="forms-card__type">{{ $group['label'] }}</span><h3>{{ $form->name }}</h3><span class="forms-card__path">{{ $form->page_key ?: 'general form' }}</span></div>
                                    <div class="forms-card__tools">
                                        <span class="admin-badge admin-badge--{{ $form->status }}">{{ ucfirst($form->status) }}</span>
                                        <details class="forms-card__menu">
                                            <summary aria-label="Actions for {{ $form->name }}" title="Form actions"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg></summary>
                                            <nav>
                                                <a href="{{ route('admin.forms.edit',$form) }}">Edit fields</a>
                                                @if($form->status === 'published')
                                                    <form method="post" action="{{ route('admin.forms.unpublish',$form) }}">@csrf<button type="submit">Unpublish</button></form>
                                                @else
                                                    <form method="post" action="{{ route('admin.forms.publish',$form) }}">@csrf<button type="submit">Publish</button></form>
                                                @endif
                                                @if($form->public_path)<a href="{{ url($form->public_path) }}">View page</a>@endif
                                                <form method="post" action="{{ route('admin.forms.destroy',$form) }}" onsubmit="return confirm('Delete this form?')">@csrf @method('DELETE')<button class="is-danger" type="submit">Delete</button></form>
                                            </nav>
                                        </details>
                                    </div>
                                </div>
                                <p class="forms-card__description">{{ $form->description ?: 'Reusable enquiry form for this page.' }}</p>
                                <div class="forms-card__meta"><span>{{ count($form->fields ?? []) }} fields</span><span>Updated {{ optional($form->updated_at)->diffForHumans() }}</span></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="admin-card forms-empty"><h2>No forms match these filters</h2><p>Clear the filters or create a new enquiry form for a page.</p></div>
        @endforelse
    </div>
</div>
@endsection
