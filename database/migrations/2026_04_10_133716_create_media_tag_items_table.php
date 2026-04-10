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

            $table->foreignId('media_tag_id')->constrained('media_tags')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['media_tag_id', 'media_id']);
            $table->index('media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_tag_items');
    }
};