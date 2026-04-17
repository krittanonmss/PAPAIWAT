<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_related_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->foreignId('related_article_id')
                ->constrained('articles')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('article_id', 'article_related_items_article_id_idx');
            $table->index('related_article_id', 'article_related_items_related_article_id_idx');
            $table->index('sort_order', 'article_related_items_sort_order_idx');

            $table->unique(
                ['article_id', 'related_article_id'],
                'article_related_items_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_related_items');
    }
};