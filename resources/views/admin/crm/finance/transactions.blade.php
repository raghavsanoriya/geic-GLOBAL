@extends('admin.layout')
@section('title', 'Transactions | GEIC Indore')
@section('crumb', 'Finance · Transactions')
@section('content')
<div class="crm-workspace">
    <section class="page-head"><div><span class="eyebrow">Indore · Receipt ledger</span><h1>Transactions</h1><p class="sub">Recorded receipts and negative reversals. This system does not execute bank transfers.</p></div><a class="button button--quiet" href="{{ route('admin.crm.invoices.index') }}">Invoices</a></section>
    <form class="panel crm-filters" method="get"><label>From payment date<input class="input" type="date" name="from" value="{{request('from')}}"></label><label>To payment date<input class="input" type="date" name="to" value="{{request('to')}}"></label>@if(request('invoice'))<input type="hidden" name="invoice" value="{{request('invoice')}}">@endif<button class="button" type="submit">Apply filters</button></form>
    @can('crm.export')<nav class="crm-actions" aria-label="Transaction exports"><a class="button button--quiet" href="{{route('admin.crm.transactions.index',array_merge(request()->only(['invoice','from','to']),['format'=>'csv']))}}">Export CSV</a><a class="button button--quiet" href="{{route('admin.crm.transactions.index',array_merge(request()->only(['invoice','from','to']),['format'=>'print']))}}">Print filtered ledger</a></nav>@endcan
    <section class="panel crm-student-list">@forelse($payments as $payment)<article class="crm-student-row">
        <div><strong>{{ $payment->reference }}</strong><p>{{ $payment->reversal_of_id ? 'Reversal of receipt #'.$payment->reversal_of_id : 'Receipt #'.$payment->id }}</p></div>
        <div>{{ $payment->invoice->currency }} {{ \App\Support\CrmMoney::display($payment->amount_minor) }}<p>{{ $payment->paid_on->format('d M Y') }}</p></div>
        <div>{{ $payment->invoice->number }}<p>{{ $payment->reason }}</p></div>
        <a class="button button--quiet" href="{{ route('admin.crm.invoices.show', $payment->invoice_id) }}">Open invoice</a>
    </article>@empty<div class="empty"><h2>No transactions recorded</h2><p>Receipts appear after recording a payment against an issued invoice.</p></div>@endforelse</section>{{ $payments->links() }}
</div>
@endsection
@include('admin.crm.students.styles')
