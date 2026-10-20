<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('original_enquiry_id')->unique()->constrained('counselling_enquiries')->restrictOnDelete();
            $table->string('full_name', 120);
            $table->string('email', 160)->nullable()->index();
            $table->string('phone', 24)->nullable()->index();
            $table->string('stage', 24)->default('lead');
            $table->string('lead_status', 32)->default('new');
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_counsellor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_re_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('profile')->nullable();
            $table->text('sensitive_profile')->nullable();
            $table->string('correction_reason', 160)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['stage', 'is_active', 'id']);
            $table->index(['assigned_counsellor_id', 'id']);
            $table->index(['assigned_re_id', 'id']);
        });
        Schema::create('crm_student_enquiries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('enquiry_id')->unique()->constrained('counselling_enquiries')->restrictOnDelete();
            $table->timestamp('created_at');
        });
        Schema::create('crm_audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);
            $table->json('changes');
            $table->timestamp('created_at');
            $table->index(['student_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_audit_events');
        Schema::dropIfExists('crm_student_enquiries');
        Schema::dropIfExists('crm_students');
    }
};
