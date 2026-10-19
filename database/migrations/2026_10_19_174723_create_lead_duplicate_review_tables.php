<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_contact_keys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('enquiry_id')->unique()->constrained('counselling_enquiries')->cascadeOnDelete();
            $table->char('email_key', 64)->nullable()->index();
            $table->char('phone_key', 64)->nullable()->index();
        });
        Schema::create('lead_duplicate_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('enquiry_id')->unique()->constrained('counselling_enquiries')->cascadeOnDelete();
            $table->text('matches');
            $table->string('status', 24)->default('pending')->index();
            $table->foreignId('linked_enquiry_id')->nullable()->constrained('counselling_enquiries')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_duplicate_reviews');
        Schema::dropIfExists('lead_contact_keys');
    }
};
