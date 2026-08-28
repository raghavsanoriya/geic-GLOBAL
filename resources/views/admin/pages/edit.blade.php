@extends('admin.layout')

@section('title', 'Edit '.$page['name'].' | Trans Globe Indore LMS')
@section('crumb', 'Edit page')
@section('backUrl', route('admin.pages.index'))
@section('backLabel', 'Back to pages')

@push('styles')
    <style>
        .cms-workflow{display:grid;grid-template-columns:minmax(0,1fr) 300px;align-items:start;gap:18px}.cms-editor{padding:0;overflow:hidden}.cms-editor__top{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:20px 22px;border-bottom:1px solid var(--admin-line)}.cms-editor__top h2{margin:0;color:var(--admin-ink);font-size:15px;letter-spacing:-.035em}.cms-editor__top p{margin:3px 0 0;color:#91a0b9;font-size:11px}.cms-state{display:inline-flex;min-height:29px;align-items:center;gap:7px;padding:0 10px;border-radius:99px;background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-size:10px;font-weight:800;white-space:nowrap}.cms-state:before{width:7px;height:7px;border-radius:50%;background:currentColor;content:''}.cms-state--published{background:#ecfdf3;color:#16734d}.cms-state--unpublished{background:#f3f5f8;color:#758198}.cms-stepper{display:flex;align-items:center;overflow-x:auto;padding:18px 22px;border-bottom:1px solid var(--admin-line);background:#fbfcff;scrollbar-width:none}.cms-stepper::-webkit-scrollbar{display:none}.cms-stepper__item{display:flex;min-width:44px;align-items:center;gap:9px;flex:0 0 auto;padding:0;border:0;background:transparent;color:#91a0b8;text-align:left;white-space:nowrap;cursor:pointer}.cms-stepper__marker{display:grid;width:44px;height:44px;place-items:center;flex:0 0 44px;border:1px solid #e3e9f2;border-radius:50%;background:#f6f8fc;color:#9aa8bc;font-size:10px;font-weight:900;transition:background-color .18s ease,border-color .18s ease,color .18s ease,transform .18s ease}.cms-stepper__label{display:grid;width:0;flex:0 0 auto;overflow:hidden;gap:1px;opacity:0;transition:width .22s ease,opacity .16s ease}.cms-stepper__label small{color:#9ba8bb;font-size:9px;font-weight:700;letter-spacing:.02em}.cms-stepper__label strong{max-width:150px;overflow:hidden;color:#1f2f4d;font-size:11px;line-height:1.25;text-overflow:ellipsis}.cms-stepper__rail{width:18px;height:1px;flex:0 0 18px;margin:0 6px;background:#e2e8f1}.cms-stepper__item:hover .cms-stepper__marker{border-color:#f2aeb5;color:var(--admin-primary);transform:translateY(-1px)}.cms-stepper__item[aria-selected=true] .cms-stepper__marker{border-color:var(--admin-primary);background:var(--admin-primary);color:#fff;box-shadow:0 8px 18px rgba(229,36,46,.2)}.cms-stepper__item[aria-selected=true] .cms-stepper__label{width:160px;opacity:1}.cms-stepper__item[data-state=complete] .cms-stepper__marker{border-color:#f5c4c9;background:#fff1f2;color:var(--admin-primary)}.cms-stepper__item[data-state=complete]+.cms-stepper__rail{background:#f2b7bd}.cms-stepper__item:focus-visible,.cms-next:focus-visible,.cms-previous:focus-visible{outline:3px solid rgba(229,36,46,.28);outline-offset:3px}.cms-step-panel{display:grid;gap:16px;padding:22px}.cms-step-panel[hidden]{display:none}.cms-section{display:grid;gap:15px;margin:0;padding:20px;border:1px solid #e8edf5;border-radius:14px;background:#fbfcff}.cms-section legend{padding:0 8px;color:var(--admin-primary);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.cms-image-control{display:grid;grid-template-columns:112px minmax(0,1fr);align-items:center;gap:12px}.cms-image-control img{width:112px;height:82px;border:1px solid #e2e8f1;border-radius:11px;background:#eef2f7;object-fit:cover}.cms-image-control__inputs{display:grid;gap:8px}.cms-file{padding:8px;border:1px dashed #ccd6e5;border-radius:9px;background:#fff;font-size:10px}.cms-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:17px 22px;border-top:1px solid var(--admin-line);background:#fbfcff}.cms-actions__left,.cms-actions__right{display:flex;flex-wrap:wrap;gap:9px}.cms-button{display:inline-flex;min-height:39px;align-items:center;justify-content:center;gap:7px;padding:0 13px;border:1px solid #dbe3ef;border-radius:9px;background:#fff;color:#4d5e7d;font-size:11px;font-weight:800}.cms-button:hover:not(:disabled){border-color:#facbd0;background:var(--admin-primary-soft);color:var(--admin-primary-dark)}.cms-button:disabled{cursor:not-allowed;opacity:.42}.cms-button--publish{border-color:var(--admin-primary);background:var(--admin-primary);color:#fff}.cms-button--publish:hover{background:var(--admin-primary-dark);box-shadow:0 7px 14px rgba(229,36,46,.18)}.cms-button--danger{border-color:#f4c8cc;color:#bd2632}.cms-inspector{display:grid;gap:15px;padding:20px}.cms-inspector h2{margin:0;font-size:14px;letter-spacing:-.035em}.cms-inspector p{margin:5px 0 0;color:#8f9db5;font-size:11px}.cms-metrics{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.cms-metric{padding:12px;border-radius:12px;background:var(--admin-primary-soft)}.cms-metric strong{display:block;color:var(--admin-primary-dark);font-size:18px;letter-spacing:-.06em}.cms-metric span{display:block;margin-top:2px;color:#a65561;font-size:9px;font-weight:800;line-height:1.3;text-transform:uppercase}.cms-media-list{display:grid;gap:10px}.cms-media{display:grid;grid-template-columns:70px minmax(0,1fr);gap:10px;padding:9px;border:1px solid var(--admin-line);border-radius:12px;background:#fff}.cms-media img{width:70px;height:58px;border-radius:8px;object-fit:cover;background:#f0f3f8}.cms-media__details{min-width:0}.cms-media__details strong,.cms-media__details span,.cms-media__details code{display:block}.cms-media__details strong{overflow:hidden;color:var(--admin-ink);font-size:10px;text-overflow:ellipsis;white-space:nowrap}.cms-media__details span{margin-top:2px;color:#8696b0;font-size:9px}.cms-media__details code{overflow:hidden;margin-top:5px;color:#a05b65;font-size:8px;text-overflow:ellipsis;white-space:nowrap}.cms-media__badge{display:inline-flex;margin-top:5px;padding:2px 5px;border-radius:99px;background:#f3f5f8;color:#6e7d96;font-size:8px;font-weight:800}.cms-divider{height:1px;background:var(--admin-line)}.cms-publication{padding:12px;border-radius:12px;background:#f8fafc}.cms-publication strong{display:block;color:#40516e;font-size:11px}.cms-publication span{display:block;margin-top:3px;color:#95a3ba;font-size:10px}.cms-unpublish-form{margin:0}@media(max-width:1050px){.cms-workflow{grid-template-columns:1fr}.cms-media-list{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.cms-editor__top,.cms-actions{align-items:flex-start;flex-direction:column}.cms-stepper{padding:14px}.cms-stepper__marker{width:40px;height:40px;flex-basis:40px}.cms-stepper__rail{width:12px;flex-basis:12px;margin:0 4px}.cms-stepper__item[aria-selected=true] .cms-stepper__label{width:135px}.cms-step-panel{padding:14px}.cms-section{padding:15px}.cms-actions__left,.cms-actions__right{width:100%}.cms-actions__right .cms-button{flex:1}.cms-media-list{grid-template-columns:1fr}.cms-image-control{grid-template-columns:1fr}.cms-image-control img{width:100%;height:150px}}
        .cms-media-field{display:grid;grid-template-columns:130px minmax(0,1fr);gap:14px;padding:14px;border:1px solid #e3e9f2;border-radius:15px;background:#fff}.cms-media-field__preview{position:relative;min-height:150px;overflow:hidden;border-radius:12px;background:#eef2f7}.cms-media-field__preview img{width:100%;height:100%;min-height:150px;object-fit:cover}.cms-media-field__preview span{position:absolute;left:8px;bottom:8px;padding:4px 7px;border-radius:99px;background:rgba(14,33,69,.82);color:#fff;font-size:8px;font-weight:800;backdrop-filter:blur(6px)}.cms-media-field__main{display:grid;gap:9px;min-width:0}.cms-dropzone{display:grid;min-height:96px;place-items:center;padding:13px;border:1.5px dashed #cbd6e7;border-radius:12px;background:#f8faff;color:#8291aa;text-align:center;cursor:pointer;transition:border-color .18s ease,background-color .18s ease,box-shadow .18s ease}.cms-dropzone:hover,.cms-dropzone.is-dragging{border-color:var(--admin-primary);background:#fff3f4;box-shadow:0 0 0 3px rgba(229,36,46,.07)}.cms-dropzone:focus-visible{outline:3px solid rgba(229,36,46,.25);outline-offset:2px}.cms-dropzone svg{width:25px;height:25px;margin-bottom:5px;fill:none;stroke:var(--admin-primary);stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}.cms-dropzone strong{display:block;color:#34445f;font-size:11px}.cms-dropzone strong span{color:var(--admin-primary);text-decoration:underline}.cms-dropzone small{display:block;margin-top:3px;font-size:9px}.cms-file-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}.cms-media-field__foot{display:flex;align-items:center;justify-content:space-between;gap:10px;color:#91a0b7;font-size:9px}.cms-library-button{min-height:34px;padding:0 11px;border:1px solid #dce4ef;border-radius:8px;background:#fff;color:#435574;font-size:9px;font-weight:800;cursor:pointer}.cms-library-button:hover{border-color:#f3b6bd;background:#fff1f2;color:var(--admin-primary-dark)}.cms-media-dialog{width:min(820px,calc(100% - 28px));max-height:min(720px,calc(100vh - 30px));padding:0;border:0;border-radius:18px;background:#fff;box-shadow:0 28px 90px rgba(14,33,69,.28)}.cms-media-dialog::backdrop{background:rgba(14,33,69,.62);backdrop-filter:blur(4px)}.cms-media-dialog__head{position:sticky;z-index:2;top:0;display:flex;align-items:center;justify-content:space-between;gap:15px;padding:18px 20px;border-bottom:1px solid #e8edf4;background:#fff}.cms-media-dialog__head h2{margin:0;color:#1e2e4a;font-size:17px}.cms-media-dialog__head p{margin:3px 0 0;color:#8b99b0;font-size:10px}.cms-media-dialog__close{display:grid;width:38px;height:38px;place-items:center;border:1px solid #e0e7f1;border-radius:50%;background:#fff;color:#52627e;font-size:20px;cursor:pointer}.cms-library-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;overflow-y:auto;padding:18px 20px 22px}.cms-library-choice{overflow:hidden;padding:0;border:1px solid #e3e9f2;border-radius:12px;background:#fff;text-align:left;cursor:pointer}.cms-library-choice:hover,.cms-library-choice:focus-visible{border-color:var(--admin-primary);box-shadow:0 7px 18px rgba(229,36,46,.1);outline:0}.cms-library-choice img{display:block;width:100%;height:105px;background:#eef2f7;object-fit:cover}.cms-library-choice span{display:block;overflow:hidden;padding:8px;color:#41516e;font-size:9px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}.cms-library-empty{grid-column:1/-1;padding:35px;text-align:center;color:#8492a9}.cms-library-empty strong{display:block;color:#34445f}.cms-library-empty a{display:inline-flex;margin-top:10px}@media(max-width:720px){.cms-media-field{grid-template-columns:1fr}.cms-media-field__preview{min-height:180px}.cms-media-field__preview img{height:180px}.cms-library-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        .cms-dynamic-help{margin:0;color:#8795ac;font-size:11px}.cms-custom-field{display:grid;grid-template-columns:minmax(150px,.6fr) minmax(180px,1fr) auto;align-items:center;gap:10px;padding:12px;border:1px solid #e3e9f2;border-radius:11px;background:#fff}.cms-custom-field strong,.cms-custom-field small{display:block}.cms-custom-field strong{color:#34445f;font-size:11px}.cms-custom-field small{margin-top:3px;color:#91a0b7;font-size:9px}.cms-custom-meta{display:grid;grid-template-columns:1fr 1fr 110px;gap:7px;grid-column:1/-1}.cms-custom-field[data-custom-field]:has(.cms-custom-meta){grid-template-columns:1fr auto}.cms-custom-field[data-custom-field]:has(.cms-custom-meta) .cms-custom-meta{grid-column:1/-1}@media(max-width:700px){.cms-custom-field,.cms-custom-meta{grid-template-columns:1fr}.cms-custom-field .cms-button{width:100%}}
    </style>
@endpush

@section('content')
    @php
        $steps = collect($page['fields'])->groupBy(fn (array $field) => $field['section'] ?? 'Page content');
        $errorStep = $steps->search(fn ($fields) => $fields->contains(fn (array $field) => $errors->has('content.'.$field['key'])));
        $initialStep = $errorStep === false ? 0 : $errorStep;
        $statusClass = match ($workflow['status']) {
            'published' => 'cms-state--published',
            'unpublished' => 'cms-state--unpublished',
            default => '',
        };
        $visualAssetCount = collect($mediaUsage)->sum('usageCount');
        $editableImageCount = collect($mediaUsage)->where('editable', true)->count();
    @endphp

    <section class="page-head">
        <div><span class="eyebrow">Website content</span><h1>{{ $page['name'] }}</h1></div>
        <p>{{ $page['description'] }}</p>
    </section>

    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif

    <section class="cms-workflow">
        <form class="panel cms-editor" method="post" action="{{ route('admin.pages.update', $page['key']) }}" enctype="multipart/form-data" data-cms-wizard data-initial-step="{{ $initialStep }}">
            @csrf @method('PUT')
            <div class="cms-editor__top">
                <div><h2>Guided content workflow</h2><p>Edit one section at a time, then save a draft or publish it live.</p></div>
                <span class="cms-state {{ $statusClass }}">{{ $workflow['label'] }}</span>
            </div>

            <nav class="cms-stepper" aria-label="{{ $page['name'] }} editing steps" role="tablist">
                @foreach($steps as $section => $fields)
                    <button class="cms-stepper__item" type="button" role="tab" id="cms-step-{{ $loop->index }}" aria-controls="cms-panel-{{ $loop->index }}" aria-selected="{{ $loop->index === $initialStep ? 'true' : 'false' }}" data-wizard-step="{{ $loop->index }}">
                        <span class="cms-stepper__marker" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="cms-stepper__label"><small>Step {{ $loop->iteration }} of {{ $steps->count() }}</small><strong>{{ $section }}</strong></span>
                    </button>
                    @unless($loop->last)<span class="cms-stepper__rail" aria-hidden="true"></span>@endunless
                @endforeach
            </nav>

            @foreach($steps as $section => $fields)
                <section class="cms-step-panel" id="cms-panel-{{ $loop->index }}" role="tabpanel" aria-labelledby="cms-step-{{ $loop->index }}" @if($loop->index !== $initialStep) hidden @endif data-wizard-panel="{{ $loop->index }}">
                    <fieldset class="cms-section">
                        <legend>{{ $section }}</legend>
                        @foreach($fields as $field)
                            <div class="field">
                                <label for="content-{{ $field['key'] }}">{{ $field['label'] }}</label>
                                @php($fieldValue = old('content.'.$field['key'], $values[$field['key']] ?? $field['default']))
                                @if($field['type'] === 'textarea')
                                    <textarea class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" maxlength="12000">{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}</textarea>
                                @elseif($field['type'] === 'image')
                                    @php($previewUrl = str_starts_with($fieldValue, 'http://') || str_starts_with($fieldValue, 'https://') ? $fieldValue : asset($fieldValue))
                                    <div class="cms-media-field" data-media-field="{{ $field['key'] }}">
                                        <div class="cms-media-field__preview"><img src="{{ $previewUrl }}" alt="{{ $field['label'] }} preview" loading="lazy" data-image-preview="{{ $field['key'] }}"><span>Current image</span></div>
                                        <div class="cms-media-field__main">
                                            <input class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" value="{{ $fieldValue }}" maxlength="12000" inputmode="url" data-image-path="{{ $field['key'] }}">
                                            <div class="cms-dropzone" role="button" tabindex="0" aria-label="Drag and drop or browse to replace {{ $field['label'] }}" data-dropzone="{{ $field['key'] }}">
                                                <div><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18H6a4 4 0 0 1-.7-7.94A7 7 0 0 1 18.8 9.2 4.5 4.5 0 0 1 18.5 18H17"/><path d="m9 12 3-3 3 3M12 9v9"/></svg><strong>Drag &amp; drop or <span>browse</span></strong><small>JPG, PNG, WebP or GIF · maximum 6 MB</small></div>
                                            </div>
                                            <input class="cms-file-hidden" type="file" name="content_images[{{ $field['key'] }}]" accept="image/jpeg,image/png,image/webp,image/gif" aria-label="Upload replacement for {{ $field['label'] }}" data-image-upload="{{ $field['key'] }}">
                                            <div class="cms-media-field__foot"><span data-file-status="{{ $field['key'] }}">No new file selected</span><button class="cms-library-button" type="button" data-open-media-library="{{ $field['key'] }}">Choose from library</button></div>
                                        </div>
                                    </div>
                                @else
                                    <input class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" value="{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}" maxlength="12000" @if($field['type'] === 'image') inputmode="url" @endif>
                                @endif
                                @if($field['type'] === 'image')<small>Choose an existing library image, upload a new one, or paste an absolute image URL.</small>@endif
                                @error('content.'.$field['key'])<small style="color:#c81420">{{ $message }}</small>@enderror
                            </div>
                        @endforeach
                    </fieldset>
                </section>
            @endforeach

            <section class="cms-step-panel" data-custom-fields-panel>
                <fieldset class="cms-section">
                    <legend>Dynamic form fields</legend>
                    <p class="cms-dynamic-help">Add page-specific fields without code. These values are saved with this page and can be edited or removed anytime.</p>
                    <div data-custom-fields-list>
                        @foreach($page['fields'] as $field)
                            @if($field['custom'] ?? false)
                                <div class="cms-custom-field" data-custom-field>
                                    <input type="hidden" name="custom_fields[{{ $loop->index }}][key]" value="{{ $field['key'] }}" data-custom-key>
                                    <input type="hidden" name="custom_fields[{{ $loop->index }}][label]" value="{{ $field['label'] }}" data-custom-label>
                                    <input type="hidden" name="custom_fields[{{ $loop->index }}][type]" value="{{ $field['type'] }}" data-custom-type>
                                    <input type="hidden" name="custom_fields[{{ $loop->index }}][section]" value="{{ $field['section'] }}" data-custom-section>
                                    <div><strong>{{ $field['label'] }}</strong><small>{{ $field['key'] }} · {{ $field['type'] }}</small></div>
                                    <input class="input" name="content[{{ $field['key'] }}]" value="{{ $values[$field['key']] ?? '' }}" maxlength="12000" aria-label="{{ $field['label'] }} value">
                                    <button class="cms-button cms-button--danger" type="button" data-remove-custom-field>Remove</button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button class="cms-button" type="button" data-add-custom-field>+ Add dynamic field</button>
                </fieldset>
            </section>

            <div class="cms-actions">
                <div class="cms-actions__left">
                    <button class="cms-button cms-previous" type="button" data-wizard-previous>← Previous</button>
                    <button class="cms-button cms-next" type="button" data-wizard-next>Next →</button>
                    <a class="cms-button" href="{{ route('admin.pages.index') }}">Back to pages</a>
                </div>
                <div class="cms-actions__right">
                    <button class="cms-button" type="submit" name="intent" value="draft">Save draft</button>
                    <button class="cms-button cms-button--publish" type="submit" name="intent" value="publish">Publish changes</button>
                </div>
            </div>
        </form>

        <aside class="panel cms-inspector" aria-label="Page publishing and media information">
            <div><span class="eyebrow">Page overview</span><h2>Publishing &amp; media</h2><p>See exactly what the editor controls before you make the page live.</p></div>
            <div class="cms-metrics">
                <div class="cms-metric"><strong>{{ $steps->count() }}</strong><span>guided steps</span></div>
                <div class="cms-metric"><strong>{{ $visualAssetCount }}</strong><span>visual assets</span></div>
            </div>
            <div class="cms-publication"><strong>{{ $workflow['label'] }}</strong><span>@if($workflow['publishedAt']) Last published {{ $workflow['publishedAt']->format('d M Y, h:i A') }}@elseif($workflow['status'] === 'draft') Not visible to visitors until published.@else This page uses the currently approved site baseline.@endif</span></div>
            <div class="cms-divider"></div>
            <div><h2>Media usage</h2><p>{{ $visualAssetCount }} visual asset{{ $visualAssetCount === 1 ? '' : 's' }} across {{ count($mediaUsage) }} page group{{ count($mediaUsage) === 1 ? '' : 's' }}. {{ $editableImageCount }} can be changed here.</p></div>
            <div class="cms-media-list">
                @forelse($mediaUsage as $media)
                    @php($mediaUrl = str_starts_with($media['draftPath'], 'http://') || str_starts_with($media['draftPath'], 'https://') ? $media['draftPath'] : asset($media['draftPath']))
                    <article class="cms-media">
                        <img src="{{ $mediaUrl }}" alt="{{ $media['label'] }} preview" loading="lazy">
                        <div class="cms-media__details"><strong>{{ $media['label'] }}</strong><span>{{ $media['section'] }} · {{ $media['editable'] ? ($media['libraryAsset'] ? 'Media library asset' : 'Editable image slot') : 'Shared site asset' }}</span><code title="{{ $media['draftPath'] }}">{{ $media['draftPath'] }}</code><span class="cms-media__badge">{{ $media['usageCount'] }} use{{ $media['usageCount'] === 1 ? '' : 's' }} · {{ $media['status'] }}</span></div>
                    </article>
                @empty
                    <div class="cms-publication"><strong>No editable image slots</strong><span>This page currently uses shared site imagery.</span></div>
                @endforelse
            </div>
            <a class="button button--quiet" href="{{ route('admin.media.index') }}">Open media library</a>
            @if($workflow['status'] === 'published')
                <form class="cms-unpublish-form" method="post" action="{{ route('admin.pages.unpublish', $page['key']) }}">
                    @csrf @method('DELETE')
                    <button class="cms-button cms-button--danger" type="submit">Unpublish CMS version</button>
                </form>
            @endif
        </aside>
    </section>

    <template data-custom-field-template>
        <div class="cms-custom-field" data-custom-field>
            <input type="hidden" name="custom_fields[__INDEX__][key]" value="" data-custom-key>
            <input type="hidden" name="custom_fields[__INDEX__][label]" value="" data-custom-label>
            <input type="hidden" name="custom_fields[__INDEX__][type]" value="text" data-custom-type>
            <input type="hidden" name="custom_fields[__INDEX__][section]" value="Custom fields" data-custom-section>
            <div class="cms-custom-meta"><input class="input" placeholder="Field key (e.g. intake_note)" data-edit-key><input class="input" placeholder="Label" data-edit-label><select class="input" data-edit-type><option value="text">Short text</option><option value="textarea">Long text</option><option value="image">Image URL</option></select></div>
            <input class="input" name="content[__KEY__]" value="" maxlength="12000" placeholder="Field value" data-custom-value>
            <button class="cms-button cms-button--danger" type="button" data-remove-custom-field>Remove</button>
        </div>
    </template>

    <script>
    (() => {
        const list = document.querySelector('[data-custom-fields-list]');
        const template = document.querySelector('[data-custom-field-template]');
        const add = document.querySelector('[data-add-custom-field]');
        if (!list || !template || !add) return;
        let index = {{ count($page['fields']) + 1 }};
        const bind = (row) => {
            row.querySelector('[data-remove-custom-field]')?.addEventListener('click', () => {
                const key = row.querySelector('[data-custom-key]')?.value;
                if (key) { const input = document.createElement('input'); input.type='hidden'; input.name='remove_fields[]'; input.value=key; row.appendChild(input); }
                row.style.display='none';
            });
            const sync = () => {
                const key = row.querySelector('[data-edit-key]')?.value || row.querySelector('[data-custom-key]')?.value;
                if (!key) return;
                row.querySelector('[data-custom-key]').value = key;
                row.querySelector('[data-custom-label]').value = row.querySelector('[data-edit-label]')?.value || key;
                row.querySelector('[data-custom-type]').value = row.querySelector('[data-edit-type]')?.value || 'text';
                const value = row.querySelector('[data-custom-value]'); if (value) value.name = `content[${key}]`;
            };
            row.querySelectorAll('[data-edit-key],[data-edit-label],[data-edit-type]').forEach(el => el.addEventListener('input', sync));
            row.querySelectorAll('[data-edit-key],[data-edit-label],[data-edit-type]').forEach(el => el.addEventListener('change', sync));
        };
        list.querySelectorAll('[data-custom-field]').forEach(bind);
        add.addEventListener('click', () => { const key = String(index++); const html = template.innerHTML.replaceAll('__INDEX__', key).replaceAll('__KEY__', `custom_${key}`); const wrap = document.createElement('div'); wrap.innerHTML = html.trim(); const row = wrap.firstElementChild; list.appendChild(row); bind(row); });
    })();
    </script>

    <dialog class="cms-media-dialog" data-media-dialog aria-labelledby="cms-media-library-title">
        <div class="cms-media-dialog__head"><div><h2 id="cms-media-library-title">Choose from media library</h2><p>Select an uploaded image for the current page field.</p></div><button class="cms-media-dialog__close" type="button" aria-label="Close media library" data-close-media-library>×</button></div>
        <div class="cms-library-grid">
            @forelse($libraryAssets as $asset)
                <button class="cms-library-choice" type="button" data-media-choice="{{ $asset->path }}" data-media-alt="{{ $asset->alt_text }}"><img src="{{ asset($asset->path) }}" alt="{{ $asset->alt_text ?: '' }}" loading="lazy"><span title="{{ $asset->original_name }}">{{ $asset->original_name }}</span></button>
            @empty
                <div class="cms-library-empty"><strong>Your media library is empty.</strong><span>Upload an image first, then it will appear here.</span><a class="button" href="{{ route('admin.media.index') }}">Open media library</a></div>
            @endforelse
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        (() => {
            const wizard = document.querySelector('[data-cms-wizard]');
            if (!wizard) return;

            const steps = Array.from(wizard.querySelectorAll('[data-wizard-step]'));
            const panels = Array.from(wizard.querySelectorAll('[data-wizard-panel]'));
            const previous = wizard.querySelector('[data-wizard-previous]');
            const next = wizard.querySelector('[data-wizard-next]');
            let activeStep = Number(wizard.dataset.initialStep || 0);

            function showStep(step) {
                activeStep = Math.max(0, Math.min(step, steps.length - 1));
                steps.forEach((item, index) => {
                    const isActive = index === activeStep;
                    item.setAttribute('aria-selected', String(isActive));
                    item.setAttribute('tabindex', isActive ? '0' : '-1');
                    item.dataset.state = isActive ? 'current' : (index < activeStep ? 'complete' : 'upcoming');
                });
                panels.forEach((panel, index) => panel.hidden = index !== activeStep);
                previous.disabled = activeStep === 0;
                next.disabled = activeStep === steps.length - 1;
                steps[activeStep].scrollIntoView({block: 'nearest', inline: 'center'});
            }

            steps.forEach((step, index) => step.addEventListener('click', () => showStep(index)));
            steps.forEach((step) => step.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
                event.preventDefault();
                const target = event.key === 'Home' ? 0 : event.key === 'End' ? steps.length - 1 : activeStep + (event.key === 'ArrowRight' ? 1 : -1);
                showStep(target);
                steps[activeStep].focus();
            }));
            previous.addEventListener('click', () => showStep(activeStep - 1));
            next.addEventListener('click', () => showStep(activeStep + 1));
            const mediaDialog = document.querySelector('[data-media-dialog]');
            let activeMediaField = null;

            function updateFilePreview(input) {
                const key = input.dataset.imageUpload;
                const file = input.files?.[0];
                const preview = wizard.querySelector(`[data-image-preview="${key}"]`);
                const status = wizard.querySelector(`[data-file-status="${key}"]`);
                if (!file || !preview) return;
                preview.src = URL.createObjectURL(file);
                if (status) status.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            }

            wizard.querySelectorAll('[data-image-upload]').forEach((input) => {
                const key = input.dataset.imageUpload;
                const dropzone = wizard.querySelector(`[data-dropzone="${key}"]`);
                input.addEventListener('change', () => updateFilePreview(input));
                if (!dropzone) return;
                dropzone.addEventListener('click', () => input.click());
                dropzone.addEventListener('keydown', (event) => {
                    if (!['Enter', ' '].includes(event.key)) return;
                    event.preventDefault();
                    input.click();
                });
                ['dragenter', 'dragover'].forEach((name) => dropzone.addEventListener(name, (event) => {
                    event.preventDefault();
                    dropzone.classList.add('is-dragging');
                }));
                ['dragleave', 'drop'].forEach((name) => dropzone.addEventListener(name, (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('is-dragging');
                }));
                dropzone.addEventListener('drop', (event) => {
                    if (!event.dataTransfer?.files?.length) return;
                    input.files = event.dataTransfer.files;
                    updateFilePreview(input);
                });
            });

            wizard.querySelectorAll('[data-image-path]').forEach((input) => input.addEventListener('change', () => {
                const preview = wizard.querySelector(`[data-image-preview="${input.dataset.imagePath}"]`);
                if (preview && input.value) preview.src = input.value.startsWith('http') ? input.value : `/${input.value.replace(/^\//, '')}`;
            }));

            wizard.querySelectorAll('[data-open-media-library]').forEach((button) => button.addEventListener('click', () => {
                activeMediaField = button.dataset.openMediaLibrary;
                mediaDialog?.showModal();
            }));
            document.querySelector('[data-close-media-library]')?.addEventListener('click', () => mediaDialog?.close());
            document.querySelectorAll('[data-media-choice]').forEach((choice) => choice.addEventListener('click', () => {
                if (!activeMediaField) return;
                const pathInput = wizard.querySelector(`[data-image-path="${activeMediaField}"]`);
                const uploadInput = wizard.querySelector(`[data-image-upload="${activeMediaField}"]`);
                const preview = wizard.querySelector(`[data-image-preview="${activeMediaField}"]`);
                const status = wizard.querySelector(`[data-file-status="${activeMediaField}"]`);
                if (pathInput) pathInput.value = choice.dataset.mediaChoice;
                if (uploadInput) uploadInput.value = '';
                if (preview) preview.src = choice.querySelector('img')?.src || '';
                if (status) status.textContent = 'Selected from media library';
                mediaDialog?.close();
            }));
            showStep(activeStep);
        })();
    </script>
@endpush
