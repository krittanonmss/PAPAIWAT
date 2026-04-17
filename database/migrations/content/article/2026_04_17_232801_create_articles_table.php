<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_id')
                ->constrained('contents')
                ->cascadeOnDelete()
                ->unique();

            $table->string('title_en')->nullable();
            $table->text('excerpt_en')->nullable();

            $table->longText('body')->nullable();
            $table->string('body_format', 20)->default('markdown'); // markdown, html, editorjs

            $table->string('author_name')->nullable();
            $table->unsignedInteger('reading_time_minutes')->nullable();

            $table->text('seo_keywords')->nullable();

            $table->boolean('allow_comments')->default(true);
            $table->boolean('show_on_homepage')->default(false);

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();

            $table->index('content_id', 'articles_content_id_idx');
            $table->index('body_format', 'articles_body_format_idx');
            $table->index('allow_comments', 'articles_allow_comments_idx');
            $table->index('show_on_homepage', 'articles_show_on_homepage_idx');
            $table->index('scheduled_at', 'articles_scheduled_at_idx');
            $table->index('expired_at', 'articles_expired_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};