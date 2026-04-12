<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            $table->string('entity_type', 50); // temple, article, page, admin
            $table->unsignedBigInteger('entity_id');
            $table->string('role_key', 50); // cover, gallery, seo, attachment, avatar
            $table->unsignedInteger('sort_order')->default(0);

            $table->foreignId('created_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['entity_type', 'entity_id'], 'media_usages_entity_idx');
            $table->index(['media_id', 'role_key'], 'media_usages_media_role_idx');
            $table->index('created_by_admin_id', 'media_usages_created_by_admin_id_idx');
            $table->index(['entity_type', 'entity_id', 'role_key'], 'media_usages_entity_role_idx');

            $table->unique(
                ['media_id', 'entity_type', 'entity_id', 'role_key'],
                'media_usages_unique_link'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_usages');
    }
};