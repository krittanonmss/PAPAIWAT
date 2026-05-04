<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('contents', 'template_id')) {
            return;
        }

        Schema::table('contents', function (Blueprint $table) {
            $table->foreignId('template_id')
                ->nullable()
                ->after('slug')
                ->constrained('templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('contents', 'template_id')) {
            return;
        }

        Schema::table('contents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
        });
    }
};
