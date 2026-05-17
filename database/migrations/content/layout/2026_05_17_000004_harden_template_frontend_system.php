<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('template_type')->default('page')->after('view_path')->index();
            $table->string('content_type')->default('global')->after('template_type')->index();
            $table->json('schema')->nullable()->after('content_type');

            $table->index(['template_type', 'content_type', 'is_default']);
        });

        DB::table('templates')->where('view_path', 'like', 'frontend.templates.details.article-%')->update([
            'template_type' => 'detail',
            'content_type' => 'article',
        ]);
        DB::table('templates')->where('view_path', 'like', 'frontend.templates.details.temple-%')->update([
            'template_type' => 'detail',
            'content_type' => 'temple',
        ]);
        DB::table('templates')->where('view_path', 'like', 'frontend.templates.lists.article%')->update([
            'template_type' => 'list',
            'content_type' => 'article',
        ]);
        DB::table('templates')->where('view_path', 'like', 'frontend.templates.lists.temple%')->update([
            'template_type' => 'list',
            'content_type' => 'temple',
        ]);

        Schema::create('template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->string('version_name')->nullable();
            $table->json('snapshot');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('template_id');
        });

        Schema::create('page_section_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained('page_sections')->cascadeOnDelete();
            $table->string('version_name')->nullable();
            $table->json('snapshot');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('page_section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_versions');
        Schema::dropIfExists('template_versions');

        Schema::table('templates', function (Blueprint $table) {
            $table->dropIndex(['template_type', 'content_type', 'is_default']);
            $table->dropColumn(['template_type', 'content_type', 'schema']);
        });
    }
};
