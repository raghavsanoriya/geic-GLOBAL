<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_inbound_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained('crm_inbound_forms')->restrictOnDelete();
            $table->unsignedSmallInteger('response_status');
            $table->string('outcome', 32);
            $table->json('invalid_fields')->nullable();
            $table->timestamp('created_at')->index();
            $table->index(['form_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_inbound_attempts');
    }
};
