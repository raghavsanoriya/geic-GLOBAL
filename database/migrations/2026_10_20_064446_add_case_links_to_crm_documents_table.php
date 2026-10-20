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
        Schema::table('crm_documents', function (Blueprint $table) {
            $table->foreignId('application_id')->nullable()->constrained('crm_applications')->restrictOnDelete();
            $table->foreignId('visa_id')->nullable()->constrained('crm_visas')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visa_id');
            $table->dropConstrainedForeignId('application_id');
        });
    }
};
