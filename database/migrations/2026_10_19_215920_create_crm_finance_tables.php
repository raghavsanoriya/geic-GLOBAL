<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('crm_applications')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('creation_key')->unique();
            $table->string('number', 40)->nullable()->unique();
            $table->string('bill_to', 160);
            $table->string('description', 500);
            $table->char('currency', 3);
            $table->unsignedBigInteger('base_minor');
            $table->unsignedInteger('tax_bps');
            $table->unsignedBigInteger('tax_minor');
            $table->unsignedBigInteger('total_minor');
            $table->string('status', 16)->default('draft');
            $table->date('due_on');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->string('void_reason', 500)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'id']);
            $table->index(['status', 'due_on', 'id']);
        });
        Schema::create('crm_instalments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('crm_invoices')->restrictOnDelete();
            $table->uuid('creation_key')->unique();
            $table->unsignedBigInteger('amount_minor');
            $table->date('due_on');
            $table->timestamps();
        });
        Schema::create('crm_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('crm_invoices')->restrictOnDelete();
            $table->foreignId('instalment_id')->nullable()->constrained('crm_instalments')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversal_of_id')->nullable()->unique()->constrained('crm_payments')->restrictOnDelete();
            $table->uuid('creation_key')->unique();
            $table->bigInteger('amount_minor');
            $table->string('reference', 160);
            $table->string('reason', 500)->nullable();
            $table->date('paid_on');
            $table->timestamps();
            $table->index(['invoice_id', 'id']);
        });
        Schema::create('crm_finance_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('crm_invoices')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->json('fields');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_finance_events');
        Schema::dropIfExists('crm_payments');
        Schema::dropIfExists('crm_instalments');
        Schema::dropIfExists('crm_invoices');
    }
};
