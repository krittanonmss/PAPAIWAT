<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('location_key')->nullable();
            $table->string('description')->nullable();

            $table->string('status')->default('active');
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'location_key']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};