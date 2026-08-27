@extends('admin.layout')

@section('title', 'Media library | Trans Globe Indore LMS')
@section('crumb', 'Media library')
@section('backUrl', route('admin.dashboard'))
@section('backLabel', 'Back to dashboard')

@push('styles')
<style>
    .media-upload-layout{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(260px,.6fr);gap:18px;align-items:start}.media-upload{padding:22px}.media-upload__head h2{margin:0;color:#22324f;font-size:17px}.media-upload__head p{margin:5px 0 0;color:#8a98b0;font-size:11px}.media-dropzone{display:grid;min-height:230px;place-items:center;margin-top:18px;padding:25px;border:2px dashed #cad5e6;border-radius:16px;background:#f7faff;color:#8290a8;text-align:center;cursor:pointer;transition:border-color .18s ease,background-color .18s ease,box-shadow .18s ease}.media-dropzone:hover,.media-dropzone.is-dragging{border-color:var(--admin-primary);background:#fff3f4;box-shadow:0 0 0 4px rgba(229,36,46,.06)}.media-dropzone:focus-visible{outline:3px solid rgba(229,36,46,.26);outline-offset:3px}.media-dropzone svg{width:42px;height:42px;fill:none;stroke:var(--admin-primary);stroke-linecap:round;stroke-linejoin:round;stroke-width:1.55}.media-dropzone strong{display:block;margin-top:12px;color:#2e3f5d;font-size:14px}.media-dropzone strong span{color:var(--admin-primary);text-decoration:underline}.media-dropzone p{margin:5px 0 0;font-size:10px}.media-file{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}.media-upload__meta{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:10px;color:#8d9bb2;font-size:10px}.media-upload__preview{display:none;align-items:center;gap:11px;margin-top:12px;padding:10px;border:1px solid #e1e7f0;border-radius:12px;background:#fff}.media-upload__preview.is-visible{display:flex}.media-upload__preview img{width:62px;height:52px;border-radius:8px;background:#eef2f7;object-fit:cover}.media-upload__preview strong,.media-upload__preview span{display:block}.media-upload__preview strong{max-width:340px;overflow:hidden;color:#33445f;font-size:11px;text-overflow:ellipsis;white-space:nowrap}.media-upload__preview span{margin-top:3px;color:#8b99af;font-size:9px}.media-upload__fields{display:grid;gap:12px;margin-top:15px}.media-upload__actions{display:flex;justify-content:flex-end;margin-top:15px}.media-library-help{padding:21px}.media-library-help__icon{display:grid;width:48px;height:48px;place-items:center;border-radius:13px;background:#fff0f1;color:var(--admin-primary)}.media-library-help__icon svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:1.8}.media-library-help h2{margin:16px 0 0;color:#263754;font-size:15px}.media-library-help p{margin:6px 0 0;color:#8795ac;font-size:11px}.media-library-help ul{display:grid;gap:9px;margin:16px 0 0;padding:0;list-style:none}.media-library-help li{display:flex;gap:8px;color:#53637e;font-size:10px}.media-library-help li:before{display:grid;width:18px;height:18px;place-items:center;flex:0 0 18px;border-radius:50%;background:#edfff7;color:#13875e;font-size:9px;content:'✓'}.media-section-head{display:flex;align-items:end;justify-content:space-between;gap:15px;margin:24px 0 12px}.media-section-head h2{margin:0;color:#253653;font-size:17px}.media-section-head p{margin:4px 0 0;color:#8c9ab0;font-size:10px}@media(max-width:900px){.media-upload-layout{grid-template-columns:1fr}}@media(max-width:620px){.media-upload{padding:16px}.media-dropzone{min-height:190px}.media-upload__meta{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
    <section class="page-head"><div><span class="eyebrow">GEIC media library</span><h1>Website media</h1></div><p>Upload once, then select the image directly inside any page editor.</p></section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}@if(session('latestAsset')) <code>{{ session('latestAsset') }}</code>@endif</div>@endif
    <section class="media-upload-layout">
        <form class="panel media-upload" method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="media-upload__head"><h2>Upload a new image</h2><p>Add an image that can be reused across all website pages.</p></div>
            <div class="media-dropzone" role="button" tabindex="0" aria-label="Drag and drop an image or browse files" data-media-dropzone><div><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18H6a4 4 0 0 1-.7-7.94A7 7 0 0 1 18.8 9.2 4.5 4.5 0 0 1 18.5 18H17"/><path d="m9 12 3-3 3 3M12 9v9"/></svg><strong>Drag &amp; drop your image or <span>browse</span></strong><p>Use a clear, high-quality image suitable for the website.</p></div></div>
            <input class="media-file" id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" required data-media-file>
            <div class="media-upload__meta"><span>Supported formats: JPG, PNG, WebP</span><span>Maximum size: 6 MB</span></div>
            <div class="media-upload__preview" data-media-preview><img src="" alt="Selected image preview"><div><strong data-media-file-name></strong><span data-media-file-size></span></div></div>
            @error('image')<small style="display:block;margin-top:8px;color:#c81420">{{ $message }}</small>@enderror
            <div class="media-upload__fields"><div class="field"><label for="alt_text">Image description</label><input class="input" id="alt_text" name="alt_text" value="{{ old('alt_text') }}" maxlength="180" placeholder="Describe the image for visitors using screen readers">@error('alt_text')<small style="color:#c81420">{{ $message }}</small>@enderror</div></div>
            <div class="media-upload__actions"><button class="button" type="submit">Upload image</button></div>
        </form>
        <aside class="panel media-library-help"><span class="media-library-help__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><circle cx="9" cy="9" r="1.5"/><path d="m6 17 4-4 3 3 2-2 3 3"/></svg></span><h2>Available everywhere</h2><p>Every successful upload is added to the library automatically.</p><ul><li>Open any page in Website content.</li><li>Find an image field and choose “Choose from library”.</li><li>Pick an image, save a draft, or publish it live.</li></ul></aside>
    </section>
    <div class="media-section-head"><div><h2>Uploaded images</h2><p>{{ $assets->total() }} reusable image{{ $assets->total() === 1 ? '' : 's' }} currently in the library.</p></div></div>
    <section class="media-grid" aria-label="Uploaded images">
        @forelse($assets as $asset)
            <article class="media-card"><img src="{{ asset($asset->path) }}" alt="{{ $asset->alt_text ?: '' }}" loading="lazy"><div class="media-card__body"><strong style="display:block;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $asset->original_name }}</strong><code>{{ $asset->path }}</code></div></article>
        @empty
            <div class="panel empty" style="grid-column:1/-1"><h3>Your image library is empty</h3><p>Upload a Trans Globe Indore image above to make it available in every content editor.</p></div>
        @endforelse
    </section>
    <div class="pagination">{{ $assets->onEachSide(1)->links() }}</div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.querySelector('[data-media-file]');
        const dropzone = document.querySelector('[data-media-dropzone]');
        const preview = document.querySelector('[data-media-preview]');
        if (!input || !dropzone || !preview) return;
        function showFile() { const file = input.files?.[0]; if (!file) return; preview.querySelector('img').src = URL.createObjectURL(file); preview.querySelector('[data-media-file-name]').textContent = file.name; preview.querySelector('[data-media-file-size]').textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`; preview.classList.add('is-visible'); }
        dropzone.addEventListener('click', () => input.click());
        dropzone.addEventListener('keydown', (event) => { if (!['Enter', ' '].includes(event.key)) return; event.preventDefault(); input.click(); });
        ['dragenter', 'dragover'].forEach((name) => dropzone.addEventListener(name, (event) => { event.preventDefault(); dropzone.classList.add('is-dragging'); }));
        ['dragleave', 'drop'].forEach((name) => dropzone.addEventListener(name, (event) => { event.preventDefault(); dropzone.classList.remove('is-dragging'); }));
        dropzone.addEventListener('drop', (event) => { if (!event.dataTransfer?.files?.length) return; input.files = event.dataTransfer.files; showFile(); });
        input.addEventListener('change', showFile);
    })();
</script>
@endpush
