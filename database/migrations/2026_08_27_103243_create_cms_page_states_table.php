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
        Schema::create('cms_page_states', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 120)->unique();
            $table->string('status', 24)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('drafted_at')->nullable();
            $table->timestamp('unpublished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_page_states');
    }
};
