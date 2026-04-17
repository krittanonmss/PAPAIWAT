<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            // core identity
            $table->string('content_type', 30); // temple, article
            $table->string('title');
            $table->string('slug');

            // content summary
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            // status
            $table->string('status', 20)->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // publish
            $table->timestamp('published_at')->nullable();

            // audit
            $table->foreignId('created_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->foreignId('updated_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // indexes
            $table->index('content_type', 'contents_content_type_idx');
            $table->index('status', 'contents_status_idx');
            $table->index('is_featured', 'contents_is_featured_idx');
            $table->index('is_popular', 'contents_is_popular_idx');
            $table->index('published_at', 'contents_published_at_idx');

            // unique slug ต่อ type (สำคัญ)
            $table->unique(
                ['content_type', 'slug'],
                'contents_type_slug_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};