<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_nearby_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temple_id')->constrained('temples')->cascadeOnDelete();
            $table->string('provider', 40)->default('google');
            $table->string('provider_place_id', 255);
            $table->string('category', 50);
            $table->string('name');
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('user_ratings_total')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->string('maps_url', 2048)->nullable();
            $table->decimal('sort_score', 8, 2)->default(0);
            $table->json('provider_types')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('stale_until')->nullable();
            $table->timestamps();

            $table->unique(['temple_id', 'provider', 'provider_place_id', 'category'], 'temple_nearby_provider_unique');
            $table->index(['temple_id', 'category', 'sort_score'], 'temple_nearby_cat_score_idx');
            $table->index(['expires_at', 'stale_until'], 'temple_nearby_expiry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temple_nearby_recommendations');
    }
};
