@extends('admin.layout')

@section('title', 'Add new page | Trans Globe Indore LMS')
@section('crumb', 'Add page')
@section('backUrl', route('admin.pages.index', ['group' => request('group', 'landing')]))
@section('backLabel', 'Back to pages')

@push('styles')
    <style>
        .create-page{display:grid;grid-template-columns:minmax(0,1fr) 290px;align-items:start;gap:18px}.create-page__form{display:grid;gap:18px;padding:22px}.create-page__grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.create-page__full{grid-column:1/-1}.create-page__actions{display:flex;justify-content:flex-end;gap:9px;padding-top:3px}.create-page__aside{display:grid;gap:12px;padding:20px}.create-page__aside h2{margin:0;font-size:14px}.create-page__aside p{margin:0;color:#8492ab;font-size:11px}.create-page__note{padding:13px;border-radius:12px;background:var(--admin-primary-soft);color:#8e3b47;font-size:10px;line-height:1.6}.create-page__note strong{display:block;margin-bottom:3px;color:var(--admin-primary-dark);font-size:11px}@media(max-width:900px){.create-page{grid-template-columns:1fr}}@media(max-width:620px){.create-page__grid{grid-template-columns:1fr}.create-page__full{grid-column:auto}.create-page__actions{align-items:stretch;flex-direction:column-reverse}.create-page__actions .button{width:100%}}
    </style>
@endpush

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Website content</span><h1>Add a new page</h1></div>
        <p>Create the page as a private draft first. Nothing appears publicly until you review and publish it.</p>
    </section>

    <section class="create-page">
        <form class="panel create-page__form" method="post" action="{{ route('admin.pages.store') }}">
            @csrf
            <div class="create-page__grid">
                <div class="field"><label for="page-name">Page name</label><input class="input" id="page-name" name="name" value="{{ old('name') }}" maxlength="160" required autocomplete="off"><small>Used in the dashboard and as the default page heading.</small>@error('name')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field"><label for="page-group">Page group</label><select class="select" id="page-group" name="group" required data-page-group>@foreach($groups as $key => $group)<option value="{{ $key }}" @selected(old('group', $selectedGroup) === $key)>{{ $group['label'] }}</option>@endforeach</select><small>Controls where the page is grouped and its public URL. The existing Home page remains unchanged.</small>@error('group')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field create-page__full"><label for="page-slug">Page URL</label><div style="display:flex;align-items:center;gap:8px"><span style="color:#9aa6ba;font-size:11px;white-space:nowrap" data-url-prefix>/</span><input class="input" id="page-slug" name="slug" value="{{ old('slug') }}" maxlength="120" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="example-page" required autocomplete="off"></div><small>Use lowercase letters, numbers and hyphens. Home and system URLs are protected.</small>@error('slug')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field create-page__full"><label for="page-description">Dashboard description</label><textarea class="input" id="page-description" name="description" maxlength="500">{{ old('description') }}</textarea><small>A short internal summary to help editors identify the page.</small>@error('description')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field create-page__full"><label for="hero-title">Hero title</label><input class="input" id="hero-title" name="hero_title" value="{{ old('hero_title') }}" maxlength="220" required>@error('hero_title')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field create-page__full"><label for="hero-copy">Hero description</label><textarea class="input" id="hero-copy" name="hero_copy" maxlength="1200" required>{{ old('hero_copy') }}</textarea>@error('hero_copy')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
                <div class="field create-page__full"><label for="hero-image">Hero image URL</label><input class="input" id="hero-image" name="hero_image" value="{{ old('hero_image', 'assets/services/expert-counselling.jpg') }}" maxlength="1200" inputmode="url"><small>Use a Media Library path or an absolute image URL.</small>@error('hero_image')<small style="color:#c81420">{{ $message }}</small>@enderror</div>
            </div>
            <div class="create-page__actions"><a class="button button--quiet" href="{{ route('admin.pages.index', ['group' => $selectedGroup]) }}">Cancel</a><button class="button" type="submit">Create draft page <span aria-hidden="true">→</span></button></div>
        </form>
        <aside class="panel create-page__aside"><span class="eyebrow">Safe publishing</span><h2>Home stays protected</h2><p>The existing Home page cannot be duplicated or replaced from this form.</p><div class="create-page__note"><strong>What happens next?</strong>The new page opens in the guided editor as a draft. Add the remaining content and publish only after previewing it.</div></aside>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const group = document.querySelector('[data-page-group]');
            const prefix = document.querySelector('[data-url-prefix]');
            if (!group || !prefix) return;
            const prefixes = {landing: '/', promotions: '/promotions/', destinations: '/destinations/', services: '/services/', events: '/events/', scholarships: '/scholarships/', tests: '/tests/'};
            const updatePrefix = () => { prefix.textContent = prefixes[group.value] || '/'; };
            group.addEventListener('change', updatePrefix);
            updatePrefix();
        })();
    </script>
@endpush
