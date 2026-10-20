@extends('admin.layout')
@section('title', 'Invoices | GEIC Indore')
@section('crumb', 'Finance · Invoices')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Finance</span><h1>Commission invoices</h1><p class="sub">Draft, issue and record receipts. Financial corrections preserve the original ledger.</p></div><a class="button button--quiet" href="{{ route('admin.crm.transactions.index') }}">Transactions</a></section>
    <form method="get" class="panel crm-filters" action="{{ route('admin.crm.invoices.index') }}">@if(request('student'))<input type="hidden" name="student" value="{{ request('student') }}">@endif<label>Status<select class="select" name="status"><option value="">All invoices</option>@foreach(['draft','issued','void'] as $value)<option value="{{ $value }}" @selected(request('status') === $value)>{{ ucfirst($value) }}</option>@endforeach</select></label><button class="button" type="submit">Apply filter</button></form>
    @can('crm.export')<nav class="crm-actions" aria-label="Invoice exports"><a class="button button--quiet" href="{{route('admin.crm.invoices.index',array_merge(request()->only(['student','status']),['format'=>'csv']))}}">Export CSV</a><a class="button button--quiet" href="{{route('admin.crm.invoices.index',array_merge(request()->only(['student','status']),['format'=>'print']))}}">Print filtered invoices</a></nav>@endcan
    <section class="panel crm-student-list">@forelse($invoices as $invoice)<article class="crm-student-row">
        <div><strong>{{ $invoice->number }}</strong><p>{{ $invoice->student->full_name }}</p><p>{{ $invoice->bill_to }}</p></div>
        <div><p>Total: {{ $invoice->currency }} {{ \App\Support\CrmMoney::display($invoice->total_minor) }}</p><p>Recorded balance: {{ \App\Support\CrmMoney::display($invoice->total_minor - (int) $invoice->payments_sum_amount_minor) }}</p></div>
        <div><span class="pill">{{ ucfirst($invoice->status) }}</span><p>Due {{ $invoice->due_on->format('d M Y') }}</p></div>
        <a class="button button--quiet" href="{{ route('admin.crm.invoices.show', $invoice->id) }}">Open invoice</a>
    </article>@empty<div class="empty"><h2>No matching invoices</h2><p>Open a student profile to create an invoice if you have finance-management permission.</p></div>@endforelse</section>
    {{ $invoices->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
