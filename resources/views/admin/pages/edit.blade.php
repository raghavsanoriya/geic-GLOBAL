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
            @foreach(collect($page['fields'])->groupBy(fn (array $field) => $field['section'] ?? 'Page content') as $section => $fields)
                <fieldset style="display:grid;gap:15px;margin:0;padding:20px;border:1px solid #e8edf5;border-radius:13px;background:#fbfcff">
                    <legend style="padding:0 8px;color:var(--admin-primary);font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase">{{ $section }}</legend>
                    @foreach($fields as $field)
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
                </fieldset>
            @endforeach
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px"><button class="button" type="submit">Save changes</button><a class="button button--quiet" href="{{ route('admin.pages.index') }}">Back to pages</a></div>
        </form>
        <aside class="panel editor__hint"><h2>Everything on one page</h2><p>Use the grouped controls to manage the header, hero, main page sections, conversion CTA and footer without editing code.</p><p>Only the sections shown here change. Leave a value as provided to keep the current approved copy.</p><a class="button button--quiet" href="{{ route('admin.media.index') }}">Open media library</a></aside>
    </section>
@endsection
