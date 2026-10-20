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
        Schema::table('crm_import_records', function (Blueprint $table): void {
            $table->unsignedBigInteger('student_audit_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_import_records', function (Blueprint $table): void {
            $table->dropColumn('student_audit_id');
        });
    }
};
