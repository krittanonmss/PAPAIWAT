<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temple_id')
                ->constrained('temples')
                ->cascadeOnDelete();

            $table->string('fee_type');
            $table->string('label');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('THB');
            $table->string('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('temple_id');
            $table->index('fee_type');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temple_fees');
    }
};