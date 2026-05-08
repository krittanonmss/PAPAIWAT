<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temple_id')->constrained('temples')->cascadeOnDelete();
            $table->foreignId('anonymous_visitor_id')
                ->constrained('anonymous_visitors')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('display_name')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['temple_id', 'anonymous_visitor_id'],
                'temple_reviews_temple_visitor_unique'
            );
            $table->index(['temple_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temple_reviews');
    }
};
