<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_campaign_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('crm_campaigns')->restrictOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('creation_key')->unique();
            $table->string('label', 160);
            $table->string('path')->unique();
            $table->string('mime', 100);
            $table->unsignedBigInteger('bytes');
            $table->char('sha256', 64);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['campaign_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_campaign_attachments');
    }
};
