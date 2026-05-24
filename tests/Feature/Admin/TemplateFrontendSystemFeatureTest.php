<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Layout\Template;
use App\Models\Content\Temple\Temple;
use App\Support\TemplateRegistry;
use Database\Seeders\SystemAccessSeeder;
use Database\Seeders\SystemTemplateSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class TemplateFrontendSystemFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->seed(SystemTemplateSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($admin, 'admin');
    }

    public function test_template_registry_overrides_submitted_view_path_and_sets_compatibility_fields(): void
    {
        $template = Template::query()->where('key', 'article-editorial-detail')->firstOrFail();

        $this->put(route('admin.content.templates.update', $template), [
            'name' => 'Registry Only',
            'key' => 'article-editorial-detail',
            'view_path' => 'frontend.templates.pages.default',
            'status' => 'active',
        ])->assertRedirect(route('admin.content.templates.index'));

        $this->assertDatabaseHas('templates', [
            'key' => 'article-editorial-detail',
            'view_path' => 'frontend.templates.details.article-editorial',
            'template_type' => 'detail',
            'content_type' => 'article',
        ]);
    }

    public function test_template_registry_views_and_seeded_templates_stay_in_sync(): void
    {
        $registry = app(TemplateRegistry::class);

        foreach ($registry->templates() as $template) {
            $this->assertTrue(
                view()->exists($template['view_path']),
                "Missing template view for {$template['key']}: {$template['view_path']}"
            );

            $this->assertDatabaseHas('templates', [
                'key' => $template['key'],
                'view_path' => $template['view_path'],
                'template_type' => $template['template_type'],
                'content_type' => $template['content_type'],
            ]);
        }

        foreach ($registry->sections() as $section) {
            $viewPath = 'frontend.templates.sections.' . str_replace('-', '_', $section['key']);

            $this->assertTrue(
                view()->exists($viewPath),
                "Missing section view for {$section['key']}: {$viewPath}"
            );
        }
    }

    public function test_template_delete_is_blocked_when_default_or_used_by_page(): void
    {
        $defaultTemplate = Template::query()->where('key', 'page-builder')->firstOrFail();

        $this->delete(route('admin.content.templates.destroy', $defaultTemplate))
            ->assertSessionHasErrors('template');

        $usedTemplate = Template::query()->where('key', 'page-default')->firstOrFail();
        Page::query()->create([
            'template_id' => $usedTemplate->id,
            'title' => 'Used Page',
            'slug' => 'used-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        $this->delete(route('admin.content.templates.destroy', $usedTemplate))
            ->assertSessionHasErrors('template');
    }

    public function test_section_schema_requires_hero_title(): void
    {
        $page = Page::query()->create([
            'title' => 'Schema Page',
            'slug' => 'schema-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        $this->post(route('admin.content.pages.sections.store', $page), [
            'component_key' => 'hero',
            'content' => json_encode(['subtitle' => 'Missing title']),
            'settings' => json_encode([]),
            'status' => 'active',
            'is_visible' => '1',
        ])->assertSessionHasErrors('content');
    }

    public function test_section_editor_shows_existing_pages_as_button_destinations(): void
    {
        $page = Page::query()->create([
            'title' => 'Editor Page',
            'slug' => 'editor-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);
        Page::query()->create([
            'title' => 'Destination Page',
            'slug' => 'destination-page',
            'page_type' => 'custom',
            'status' => 'published',
        ]);

        $this->get(route('admin.content.pages.sections.create', $page))
            ->assertOk()
            ->assertSee('Destination Page')
            ->assertSee('destination-page')
            ->assertSee('โทนสีพื้นการ์ดโปร่ง')
            ->assertSee('สไตล์แผงตัวกรอง')
            ->assertSee('ช่องตัวกรองต่อแถว')
            ->assertSee('ระยะห่างแผงตัวกรอง')
            ->assertDontSee('ขนาดตัวอักษร')
            ->assertDontSee('น้ำหนักตัวอักษร');
    }

    public function test_cta_requires_at_least_one_enabled_button(): void
    {
        $page = Page::query()->create([
            'title' => 'CTA Page',
            'slug' => 'cta-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        $this->post(route('admin.content.pages.sections.store', $page), [
            'component_key' => 'cta',
            'content' => json_encode([
                'title' => 'CTA',
                'primary_enabled' => false,
                'primary_label' => 'Open',
                'primary_url' => '/destination',
                'secondary_enabled' => false,
            ]),
            'settings' => json_encode([]),
            'status' => 'active',
            'is_visible' => '1',
        ])->assertSessionHasErrors('content');
    }

    public function test_section_preview_renders_incomplete_required_content_instead_of_422(): void
    {
        $page = Page::query()->create([
            'title' => 'Preview Page',
            'slug' => 'preview-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        $this->postJson(route('admin.content.pages.sections.preview', $page), [
            'component_key' => 'rich_text',
            'content' => json_encode(['title' => 'ข้อความ section']),
            'settings' => json_encode([]),
        ])
            ->assertOk()
            ->assertJsonStructure(['html'])
            ->assertJsonPath('html', fn ($html) => is_string($html)
                && str_contains($html, 'ข้อความ section')
                && ! str_contains($html, 'ตัวอย่างเซกชันมีข้อผิดพลาด'));
    }

    public function test_full_list_preview_applies_filter_panel_layout_controls(): void
    {
        $page = Page::query()->create([
            'title' => 'Filter Layout Preview',
            'slug' => 'filter-layout-preview',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        $this->postJson(route('admin.content.pages.sections.preview', $page), [
            'component_key' => 'article_list_full',
            'content' => json_encode(['title' => 'รายการบทความ']),
            'settings' => json_encode([
                'filter_panel_style' => 'outline',
                'filter_panel_spacing' => 'spacious',
                'filter_columns' => 2,
            ]),
        ])
            ->assertOk()
            ->assertJsonPath('html', fn ($html) => is_string($html)
                && str_contains($html, 'data-section-filter-controls')
                && str_contains($html, 'p-8')
                && str_contains($html, 'gap-5 sm:grid-cols-2 xl:grid-cols-2')
                && str_contains($html, '--section-filter-bg: transparent'));
    }

    public function test_page_preview_uses_frontend_section_pipeline(): void
    {
        $pageTemplate = Template::query()->where('key', 'page-builder')->firstOrFail();
        $page = Page::query()->create([
            'template_id' => $pageTemplate->id,
            'title' => 'Preview Pipeline Page',
            'slug' => 'preview-pipeline-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);

        PageSection::query()->create([
            'page_id' => $page->id,
            'name' => 'Preview Hero',
            'section_key' => 'preview-hero',
            'component_key' => 'hero',
            'content' => [
                'title' => 'Preview Pipeline Hero',
                'subtitle' => 'Rendered through buildPageSections',
            ],
            'settings' => [
                'text_color' => '#123456',
                'heading_color' => '#abcdef',
            ],
            'status' => 'active',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $this->postJson(route('admin.content.pages.preview', $page), [
            'template_id' => $pageTemplate->id,
            'title' => 'Preview Pipeline Page',
            'slug' => 'preview-pipeline-page',
            'page_type' => 'custom',
            'status' => 'draft',
        ])
            ->assertOk()
            ->assertJsonPath('html', fn ($html) => is_string($html)
                && str_contains($html, 'Preview Pipeline Hero')
                && str_contains($html, 'Rendered through buildPageSections')
                && str_contains($html, '--section-heading-color: #abcdef'));
    }

    public function test_page_version_history_can_rollback_page_and_sections(): void
    {
        $page = Page::query()->create([
            'title' => 'Version One',
            'slug' => 'version-one',
            'page_type' => 'custom',
            'status' => 'draft',
        ]);
        PageSection::query()->create([
            'page_id' => $page->id,
            'name' => 'Original Hero',
            'section_key' => 'original-hero',
            'component_key' => 'hero',
            'content' => ['title' => 'Original Hero'],
            'settings' => [],
            'status' => 'active',
            'is_visible' => true,
        ]);

        app(\App\Services\Admin\Content\Layout\LayoutVersionService::class)->snapshotPage($page, 'manual');
        $version = $page->versions()->latest()->firstOrFail();

        $page->update(['title' => 'Version Two']);
        $page->sections()->delete();

        $this->post(route('admin.content.pages.versions.rollback', [$page, $version]))
            ->assertRedirect(route('admin.content.pages.show', $page));

        $page->refresh();
        $this->assertSame('Version One', $page->title);
        $this->assertDatabaseHas('page_sections', [
            'page_id' => $page->id,
            'section_key' => 'original-hero',
            'component_key' => 'hero',
        ]);
    }

    public function test_frontend_snapshot_views_render_home_page_article_and_temple_detail(): void
    {
        $pageTemplate = Template::query()->where('key', 'page-builder')->firstOrFail();
        $page = Page::query()->create([
            'template_id' => $pageTemplate->id,
            'title' => 'Snapshot Home',
            'slug' => 'snapshot-home',
            'page_type' => 'home',
            'status' => 'published',
            'is_homepage' => true,
        ]);
        PageSection::query()->create([
            'page_id' => $page->id,
            'name' => 'Home Hero',
            'section_key' => 'home-hero',
            'component_key' => 'hero',
            'content' => ['title' => 'Snapshot Hero'],
            'settings' => [],
            'status' => 'active',
            'is_visible' => true,
        ]);

        $articleContent = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Snapshot Article',
            'slug' => 'snapshot-article',
            'status' => 'published',
            'published_at' => now(),
        ]);
        Article::query()->create([
            'content_id' => $articleContent->id,
            'body' => 'Snapshot article body',
            'body_format' => 'markdown',
        ]);

        $templeContent = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Snapshot Temple',
            'slug' => 'snapshot-temple',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $temple = Temple::query()->create([
            'content_id' => $templeContent->id,
            'temple_type' => 'วัด',
        ]);

        $this->get(route('home'))->assertOk()->assertSee('Snapshot Hero');
        $this->get(route('pages.show', 'snapshot-home'))->assertOk()->assertSee('Snapshot Hero');
        $this->get(route('articles.show', 'snapshot-article'))->assertOk()->assertSee('Snapshot Article');
        $this->get(route('temples.show', $temple))->assertOk()->assertSee('Snapshot Temple');
    }
}
