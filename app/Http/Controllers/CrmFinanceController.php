<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmInvoiceRequest;
use App\Models\CrmApplication;
use App\Models\CrmInvoice;
use App\Models\CrmPayment;
use App\Models\CrmStudent;
use App\Support\CrmFinance;
use App\Support\CrmMoney;
use App\Support\CrmTableExport;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmFinanceController extends Controller
{
    private function authorizeFinance(bool $write = false): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.finance.view');
        if ($write) {
            Gate::authorize('crm.finance.manage');
        }
    }

    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        $this->authorizeFinance();
        $filters = $request->validate(['student' => ['nullable', 'integer', 'min:1'], 'status' => ['nullable', Rule::in(['draft', 'issued', 'void'])], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'format' => ['nullable', 'in:csv,print']]);
        $query = CrmInvoice::visibleTo($request->user())->with('student:id,full_name')->withSum('payments', 'amount_minor');
        if ($request->filled('student')) {
            CrmStudent::visibleTo($request->user())->findOrFail($filters['student']);
            $query->where('student_id', $filters['student']);
        }
        if ($request->filled('status')) {
            $query->where('status', $filters['status']);
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Commission invoices', 'geic-invoices.csv',
                ['Invoice', 'Student reference', 'Student', 'Bill to', 'Currency', 'Total', 'Recorded receipts', 'Balance', 'Status', 'Due date'],
                fn (CrmInvoice $invoice) => [$invoice->number, 'CRM-'.$invoice->student_id, $invoice->student->full_name, $invoice->bill_to, $invoice->currency,
                    CrmMoney::display($invoice->total_minor), CrmMoney::display((int) $invoice->payments_sum_amount_minor),
                    CrmMoney::display($invoice->total_minor - (int) $invoice->payments_sum_amount_minor), $invoice->status, $invoice->due_on->toDateString()]);
        }
        $invoices = $query->orderByDesc('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($invoices, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.finance.index', compact('invoices'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function transactions(Request $request): Response|JsonResponse|StreamedResponse
    {
        $this->authorizeFinance();
        $filters = $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'format' => ['nullable', 'in:csv,print'],
            'invoice' => ['nullable', 'integer', 'min:1'], 'from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('from'), ['after_or_equal:from'])]]);
        $query = CrmPayment::whereIn('invoice_id', CrmInvoice::visibleTo($request->user())->select('id'))->with('invoice:id,number,currency');
        if ($request->filled('invoice')) {
            CrmInvoice::visibleTo($request->user())->findOrFail($filters['invoice']);
            $query->where('invoice_id', $filters['invoice']);
        }
        if ($request->filled('from')) {
            $query->where('paid_on', '>=', $filters['from']);
        }
        if ($request->filled('to')) {
            $query->where('paid_on', '<', CarbonImmutable::parse($filters['to'])->addDay()->toDateString());
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Recorded receipt ledger', 'geic-transactions.csv',
                ['Receipt ID', 'Invoice', 'Currency', 'Amount', 'Payment reference', 'Paid on', 'Reversal of receipt', 'Correction reason'],
                fn (CrmPayment $payment) => [$payment->id, $payment->invoice->number, $payment->invoice->currency, CrmMoney::display($payment->amount_minor),
                    $payment->reference, $payment->paid_on->toDateString(), $payment->reversal_of_id, $payment->reason]);
        }
        $payments = $query->orderByDesc('id')->paginate(30)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($payments, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.finance.transactions', compact('payments'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request): Response
    {
        $this->authorizeFinance(true);
        $data = $request->validate(['student' => ['required', 'integer', 'min:1']]);

        return $this->form(CrmStudent::visibleTo($request->user())->findOrFail($data['student']));
    }

    public function show(Request $request, int $invoice): Response|JsonResponse
    {
        $this->authorizeFinance();
        $record = CrmInvoice::visibleTo($request->user())->with(['student', 'payments' => fn ($q) => $q->orderByDesc('id'), 'instalments' => fn ($q) => $q->orderBy('due_on')->orderBy('id')])->findOrFail($invoice);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, 'balance_minor' => $record->total_minor - (int) $record->payments->sum('amount_minor')], 200, ['Cache-Control' => 'private, no-store']);
        }

        return $this->form($record->student, $record);
    }

    public function print(Request $request, int $invoice): Response
    {
        $this->authorizeFinance();
        $record = CrmInvoice::visibleTo($request->user())->with(['student:id,full_name', 'payments' => fn ($query) => $query->orderBy('id'), 'instalments' => fn ($query) => $query->orderBy('due_on')])->findOrFail($invoice);

        return response()->view('admin.crm.finance.print', ['invoice' => $record, 'received' => (int) $record->payments->sum('amount_minor')], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(CrmInvoiceRequest $request, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        return $this->saved($request, $finance->invoice($request->user(), $request->validated()));
    }

    public function update(CrmInvoiceRequest $request, int $invoice, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        return $this->saved($request, $finance->invoice($request->user(), $request->validated(), $invoice));
    }

    public function status(Request $request, int $invoice, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        $this->authorizeFinance(true);
        CrmInvoice::visibleTo($request->user())->findOrFail($invoice);
        $data = $request->validate(['version' => ['required', 'integer', 'min:1'], 'status' => ['required', Rule::in(['issued', 'void'])],
            'reason' => ['required_if:status,void', 'nullable', 'string', 'max:500']]);

        return $this->saved($request, $finance->status($request->user(), $invoice, (int) $data['version'], $data['status'], $data['reason'] ?? null));
    }

    public function payment(Request $request, int $invoice, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        $this->authorizeFinance(true);
        CrmInvoice::visibleTo($request->user())->findOrFail($invoice);
        $data = $request->validate(['creation_key' => ['required', 'uuid'], 'amount' => $this->amountRules(),
            'instalment_id' => ['nullable', 'integer', 'min:1'], 'reference' => ['required', 'string', 'max:160'], 'paid_on' => ['required', 'date_format:Y-m-d']]);
        $payment = $finance->payment($request->user(), $invoice, $data);
        if ($request->expectsJson()) {
            return response()->json(['data' => $payment], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.invoices.show', $invoice)->with('status', 'Receipt recorded. No money transfer was initiated.');
    }

    public function reverse(Request $request, int $payment, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        $this->authorizeFinance(true);
        $original = CrmPayment::findOrFail($payment);
        CrmInvoice::visibleTo($request->user())->findOrFail($original->invoice_id);
        $data = $request->validate(['creation_key' => ['required', 'uuid'], 'reason' => ['required', 'string', 'max:500'], 'paid_on' => ['required', 'date_format:Y-m-d']]);
        $reversal = $finance->reverse($request->user(), $payment, $data);
        if ($request->expectsJson()) {
            return response()->json(['data' => $reversal], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.invoices.show', $original->invoice_id)->with('status', 'Reversal recorded. The original receipt was preserved.');
    }

    public function instalment(Request $request, int $invoice, CrmFinance $finance): RedirectResponse|JsonResponse
    {
        $this->authorizeFinance(true);
        CrmInvoice::visibleTo($request->user())->findOrFail($invoice);
        $data = $request->validate(['creation_key' => ['required', 'uuid'], 'amount' => $this->amountRules(), 'due_on' => ['required', 'date_format:Y-m-d']]);
        $record = $finance->instalment($request->user(), $invoice, $data);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.invoices.show', $invoice)->with('status', 'Instalment added.');
    }

    private function saved(Request $request, CrmInvoice $record): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.invoices.show', $record->id)->with('status', 'Invoice saved.');
    }

    private function form(CrmStudent $student, ?CrmInvoice $record = null): Response
    {
        return response()->view('admin.crm.finance.form', [
            'student' => $student, 'record' => $record,
            'applications' => CrmApplication::where('student_id', $student->id)->orderByDesc('id')->get(['id', 'course']),
            'events' => $record ? DB::table('crm_finance_events')->where('invoice_id', $record->id)->orderByDesc('id')->limit(50)->get(['action', 'created_at']) : collect(),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    private function amountRules(): array
    {
        return ['required', 'string', 'regex:/^(0|[1-9][0-9]{0,8})(\.[0-9]{1,2})?$/D'];
    }
}
