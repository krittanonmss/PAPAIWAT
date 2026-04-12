<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('status');
            $table->unsignedBigInteger('avatar_media_id')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role_id');
            $table->index('status');
            $table->index('avatar_media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};