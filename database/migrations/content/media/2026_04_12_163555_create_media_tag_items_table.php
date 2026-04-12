<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_tag_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_tag_id')
                ->constrained('media_tags')
                ->cascadeOnDelete();

            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('media_tag_id', 'media_tag_items_media_tag_id_idx');
            $table->index('media_id', 'media_tag_items_media_id_idx');

            $table->unique(
                ['media_tag_id', 'media_id'],
                'media_tag_items_tag_media_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_tag_items');
    }
};