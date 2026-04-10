<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_opening_hours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temple_id')
                ->constrained('temples')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_closed')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('note')->nullable();

            $table->timestamps();

            $table->unique(['temple_id', 'day_of_week']);
            $table->index('day_of_week');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temple_opening_hours');
    }
};