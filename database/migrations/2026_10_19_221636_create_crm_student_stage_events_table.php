<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_student_stage_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_stage', 24)->nullable();
            $table->string('to_stage', 24);
            $table->timestamp('occurred_at');
            $table->timestamp('created_at');
            $table->index(['student_id', 'to_stage', 'id']);
        });
        Schema::table('crm_references', function (Blueprint $table): void {
            $table->string('outcome', 16)->default('progress');
        });
    }

    public function down(): void
    {
        Schema::table('crm_references', function (Blueprint $table): void {
            $table->dropColumn('outcome');
        });
        Schema::dropIfExists('crm_student_stage_events');
    }
};
