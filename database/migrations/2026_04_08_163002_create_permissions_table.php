<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('group_key');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};