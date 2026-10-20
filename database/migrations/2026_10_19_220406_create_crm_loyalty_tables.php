<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_loyalty_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 160);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_loyalty_referrals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('crm_loyalty_members')->restrictOnDelete();
            $table->foreignId('student_id')->unique()->constrained('crm_students')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at');
        });
        Schema::create('crm_loyalty_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('name', 160);
            $table->string('milestone', 40);
            $table->unsignedInteger('points');
            $table->boolean('is_enabled')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('crm_loyalty_points', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('crm_loyalty_members')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_key', 160)->unique();
            $table->integer('points');
            $table->string('reason', 500);
            $table->timestamp('created_at');
        });
        Schema::create('crm_loyalty_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('crm_loyalty_members')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->json('fields');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_loyalty_events');
        Schema::dropIfExists('crm_loyalty_points');
        Schema::dropIfExists('crm_loyalty_rules');
        Schema::dropIfExists('crm_loyalty_referrals');
        Schema::dropIfExists('crm_loyalty_members');
    }
};
