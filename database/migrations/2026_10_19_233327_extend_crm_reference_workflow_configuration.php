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
        Schema::table('crm_references', function (Blueprint $table): void {
            $table->json('configuration')->nullable();
            $table->longText('restricted_details')->nullable();
            $table->foreignId('linked_application_status_id')->nullable()->constrained('crm_references')->restrictOnDelete();
        });
        Schema::table('crm_students', function (Blueprint $table): void {
            $table->foreignId('stage_status_id')->nullable()->constrained('crm_references')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_students', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('stage_status_id');
        });
        Schema::table('crm_references', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('linked_application_status_id');
            $table->dropColumn(['configuration', 'restricted_details']);
        });
    }
};
