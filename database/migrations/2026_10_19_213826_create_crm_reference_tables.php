<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_references', function (Blueprint $table): void {
            $table->id();
            $table->string('kind', 30);
            $table->string('code', 60);
            $table->string('name', 160);
            $table->foreignId('parent_id')->nullable()->constrained('crm_references')->restrictOnDelete();
            $table->string('country', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->unique(['kind', 'code']);
            $table->index(['kind', 'is_active', 'name']);
        });
        Schema::create('crm_status_transitions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('to_id')->constrained('crm_references')->restrictOnDelete();
            $table->unique(['from_id', 'to_id']);
        });
        Schema::create('crm_reference_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reference_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->json('changes');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reference_events');
        Schema::dropIfExists('crm_status_transitions');
        Schema::dropIfExists('crm_references');
    }
};
