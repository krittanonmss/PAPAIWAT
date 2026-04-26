<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id')
                ->constrained('menus')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('menu_items')
                ->cascadeOnDelete();

            $table->string('label');
            $table->string('slug')->nullable();

            $table->string('menu_item_type')->default('external_url');

            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();

            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->foreignId('content_id')->nullable()->constrained('contents')->nullOnDelete();

            $table->string('url')->nullable();
            $table->string('external_url')->nullable();
            $table->string('anchor')->nullable();

            $table->string('target')->default('_self');
            $table->string('rel')->nullable();
            $table->string('icon')->nullable();
            $table->string('css_class')->nullable();

            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->text('description')->nullable();

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['menu_id', 'parent_id', 'sort_order']);
            $table->index(['menu_item_type', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};