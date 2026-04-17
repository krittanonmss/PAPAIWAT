<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tag_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('article_tag_id')
                ->constrained('article_tags')
                ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();

            $table->index('article_id', 'article_tag_items_article_id_idx');
            $table->index('article_tag_id', 'article_tag_items_article_tag_id_idx');

            $table->unique(
                ['article_id', 'article_tag_id'],
                'article_tag_items_article_tag_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tag_items');
    }
};