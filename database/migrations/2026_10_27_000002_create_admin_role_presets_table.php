<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_role_presets', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 40)->unique();
            $table->string('label', 120);
            $table->string('description', 500);
            $table->json('permissions');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['is_active', 'label']);
        });
        Schema::create('admin_role_preset_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('admin_role_presets')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_preset_events');
        Schema::dropIfExists('admin_role_presets');
    }
};
