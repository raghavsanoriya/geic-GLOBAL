<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_inbound_forms', function (Blueprint $table): void {
            $table->id();
            $table->string('form_key', 24)->unique();
            $table->char('api_key_hash', 64);
            $table->string('name', 160);
            $table->json('field_map');
            $table->json('defaults');
            $table->json('allowed_origins');
            $table->string('duplicate_policy', 16)->default('review');
            $table->boolean('forward_to_foris')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamp('key_expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_inbound_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained('crm_inbound_forms')->restrictOnDelete();
            $table->char('idempotency_hash', 64);
            $table->char('payload_hash', 64);
            $table->foreignId('enquiry_id')->constrained('counselling_enquiries')->restrictOnDelete();
            $table->unsignedSmallInteger('response_status');
            $table->timestamp('created_at');
            $table->unique(['form_id', 'idempotency_hash']);
        });
        Schema::create('crm_inbound_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained('crm_inbound_forms')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_inbound_events');
        Schema::dropIfExists('crm_inbound_submissions');
        Schema::dropIfExists('crm_inbound_forms');
    }
};
