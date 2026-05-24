<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temple_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('temple_reviews', 'moderation_reason')) {
                $table->string('moderation_reason', 40)->nullable()->after('report_count');
                $table->text('moderation_note')->nullable()->after('moderation_reason');
                $table->foreignId('moderated_by_admin_id')->nullable()->after('moderation_note')->constrained('admins')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable()->after('moderated_by_admin_id');
            }
        });

        Schema::table('public_comments', function (Blueprint $table) {
            if (! Schema::hasColumn('public_comments', 'moderation_reason')) {
                $table->string('moderation_reason', 40)->nullable()->after('report_count');
                $table->text('moderation_note')->nullable()->after('moderation_reason');
                $table->foreignId('moderated_by_admin_id')->nullable()->after('moderation_note')->constrained('admins')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable()->after('moderated_by_admin_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('public_comments', function (Blueprint $table) {
            if (Schema::hasColumn('public_comments', 'moderation_reason')) {
                $table->dropConstrainedForeignId('moderated_by_admin_id');
                $table->dropColumn(['moderation_reason', 'moderation_note', 'moderated_at']);
            }
        });

        Schema::table('temple_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('temple_reviews', 'moderation_reason')) {
                $table->dropConstrainedForeignId('moderated_by_admin_id');
                $table->dropColumn(['moderation_reason', 'moderation_note', 'moderated_at']);
            }
        });
    }
};
