<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160)->unique();
            $table->text('address')->nullable();
            $table->string('state', 100)->nullable();
            $table->string('state_code', 20)->nullable();
            $table->string('contact_name', 160)->nullable();
            $table->string('contact_phone', 24)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('tax_details')->nullable();
            $table->text('banking_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['is_active', 'name']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('crm_branch_id')->nullable()->after('admin_role')->constrained('crm_branches')->nullOnDelete();
        });

        Schema::create('crm_branch_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('crm_branches')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_branch_events');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('crm_branch_id');
        });
        Schema::dropIfExists('crm_branches');
    }
};
