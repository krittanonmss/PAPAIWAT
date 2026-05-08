<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anonymous_visitors', function (Blueprint $table) {
            if (! Schema::hasColumn('anonymous_visitors', 'status')) {
                $table->string('status', 20)->default('active')->after('user_agent_hash');
                $table->timestamp('banned_at')->nullable()->after('status');
            }
        });

        Schema::table('temple_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('temple_reviews', 'report_count')) {
                $table->unsignedInteger('report_count')->default(0)->after('status');
            }
        });

        Schema::table('public_comments', function (Blueprint $table) {
            if (! Schema::hasColumn('public_comments', 'report_count')) {
                $table->unsignedInteger('report_count')->default(0)->after('status');
            }
        });

        Schema::create('interaction_bans', function (Blueprint $table) {
            $table->id();
            $table->string('ban_type', 20);
            $table->string('value_hash', 64);
            $table->string('reason')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['ban_type', 'value_hash']);
            $table->index(['ban_type', 'expires_at']);
        });

        Schema::create('interaction_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anonymous_visitor_id')->nullable()->constrained('anonymous_visitors')->nullOnDelete();
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->string('reason', 80)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamps();

            $table->unique(
                ['anonymous_visitor_id', 'reportable_type', 'reportable_id'],
                'interaction_reports_visitor_reportable_unique'
            );
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interaction_reports');
        Schema::dropIfExists('interaction_bans');

        Schema::table('public_comments', function (Blueprint $table) {
            if (Schema::hasColumn('public_comments', 'report_count')) {
                $table->dropColumn('report_count');
            }
        });

        Schema::table('temple_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('temple_reviews', 'report_count')) {
                $table->dropColumn('report_count');
            }
        });

        Schema::table('anonymous_visitors', function (Blueprint $table) {
            if (Schema::hasColumn('anonymous_visitors', 'status')) {
                $table->dropColumn(['status', 'banned_at']);
            }
        });
    }
};
