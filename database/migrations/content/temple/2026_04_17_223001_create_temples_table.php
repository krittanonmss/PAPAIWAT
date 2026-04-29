<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temples', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_id')
                ->constrained('contents')
                ->cascadeOnDelete()
                ->unique();

            $table->string('temple_type', 50)->nullable();
            $table->string('sect', 100)->nullable();
            $table->string('architecture_style', 150)->nullable();
            $table->string('founded_year', 20)->nullable();

            $table->text('history')->nullable();
            $table->text('dress_code')->nullable();

            $table->time('recommended_visit_start_time')->nullable();
            $table->time('recommended_visit_end_time')->nullable();

            $table->timestamps();

            $table->index('content_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temples');
    }
};