@extends('admin.layout')

@section('title', 'Edit '.$page['name'].' | Trans Globe Indore LMS')

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Website content</span><h1>{{ $page['name'] }}</h1></div>
        <p>{{ $page['description'] }}</p>
    </section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <section class="editor">
        <form class="panel editor__fields" method="post" action="{{ route('admin.pages.update', $page['key']) }}">
            @csrf @method('PUT')
            @foreach($page['fields'] as $field)
                <div class="field">
                    <label for="content-{{ $field['key'] }}">{{ $field['label'] }}</label>
                    @if($field['type'] === 'textarea')
                        <textarea class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" maxlength="12000">{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}</textarea>
                    @else
                        <input class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" value="{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}" maxlength="12000" @if($field['type'] === 'image') inputmode="url" @endif>
                    @endif
                    @if($field['type'] === 'image')<small>Paste a site path such as <code>storage/cms/my-image.webp</code>, or upload an image in the Media library.</small>@endif
                    @error('content.'.$field['key'])<small style="color:#c81420">{{ $message }}</small>@enderror
                </div>
            @endforeach
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px"><button class="button" type="submit">Save changes</button><a class="button button--quiet" href="{{ route('admin.pages.index') }}">Back to pages</a></div>
        </form>
        <aside class="panel editor__hint"><h2>How this works</h2><p>These fields control the primary message and visual at the top of this public page.</p><p>Use a clear title, a short description, and an image URL from your Media library.</p><a class="button button--quiet" href="{{ route('admin.media.index') }}">Open media library</a></aside>
    </section>
@endsection
