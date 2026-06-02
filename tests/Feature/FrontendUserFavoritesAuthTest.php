<?php

namespace Tests\Feature;

use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\User;
use App\Models\UserFavorite;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class FrontendUserFavoritesAuthTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
    }

    public function test_user_registration_requires_email_verification(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Favorite Reader',
            'email' => 'reader@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect(route('favorites.index'))
            ->assertSessionHas('success');

        $user = User::query()->where('email', 'reader@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_sync_favorites(): void
    {
        $article = $this->createPublishedArticle();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.sync'), [
                'items' => [
                    ['type' => 'article', 'id' => $article->id],
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('user_favorites', 0);
    }

    public function test_email_verification_returns_to_custom_favorites_page_path(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice', ['redirect' => '/fav']))
            ->assertOk();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect('/fav')
            ->assertSessionHas('success', 'ยืนยันอีเมลเรียบร้อยแล้ว');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verified_user_can_sync_favorites_across_devices_and_replace_removed_items(): void
    {
        $article = $this->createPublishedArticle();
        $temple = $this->createPublishedTemple();
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('favorites.sync'), [
                'mode' => 'merge',
                'items' => [
                    ['type' => 'article', 'id' => $article->id, 'addedAt' => now()->subMinute()->toISOString()],
                    ['type' => 'temple', 'id' => $temple->id, 'addedAt' => now()->toISOString()],
                ],
            ])
            ->assertOk()
            ->assertJsonCount(2, 'items');

        $this->assertDatabaseHas('user_favorites', [
            'user_id' => $user->id,
            'favoritable_type' => 'article',
            'favoritable_id' => $article->id,
        ]);
        $this->assertDatabaseHas('user_favorites', [
            'user_id' => $user->id,
            'favoritable_type' => 'temple',
            'favoritable_id' => $temple->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('favorites.sync'), [
                'mode' => 'merge',
                'items' => [],
            ])
            ->assertOk()
            ->assertJsonCount(2, 'items')
            ->assertJsonFragment(['type' => 'article', 'id' => $article->id])
            ->assertJsonFragment(['type' => 'temple', 'id' => $temple->id]);

        $this->actingAs($user)
            ->postJson(route('favorites.sync'), [
                'mode' => 'replace',
                'items' => [
                    ['type' => 'temple', 'id' => $temple->id],
                ],
            ])
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['type' => 'temple', 'id' => $temple->id]);

        $this->assertDatabaseMissing('user_favorites', [
            'user_id' => $user->id,
            'favoritable_type' => 'article',
            'favoritable_id' => $article->id,
        ]);
        $this->assertSame(1, UserFavorite::query()->where('user_id', $user->id)->count());
    }

    private function createPublishedArticle(): Article
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Favorite Article',
            'slug' => 'favorite-article',
            'status' => 'published',
            'published_at' => now(),
        ]);

        return Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'html',
        ]);
    }

    private function createPublishedTemple(): Temple
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Favorite Temple',
            'slug' => 'favorite-temple',
            'status' => 'published',
            'published_at' => now(),
        ]);

        return Temple::query()->create([
            'content_id' => $content->id,
        ]);
    }
}
