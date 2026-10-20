<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_integration_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 30)->unique();
            $table->json('configuration');
            $table->text('secret')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->char('verified_fingerprint', 64)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('last_test_result', 200)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_integration_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('setting_id')->constrained('crm_integration_settings')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_integration_events');
        Schema::dropIfExists('crm_integration_settings');
    }
};
