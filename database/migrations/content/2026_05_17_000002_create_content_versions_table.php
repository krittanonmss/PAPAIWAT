<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->string('content_type', 30);
            $table->string('version_name')->nullable();
            $table->json('snapshot');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['content_id', 'created_at']);
            $table->index('content_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_versions');
    }
};
