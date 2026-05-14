<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Temple;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class MediaSelectionEditFormTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($admin, 'admin');
    }

    public function test_temple_edit_prepends_previously_selected_cover_and_gallery_media(): void
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'วัดมีรูปเดิม',
            'slug' => 'temple-with-selected-media',
            'status' => 'published',
        ]);
        $temple = Temple::query()->create(['content_id' => $content->id]);

        $cover = $this->createImageMedia('old-cover.jpg');
        $gallery = $this->createImageMedia('old-gallery.jpg');

        foreach (range(1, 12) as $index) {
            $this->createImageMedia("newer-$index.jpg");
        }

        $content->mediaUsages()->create([
            'media_id' => $cover->id,
            'entity_type' => Content::class,
            'entity_id' => $content->id,
            'role_key' => 'cover',
            'sort_order' => 0,
            'created_by_admin_id' => Admin::query()->first()->id,
        ]);
        $content->mediaUsages()->create([
            'media_id' => $gallery->id,
            'entity_type' => Content::class,
            'entity_id' => $content->id,
            'role_key' => 'gallery',
            'sort_order' => 0,
            'created_by_admin_id' => Admin::query()->first()->id,
        ]);

        $this->get(route('admin.temples.edit', $temple))
            ->assertOk()
            ->assertSee('old-cover.jpg')
            ->assertSee('old-gallery.jpg')
            ->assertSee("selectedCover: window.templeDraftMediaId('cover_media_id', '{$cover->id}')", false)
            ->assertSee("\"{$gallery->id}\"", false);
    }

    public function test_article_edit_prepends_previously_selected_cover_media(): void
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'บทความมีรูปเดิม',
            'slug' => 'article-with-selected-media',
            'status' => 'published',
        ]);
        $article = Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'html',
        ]);

        $cover = $this->createImageMedia('old-article-cover.jpg');

        foreach (range(1, 10) as $index) {
            $this->createImageMedia("newer-article-$index.jpg");
        }

        $content->mediaUsages()->create([
            'media_id' => $cover->id,
            'entity_type' => Content::class,
            'entity_id' => $content->id,
            'role_key' => 'cover',
            'sort_order' => 0,
            'created_by_admin_id' => Admin::query()->first()->id,
        ]);

        $this->get(route('admin.content.articles.edit', $article))
            ->assertOk()
            ->assertSee('old-article-cover.jpg')
            ->assertSee("selectedCover: window.articleDraftMediaId('cover_media_id', '{$cover->id}')", false);
    }

    private function createImageMedia(string $filename): Media
    {
        return Media::query()->create([
            'filename' => $filename,
            'path' => 'uploads/'.$filename,
            'original_filename' => $filename,
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => Admin::query()->first()->id,
            'uploaded_at' => now(),
        ]);
    }
}
