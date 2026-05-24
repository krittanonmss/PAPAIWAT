<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminPreference;
use App\Models\Admin\Role;
use App\Models\Content\ContentVersion;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleTag;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class ArticleManagementFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $this->admin = Admin::query()->where('status', 'active')->firstOrFail();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_create_article_with_category_tag_and_cover_media(): void
    {
        $category = $this->createCategory('article', 'ข่าววัด');
        $tag = ArticleTag::query()->create([
            'name' => 'งานบุญ',
            'slug' => 'merit',
            'status' => 'active',
        ]);
        $media = $this->createImageMedia();

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความทดสอบ',
            'slug' => 'test-article',
            'status' => 'draft',
            'excerpt' => '<p>คำโปรย<script>alert(1)</script></p>',
            'description' => '<p onclick="alert(1)">รายละเอียดบทความ</p>',
            'body' => '<p>เนื้อหาบทความ</p><script>alert(1)</script>',
            'body_format' => 'markdown',
            'author_name' => 'ทีมงาน',
            'reading_time_minutes' => 3,
            'category_ids' => [$category->id],
            'tag_ids' => [$tag->id],
            'cover_media_id' => $media->id,
            'allow_comments' => '1',
            'show_on_homepage' => '1',
        ])->assertRedirect(route('admin.content.articles.index'));

        $content = Content::query()->where('slug', 'test-article')->firstOrFail();
        $article = Article::query()->where('content_id', $content->id)->firstOrFail();

        $this->assertSame('article', $content->content_type);
        $this->assertSame('draft', $content->status);
        $this->assertSame($this->admin->id, $content->created_by_admin_id);
        $this->assertSame('ทีมงาน', $article->author_name);
        $this->assertStringNotContainsString('<script', (string) $content->excerpt);
        $this->assertStringNotContainsString('onclick', (string) $content->description);
        $this->assertStringNotContainsString('<script', (string) $article->body);
        $this->assertDatabaseHas('article_stats', ['article_id' => $article->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.created',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);
        $this->assertDatabaseHas('content_versions', [
            'content_id' => $content->id,
            'content_type' => 'article',
            'version_name' => 'created',
        ]);
        $this->assertDatabaseHas('article_tag_items', [
            'article_id' => $article->id,
            'article_tag_id' => $tag->id,
        ]);
        $this->assertDatabaseHas('categorizables', [
            'category_id' => $category->id,
            'categorizable_type' => 'content',
            'categorizable_id' => $content->id,
            'is_primary' => true,
        ]);
        $this->assertDatabaseHas('media_usages', [
            'media_id' => $media->id,
            'entity_type' => 'content',
            'entity_id' => $content->id,
            'role_key' => 'cover',
        ]);
    }

    public function test_admin_can_update_article_and_replace_relationships(): void
    {
        $oldCategory = $this->createCategory('article', 'เก่า');
        $newCategory = $this->createCategory('article', 'ใหม่');
        $oldTag = ArticleTag::query()->create(['name' => 'เก่า', 'slug' => 'old', 'status' => 'active']);
        $newTag = ArticleTag::query()->create(['name' => 'ใหม่', 'slug' => 'new', 'status' => 'active']);
        $oldMedia = $this->createImageMedia('old-cover.jpg');
        $newMedia = $this->createImageMedia('new-cover.jpg');

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความเดิม',
            'slug' => 'old-article',
            'status' => 'draft',
            'body_format' => 'markdown',
            'category_ids' => [$oldCategory->id],
            'tag_ids' => [$oldTag->id],
            'cover_media_id' => $oldMedia->id,
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->firstOrFail();

        $this->put(route('admin.content.articles.update', $article), [
            'title' => 'บทความใหม่',
            'slug' => 'new-article',
            'status' => 'review',
            'body' => '<p>อัปเดต</p>',
            'body_format' => 'html',
            'category_ids' => [$newCategory->id],
            'tag_ids' => [$newTag->id],
            'cover_media_id' => $newMedia->id,
            'allow_comments' => '0',
        ])->assertRedirect(route('admin.content.articles.edit', $article));

        $article->refresh();
        $content = $article->content()->firstOrFail();

        $this->assertSame('บทความใหม่', $content->title);
        $this->assertSame('new-article', $content->slug);
        $this->assertSame('review', $content->status);
        $this->assertSame('html', $article->body_format);
        $this->assertFalse($article->allow_comments);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.status_changed',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.media_changed',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);
        $this->assertDatabaseHas('content_versions', [
            'content_id' => $content->id,
            'content_type' => 'article',
            'version_name' => 'updated',
        ]);
        $this->assertDatabaseMissing('article_tag_items', [
            'article_id' => $article->id,
            'article_tag_id' => $oldTag->id,
        ]);
        $this->assertDatabaseHas('article_tag_items', [
            'article_id' => $article->id,
            'article_tag_id' => $newTag->id,
        ]);
        $this->assertDatabaseHas('categorizables', [
            'category_id' => $newCategory->id,
            'categorizable_type' => 'content',
            'categorizable_id' => $content->id,
        ]);
        $this->assertDatabaseHas('media_usages', [
            'media_id' => $newMedia->id,
            'entity_type' => 'content',
            'entity_id' => $content->id,
            'role_key' => 'cover',
        ]);
    }

    public function test_admin_can_soft_delete_article_content(): void
    {
        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความที่จะลบ',
            'slug' => 'deleted-article',
            'status' => 'draft',
            'body_format' => 'markdown',
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();

        $this->get(route('admin.content.articles.show', $article))
            ->assertOk()
            ->assertSee(route('admin.content.articles.destroy', $article), false)
            ->assertSee('ลบ');

        $this->get(route('admin.content.articles.edit', $article))
            ->assertOk()
            ->assertSee('delete-article-form')
            ->assertSee('ลบบทความ');

        $this->delete(route('admin.content.articles.destroy', $article))
            ->assertRedirect(route('admin.content.articles.index'));

        $this->assertSoftDeleted('contents', ['id' => $article->content_id]);
    }

    public function test_admin_can_publish_and_unpublish_article_through_publish_permission_flow(): void
    {
        Mail::fake();
        AdminPreference::query()->create([
            'admin_id' => $this->admin->id,
            'key' => 'notifications.email',
            'value' => ['value' => true],
        ]);

        $category = $this->createCategory('article', 'เผยแพร่');

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความรอตรวจสอบ',
            'slug' => 'review-article',
            'status' => 'review',
            'body' => 'เนื้อหาพร้อมเผยแพร่',
            'body_format' => 'markdown',
            'category_ids' => [$category->id],
            'allow_comments' => '1',
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();

        $this->patch(route('admin.content.articles.publish', $article))
            ->assertRedirect(route('admin.content.articles.edit', $article));

        $content = $article->fresh()->content()->firstOrFail();

        $this->assertSame('published', $content->status);
        $this->assertNotNull($content->published_at);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.published',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);
        $this->assertDatabaseHas('content_versions', [
            'content_id' => $content->id,
            'content_type' => 'article',
            'version_name' => 'published',
        ]);
        $this->assertDatabaseHas('admin_notifications', [
            'admin_id' => $this->admin->id,
            'type' => 'content',
            'title' => 'เผยแพร่บทความแล้ว',
        ]);

        $this->patch(route('admin.content.articles.unpublish', $article))
            ->assertRedirect(route('admin.content.articles.edit', $article));

        $this->assertSame('review', $article->fresh()->content->status);
        $this->assertDatabaseHas('admin_notifications', [
            'admin_id' => $this->admin->id,
            'type' => 'content',
            'title' => 'ยกเลิกเผยแพร่บทความแล้ว',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.unpublished',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);

        $this->patch(route('admin.content.articles.unpublish', $article))
            ->assertSessionHasErrors('status');

        Mail::assertQueuedCount(3);
    }

    public function test_direct_article_publish_requires_ready_content_and_records_publication(): void
    {
        $category = $this->createCategory('article', 'เผยแพร่ตรง');
        $editor = Admin::query()->create([
            'username' => 'article-editor',
            'email' => 'article-editor@example.com',
            'password_hash' => Hash::make('EditorPassword12345'),
            'role_id' => Role::query()->where('role_key', 'editor')->firstOrFail()->id,
            'status' => 'active',
        ]);

        $this->actingAs($editor, 'admin');
        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความไม่มีสิทธิ์เผยแพร่',
            'slug' => 'cannot-direct-publish',
            'status' => 'published',
            'body' => 'เนื้อหาพร้อม',
            'body_format' => 'markdown',
            'category_ids' => [$category->id],
        ])->assertSessionHasErrors('status');

        $this->actingAs($this->admin, 'admin');
        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความเผยแพร่ตรง',
            'slug' => 'direct-published-article',
            'status' => 'published',
            'body_format' => 'markdown',
        ])->assertSessionHasErrors(['body', 'category_ids']);

        $this->assertSame(0, Article::query()->count());

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความเผยแพร่ตรง',
            'slug' => 'direct-published-article',
            'status' => 'published',
            'body' => 'เนื้อหาพร้อมเผยแพร่',
            'body_format' => 'markdown',
            'category_ids' => [$category->id],
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();

        $this->assertSame('published', $article->content->status);
        $this->assertNotNull($article->content->published_at);
        $this->assertDatabaseHas('content_versions', [
            'content_id' => $article->content_id,
            'version_name' => 'published',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.published',
            'table_name' => 'articles',
            'record_id' => $article->id,
        ]);
        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'content',
            'title' => 'เผยแพร่บทความแล้ว',
        ]);

        $this->patch(route('admin.content.articles.publish', $article))
            ->assertSessionHasErrors('status');

        $this->assertSame(1, ContentVersion::query()
            ->where('content_id', $article->content_id)
            ->where('version_name', 'published')
            ->count());
    }

    public function test_article_date_validation_blocks_invalid_schedule_publish_and_expiry_dates(): void
    {
        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความวันที่ผิด',
            'slug' => 'invalid-date-article',
            'status' => 'draft',
            'body_format' => 'markdown',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'expired_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors(['expired_at']);

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความวันที่เผยแพร่ผิด',
            'slug' => 'invalid-published-date-article',
            'status' => 'draft',
            'body_format' => 'markdown',
            'published_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors(['published_at']);
    }

    public function test_publish_requires_ready_article_and_valid_schedule(): void
    {
        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความยังไม่พร้อม',
            'slug' => 'not-ready-article',
            'status' => 'review',
            'body_format' => 'markdown',
            'body' => '',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();

        $this->patch(route('admin.content.articles.publish', $article))
            ->assertSessionHasErrors(['body', 'category_ids', 'scheduled_at']);

        $this->assertSame('review', $article->fresh()->content->status);
    }

    public function test_article_index_shows_five_items_per_page(): void
    {
        foreach (range(1, 6) as $index) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => 'บทความ '.$index,
                'slug' => 'article-'.$index,
                'status' => 'published',
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'markdown',
            ]);
        }

        $this->get(route('admin.content.articles.index'))
            ->assertOk()
            ->assertViewHas('articles', function ($articles) {
                return $articles->perPage() === 5
                    && $articles->count() === 5
                    && $articles->lastPage() === 2;
            });
    }

    public function test_article_index_uses_preferred_default_per_page_when_filter_is_not_set(): void
    {
        AdminPreference::query()->create([
            'admin_id' => $this->admin->id,
            'key' => 'tables.default_per_page',
            'value' => ['value' => 15],
        ]);

        foreach (range(1, 16) as $index) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => 'บทความ preference '.$index,
                'slug' => 'article-preference-'.$index,
                'status' => 'published',
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'markdown',
            ]);
        }

        $this->get(route('admin.content.articles.index'))
            ->assertOk()
            ->assertViewHas('articles', function ($articles) {
                return $articles->perPage() === 15
                    && $articles->count() === 15
                    && $articles->lastPage() === 2;
            });
    }

    public function test_admin_can_bulk_assign_selected_articles_to_category_without_replacing_existing_categories(): void
    {
        $oldCategory = $this->createCategory('article', 'หมวดเดิม');
        $newCategory = $this->createCategory('article', 'หมวดใหม่');

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความแรก',
            'slug' => 'first-bulk-article',
            'status' => 'draft',
            'body_format' => 'markdown',
            'category_ids' => [$oldCategory->id],
        ])->assertRedirect(route('admin.content.articles.index'));

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความสอง',
            'slug' => 'second-bulk-article',
            'status' => 'draft',
            'body_format' => 'markdown',
            'category_ids' => [$oldCategory->id],
        ])->assertRedirect(route('admin.content.articles.index'));

        $articles = Article::query()->with('content')->orderBy('id')->get();

        $this->get(route('admin.content.articles.index'))
            ->assertOk()
            ->assertSee('bulk_article_category_id', false)
            ->assertSee('type=article', false)
            ->assertSee('status=active', false)
            ->assertSee('ค้นหาหมวดหมู่บทความ');

        $this->patch(route('admin.content.articles.bulk-category'), [
            'article_ids' => $articles->pluck('id')->all(),
            'category_id' => $newCategory->id,
        ])->assertRedirect(route('admin.content.articles.index'))
            ->assertSessionHas('success');

        foreach ($articles as $article) {
            $this->assertDatabaseHas('categorizables', [
                'category_id' => $oldCategory->id,
                'categorizable_type' => 'content',
                'categorizable_id' => $article->content_id,
                'is_primary' => true,
            ]);

            $this->assertDatabaseHas('categorizables', [
                'category_id' => $newCategory->id,
                'categorizable_type' => 'content',
                'categorizable_id' => $article->content_id,
                'is_primary' => false,
            ]);
        }

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.category_assigned',
            'table_name' => 'articles',
            'record_id' => $articles->first()->id,
        ]);
    }

    public function test_article_template_live_preview_uses_fallback_slug_for_thai_title(): void
    {
        $this->postJson(route('admin.content.template-preview.live', ['type' => 'article']), [
            'title' => 'บทความภาษาไทย',
            'slug' => '',
            'status' => 'draft',
            'body' => '<p>เนื้อหาพรีวิว</p>',
            'body_format' => 'html',
        ])
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, 'บทความภาษาไทย')
                && ! str_contains($html, 'Template preview error'));
    }

    public function test_article_template_live_preview_renders_editorial_template(): void
    {
        $template = Template::query()->create([
            'name' => 'Article Editorial',
            'key' => 'article-editorial-test',
            'view_path' => 'frontend.templates.details.article-editorial',
            'status' => 'active',
            'is_default' => false,
            'sort_order' => 1,
        ]);

        $this->postJson(route('admin.content.template-preview.live', ['type' => 'article']), [
            'title' => 'บทความ Editorial',
            'slug' => 'editorial-preview',
            'status' => 'draft',
            'body' => '<p>เนื้อหา editorial</p>',
            'body_format' => 'html',
            'template_id' => $template->id,
        ])
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, 'บทความ Editorial')
                && ! str_contains($html, 'Template preview error'));
    }

    public function test_article_comment_toggle_controls_frontend_comment_form(): void
    {
        $category = $this->createCategory('article', 'ความคิดเห็น');

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความปิดความคิดเห็น',
            'slug' => 'comments-disabled-article',
            'status' => 'review',
            'body' => 'เนื้อหาพร้อมเผยแพร่',
            'body_format' => 'markdown',
            'category_ids' => [$category->id],
            'allow_comments' => '0',
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();
        $this->assertFalse($article->allow_comments);

        $this->patch(route('admin.content.articles.publish', $article))
            ->assertRedirect(route('admin.content.articles.edit', $article));

        $this->get(route('articles.show', 'comments-disabled-article'))
            ->assertOk()
            ->assertDontSee('เขียนความคิดเห็น')
            ->assertDontSee(route('articles.comments.store', $article), false);
    }

    public function test_article_frontend_markdown_output_is_sanitized(): void
    {
        $category = $this->createCategory('article', 'ปลอดภัย');

        $this->post(route('admin.content.articles.store'), [
            'title' => 'บทความ XSS',
            'slug' => 'xss-article',
            'status' => 'review',
            'body' => '<img src=x onerror=alert(1)> **ปลอดภัย**',
            'body_format' => 'markdown',
            'category_ids' => [$category->id],
        ])->assertRedirect(route('admin.content.articles.index'));

        $article = Article::query()->with('content')->firstOrFail();

        $this->patch(route('admin.content.articles.publish', $article))
            ->assertRedirect(route('admin.content.articles.edit', $article));

        $this->get(route('articles.show', 'xss-article'))
            ->assertOk()
            ->assertDontSee('onerror', false)
            ->assertDontSee('alert(1)', false)
            ->assertSee('ปลอดภัย');
    }

    public function test_article_form_lookup_endpoints_search_server_side(): void
    {
        $category = $this->createCategory('article', 'หมวดค้นหาเฉพาะ');
        $this->createCategory('article', 'หมวดอื่น');

        $tag = ArticleTag::query()->create([
            'name' => 'แท็กค้นหาเฉพาะ',
            'slug' => 'search-only-tag',
            'status' => 'active',
        ]);
        ArticleTag::query()->create([
            'name' => 'แท็กอื่น',
            'slug' => 'other-tag',
            'status' => 'active',
        ]);

        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'บทความค้นหาเฉพาะ',
            'slug' => 'lookup-target-article',
            'status' => 'published',
        ]);
        $article = Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'markdown',
        ]);

        $this->getJson(route('admin.lookups.categories', ['type' => 'article', 'q' => 'เฉพาะ']))
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $category->id, 'label' => 'หมวดค้นหาเฉพาะ'])
            ->assertJsonMissing(['label' => 'หมวดอื่น']);

        $this->getJson(route('admin.lookups.article-tags', ['q' => 'เฉพาะ']))
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $tag->id, 'label' => 'แท็กค้นหาเฉพาะ'])
            ->assertJsonMissing(['label' => 'แท็กอื่น']);

        $this->getJson(route('admin.lookups.articles', ['q' => 'lookup-target']))
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $article->id, 'label' => 'บทความค้นหาเฉพาะ']);
    }

    private function createCategory(string $typeKey, string $name): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => str($name)->slug('-')->toString() ?: 'category-'.Category::query()->count(),
            'type_key' => $typeKey,
            'status' => 'active',
        ]);
    }

    private function createImageMedia(string $filename = 'cover.jpg'): Media
    {
        return Media::query()->create([
            'filename' => $filename,
            'path' => 'uploads/'.$filename,
            'original_filename' => $filename,
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => $this->admin->id,
            'uploaded_at' => now(),
        ]);
    }
}
