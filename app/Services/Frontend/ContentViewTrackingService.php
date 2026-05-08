<?php

namespace App\Services\Frontend;

use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleStat;
use Illuminate\Support\Facades\DB;

class ContentViewTrackingService
{
    public function trackArticle(Article $article): ArticleStat
    {
        $stat = ArticleStat::query()->firstOrCreate(
            ['article_id' => $article->id],
            [
                'view_count' => 0,
                'like_count' => 0,
                'bookmark_count' => 0,
                'share_count' => 0,
                'updated_at' => now(),
            ]
        );

        if (! $this->shouldCount('article', $article->id)) {
            return $stat->refresh();
        }

        ArticleStat::query()
            ->whereKey($stat->id)
            ->update([
                'view_count' => DB::raw('view_count + 1'),
                'updated_at' => now(),
            ]);

        $this->markCounted('article', $article->id);

        return $stat->refresh();
    }

    public function trackTemple(Temple $temple): TempleStat
    {
        $stat = TempleStat::query()->firstOrCreate(
            ['temple_id' => $temple->id],
            [
                'view_count' => 0,
                'review_count' => 0,
                'average_rating' => 0,
                'favorite_count' => 0,
                'score' => 0,
                'updated_at' => now(),
            ]
        );

        if (! $this->shouldCount('temple', $temple->id)) {
            return $stat->refresh();
        }

        TempleStat::query()
            ->whereKey($stat->id)
            ->update([
                'view_count' => DB::raw('view_count + 1'),
                'updated_at' => now(),
            ]);

        $this->markCounted('temple', $temple->id);

        return $stat->refresh();
    }

    private function shouldCount(string $type, int $id): bool
    {
        return session()->get($this->sessionKey($type, $id)) !== now()->toDateString();
    }

    private function markCounted(string $type, int $id): void
    {
        session()->put($this->sessionKey($type, $id), now()->toDateString());
    }

    private function sessionKey(string $type, int $id): string
    {
        return "frontend_views.{$type}.{$id}";
    }
}
