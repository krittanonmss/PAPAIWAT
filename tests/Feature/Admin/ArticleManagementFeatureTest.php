<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleTag;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use Database\Seeders\SystemAccessSeeder;
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

        $this->admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
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
            'status' => 'published',
            'description' => 'รายละเอียดบทความ',
            'body' => 'เนื้อหาบทความ',
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
        $this->assertSame('published', $content->status);
        $this->assertSame($this->admin->id, $content->created_by_admin_id);
        $this->assertSame('ทีมงาน', $article->author_name);
        $this->assertDatabaseHas('article_stats', ['article_id' => $article->id]);
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
            'status' => 'published',
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
        $this->assertSame('published', $content->status);
        $this->assertSame('html', $article->body_format);
        $this->assertFalse($article->allow_comments);
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

        $this->delete(route('admin.content.articles.destroy', $article))
            ->assertRedirect(route('admin.content.articles.index'));

        $this->assertSoftDeleted('contents', ['id' => $article->content_id]);
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
