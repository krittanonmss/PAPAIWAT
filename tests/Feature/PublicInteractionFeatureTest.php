<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\PublicComment;
use App\Models\Interaction\TempleReview;
use App\Services\Interaction\PublicInteractionService;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class PublicInteractionFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
    }

    public function test_favorites_page_is_local_only_and_server_favorites_table_is_not_used(): void
    {
        $this->assertFalse(Schema::hasTable('favorites'));

        $this->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('รายการโปรดของฉัน')
            ->assertSee('โดยไม่เก็บว่าใครเป็นคนบันทึก');
    }

    public function test_temple_detail_renders_parseable_favorite_payload_and_count(): void
    {
        $temple = $this->createPublishedTemple();

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('data-local-favorite-toggle', false)
            ->assertSee('data-favorite=\'{"type":"temple","id":'.$temple->id, false)
            ->assertSee('data-favorite-count="temple:'.$temple->id.'"', false)
            ->assertSee('คนกด')
            ->assertDontSee('&quot;type&quot;', false)
            ->assertDontSee('&amp;quot;type&amp;quot;', false);
    }

    public function test_temple_review_waits_for_admin_approval_before_updating_rating(): void
    {
        $temple = $this->createPublishedTemple();

        $this->post(route('temples.reviews.store', $temple), [
            'rating' => 5,
            'display_name' => 'Visitor',
            'comment' => 'ดีมาก',
        ])->assertRedirect()
            ->assertSessionHas('success', 'รีวิวของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');

        $review = TempleReview::query()->firstOrFail();

        $this->assertSame('pending', $review->status);
        $this->assertNull($review->approved_at);
        $this->assertDatabaseHas('temple_stats', [
            'temple_id' => $temple->id,
            'review_count' => 0,
            'average_rating' => 0,
        ]);
    }

    public function test_same_visitor_can_update_existing_temple_review(): void
    {
        $temple = $this->createPublishedTemple();

        $this->post(route('temples.reviews.store', $temple), [
            'rating' => 4,
            'display_name' => 'Visitor',
            'comment' => 'ดี',
        ])->assertRedirect();

        $this->post(route('temples.reviews.store', $temple), [
            'rating' => 5,
            'display_name' => 'Visitor',
            'comment' => 'ดีมาก',
        ])->assertRedirect();

        $this->assertSame(1, TempleReview::query()->where('temple_id', $temple->id)->count());
        $this->assertDatabaseHas('temple_reviews', [
            'temple_id' => $temple->id,
            'rating' => 5,
            'comment' => 'ดีมาก',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('temple_stats', [
            'temple_id' => $temple->id,
            'review_count' => 0,
            'average_rating' => 0,
        ]);
    }

    public function test_review_goes_pending_until_admin_approves_it(): void
    {
        $temple = $this->createPublishedTemple();

        $this->post(route('temples.reviews.store', $temple), [
            'rating' => 4,
            'display_name' => 'Visitor',
            'comment' => 'ดูเพิ่มที่ https://spam.example',
        ])->assertRedirect();

        $review = TempleReview::query()->firstOrFail();

        $this->assertSame('pending', $review->status);
        $this->assertNull($review->approved_at);

        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('email', 'admin@example.com')->firstOrFail(), 'admin');

        $this->patch(route('admin.interactions.reviews.approve', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('temple_reviews', [
            'id' => $review->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('temple_stats', [
            'temple_id' => $temple->id,
            'review_count' => 1,
            'average_rating' => 4,
        ]);
    }

    public function test_public_comment_waits_for_admin_approval_and_admin_can_reject_it(): void
    {
        $article = $this->createPublishedArticle();

        $this->post(route('articles.comments.store', $article), [
            'display_name' => 'Reader',
            'body' => 'มีประโยชน์',
        ])->assertRedirect()
            ->assertSessionHas('success', 'ความคิดเห็นของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');

        $comment = PublicComment::query()->firstOrFail();

        $this->assertSame('pending', $comment->status);
        $this->assertNull($comment->approved_at);

        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('email', 'admin@example.com')->firstOrFail(), 'admin');

        $this->patch(route('admin.interactions.comments.reject', $comment))
            ->assertRedirect();

        $this->assertDatabaseHas('public_comments', [
            'id' => $comment->id,
            'status' => 'rejected',
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
        ]);
    }

    public function test_comment_goes_pending_and_reports_can_auto_hide_content(): void
    {
        $article = $this->createPublishedArticle();

        $this->post(route('articles.comments.store', $article), [
            'display_name' => 'Reader',
            'body' => 'ดูเพิ่มที่ https://spam.example',
        ])->assertRedirect();

        $comment = PublicComment::query()->firstOrFail();

        $this->assertSame('pending', $comment->status);

        $interactionService = app(PublicInteractionService::class);

        foreach (range(1, 3) as $index) {
            $visitor = AnonymousVisitor::query()->create([
                'visitor_uuid' => (string) str()->uuid(),
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);

            $interactionService->report($comment->refresh(), $visitor, 'spam '.$index, null, null);
        }

        $this->assertDatabaseHas('public_comments', [
            'id' => $comment->id,
            'status' => 'rejected',
            'report_count' => 3,
        ]);
    }

    public function test_detail_pages_show_pending_comment_only_to_same_visitor_and_approved_comments_publicly(): void
    {
        $article = $this->createPublishedArticle();

        $this->post(route('articles.comments.store', $article), [
            'display_name' => 'Reader',
            'body' => 'รอตรวจข้อความนี้',
        ])->assertRedirect();

        $this->get(route('articles.show', $article->content->slug))
            ->assertOk()
            ->assertSee('รอตรวจข้อความนี้');

        $visitorId = PublicComment::query()
            ->where('body', 'รอตรวจข้อความนี้')
            ->value('anonymous_visitor_id');

        PublicComment::query()->create([
            'anonymous_visitor_id' => $visitorId,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'display_name' => 'Approved Reader',
            'body' => 'อนุมัติแล้ว',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->get(route('articles.show', $article->content->slug))
            ->assertOk()
            ->assertSee('อนุมัติแล้ว');
    }

    private function createPublishedTemple(): Temple
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Public Temple',
            'slug' => 'public-temple',
            'status' => 'published',
            'published_at' => now(),
        ]);

        return Temple::query()->create([
            'content_id' => $content->id,
        ]);
    }

    private function createPublishedArticle(): Article
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Public Article',
            'slug' => 'public-article',
            'status' => 'published',
            'published_at' => now(),
        ]);

        return Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'markdown',
        ]);
    }
}
