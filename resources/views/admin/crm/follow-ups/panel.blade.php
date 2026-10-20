<section data-follow-up-panel data-follow-up-id="{{ $record->id }}">
    <p class="crm-help">CRM-{{ $student->id }} · <strong>{{ $student->full_name }}</strong></p>
    @include('admin.crm.follow-ups.details')
</section>
