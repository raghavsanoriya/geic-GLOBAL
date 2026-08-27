@extends('admin.layout')

@section('title', 'Edit '.$page['name'].' | Trans Globe Indore LMS')

@push('styles')
    <style>
        .cms-workflow{display:grid;grid-template-columns:minmax(0,1fr) 300px;align-items:start;gap:18px}.cms-editor{padding:0;overflow:hidden}.cms-editor__top{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:20px 22px;border-bottom:1px solid var(--admin-line)}.cms-editor__top h2{margin:0;color:var(--admin-ink);font-size:15px;letter-spacing:-.035em}.cms-editor__top p{margin:3px 0 0;color:#91a0b9;font-size:11px}.cms-state{display:inline-flex;min-height:29px;align-items:center;gap:7px;padding:0 10px;border-radius:99px;background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-size:10px;font-weight:800;white-space:nowrap}.cms-state:before{width:7px;height:7px;border-radius:50%;background:currentColor;content:''}.cms-state--published{background:#ecfdf3;color:#16734d}.cms-state--unpublished{background:#f3f5f8;color:#758198}.cms-stepper{display:flex;gap:7px;overflow-x:auto;padding:14px 22px;border-bottom:1px solid var(--admin-line);scrollbar-width:thin}.cms-stepper__item{display:grid;min-width:126px;gap:2px;padding:10px 12px;border:1px solid #e4eaf3;border-radius:11px;background:#fff;color:#71809b;text-align:left;white-space:nowrap}.cms-stepper__item small{font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.cms-stepper__item strong{font-size:11px}.cms-stepper__item:hover{border-color:#f5b7bd;color:var(--admin-primary-dark)}.cms-stepper__item[aria-selected=true]{border-color:var(--admin-primary);background:var(--admin-primary);color:#fff;box-shadow:0 7px 16px rgba(229,36,46,.18)}.cms-stepper__item:focus-visible,.cms-next:focus-visible,.cms-previous:focus-visible{outline:3px solid rgba(229,36,46,.28);outline-offset:2px}.cms-stepper__item[aria-selected=true] small{opacity:.76}.cms-step-panel{display:grid;gap:16px;padding:22px}.cms-step-panel[hidden]{display:none}.cms-section{display:grid;gap:15px;margin:0;padding:20px;border:1px solid #e8edf5;border-radius:14px;background:#fbfcff}.cms-section legend{padding:0 8px;color:var(--admin-primary);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.cms-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:17px 22px;border-top:1px solid var(--admin-line);background:#fbfcff}.cms-actions__left,.cms-actions__right{display:flex;flex-wrap:wrap;gap:9px}.cms-button{display:inline-flex;min-height:39px;align-items:center;justify-content:center;gap:7px;padding:0 13px;border:1px solid #dbe3ef;border-radius:9px;background:#fff;color:#4d5e7d;font-size:11px;font-weight:800}.cms-button:hover:not(:disabled){border-color:#facbd0;background:var(--admin-primary-soft);color:var(--admin-primary-dark)}.cms-button:disabled{cursor:not-allowed;opacity:.42}.cms-button--publish{border-color:var(--admin-primary);background:var(--admin-primary);color:#fff}.cms-button--publish:hover{background:var(--admin-primary-dark);box-shadow:0 7px 14px rgba(229,36,46,.18)}.cms-button--danger{border-color:#f4c8cc;color:#bd2632}.cms-inspector{display:grid;gap:15px;padding:20px}.cms-inspector h2{margin:0;font-size:14px;letter-spacing:-.035em}.cms-inspector p{margin:5px 0 0;color:#8f9db5;font-size:11px}.cms-metrics{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.cms-metric{padding:12px;border-radius:12px;background:var(--admin-primary-soft)}.cms-metric strong{display:block;color:var(--admin-primary-dark);font-size:18px;letter-spacing:-.06em}.cms-metric span{display:block;margin-top:2px;color:#a65561;font-size:9px;font-weight:800;line-height:1.3;text-transform:uppercase}.cms-media-list{display:grid;gap:10px}.cms-media{display:grid;grid-template-columns:70px minmax(0,1fr);gap:10px;padding:9px;border:1px solid var(--admin-line);border-radius:12px;background:#fff}.cms-media img{width:70px;height:58px;border-radius:8px;object-fit:cover;background:#f0f3f8}.cms-media__details{min-width:0}.cms-media__details strong,.cms-media__details span,.cms-media__details code{display:block}.cms-media__details strong{overflow:hidden;color:var(--admin-ink);font-size:10px;text-overflow:ellipsis;white-space:nowrap}.cms-media__details span{margin-top:2px;color:#8696b0;font-size:9px}.cms-media__details code{overflow:hidden;margin-top:5px;color:#a05b65;font-size:8px;text-overflow:ellipsis;white-space:nowrap}.cms-media__badge{display:inline-flex;margin-top:5px;padding:2px 5px;border-radius:99px;background:#f3f5f8;color:#6e7d96;font-size:8px;font-weight:800}.cms-divider{height:1px;background:var(--admin-line)}.cms-publication{padding:12px;border-radius:12px;background:#f8fafc}.cms-publication strong{display:block;color:#40516e;font-size:11px}.cms-publication span{display:block;margin-top:3px;color:#95a3ba;font-size:10px}.cms-unpublish-form{margin:0}@media(max-width:1050px){.cms-workflow{grid-template-columns:1fr}.cms-media-list{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.cms-editor__top,.cms-actions{align-items:flex-start;flex-direction:column}.cms-stepper{padding:12px 14px}.cms-step-panel{padding:14px}.cms-section{padding:15px}.cms-actions__left,.cms-actions__right{width:100%}.cms-actions__right .cms-button{flex:1}.cms-media-list{grid-template-columns:1fr}}
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
        <form class="panel cms-editor" method="post" action="{{ route('admin.pages.update', $page['key']) }}" data-cms-wizard data-initial-step="{{ $initialStep }}">
            @csrf @method('PUT')
            <div class="cms-editor__top">
                <div><h2>Guided content workflow</h2><p>Edit one section at a time, then save a draft or publish it live.</p></div>
                <span class="cms-state {{ $statusClass }}">{{ $workflow['label'] }}</span>
            </div>

            <nav class="cms-stepper" aria-label="{{ $page['name'] }} editing steps" role="tablist">
                @foreach($steps as $section => $fields)
                    <button class="cms-stepper__item" type="button" role="tab" id="cms-step-{{ $loop->index }}" aria-controls="cms-panel-{{ $loop->index }}" aria-selected="{{ $loop->index === $initialStep ? 'true' : 'false' }}" data-wizard-step="{{ $loop->index }}">
                        <small>Step {{ $loop->iteration }} of {{ $steps->count() }}</small><strong>{{ $section }}</strong>
                    </button>
                @endforeach
            </nav>

            @foreach($steps as $section => $fields)
                <section class="cms-step-panel" id="cms-panel-{{ $loop->index }}" role="tabpanel" aria-labelledby="cms-step-{{ $loop->index }}" @if($loop->index !== $initialStep) hidden @endif data-wizard-panel="{{ $loop->index }}">
                    <fieldset class="cms-section">
                        <legend>{{ $section }}</legend>
                        @foreach($fields as $field)
                            <div class="field">
                                <label for="content-{{ $field['key'] }}">{{ $field['label'] }}</label>
                                @if($field['type'] === 'textarea')
                                    <textarea class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" maxlength="12000">{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}</textarea>
                                @else
                                    <input class="input" id="content-{{ $field['key'] }}" name="content[{{ $field['key'] }}]" value="{{ old('content.'.$field['key'], $values[$field['key']] ?? $field['default']) }}" maxlength="12000" @if($field['type'] === 'image') inputmode="url" @endif>
                                @endif
                                @if($field['type'] === 'image')<small>This is shown in the Media usage panel. Use a Media Library path or an absolute image URL.</small>@endif
                                @error('content.'.$field['key'])<small style="color:#c81420">{{ $message }}</small>@enderror
                            </div>
                        @endforeach
                    </fieldset>
                </section>
            @endforeach

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
                steps.forEach((item, index) => item.setAttribute('aria-selected', String(index === activeStep)));
                panels.forEach((panel, index) => panel.hidden = index !== activeStep);
                previous.disabled = activeStep === 0;
                next.disabled = activeStep === steps.length - 1;
                steps[activeStep].scrollIntoView({block: 'nearest', inline: 'center'});
            }

            steps.forEach((step, index) => step.addEventListener('click', () => showStep(index)));
            previous.addEventListener('click', () => showStep(activeStep - 1));
            next.addEventListener('click', () => showStep(activeStep + 1));
            showStep(activeStep);
        })();
    </script>
@endpush
