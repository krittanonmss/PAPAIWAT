<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temple_stats', function (Blueprint $table) {
            if (! Schema::hasColumn('temple_stats', 'share_count')) {
                $table->unsignedInteger('share_count')->default(0)->after('favorite_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('temple_stats', function (Blueprint $table) {
            if (Schema::hasColumn('temple_stats', 'share_count')) {
                $table->dropColumn('share_count');
            }
        });
    }
};
