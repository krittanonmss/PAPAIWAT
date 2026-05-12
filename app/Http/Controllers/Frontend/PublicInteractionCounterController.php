<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PublicInteractionCounterController extends Controller
{
    public function favorite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['article', 'temple'])],
            'id' => ['required', 'integer'],
            'action' => ['required', Rule::in(['add', 'remove'])],
        ]);

        $count = $this->updateCounter(
            type: $validated['type'],
            id: (int) $validated['id'],
            metric: 'favorite',
            delta: $validated['action'] === 'add' ? 1 : -1
        );

        return response()->json(['count' => $count]);
    }

    public function share(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['article', 'temple'])],
            'id' => ['required', 'integer'],
        ]);

        $count = $this->updateCounter(
            type: $validated['type'],
            id: (int) $validated['id'],
            metric: 'share',
            delta: 1
        );

        return response()->json(['count' => $count]);
    }

    private function updateCounter(string $type, int $id, string $metric, int $delta): int
    {
        return DB::transaction(function () use ($type, $id, $metric, $delta): int {
            if ($type === 'article') {
                $article = Article::query()->with('content')->findOrFail($id);
                abort_unless($article->content?->status === 'published', 404);

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

                $column = $metric === 'share' ? 'share_count' : 'bookmark_count';

                return $this->applyDelta($stat, $column, $delta);
            }

            $temple = Temple::query()->with('content')->findOrFail($id);
            abort_unless($temple->content?->status === 'published', 404);

            $stat = TempleStat::query()->firstOrCreate(
                ['temple_id' => $temple->id],
                [
                    'view_count' => 0,
                    'review_count' => 0,
                    'average_rating' => 0,
                    'favorite_count' => 0,
                    'share_count' => 0,
                    'score' => 0,
                    'updated_at' => now(),
                ]
            );

            $column = $metric === 'share' ? 'share_count' : 'favorite_count';

            return $this->applyDelta($stat, $column, $delta);
        });
    }

    private function applyDelta(object $stat, string $column, int $delta): int
    {
        $expression = $delta >= 0
            ? "{$column} + {$delta}"
            : "CASE WHEN {$column} > 0 THEN {$column} - 1 ELSE 0 END";

        $stat->newQuery()
            ->whereKey($stat->getKey())
            ->update([
                $column => DB::raw($expression),
                'updated_at' => now(),
            ]);

        return (int) $stat->refresh()->{$column};
    }
}
