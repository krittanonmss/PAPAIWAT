<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tags', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active');

            $table->timestamps();

            $table->index('name', 'article_tags_name_idx');
            $table->index('sort_order', 'article_tags_sort_order_idx');
            $table->index('status', 'article_tags_status_idx');

            $table->unique('slug', 'article_tags_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tags');
    }
};