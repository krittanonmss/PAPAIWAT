<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anonymous_visitor_id')
                ->constrained('anonymous_visitors')
                ->cascadeOnDelete();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('public_comments')
                ->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->text('body');
            $table->string('status', 20)->default('pending');
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_type', 'commentable_id', 'status']);
            $table->index(['parent_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_comments');
    }
};
