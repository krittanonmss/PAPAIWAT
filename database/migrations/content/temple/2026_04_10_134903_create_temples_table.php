<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temples', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('history')->nullable();

            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);

            $table->unsignedBigInteger('cover_media_id')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->unsignedBigInteger('updated_by_admin_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('status');
            $table->index('is_featured');
            $table->index('sort_order');
            $table->index('published_at');
            $table->index('cover_media_id');
            $table->index('created_by_admin_id');
            $table->index('updated_by_admin_id');

            $table->foreign('cover_media_id')
                ->references('id')
                ->on('media')
                ->nullOnDelete();

            $table->foreign('created_by_admin_id')
                ->references('id')
                ->on('admins')
                ->nullOnDelete();

            $table->foreign('updated_by_admin_id')
                ->references('id')
                ->on('admins')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temples');
    }
};