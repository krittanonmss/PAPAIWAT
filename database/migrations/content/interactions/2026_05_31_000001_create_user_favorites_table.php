<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('favoritable_type', 20);
            $table->unsignedBigInteger('favoritable_id');
            $table->timestamp('added_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'favoritable_type', 'favoritable_id'], 'user_favorites_user_item_unique');
            $table->index(['favoritable_type', 'favoritable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
