<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselling_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('destination', 80);
            $table->string('full_name', 120);
            $table->string('email', 160);
            $table->string('phone', 24);
            $table->string('city', 100);
            $table->string('study_level', 50);
            $table->string('preferred_intake', 50);
            $table->string('preferred_course', 160)->nullable();
            $table->string('english_test', 50);
            $table->text('message')->nullable();
            $table->string('source_page', 160);
            $table->timestamps();

            $table->index(['destination', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselling_enquiries');
    }
};
