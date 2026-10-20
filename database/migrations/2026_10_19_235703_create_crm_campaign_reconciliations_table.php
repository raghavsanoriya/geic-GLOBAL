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
        Schema::create('crm_campaign_reconciliations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipient_id')->unique()->constrained('crm_campaign_recipients')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('creation_key')->unique();
            $table->string('outcome', 24);
            $table->string('provider_reference', 250)->nullable();
            $table->text('evidence');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_campaign_reconciliations');
    }
};
