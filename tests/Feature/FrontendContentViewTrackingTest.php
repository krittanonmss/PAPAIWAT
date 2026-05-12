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

    public function test_article_html_body_renders_safe_rich_text_and_youtube_embeds_only(): void
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Article With HTML',
            'slug' => 'article-with-html',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'html',
            'body' => implode('', [
                '<h2 onclick="alert(1)">หัวข้อ HTML</h2>',
                '<p style="color: green; background-image: url(javascript:alert(1));">hello</p>',
                '<p><a href="javascript:alert(1)">unsafe link</a></p>',
                '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video"></iframe>',
                '<iframe src="https://player.vimeo.com/video/123" title="Unsafe"></iframe>',
                '<script>alert(1)</script>',
            ]),
        ]);

        $this->get(route('articles.show', $content->slug))
            ->assertOk()
            ->assertSee('<h2>หัวข้อ HTML</h2>', false)
            ->assertSee('<p style="color: green;">hello</p>', false)
            ->assertSee('<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video"></iframe>', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('background-image', false)
            ->assertDontSee('javascript:alert', false)
            ->assertDontSee('player.vimeo.com', false)
            ->assertDontSee('alert(1)', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_public_favorite_and_share_counters_store_only_aggregate_stats(): void
    {
        $articleContent = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Article With Counters',
            'slug' => 'article-with-counters',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $article = Article::query()->create([
            'content_id' => $articleContent->id,
            'body_format' => 'html',
        ]);

        $templeContent = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Temple With Counters',
            'slug' => 'temple-with-counters',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $temple = Temple::query()->create([
            'content_id' => $templeContent->id,
        ]);

        $this->postJson(route('interactions.favorite'), [
            'type' => 'article',
            'id' => $article->id,
            'action' => 'add',
        ])->assertOk()->assertJson(['count' => 1]);

        $this->postJson(route('interactions.favorite'), [
            'type' => 'article',
            'id' => $article->id,
            'action' => 'remove',
        ])->assertOk()->assertJson(['count' => 0]);

        $this->postJson(route('interactions.share'), [
            'type' => 'article',
            'id' => $article->id,
        ])->assertOk()->assertJson(['count' => 1]);

        $this->postJson(route('interactions.favorite'), [
            'type' => 'temple',
            'id' => $temple->id,
            'action' => 'add',
        ])->assertOk()->assertJson(['count' => 1]);

        $this->postJson(route('interactions.share'), [
            'type' => 'temple',
            'id' => $temple->id,
        ])->assertOk()->assertJson(['count' => 1]);

        $this->postJson(route('favorites.items'), [
            'items' => [
                ['type' => 'article', 'id' => $article->id],
                ['type' => 'temple', 'id' => $temple->id],
            ],
        ])->assertOk()
            ->assertJsonPath('items.0.type', 'article')
            ->assertJsonPath('items.0.id', $article->id)
            ->assertJsonPath('items.0.count', 0)
            ->assertJsonPath('items.1.type', 'temple')
            ->assertJsonPath('items.1.id', $temple->id)
            ->assertJsonPath('items.1.count', 1);

        $this->assertDatabaseHas('article_stats', [
            'article_id' => $article->id,
            'bookmark_count' => 0,
            'share_count' => 1,
        ]);
        $this->assertDatabaseHas('temple_stats', [
            'temple_id' => $temple->id,
            'favorite_count' => 1,
            'share_count' => 1,
        ]);
        $this->assertDatabaseMissing('anonymous_visitors', [
            'id' => 1,
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
