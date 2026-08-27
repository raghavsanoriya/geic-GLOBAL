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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('admin_role', 40)->default('super_admin')->after('is_admin');
            $table->json('admin_permissions')->nullable()->after('admin_role');
            $table->boolean('is_active')->default(true)->after('admin_permissions');
            $table->json('admin_preferences')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['admin_role', 'admin_permissions', 'is_active', 'admin_preferences']);
        });
    }
};
