<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_programs', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('name', 160);
            $table->foreignId('university_id')->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('crm_references')->restrictOnDelete();
            $table->foreignId('qualification_id')->nullable()->constrained('crm_references')->restrictOnDelete();
            $table->string('country', 100);
            $table->string('study_level', 80);
            $table->string('subject', 100);
            $table->unsignedInteger('ranking')->nullable();
            $table->decimal('ielts', 3, 1)->nullable();
            $table->unsignedSmallInteger('toefl')->nullable();
            $table->unsignedSmallInteger('pte')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['is_active', 'country', 'id']);
            $table->index(['university_id', 'id']);
        });
        Schema::table('crm_applications', function (Blueprint $table): void {
            $table->foreignId('program_id')->nullable()->constrained('crm_programs')->restrictOnDelete();
        });
        Schema::create('crm_program_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_id')->constrained('crm_programs')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('fields');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_program_events');
        Schema::table('crm_applications', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('program_id');
        });
        Schema::dropIfExists('crm_programs');
    }
};
