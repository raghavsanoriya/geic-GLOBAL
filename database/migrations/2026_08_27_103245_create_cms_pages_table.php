<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('page_key', 120)->unique();
            $table->string('group', 32);
            $table->string('name', 160);
            $table->string('slug', 120);
            $table->string('path', 180)->unique();
            $table->string('description', 500)->nullable();
            $table->timestamps();

            $table->index(['group', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
