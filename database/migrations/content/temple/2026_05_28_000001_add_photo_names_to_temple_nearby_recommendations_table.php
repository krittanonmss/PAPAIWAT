<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temple_nearby_recommendations', function (Blueprint $table) {
            $table->json('photo_names')->nullable()->after('sort_score');
        });
    }

    public function down(): void
    {
        Schema::table('temple_nearby_recommendations', function (Blueprint $table) {
            $table->dropColumn('photo_names');
        });
    }
};
