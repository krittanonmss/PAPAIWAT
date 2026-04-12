<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            $table->string('variant_name', 50); // thumbnail, small, medium, large, webp
            $table->string('disk', 50)->default('public');
            $table->string('directory')->nullable();
            $table->string('filename');
            $table->string('path');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 100);

            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->string('processing_status', 30)->default('completed'); // pending, processing, completed, failed
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index('media_id', 'media_variants_media_id_idx');
            $table->index('variant_name', 'media_variants_variant_name_idx');
            $table->index('processing_status', 'media_variants_processing_status_idx');

            $table->unique(
                ['media_id', 'variant_name'],
                'media_variants_media_variant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_variants');
    }
};