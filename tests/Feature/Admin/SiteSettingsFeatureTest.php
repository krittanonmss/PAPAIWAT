<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Media\Media;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\PublicComment;
use App\Services\Interaction\PublicInteractionService;
use App\Support\SiteSettings;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class SiteSettingsFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('email', 'admin@example.com')->firstOrFail(), 'admin');
    }

    public function test_admin_can_open_all_setting_tabs_and_updates_are_audited(): void
    {
        $this->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('General')
            ->assertSee('Audit History');

        $this->put(route('admin.settings.update', 'general'), [
            'settings' => [
                'site_name' => 'Wat Directory',
                'tagline' => 'Discover temples',
                'contact_email' => 'contact@example.com',
                'contact_phone' => '02-000-0000',
                'contact_address' => 'Bangkok',
                'locale' => 'th',
                'timezone' => 'Asia/Bangkok',
            ],
        ])->assertRedirect(route('admin.settings.edit', ['tab' => 'general']));

        $this->assertSame('Wat Directory', SiteSettings::get('general', 'site_name'));
        $this->assertDatabaseHas('site_settings', [
            'group_key' => 'general',
            'key' => 'site_name',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'settings.general.updated',
            'table_name' => 'site_settings',
        ]);

        $this->get(route('admin.settings.edit', ['tab' => 'audit']))
            ->assertOk()
            ->assertSee('settings.general.updated');
    }

    public function test_seo_and_maintenance_settings_render_on_public_pages(): void
    {
        Page::query()->create([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        SiteSettings::saveGroup('seo', [
            'default_title' => 'Directory SEO',
            'default_description' => 'Explore local temples',
            'indexing_enabled' => false,
        ]);
        SiteSettings::saveGroup('maintenance', [
            'announcement_enabled' => true,
            'announcement_text' => 'ปิดปรับปรุงเวลา 22:00 น.',
            'announcement_level' => 'warning',
            'sitemap_enabled' => true,
        ]);

        $layoutHtml = view('frontend.layouts.app')->render();

        $this->assertStringContainsString('Directory SEO', $layoutHtml);
        $this->assertStringContainsString('Explore local temples', $layoutHtml);
        $this->assertStringContainsString('noindex,nofollow', $layoutHtml);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('ปิดปรับปรุงเวลา 22:00 น.');
    }

    public function test_general_and_integration_settings_drive_public_runtime_output(): void
    {
        Page::query()->create([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        SiteSettings::saveGroup('general', [
            'site_name' => 'Wat Runtime',
            'tagline' => 'Discover meaningful temples',
            'contact_email' => 'hello@example.com',
            'contact_phone' => '02-123-4567',
            'contact_address' => 'Bangkok, Thailand',
            'locale' => 'en',
            'timezone' => 'UTC',
        ]);
        SiteSettings::saveGroup('integrations', [
            'analytics_measurement_id' => 'G-ABC123',
            'tag_manager_container_id' => 'GTM-ABC123',
            'maps_enabled' => true,
            'maps_public_browser_key' => 'public-maps-key',
        ]);

        $this->assertSame('GTM-ABC123', SiteSettings::get('integrations', 'tag_manager_container_id'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('hello@example.com')
            ->assertSee('02-123-4567')
            ->assertSee('Bangkok, Thailand');

        $layoutHtml = view('frontend.layouts.app', [
            'frontendSiteSettings' => SiteSettings::all(),
        ])->render();

        $this->assertStringContainsString('<title>Wat Runtime | Discover meaningful temples</title>', $layoutHtml);
        $this->assertStringContainsString('content="Discover meaningful temples"', $layoutHtml);
        $this->assertStringContainsString('GTM-ABC123', $layoutHtml);
        $this->assertStringContainsString('G-ABC123', $layoutHtml);
        $this->assertStringContainsString('maps.googleapis.com/maps/api/js?key=public-maps-key', $layoutHtml);

        $this->assertSame('en', app()->getLocale());
        $this->assertSame('UTC', config('app.timezone'));
        $this->assertSame('UTC', date_default_timezone_get());

        $contactHtml = view('frontend.templates.sections.contact', [
            'section' => (object) [
                'content_data' => ['title' => 'Reach us'],
                'settings_data' => [],
            ],
            'frontendSiteSettings' => SiteSettings::all(),
        ])->render();

        $this->assertStringContainsString('Bangkok, Thailand', $contactHtml);
        $this->assertStringContainsString('02-123-4567', $contactHtml);
        $this->assertStringContainsString('hello@example.com', $contactHtml);
    }

    public function test_moderation_settings_block_comments_and_control_report_threshold(): void
    {
        $article = $this->publishedArticle();

        SiteSettings::saveGroup('moderation', [
            'comments_enabled' => false,
            'reviews_enabled' => true,
            'reports_enabled' => true,
            'auto_hide_report_threshold' => 2,
        ]);

        $this->post(route('articles.comments.store', $article), [
            'body' => 'should be rejected',
        ])->assertForbidden();

        $this->assertDatabaseCount('public_comments', 0);

        $commentOwner = AnonymousVisitor::query()->create([
            'visitor_uuid' => 'settings-comment-owner',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        $comment = PublicComment::query()->create([
            'anonymous_visitor_id' => $commentOwner->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'body' => 'reported comment',
            'status' => 'approved',
        ]);

        foreach (range(1, 2) as $index) {
            $visitor = AnonymousVisitor::query()->create([
                'visitor_uuid' => 'settings-visitor-'.$index,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);

            app(PublicInteractionService::class)->report($comment->refresh(), $visitor, null, null, null);
        }

        $this->assertDatabaseHas('public_comments', [
            'id' => $comment->id,
            'status' => 'rejected',
            'report_count' => 2,
        ]);
    }

    public function test_media_policy_changes_upload_visibility_and_supported_types(): void
    {
        Storage::fake('local');

        SiteSettings::saveGroup('media', [
            'max_upload_mb' => 2,
            'allowed_types' => ['image'],
            'default_visibility' => 'private',
            'image_quality' => 75,
            'duplicate_policy' => 'allow',
        ]);

        $this->post(route('admin.media.store'), [
            'files' => [UploadedFile::fake()->image('private-image.jpg')],
        ])->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseHas('media', [
            'original_filename' => 'private-image.jpg',
            'visibility' => 'private',
            'disk' => 'local',
        ]);

        $this->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'files' => [UploadedFile::fake()->create('manual.pdf', 10, 'application/pdf')],
            ])
            ->assertSessionHasErrors('files.0');

        $this->assertSame(1, Media::query()->count());
    }

    public function test_maintenance_can_generate_a_public_sitemap(): void
    {
        Storage::fake('public');
        $article = $this->publishedArticle();
        SiteSettings::saveGroup('maintenance', [
            'announcement_enabled' => true,
            'announcement_text' => 'Scheduled maintenance',
            'announcement_level' => 'warning',
            'sitemap_enabled' => true,
        ]);

        $this->post(route('admin.settings.maintenance.sitemap'))
            ->assertRedirect(route('admin.settings.edit', ['tab' => 'maintenance']));

        Storage::disk('public')->assertExists('sitemap.xml');
        $sitemap = Storage::disk('public')->get('sitemap.xml');

        $this->assertStringContainsString(route('home'), $sitemap);
        $this->assertStringContainsString(route('articles.show', $article->content->slug), $sitemap);
        $this->assertNotNull(SiteSettings::get('maintenance', 'sitemap_last_generated_at'));
        $this->assertSame('Scheduled maintenance', SiteSettings::get('maintenance', 'announcement_text'));
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'settings.maintenance.sitemap_generated',
        ]);
    }

    private function publishedArticle(): Article
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Published Article',
            'slug' => 'published-article',
            'status' => 'published',
            'published_at' => now(),
        ]);

        return Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'markdown',
            'allow_comments' => true,
        ]);
    }
}
