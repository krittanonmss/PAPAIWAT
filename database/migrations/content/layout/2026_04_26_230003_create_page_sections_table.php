<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_id')
                ->constrained('pages')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('section_key');
            $table->string('component_key');

            $table->json('settings')->nullable();
            $table->json('content')->nullable();

            $table->string('status')->default('active');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['page_id', 'status', 'sort_order']);
            $table->index('component_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};