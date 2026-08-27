<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_contents', function (Blueprint $table): void {
            $table->id();
            $table->string('page_key', 120);
            $table->string('field_key', 120);
            $table->string('label', 160);
            $table->string('type', 24)->default('text');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['page_key', 'field_key']);
        });

        Schema::create('media_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('path');
            $table->string('original_name');
            $table->string('alt_text')->nullable();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('site_contents');
    }
};
