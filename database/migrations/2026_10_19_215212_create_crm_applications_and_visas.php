<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->uuid('creation_key')->unique();
            $table->foreignId('university_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('status_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('course', 160);
            $table->string('intake', 80);
            $table->date('submitted_on')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'id']);
            $table->index(['status_id', 'id']);
        });
        Schema::create('crm_visas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('application_id')->constrained('crm_applications')->restrictOnDelete();
            $table->uuid('creation_key')->unique();
            $table->foreignId('status_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visa_type', 100);
            $table->date('lodged_on')->nullable();
            $table->date('decision_on')->nullable();
            $table->date('admission_on')->nullable();
            $table->date('census_on')->nullable();
            $table->boolean('ready_to_bill')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'id']);
            $table->index(['status_id', 'id']);
        });
        foreach (['crm_case_notes', 'crm_case_events'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name): void {
                $table->id();
                $table->foreignId('application_id')->nullable()->constrained('crm_applications')->restrictOnDelete();
                $table->foreignId('visa_id')->nullable()->constrained('crm_visas')->restrictOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                if ($name === 'crm_case_notes') {
                    $table->text('remark');
                } else {
                    $table->string('action', 40);
                    $table->foreignId('from_status_id')->nullable()->constrained('crm_references')->restrictOnDelete();
                    $table->foreignId('to_status_id')->constrained('crm_references')->restrictOnDelete();
                    $table->json('fields');
                }
                $table->timestamp('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_case_events');
        Schema::dropIfExists('crm_case_notes');
        Schema::dropIfExists('crm_visas');
        Schema::dropIfExists('crm_applications');
    }
};
