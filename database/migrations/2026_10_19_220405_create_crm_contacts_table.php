<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('creation_key')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 160);
            $table->string('company', 160)->nullable();
            $table->string('designation', 120)->nullable();
            $table->string('contact_type', 80);
            $table->string('email', 160)->nullable();
            $table->string('phone', 24)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['created_by', 'id']);
            $table->index(['country', 'contact_type', 'id']);
        });
        Schema::create('crm_contact_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contact_id')->constrained('crm_contacts')->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('fields');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contact_events');
        Schema::dropIfExists('crm_contacts');
    }
};
