<div data-follow-up-details>
    <form class="panel crm-profile-form" method="post" action="{{ $record ? route('admin.crm.follow-ups.update', $record->id) : route('admin.crm.follow-ups.store', $student->id) }}"
        @if($record) data-crm-student-form data-student-id="{{ $record->id }}" data-saved-message="Follow-up saved. Remark history is preserved." @endif>
        @csrf
        @if($record) @method('PATCH')<input type="hidden" name="version" value="{{ old('version', $record->version) }}">@else<input type="hidden" name="creation_key" value="{{ old('creation_key', (string) \Illuminate\Support\Str::uuid()) }}">@endif
        <div class="notice" role="alert" tabindex="-1" data-crm-errors hidden></div><p role="status" aria-atomic="true" data-crm-status></p>
        @if($record)<a class="button button--quiet" href="{{ route('admin.crm.follow-ups.show', $record->id) }}" data-crm-reload hidden>Reload latest follow-up</a>@endif
        <fieldset @disabled(!auth()->user()->can('crm.manage'))><legend>Schedule and ownership</legend><div class="crm-form-grid">
            <label>Subject *<input class="input" name="subject" maxlength="160" required value="{{ old('subject', $record?->subject) }}">@error('subject')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Due date and time (IST) *<input class="input" name="due_at" type="datetime-local" required value="{{ old('due_at', $record?->due_at->timezone('Asia/Kolkata')->format('Y-m-d\TH:i')) }}">@error('due_at')<small class="crm-error">{{ $message }}</small>@enderror</label>
            <label>Channel<select class="select" name="channel">@foreach($channels as $key => $label)<option value="{{ $key }}" @selected(old('channel', $record?->channel ?? 'phone') === $key)>{{ $label }}</option>@endforeach</select></label>
            <label>Status<select class="select" name="status">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(old('status', $record?->status ?? 'new') === $key)>{{ $label }}</option>@endforeach</select></label>
            @can('crm.assign')<label>Owner<select class="select" name="owner_id">@foreach($staff as $member)<option value="{{ $member->id }}" @selected((string) old('owner_id', $record?->owner_id ?? auth()->id()) === (string) $member->id)>{{ $member->name }}</option>@endforeach</select>@error('owner_id')<small class="crm-error">{{ $message }}</small>@enderror</label>@endcan
            <label>Add remark<textarea class="input" name="remark" rows="4" maxlength="4000">{{ old('remark') }}</textarea><small class="crm-help">Added to the history; previous remarks cannot be overwritten.</small>@error('remark')<small class="crm-error">{{ $message }}</small>@enderror</label>
        </div></fieldset>
        @can('crm.manage')<button class="button" type="submit">{{ $record ? 'Save follow-up' : 'Schedule follow-up' }}</button>@endcan
    </form>
    @if($record)<section class="panel crm-history" data-follow-up-history><h2>Remark history</h2>@forelse($notes as $note)<article><strong>{{ $note->actor_name ?: 'Former staff member' }}</strong><p style="white-space:pre-wrap">{{ $note->remark }}</p><time>{{ \Carbon\CarbonImmutable::parse($note->created_at, 'UTC')->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST</time></article>@empty<p>No remarks added.</p>@endforelse{{ $notes->links() }}</section>@endif
</div>
