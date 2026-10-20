<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->uuid('creation_key')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 160);
            $table->string('channel', 12);
            $table->string('subject', 200)->nullable();
            $table->text('body');
            $table->string('status', 24)->default('draft');
            $table->json('audience');
            $table->boolean('consent_confirmed')->default(false);
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_campaign_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('crm_campaigns')->restrictOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('crm_students')->restrictOnDelete();
            $table->string('address', 254);
            $table->string('name', 160)->nullable();
            $table->string('status', 24)->default('pending');
            $table->string('provider_reference')->nullable();
            $table->string('error_code', 80)->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unique(['campaign_id', 'address']);
        });
        Schema::create('crm_campaign_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('crm_campaigns')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->timestamp('created_at');
        });
        Schema::create('crm_marketing_suppressions', function (Blueprint $table): void {
            $table->id();
            $table->string('channel', 12);
            $table->string('address', 254);
            $table->string('reason', 160);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['channel', 'address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_marketing_suppressions');
        Schema::dropIfExists('crm_campaign_events');
        Schema::dropIfExists('crm_campaign_recipients');
        Schema::dropIfExists('crm_campaigns');
    }
};
