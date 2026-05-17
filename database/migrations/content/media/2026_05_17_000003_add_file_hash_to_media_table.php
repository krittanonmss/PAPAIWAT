<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (! Schema::hasColumn('media', 'file_hash')) {
                $table->string('file_hash', 64)->nullable()->after('checksum');
                $table->index('file_hash', 'media_file_hash_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'file_hash')) {
                $table->dropIndex('media_file_hash_idx');
                $table->dropColumn('file_hash');
            }
        });
    }
};
