<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_imports', function (Blueprint $table): void {
            $table->id();
            $table->uuid('creation_key')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 80);
            $table->string('status', 24)->default('preview');
            $table->json('mapping');
            $table->json('headers');
            $table->unsignedInteger('row_count');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_import_rows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_id')->constrained('crm_imports')->restrictOnDelete();
            $table->unsignedInteger('row_number');
            $table->text('payload');
            $table->string('status', 24)->default('preview');
            $table->text('reason')->nullable();
            $table->foreignId('enquiry_id')->nullable()->constrained('counselling_enquiries')->restrictOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('crm_students')->restrictOnDelete();
            $table->unsignedInteger('student_version')->nullable();
            $table->timestamps();
            $table->unique(['import_id', 'row_number']);
            $table->index(['status', 'id']);
        });
        Schema::create('crm_import_identities', function (Blueprint $table): void {
            $table->id();
            $table->char('identity_hash', 64)->unique();
            $table->foreignId('import_row_id')->constrained('crm_import_rows')->restrictOnDelete();
            $table->foreignId('enquiry_id')->constrained('counselling_enquiries')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('crm_import_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_id')->constrained('crm_imports')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_import_events');
        Schema::dropIfExists('crm_import_identities');
        Schema::dropIfExists('crm_import_rows');
        Schema::dropIfExists('crm_imports');
    }
};
