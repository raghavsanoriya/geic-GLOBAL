<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('creation_key')->unique();
            $table->string('subject', 160);
            $table->string('channel', 20);
            $table->string('status', 16)->default('new');
            $table->dateTime('due_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'status', 'due_at']);
            $table->index(['status', 'due_at', 'id']);
        });
        Schema::create('crm_follow_up_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('follow_up_id')->constrained('crm_follow_ups')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remark');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_follow_up_notes');
        Schema::dropIfExists('crm_follow_ups');
    }
};
