<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temple_stats', function (Blueprint $table) {
            if (! Schema::hasColumn('temple_stats', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('temple_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('temple_stats', function (Blueprint $table) {
            if (Schema::hasColumn('temple_stats', 'view_count')) {
                $table->dropColumn('view_count');
            }
        });
    }
};
