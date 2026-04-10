<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();

            $table->string('usage_type', 50); // temple_cover, temple_gallery, article_cover, etc.
            $table->string('entity_type', 50); // temple, article, page, admin
            $table->unsignedBigInteger('entity_id');
            $table->string('field_name', 100)->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['media_id', 'usage_type']);
            $table->index('field_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_usages');
    }
};