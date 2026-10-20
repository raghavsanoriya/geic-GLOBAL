<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crm_imports', function (Blueprint $table): void {
            $table->string('entity_type', 24)->default('lead');
        });
        Schema::create('crm_import_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_row_id')->unique()->constrained('crm_import_rows')->restrictOnDelete();
            $table->char('identity_hash', 64)->unique();
            $table->string('entity_type', 24);
            $table->unsignedBigInteger('record_id');
            $table->unsignedInteger('record_version');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_import_records');
        Schema::table('crm_imports', function (Blueprint $table): void {
            $table->dropColumn('entity_type');
        });
    }
};
