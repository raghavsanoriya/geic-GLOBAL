@if(config('crm.enabled'))
@can('crm.view')
<a class="topbar-icon crm-toolbar-link" href="{{route('admin.crm.search')}}" aria-label="Search CRM workspace" title="Search CRM workspace">
    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/></svg>
</a>
<a class="topbar-icon crm-toolbar-link" href="{{route('admin.crm.reminders.index')}}" aria-label="CRM reminders" title="CRM reminders">
    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4m10-4v4M3 11h18m-9 3v3l2 1"/></svg>
</a>
<button class="topbar-icon" type="button" data-crm-fullscreen aria-label="Enter fullscreen" aria-pressed="false" title="Enter fullscreen" hidden>
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5"/></svg>
</button>
<span class="crm-toolbar-feedback" data-crm-fullscreen-feedback role="status" aria-live="polite"></span>
@pushOnce('scripts','crm-fullscreen')
<script type="module">
    import { setupCrmFullscreen } from '{{asset('assets/transglobe/js/crm-fullscreen.js')}}?v=20261020-1';
    setupCrmFullscreen(document);
</script>
@endPushOnce
@endcan
@endif
