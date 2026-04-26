<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_id')
                ->constrained('pages')
                ->cascadeOnDelete();

            $table->string('version_name')->nullable();
            $table->json('snapshot');

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();

            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_versions');
    }
};