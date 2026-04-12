<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('categorizable_type');
            $table->unsignedBigInteger('categorizable_id');

            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['categorizable_type', 'categorizable_id'],
                'categorizables_type_id_idx'
            );

            $table->index(
                'category_id',
                'categorizables_category_id_idx'
            );

            $table->unique(
                ['category_id', 'categorizable_type', 'categorizable_id'],
                'categorizables_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorizables');
    }
};