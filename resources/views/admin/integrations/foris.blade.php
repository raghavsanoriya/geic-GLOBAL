@extends('admin.layout')

@section('title', 'Foris CRM Sync | GEIC')
@section('crumb', 'Foris CRM Sync')

@section('content')
    <section class="page-head">
        <div><span class="eyebrow">Lead management</span><h1>Foris CRM Sync</h1></div>
        <p>Website and landing-page enquiries stay in GEIC and are delivered automatically to Foris.</p>
    </section>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    <section class="panel foris-overview">
        <div>
            <h2>{{ $configured ? 'Connection enabled' : 'Delivery paused' }}</h2>
            <p>{{ $configured ? 'New submissions normally reach Foris within about one minute.' : 'Enquiries remain safely stored. A super administrator can enable delivery in the protected server configuration.' }}</p>
            <p>Last worker check: <strong>{{ $heartbeat ? \Carbon\Carbon::parse($heartbeat)->timezone('Asia/Kolkata')->format('d M Y, h:i:s A').' IST' : 'Awaiting the first worker run' }}</strong></p>
        </div>
        <a class="button button--quiet" href="https://crm.foriseducation.com/public/marketing/forms" target="_blank" rel="noopener">Open Foris CRM <span aria-hidden="true">↗</span></a>
    </section>
    <div class="foris-counts">
        @foreach($statuses as $state)
            <a class="panel foris-count" href="{{ route('admin.foris.index', ['status' => $state]) }}"><strong>{{ $counts[$state] ?? 0 }}</strong><span>{{ ucfirst(str_replace('_', ' ', $state)) }}</span></a>
        @endforeach
    </div>
    <section class="panel">
        <div class="panel__head">
            <div><h2>Delivery activity</h2><span>{{ $deliveries->total() }} matching deliveries · timestamps shown in India time</span></div>
        </div>
        <form class="filters" method="get" action="{{ route('admin.foris.index') }}">
            <label class="field"><span>Search enquiries</span><input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Name, email or phone"></label>
            <label class="field"><span>Delivery status</span><select class="select" name="status"><option value="">All statuses</option>@foreach($statuses as $state)<option value="{{ $state }}" @selected(request('status') === $state)>{{ ucfirst(str_replace('_', ' ', $state)) }}</option>@endforeach</select></label>
            <button class="button button--quiet" type="submit">Apply filters</button>
        </form>
        <div class="table-wrap"><table>
            <thead><tr><th>GEIC enquiry</th><th>Source</th><th>Delivery</th><th>Foris reference</th><th>Last attempt</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($deliveries as $delivery)
                <tr>
                    <td><strong class="student">{{ $delivery->full_name }}</strong><span class="sub">{{ $delivery->email }}<br>{{ $delivery->phone }} · GEIC-{{ $delivery->enquiry_id }}</span></td>
                    <td>{{ $delivery->source_form ?: 'Website enquiry' }}<span class="sub">{{ $delivery->source_page }}{{ $delivery->is_historical ? ' · Historical import' : '' }}</span></td>
                    <td><span class="pill">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>@if($delivery->last_error)<span class="sub foris-error">{{ $delivery->last_error }}</span>@endif</td>
                    <td>{{ $delivery->remote_lead_id ? 'Lead '.$delivery->remote_lead_id : '—' }}@if($delivery->remote_submission_id)<span class="sub">Submission {{ $delivery->remote_submission_id }}</span>@endif</td>
                    <td>{{ $delivery->attempted_at ? \Carbon\Carbon::parse($delivery->attempted_at)->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'Not sent yet' }}<span class="sub">{{ $delivery->attempts }} attempt{{ $delivery->attempts == 1 ? '' : 's' }}</span></td>
                    <td>@if($delivery->status === 'failed')<form method="post" action="{{ route('admin.foris.retry', $delivery->id) }}">@csrf<button class="button button--quiet" type="submit">Retry delivery</button></form>@elseif($delivery->status === 'uncertain')<span class="sub">Check Foris audit trail before resending.</span>@else<span class="sub">Automatic</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><h3>No matching deliveries</h3><p>New website enquiries and scheduled historical imports will appear here.</p></div></td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="pagination">{{ $deliveries->onEachSide(1)->links() }}</div>
    </section>
    <section class="panel foris-overview">
        <div><h2>Existing GEIC enquiries</h2><p>{{ $remaining }} website or historical WordPress enquiries have not been scheduled. Existing Foris contacts go to its duplicate review queue.</p><p>Email-only newsletter records need a phone number before Foris can create a lead.</p></div>
        @if($remaining > 0)<form method="post" action="{{ route('admin.foris.backfill') }}">@csrf<button class="button" type="submit">Schedule next 200</button></form>@endif
    </section>
@endsection

@push('styles')
<style>
.foris-overview{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:24px;margin-bottom:20px}.foris-overview h2{margin:0 0 8px;font-size:18px}.foris-overview p{margin:6px 0;color:var(--admin-muted);line-height:1.6}.foris-overview .button{flex:none}.foris-counts{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;margin-bottom:20px}.foris-count{display:grid;gap:6px;padding:18px;color:inherit;text-decoration:none}.foris-count strong{font-size:26px}.foris-count span{color:var(--admin-muted);font-size:13px}.foris-error{max-width:320px;white-space:normal}.filters .field{min-width:0;margin:0}.filters .field span{display:block;margin-bottom:6px;font-size:12px}.filters{align-items:end}.foris-overview a:focus-visible,.foris-count:focus-visible{outline:3px solid var(--admin-accent);outline-offset:3px}@media(max-width:1100px){.foris-counts{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:640px){.foris-overview{align-items:stretch;flex-direction:column;padding:18px}.foris-counts{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
@endpush