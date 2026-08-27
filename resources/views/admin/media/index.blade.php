@extends('admin.layout')

@section('title', 'Media library | Trans Globe Indore LMS')

@section('content')
    <section class="page-head"><div><span class="eyebrow">GEIC media library</span><h1>Upload website images</h1></div><p>Upload an image, then copy its path into a page’s Hero image URL field.</p></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}@if(session('latestAsset')) <code>{{ session('latestAsset') }}</code>@endif</div>@endif
    <section class="grid content-grid" style="margin-top:0">
        <form class="panel editor__fields" method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="field"><label for="image">Image file</label><input class="input" id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" required><small>JPG, PNG or WebP · maximum 6 MB.</small>@error('image')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
            <div class="field"><label for="alt_text">Image description</label><input class="input" id="alt_text" name="alt_text" value="{{ old('alt_text') }}" maxlength="180" placeholder="Describe the image for visitors using screen readers">@error('alt_text')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
            <div><button class="button" type="submit">Upload image</button></div>
        </form>
        <aside class="panel editor__hint"><h2>Use an uploaded image</h2><p>After uploading, copy the displayed path. Paste it in the required page’s Hero image URL field and save.</p><p>Every public page keeps its existing image until you change it.</p></aside>
    </section>
    <section class="media-grid" style="margin-top:20px" aria-label="Uploaded images">
        @forelse($assets as $asset)
            <article class="media-card"><img src="{{ asset($asset->path) }}" alt="{{ $asset->alt_text ?: '' }}" loading="lazy"><div class="media-card__body"><strong style="display:block;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $asset->original_name }}</strong><code>{{ $asset->path }}</code></div></article>
        @empty
            <div class="panel empty" style="grid-column:1/-1"><h3>Your image library is empty</h3><p>Upload a Trans Globe Indore image above to make it available in the content editor.</p></div>
        @endforelse
    </section>
    <div class="pagination">{{ $assets->onEachSide(1)->links() }}</div>
@endsection
