@extends('admin.layout')

@section('content')
<style>
    .blog-admin-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.blog-admin-card{overflow:hidden;padding:0}.blog-admin-card__thumb{display:block;aspect-ratio:2/1;overflow:hidden;background:#eef3f8}.blog-admin-card__thumb img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .25s ease}.blog-admin-card__thumb:hover img{transform:scale(1.035)}.blog-admin-card__body{position:relative;display:flex;min-width:0;flex-direction:column;padding:19px}.blog-admin-card__meta{display:flex;align-items:center;justify-content:space-between;gap:8px;padding-right:48px;color:var(--admin-muted);font-size:11px}.blog-admin-card__category{color:var(--admin-primary);font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.blog-admin-card h2{margin:8px 0 0;padding-right:38px;color:var(--admin-ink);font-size:17px;line-height:1.25}.blog-admin-card__slug{display:block;margin-top:5px;overflow-wrap:anywhere;color:#9aa7ba;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:10px}.blog-admin-card__excerpt{display:-webkit-box;overflow:hidden;margin:11px 0 0;color:var(--admin-muted);font-size:12px;line-height:1.55;-webkit-box-orient:vertical;-webkit-line-clamp:3}.blog-admin-card__menu{position:absolute;z-index:9;top:16px;right:16px}.blog-admin-card__menu summary{display:inline-grid;width:40px;height:40px;place-items:center;border:1px solid var(--admin-line);border-radius:10px;background:#fff;color:var(--admin-muted);cursor:pointer;list-style:none;transition:background-color .18s ease,border-color .18s ease,color .18s ease}.blog-admin-card__menu summary::-webkit-details-marker{display:none}.blog-admin-card__menu summary:hover,.blog-admin-card__menu[open] summary{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink)}.blog-admin-card__menu summary svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-width:2}.blog-admin-card__menu nav{position:absolute;z-index:8;top:calc(100% + 8px);right:0;display:grid;min-width:150px;padding:6px;border:1px solid var(--admin-line);border-radius:12px;background:#fff;box-shadow:0 16px 32px rgba(14,33,69,.16)}.blog-admin-card__menu nav a,.blog-admin-card__menu nav button{display:flex;min-height:36px;align-items:center;width:100%;padding:0 11px;border:0;border-radius:8px;background:transparent;color:var(--admin-ink);font-size:12px;font-weight:600;text-align:left}.blog-admin-card__menu nav a:hover,.blog-admin-card__menu nav button:hover{background:var(--admin-primary-soft);color:var(--admin-primary)}.blog-admin-card__menu nav .is-danger{color:var(--admin-primary)}.blog-admin-card__menu nav form{margin:0}.blog-admin-empty{grid-column:1/-1;padding:48px;text-align:center}.blog-admin-empty h2{margin:0;color:var(--admin-ink);font-size:17px}.blog-admin-empty p{margin:7px 0 0;color:var(--admin-muted);font-size:12px}@media(max-width:1050px){.blog-admin-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:680px){.blog-admin-grid{grid-template-columns:1fr}.admin-page-head{align-items:stretch;flex-direction:column}.admin-filter-bar{align-items:stretch;flex-direction:column}.admin-filter-bar>*{width:100%}}
</style>

<div class="admin-page-head">
    <div><span class="admin-kicker">Website content</span><h1>Blog posts</h1><p>Create, edit and publish study-abroad articles. Changes here appear on the public blog automatically.</p></div>
    <a class="admin-button admin-button--primary" href="{{ route('admin.blogs.create') }}">Create blog post <span aria-hidden="true">→</span></a>
</div>
@if(session('status'))<div class="admin-alert">{{ session('status') }}</div>@endif

<form class="admin-filter-bar" method="get" action="{{ route('admin.blogs.index') }}">
    <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search title, category or slug" aria-label="Search blog posts">
    <select name="status" aria-label="Filter by status"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
    <select name="category" aria-label="Filter by category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
    <select name="sort" aria-label="Sort blog posts"><option value="newest" @selected(request('sort','newest') === 'newest')>Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option><option value="title" @selected(request('sort') === 'title')>Title A–Z</option></select>
    <button class="admin-button admin-button--primary" type="submit">Apply filters</button>
    @if(request()->hasAny(['q','status','category','sort']))<a class="admin-button" href="{{ route('admin.blogs.index') }}">Clear</a>@endif
</form>

<div class="blog-admin-grid">
    @forelse($posts as $post)
        <article class="admin-card blog-admin-card">
            <a class="blog-admin-card__thumb" href="{{ route('admin.blogs.edit', $post) }}"><img src="{{ $post->image ? asset($post->image) : asset('store/1/geic-icon.png') }}" alt="{{ $post->title }} thumbnail" loading="lazy"></a>
            <div class="blog-admin-card__body">
                <div class="blog-admin-card__meta"><span class="blog-admin-card__category">{{ $post->category }}</span><span class="admin-badge admin-badge--{{ $post->status }}">{{ ucfirst($post->status) }}</span></div>
                <h2>{{ $post->title }}</h2><span class="blog-admin-card__slug">/blog/{{ $post->slug }}</span>
                <p class="blog-admin-card__excerpt">{{ $post->excerpt }}</p>
                <details class="blog-admin-card__menu"><summary aria-label="Actions for {{ $post->title }}" title="Blog post actions"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg></summary><nav><a href="{{ route('admin.blogs.edit', $post) }}">Edit post</a>@if($post->status === 'published')<a href="{{ url('/blog/'.$post->slug) }}" target="_blank" rel="noopener">View public page</a><form method="post" action="{{ route('admin.blogs.unpublish', $post) }}">@csrf<button type="submit">Unpublish</button></form>@else<form method="post" action="{{ route('admin.blogs.publish', $post) }}">@csrf<button type="submit">Publish</button></form>@endif<form method="post" action="{{ route('admin.blogs.destroy', $post) }}" onsubmit="return confirm('Delete this blog post?')">@csrf @method('DELETE')<button class="is-danger" type="submit">Delete</button></form></nav></details>
            </div>
        </article>
    @empty
        <div class="admin-card blog-admin-empty"><h2>No blog posts match these filters</h2><p>Clear the filters or create a new article.</p></div>
    @endforelse
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{const menus=[...document.querySelectorAll('.blog-admin-card__menu')];menus.forEach(menu=>menu.addEventListener('toggle',()=>{if(menu.open)menus.filter(other=>other!==menu).forEach(other=>{other.open=false})}));document.addEventListener('click',event=>{if(!event.target.closest('.blog-admin-card__menu'))menus.forEach(menu=>{menu.open=false})});document.addEventListener('keydown',event=>{if(event.key==='Escape')menus.forEach(menu=>{menu.open=false})})});
</script>
@endsection
