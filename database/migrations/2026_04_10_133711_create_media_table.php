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

            $table->string('disk', 50)->default('public');
            $table->string('directory')->nullable();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 100);
            $table->string('media_type', 30); // image, video, audio, document, other

            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->text('description')->nullable();

            $table->string('checksum', 64)->nullable();
            $table->string('visibility', 20)->default('public'); // public, private
            $table->string('upload_status', 30)->default('completed'); // pending, processing, completed, failed

            $table->foreignId('uploaded_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('media_type');
            $table->index('mime_type');
            $table->index('visibility');
            $table->index('upload_status');
            $table->index('uploaded_by_admin_id');
            $table->index('uploaded_at');
            $table->index('checksum');
            $table->index(['media_type', 'upload_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};