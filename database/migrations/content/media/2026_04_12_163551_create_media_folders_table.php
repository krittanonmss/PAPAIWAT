<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('media_folders')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active');

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

            $table->index('parent_id', 'media_folders_parent_id_idx');
            $table->index('status', 'media_folders_status_idx');
            $table->index('sort_order', 'media_folders_sort_order_idx');
            $table->index('created_by_admin_id', 'media_folders_created_by_admin_id_idx');
            $table->index('updated_by_admin_id', 'media_folders_updated_by_admin_id_idx');

            $table->unique(
                ['parent_id', 'slug'],
                'media_folders_parent_slug_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};