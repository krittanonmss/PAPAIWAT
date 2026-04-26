<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->nullable()
                ->constrained('templates')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('page_type')->default('custom');

            $table->string('status')->default('draft');
            $table->boolean('is_homepage')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->foreignId('og_image_media_id')->nullable()->constrained('media')->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('unpublished_at')->nullable();

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_homepage']);
            $table->index(['page_type', 'status']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};