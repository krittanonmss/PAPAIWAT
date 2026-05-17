<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('role_key')->nullable()->unique()->after('name');
            $table->unsignedSmallInteger('level')->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['role_key']);
            $table->dropColumn(['role_key', 'level']);
        });
    }
};
