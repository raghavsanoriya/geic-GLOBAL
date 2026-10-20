<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind', 16);
            $table->unsignedBigInteger('record_id');
            $table->unsignedInteger('record_version');
            $table->timestamp('due_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'kind', 'record_id', 'record_version'], 'crm_reminder_identity');
            $table->index(['user_id', 'read_at', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reminders');
    }
};
