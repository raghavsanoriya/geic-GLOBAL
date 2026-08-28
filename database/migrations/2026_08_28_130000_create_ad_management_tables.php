<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 40);
            $table->string('name', 120);
            $table->string('external_account_id', 120)->nullable();
            $table->string('status', 30)->default('not_connected');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->index(['provider', 'status']);
        });

        Schema::create('ad_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_account_id')->constrained('ad_accounts')->cascadeOnDelete();
            $table->string('external_campaign_id', 120)->nullable();
            $table->string('name', 160);
            $table->string('objective', 60)->nullable();
            $table->string('status', 30)->default('draft');
            $table->decimal('daily_budget', 12, 2)->nullable();
            $table->string('landing_page', 180)->nullable();
            $table->string('destination', 80)->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->timestamps();
            $table->index(['status', 'destination']);
        });

        Schema::create('ad_performance', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->date('metric_date');
            $table->decimal('spend', 12, 2)->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedInteger('leads')->default(0);
            $table->unsignedInteger('qualified_leads')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['ad_campaign_id', 'metric_date']);
            $table->index('metric_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_performance');
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('ad_accounts');
    }
};
