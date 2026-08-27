@extends('admin.layout')

@section('title', 'Website content | Trans Globe Indore LMS')

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Trans Globe Indore LMS</span><h1>Manage website content</h1></div>
        <p>Open a page to edit it in guided steps, inspect its image usage and control draft, publish and unpublish states.</p>
    </section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <section class="grid content-pages" aria-label="Website pages">
        @foreach($pages as $page)
            @php($pageState = $states->get($page['key']))
            <article class="content-card">
                <span class="content-card__type">{{ str_starts_with($page['key'], 'destination.') ? 'Destination' : (str_starts_with($page['key'], 'service.') ? 'Service' : (str_starts_with($page['key'], 'scholarship.') ? 'Scholarship' : (str_starts_with($page['key'], 'test.') ? 'Test prep' : 'Landing page'))) }}</span>
                <h2>{{ $page['name'] }}</h2><p>{{ $page['description'] }}</p>
                <div class="content-card__foot"><span>{{ $saved[$page['key']] ?? 0 }} saved field{{ ($saved[$page['key']] ?? 0) === 1 ? '' : 's' }} · {{ $pageState?->status ? ucfirst($pageState->status) : 'Published baseline' }}</span><a class="button" href="{{ route('admin.pages.edit', $page['key']) }}">Edit page <span aria-hidden="true">→</span></a></div>
            </article>
        @endforeach
    </section>
@endsection
