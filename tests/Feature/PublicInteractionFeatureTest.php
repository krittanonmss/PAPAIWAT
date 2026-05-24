<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminPreference;
use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\InteractionReport;
use App\Models\Interaction\PublicComment;
use App\Models\Interaction\TempleReview;
use App\Services\Interaction\PublicInteractionService;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
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

    public function test_public_write_interaction_routes_are_throttled(): void
    {
        foreach ([
            'interactions.favorite' => 'throttle:60,1',
            'interactions.share' => 'throttle:60,1',
            'temples.reviews.store' => 'throttle:10,1',
            'temples.comments.store' => 'throttle:10,1',
            'reviews.report' => 'throttle:10,1',
            'articles.comments.store' => 'throttle:10,1',
            'comments.report' => 'throttle:10,1',
        ] as $routeName => $middleware) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Route [{$routeName}] is not registered.");
            $this->assertContains($middleware, $route->gatherMiddleware());
        }
    }

    public function test_favorites_page_has_no_default_public_page_without_admin_page(): void
    {
        $this->assertFalse(Schema::hasTable('favorites'));

        $this->get(route('favorites.index'))
            ->assertNotFound();
    }

    public function test_favorites_page_renders_when_admin_creates_favorites_section(): void
    {
        $page = Page::query()->create([
            'title' => 'รายการโปรด',
            'slug' => 'favorites',
            'status' => 'published',
            'published_at' => now(),
        ]);

        PageSection::query()->create([
            'page_id' => $page->id,
            'name' => 'รายการโปรด',
            'section_key' => 'favorites-list',
            'component_key' => 'favorites_list',
            'content' => [
                'title' => 'รายการโปรดของฉัน',
                'subtitle' => 'สร้างจาก admin เท่านั้น',
                'empty_title' => 'ยังไม่มีของที่เซฟ',
                'temple_title' => 'วัดของฉัน',
                'article_title' => 'บทความของฉัน',
                'open_label' => 'ดูรายละเอียด',
                'remove_label' => 'เอาออก',
            ],
            'settings' => [],
            'status' => 'active',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('รายการโปรดของฉัน')
            ->assertSee('สร้างจาก admin เท่านั้น')
            ->assertSee('ยังไม่มีของที่เซฟ')
            ->assertSee('วัดของฉัน')
            ->assertSee('บทความของฉัน')
            ->assertSee('ดูรายละเอียด')
            ->assertSee('เอาออก');
    }

    public function test_favorites_remain_local_pointer_based_without_server_favorites_table(): void
    {
        $this->assertFalse(Schema::hasTable('favorites'));

        $script = file_get_contents(resource_path('views/frontend/templates/sections/favorites_list.blade.php'));

        $this->assertStringContainsString('type: item.type', $script);
        $this->assertStringContainsString('id: Number(item.id)', $script);
        $this->assertStringContainsString('writeFavorites(hydratedItems)', $script);
        $this->assertStringNotContainsString('...(serverItems.get(itemKey(item)) || {})', $script);
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
        Mail::fake();
        $this->enableModerationEmailAlerts();

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
        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'moderation',
            'title' => 'มีรีวิววัดรอตรวจสอบ',
        ]);
        Mail::assertQueuedCount(1);
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
        $this->actingAs(Admin::query()->where('status', 'active')->firstOrFail(), 'admin');

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
        Mail::fake();
        $this->enableModerationEmailAlerts();

        $article = $this->createPublishedArticle();

        $this->post(route('articles.comments.store', $article), [
            'display_name' => 'Reader',
            'body' => 'มีประโยชน์',
        ])->assertRedirect()
            ->assertSessionHas('success', 'ความคิดเห็นของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');

        $comment = PublicComment::query()->firstOrFail();

        $this->assertSame('pending', $comment->status);
        $this->assertNull($comment->approved_at);
        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'moderation',
            'title' => 'มีความคิดเห็นรอตรวจสอบ',
        ]);
        Mail::assertQueuedCount(1);

        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('status', 'active')->firstOrFail(), 'admin');

        $this->patch(route('admin.interactions.comments.reject', $comment))
            ->assertRedirect();

        $this->assertDatabaseHas('public_comments', [
            'id' => $comment->id,
            'status' => 'rejected',
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
        ]);
    }

    public function test_article_with_comments_disabled_hides_comment_area_and_rejects_submission(): void
    {
        $article = $this->createPublishedArticle(allowComments: false);

        $this->get(route('articles.show', $article->content->slug))
            ->assertOk()
            ->assertDontSee('ความคิดเห็น')
            ->assertDontSee('เขียนความคิดเห็น')
            ->assertDontSee('ส่งความคิดเห็น');

        $this->post(route('articles.comments.store', $article), [
            'display_name' => 'Reader',
            'body' => 'ไม่ควรถูกบันทึก',
        ])->assertForbidden();

        $this->assertDatabaseCount('public_comments', 0);
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

    public function test_admin_can_bulk_moderate_reviews_with_reason_and_note(): void
    {
        $temple = $this->createPublishedTemple();
        $visitor = AnonymousVisitor::query()->create([
            'visitor_uuid' => (string) str()->uuid(),
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
        $review = TempleReview::query()->create([
            'temple_id' => $temple->id,
            'anonymous_visitor_id' => $visitor->id,
            'rating' => 1,
            'comment' => 'ไม่เกี่ยวข้อง',
            'status' => 'pending',
        ]);

        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('status', 'active')->firstOrFail(), 'admin');

        $this->patch(route('admin.interactions.reviews.bulk'), [
            'review_ids' => [$review->id],
            'action' => 'reject',
            'moderation_reason' => 'off_topic',
            'moderation_note' => 'ไม่เกี่ยวกับเนื้อหา',
        ])->assertRedirect();

        $this->assertDatabaseHas('temple_reviews', [
            'id' => $review->id,
            'status' => 'rejected',
            'moderation_reason' => 'off_topic',
            'moderation_note' => 'ไม่เกี่ยวกับเนื้อหา',
        ]);
    }

    public function test_admin_detail_pages_show_reports_and_can_ban_visitor(): void
    {
        $article = $this->createPublishedArticle();
        $visitor = AnonymousVisitor::query()->create([
            'visitor_uuid' => (string) str()->uuid(),
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
        $comment = PublicComment::query()->create([
            'anonymous_visitor_id' => $visitor->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'display_name' => 'Reader',
            'body' => 'spam text',
            'status' => 'pending',
            'report_count' => 1,
        ]);
        InteractionReport::query()->create([
            'anonymous_visitor_id' => $visitor->id,
            'reportable_type' => PublicComment::class,
            'reportable_id' => $comment->id,
            'reason' => 'spam',
        ]);

        $this->withoutMiddleware(AdminAuthenticate::class);
        $this->actingAs(Admin::query()->where('status', 'active')->firstOrFail(), 'admin');

        $this->get(route('admin.interactions.comments.show', $comment))
            ->assertOk()
            ->assertSee('spam text')
            ->assertSee('spam');

        $this->patch(route('admin.interactions.comments.ban-visitor', $comment), [
            'reason' => 'spam',
        ])->assertRedirect();

        $this->assertDatabaseHas('anonymous_visitors', [
            'id' => $visitor->id,
            'status' => 'banned',
        ]);
        $this->assertDatabaseHas('interaction_bans', [
            'ban_type' => 'visitor',
            'reason' => 'spam',
        ]);
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

    private function enableModerationEmailAlerts(): void
    {
        $admin = Admin::query()->where('status', 'active')->firstOrFail();

        foreach ([
            'notifications.in_app' => true,
            'notifications.email' => true,
            'notifications.moderation_alerts' => true,
        ] as $key => $value) {
            AdminPreference::query()->updateOrCreate(
                ['admin_id' => $admin->id, 'key' => $key],
                ['value' => ['value' => $value]]
            );
        }
    }

    private function createPublishedArticle(bool $allowComments = true): Article
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
            'allow_comments' => $allowComments,
        ]);
    }
}
