<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->string('type_key', 50);

            $table->unsignedInteger('level')->default(0);
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('status', 20)->default('active');
            $table->boolean('is_featured')->default(false);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

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

            $table->index('parent_id', 'categories_parent_id_idx');
            $table->index('type_key', 'categories_type_key_idx');
            $table->index('status', 'categories_status_idx');
            $table->index('sort_order', 'categories_sort_order_idx');

            $table->unique(
                ['parent_id', 'slug', 'type_key'],
                'categories_parent_slug_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};