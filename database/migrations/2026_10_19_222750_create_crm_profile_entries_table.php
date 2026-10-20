<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_profile_entries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('creation_key')->unique();
            $table->foreignId('student_id')->constrained('crm_students')->restrictOnDelete();
            $table->string('kind', 24);
            $table->json('details');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'kind', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_profile_entries');
    }
};
