<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_folder_id')
                ->nullable()
                ->constrained('media_folders')
                ->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0);

            $table->string('disk', 50)->default('public');
            $table->string('directory')->nullable();
            $table->string('filename');
            $table->string('path');
            $table->string('original_filename');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 100);
            $table->string('media_type', 30); // image, video, audio, document, other

            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();

            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->text('description')->nullable();

            $table->string('checksum', 64)->nullable();
            $table->string('visibility', 20)->default('public'); // public, private
            $table->string('upload_status', 30)->default('completed'); // pending, processing, completed, failed

            $table->foreignId('uploaded_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('media_folder_id', 'media_media_folder_id_idx');
            $table->index('sort_order', 'media_sort_order_idx');
            $table->index('media_type', 'media_media_type_idx');
            $table->index('mime_type', 'media_mime_type_idx');
            $table->index('visibility', 'media_visibility_idx');
            $table->index('upload_status', 'media_upload_status_idx');
            $table->index('uploaded_by_admin_id', 'media_uploaded_by_admin_id_idx');
            $table->index('uploaded_at', 'media_uploaded_at_idx');
            $table->index('checksum', 'media_checksum_idx');
            $table->index(['media_type', 'upload_status'], 'media_type_upload_status_idx');
            $table->index(['media_folder_id', 'sort_order'], 'media_folder_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};