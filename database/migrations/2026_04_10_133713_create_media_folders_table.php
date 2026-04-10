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

            $table->foreignId('parent_id')->nullable()->constrained('media_folders')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('created_by_admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};