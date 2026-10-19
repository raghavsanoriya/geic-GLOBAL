<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foris_lead_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('enquiry_id')->unique();
            $table->string('status', 24)->default('pending');
            $table->boolean('is_historical')->default(false);
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('remote_lead_id', 100)->nullable();
            $table->string('remote_submission_id', 100)->nullable();
            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->index(['status', 'queued_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foris_lead_deliveries');
    }
};
