<?php

namespace App\Support;

use App\Models\CrmApplication;
use App\Models\CrmInstalment;
use App\Models\CrmInvoice;
use App\Models\CrmPayment;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CrmFinance
{
    public function __construct(private CrmLoyalty $loyalty) {}

    public function invoice(User $actor, array $data, ?int $id = null): CrmInvoice
    {
        return DB::transaction(function () use ($actor, $data, $id): CrmInvoice {
            $record = $id ? CrmInvoice::visibleTo($actor)->findOrFail($id) : null;
            $student = CrmStudent::visibleTo($actor)->whereKey($record?->student_id ?? $data['student_id'])->lockForUpdate()->firstOrFail();
            if ($id) {
                $record = CrmInvoice::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail();
                abort_unless($record->version === (int) $data['version'], 409, 'This invoice has changed. Reload before saving.');
                abort_unless($record->status === 'draft', 409, 'Issued invoices cannot be rewritten. Use a traceable correction.');
            } else {
                $existing = CrmInvoice::where('creation_key', $data['creation_key'])->first();
                if ($existing) {
                    abort_unless($existing->student_id === $student->id && $existing->created_by === $actor->id, 409);

                    return $existing;
                }
            }
            if (filled($data['application_id'] ?? null) && ! CrmApplication::visibleTo($actor)->where('student_id', $student->id)->whereKey($data['application_id'])->exists()) {
                throw ValidationException::withMessages(['application_id' => 'Choose an application for this student.']);
            }
            $creating = $record === null;
            $record ??= new CrmInvoice(['student_id' => $student->id, 'created_by' => $actor->id, 'creation_key' => $data['creation_key'], 'status' => 'draft', 'version' => 1]);
            $base = CrmMoney::minor($data['base_amount']);
            $tax = CrmMoney::tax($base, (int) $data['tax_bps']);
            $record->fill([...Arr::only($data, ['application_id', 'bill_to', 'description', 'currency', 'due_on', 'tax_bps']),
                'base_minor' => $base, 'tax_minor' => $tax, 'total_minor' => $base + $tax]);
            $fields = array_keys($record->getDirty());
            if (! $creating && $fields !== []) {
                $record->version++;
            }
            $record->save();
            if ($creating) {
                $record->number = 'GEIC-'.str_pad((string) $record->id, 8, '0', STR_PAD_LEFT);
                $record->save();
            }
            if ($creating || $fields !== []) {
                $this->event($record, $actor, $creating ? 'invoice.created' : 'invoice.updated', $fields);
            }

            return $record;
        });
    }

    public function status(User $actor, int $id, int $version, string $status, ?string $reason = null): CrmInvoice
    {
        return DB::transaction(function () use ($actor, $id, $version, $status, $reason): CrmInvoice {
            $record = CrmInvoice::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === $version, 409, 'This invoice has changed. Reload before saving.');
            if ($status === 'issued') {
                abort_unless($record->status === 'draft', 409, 'Only a draft can be issued.');
                $record->issued_at = now();
            } else {
                abort_unless($status === 'void' && in_array($record->status, ['draft', 'issued'], true), 409, 'This invoice cannot be voided.');
                abort_unless((int) $record->payments()->sum('amount_minor') === 0, 409, 'Reverse recorded payments before voiding.');
                $record->voided_at = now();
                $record->void_reason = $reason;
            }
            $record->status = $status;
            $record->version++;
            $record->save();
            $this->event($record, $actor, 'invoice.'.$status, ['status']);

            return $record;
        });
    }

    public function payment(User $actor, int $id, array $data): CrmPayment
    {
        return DB::transaction(function () use ($actor, $id, $data): CrmPayment {
            $invoice = CrmInvoice::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail();
            $existing = CrmPayment::where('creation_key', $data['creation_key'])->first();
            if ($existing) {
                abort_unless($existing->invoice_id === $invoice->id && $existing->created_by === $actor->id, 409);

                return $existing;
            }
            abort_unless($invoice->status === 'issued', 409, 'Payments can only be recorded against issued invoices.');
            $amount = CrmMoney::minor($data['amount']);
            $balance = $invoice->total_minor - (int) $invoice->payments()->sum('amount_minor');
            if ($amount <= 0 || $amount > $balance) {
                throw ValidationException::withMessages(['amount' => 'The amount must be positive and no greater than the remaining invoice balance.']);
            }
            if (filled($data['instalment_id'] ?? null)) {
                $instalment = $invoice->instalments()->find($data['instalment_id']);
                $paid = (int) $invoice->payments()->where('instalment_id', $data['instalment_id'])->sum('amount_minor');
                if (! $instalment || $amount > $instalment->amount_minor - $paid) {
                    throw ValidationException::withMessages(['instalment_id' => 'Choose an instalment with enough remaining balance on this invoice.']);
                }
            }
            $payment = CrmPayment::create([...Arr::only($data, ['instalment_id', 'creation_key', 'reference', 'paid_on']), 'invoice_id' => $invoice->id, 'created_by' => $actor->id, 'amount_minor' => $amount]);
            if ($invoice->total_minor === (int) $invoice->payments()->sum('amount_minor')) {
                $this->loyalty->awardForStudent($invoice->student_id, 'invoice_paid', 'invoice-'.$invoice->id);
            }
            $this->event($invoice, $actor, 'payment.recorded', ['payment:'.$payment->id]);

            return $payment;
        });
    }

    public function reverse(User $actor, int $paymentId, array $data): CrmPayment
    {
        return DB::transaction(function () use ($actor, $paymentId, $data): CrmPayment {
            $original = CrmPayment::findOrFail($paymentId);
            $invoice = CrmInvoice::visibleTo($actor)->whereKey($original->invoice_id)->lockForUpdate()->firstOrFail();
            abort_unless($original->amount_minor > 0 && $original->reversal_of_id === null, 409, 'Only an original positive receipt can be reversed.');
            $usedKey = CrmPayment::where('creation_key', $data['creation_key'])->first();
            if ($usedKey) {
                abort_unless($usedKey->reversal_of_id === $original->id && $usedKey->created_by === $actor->id, 409, 'This receipt key has already been used.');

                return $usedKey;
            }
            $existing = CrmPayment::where('reversal_of_id', $original->id)->first();
            if ($existing) {
                return $existing;
            }
            $reversal = CrmPayment::create(['invoice_id' => $invoice->id, 'instalment_id' => $original->instalment_id,
                'created_by' => $actor->id, 'creation_key' => $data['creation_key'], 'reversal_of_id' => $original->id,
                'amount_minor' => -$original->amount_minor, 'reference' => 'Reversal of receipt #'.$original->id, 'reason' => $data['reason'], 'paid_on' => $data['paid_on']]);
            $this->event($invoice, $actor, 'payment.reversed', ['payment:'.$original->id, 'reversal:'.$reversal->id]);

            return $reversal;
        });
    }

    public function instalment(User $actor, int $id, array $data): CrmInstalment
    {
        return DB::transaction(function () use ($actor, $id, $data): CrmInstalment {
            $invoice = CrmInvoice::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail();
            $existing = CrmInstalment::where('creation_key', $data['creation_key'])->first();
            if ($existing) {
                abort_unless($existing->invoice_id === $invoice->id, 409);

                return $existing;
            }
            abort_unless($invoice->status === 'issued', 409, 'Issue the invoice before setting its payment schedule.');
            $amount = CrmMoney::minor($data['amount']);
            if ($amount <= 0 || $amount + (int) $invoice->instalments()->sum('amount_minor') > $invoice->total_minor) {
                throw ValidationException::withMessages(['amount' => 'Instalments must be positive and cannot exceed the invoice total.']);
            }
            $record = CrmInstalment::create(['invoice_id' => $invoice->id, 'creation_key' => $data['creation_key'], 'amount_minor' => $amount, 'due_on' => $data['due_on']]);
            $this->event($invoice, $actor, 'instalment.created', ['instalment:'.$record->id]);

            return $record;
        });
    }

    private function event(CrmInvoice $invoice, User $actor, string $action, array $fields): void
    {
        DB::table('crm_finance_events')->insert(['invoice_id' => $invoice->id, 'actor_id' => $actor->id, 'action' => $action, 'fields' => json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now()]);
    }
}
