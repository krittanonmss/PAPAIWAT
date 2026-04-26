<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_section_data_sources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_section_id')
                ->constrained('page_sections')
                ->cascadeOnDelete();

            $table->string('source_type');
            $table->string('source_key')->nullable();

            $table->json('filters')->nullable();
            $table->json('sort')->nullable();

            $table->unsignedInteger('limit')->nullable();

            $table->timestamps();

            $table->index(['page_section_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_data_sources');
    }
};