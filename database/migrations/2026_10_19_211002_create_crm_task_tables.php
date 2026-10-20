<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('creation_key')->unique();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('new');
            $table->string('priority', 16)->default('normal');
            $table->dateTime('starts_at');
            $table->dateTime('due_at');
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['created_by', 'id']);
            $table->index(['status', 'due_at', 'id']);
        });
        Schema::create('crm_task_assignees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['task_id', 'user_id']);
            $table->index(['user_id', 'task_id']);
        });
        Schema::create('crm_task_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remark');
            $table->timestamp('created_at');
        });
        Schema::create('crm_task_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->json('changes');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_task_events');
        Schema::dropIfExists('crm_task_notes');
        Schema::dropIfExists('crm_task_assignees');
        Schema::dropIfExists('crm_tasks');
    }
};
