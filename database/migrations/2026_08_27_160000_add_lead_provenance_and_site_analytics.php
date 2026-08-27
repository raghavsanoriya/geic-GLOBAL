<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselling_enquiries', function (Blueprint $table): void {
            $table->string('source', 40)->default('website')->after('source_page');
            $table->string('external_id', 120)->nullable()->after('source');
            $table->string('source_form', 120)->nullable()->after('external_id');
            $table->string('utm_source', 120)->nullable()->after('source_form');
            $table->string('utm_medium', 120)->nullable()->after('utm_source');
            $table->string('utm_campaign', 180)->nullable()->after('utm_medium');
            $table->json('metadata')->nullable()->after('utm_campaign');

            $table->unique(['source', 'external_id']);
            $table->index(['source', 'created_at']);
        });

        Schema::create('site_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type', 32);
            $table->string('path', 500);
            $table->string('label', 180)->nullable();
            $table->string('target', 500)->nullable();
            $table->string('referrer_domain', 180)->nullable();
            $table->string('utm_source', 120)->nullable();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 180)->nullable();
            $table->string('visitor_hash', 64)->nullable();
            $table->string('session_hash', 64)->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index(['path', 'created_at']);
            $table->index(['utm_campaign', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_events');

        Schema::table('counselling_enquiries', function (Blueprint $table): void {
            $table->dropUnique(['source', 'external_id']);
            $table->dropIndex(['source', 'created_at']);
            $table->dropColumn([
                'source',
                'external_id',
                'source_form',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'metadata',
            ]);
        });
    }
};
