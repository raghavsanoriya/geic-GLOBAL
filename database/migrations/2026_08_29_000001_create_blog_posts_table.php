<?php

use App\Support\BlogCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 180)->unique();
            $table->string('title', 220);
            $table->string('category', 100)->default('Student guidance');
            $table->text('excerpt');
            $table->string('image', 500)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('read_time', 40)->default('5 min read');
            $table->string('author', 160)->default('Trans Globe Indore team');
            $table->text('intro');
            $table->json('sections')->nullable();
            $table->json('tags')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });

        // Preserve the existing public articles while making future content
        // editable from the dashboard.
        BlogCatalog::seedDefaults();
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
