<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_inbound_forms', function (Blueprint $table): void {
            $table->unsignedSmallInteger('rate_limit_per_minute')->default(30)->after('allowed_origins');
            $table->string('honeypot_field', 60)->nullable()->after('rate_limit_per_minute');
        });
    }

    public function down(): void
    {
        Schema::table('crm_inbound_forms', function (Blueprint $table): void {
            $table->dropColumn(['rate_limit_per_minute', 'honeypot_field']);
        });
    }
};
