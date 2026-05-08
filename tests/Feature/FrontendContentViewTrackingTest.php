<?php

namespace Tests\Feature;

use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleStat;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class FrontendContentViewTrackingTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
    }

    public function test_article_detail_tracks_one_view_per_session_per_day(): void
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Article With Views',
            'slug' => 'article-with-views',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $article = Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'markdown',
        ]);

        ArticleStat::query()->create([
            'article_id' => $article->id,
            'view_count' => 0,
            'like_count' => 0,
            'bookmark_count' => 0,
            'share_count' => 0,
            'updated_at' => now(),
        ]);

        $this->get(route('articles.show', $content->slug))->assertOk();
        $this->get(route('articles.show', $content->slug))->assertOk();

        $this->assertDatabaseHas('article_stats', [
            'article_id' => $article->id,
            'view_count' => 1,
        ]);
    }

    public function test_temple_detail_creates_stats_and_tracks_view(): void
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Temple With Views',
            'slug' => 'temple-with-views',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $temple = Temple::query()->create([
            'content_id' => $content->id,
        ]);

        $this->assertDatabaseMissing('temple_stats', [
            'temple_id' => $temple->id,
        ]);

        $this->get(route('temples.show', $temple))->assertOk();
        $this->get(route('temples.show', $temple))->assertOk();

        $this->assertDatabaseHas('temple_stats', [
            'temple_id' => $temple->id,
            'view_count' => 1,
            'review_count' => 0,
            'favorite_count' => 0,
        ]);

        $this->assertSame(1, TempleStat::query()->where('temple_id', $temple->id)->count());
    }
}
