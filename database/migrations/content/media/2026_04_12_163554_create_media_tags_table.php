<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_tags', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->string('status', 20)->default('active');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('name', 'media_tags_name_idx');
            $table->index('status', 'media_tags_status_idx');
            $table->index('sort_order', 'media_tags_sort_order_idx');

            $table->unique('slug', 'media_tags_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_tags');
    }
};