<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_documents', function (Blueprint $table): void {
            $table->id();
            $table->uuid('creation_key')->unique();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 32);
            $table->string('label', 160);
            $table->string('path')->unique();
            $table->string('mime', 100);
            $table->unsignedBigInteger('bytes');
            $table->char('sha256', 64);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'is_active', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_documents');
    }
};
