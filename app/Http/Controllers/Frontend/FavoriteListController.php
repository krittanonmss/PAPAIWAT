<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Layout\Page;
use App\Models\Content\Temple\Temple;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FavoriteListController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query->visible()->orderBy('sort_order'),
            ])
            ->where('slug', 'favorites')
            ->published()
            ->firstOrFail();

        $sections = app(FrontendPageController::class)->buildPageSections($page);

        return view('frontend.favorites.index', compact('page', 'sections'));
    }

    public function items(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array', 'max:100'],
            'items.*.type' => ['required', Rule::in(['article', 'temple'])],
            'items.*.id' => ['required', 'integer'],
        ]);

        $items = collect($validated['items'] ?? [])
            ->map(fn (array $item) => $this->normalizeFavoriteItem($item))
            ->filter()
            ->unique(fn (array $item) => $item['type'].':'.$item['id'])
            ->values();

        return response()->json([
            'items' => $this->hydrateItems($items),
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['nullable', Rule::in(['merge', 'replace', 'pull'])],
            'items' => ['nullable', 'array', 'max:100'],
            'items.*.type' => ['required', Rule::in(['article', 'temple'])],
            'items.*.id' => ['required', 'integer'],
            'items.*.addedAt' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        abort_unless($user && $user->hasVerifiedEmail(), 403);

        if (($validated['mode'] ?? 'merge') === 'pull') {
            return response()->json([
                'items' => $this->hydrateItems($this->storedFavoriteItems($user->id)),
            ]);
        }

        $items = collect($validated['items'] ?? [])
            ->map(fn (array $item) => $this->normalizeFavoriteItem($item))
            ->filter()
            ->unique(fn (array $item) => $item['type'].':'.$item['id'])
            ->values();

        $validItems = collect($this->hydrateItems($items))
            ->map(fn (array $item) => [
                'type' => $item['type'],
                'id' => (int) $item['id'],
                'addedAt' => $items->first(fn (array $stored) => $stored['type'] === $item['type'] && $stored['id'] === (int) $item['id'])['addedAt'] ?? now()->toISOString(),
            ]);

        if (($validated['mode'] ?? 'merge') === 'replace') {
            $keys = $validItems->map(fn (array $item) => $item['type'].':'.$item['id'])->all();

            UserFavorite::query()
                ->where('user_id', $user->id)
                ->get()
                ->reject(fn (UserFavorite $favorite) => in_array($favorite->favoritable_type.':'.$favorite->favoritable_id, $keys, true))
                ->each->delete();
        }

        foreach ($validItems as $item) {
            UserFavorite::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'favoritable_type' => $item['type'],
                    'favoritable_id' => $item['id'],
                ],
                [
                    'added_at' => $item['addedAt'] ? Carbon::parse($item['addedAt']) : now(),
                ]
            );
        }

        return response()->json([
            'items' => $this->hydrateItems($this->storedFavoriteItems($user->id)),
        ]);
    }

    private function storedFavoriteItems(int $userId): \Illuminate\Support\Collection
    {
        return UserFavorite::query()
            ->where('user_id', $userId)
            ->latest('added_at')
            ->limit(100)
            ->get()
            ->map(fn (UserFavorite $favorite) => [
                'type' => $favorite->favoritable_type,
                'id' => $favorite->favoritable_id,
                'addedAt' => $favorite->added_at?->toISOString(),
            ])
            ->values();
    }

    private function hydrateItems(mixed $items): array
    {
        $items = collect($items);
        $articleIds = $items->where('type', 'article')->pluck('id')->all();
        $templeIds = $items->where('type', 'temple')->pluck('id')->all();

        $articles = Article::query()
            ->with(['content.mediaUsages.media', 'stat'])
            ->whereIn('id', $articleIds)
            ->get()
            ->filter(fn (Article $article) => $article->content?->status === 'published')
            ->toBase()
            ->mapWithKeys(fn (Article $article) => [
                'article:'.$article->id => [
                    'type' => 'article',
                    'id' => $article->id,
                    'title' => $article->content?->title,
                    'url' => route('articles.show', $article->content?->slug),
                    'excerpt' => $article->content?->excerpt ?? $article->excerpt_en,
                    'image' => $this->coverUrl($article->content?->mediaUsages),
                    'count' => (int) ($article->stat?->bookmark_count ?? 0),
                ],
            ]);

        $temples = Temple::query()
            ->with(['content.mediaUsages.media', 'stat'])
            ->whereIn('id', $templeIds)
            ->get()
            ->filter(fn (Temple $temple) => $temple->content?->status === 'published')
            ->toBase()
            ->mapWithKeys(fn (Temple $temple) => [
                'temple:'.$temple->id => [
                    'type' => 'temple',
                    'id' => $temple->id,
                    'title' => $temple->content?->title,
                    'url' => route('temples.show', $temple),
                    'excerpt' => $temple->content?->excerpt
                        ?: Str::limit(trim(strip_tags((string) $temple->content?->description)), 140),
                    'image' => $this->coverUrl($temple->content?->mediaUsages),
                    'count' => (int) ($temple->stat?->favorite_count ?? 0),
                ],
            ]);

        $lookup = $articles->merge($temples);

        return $items
            ->map(function (array $item) use ($lookup) {
                $hydrated = $lookup->get($item['type'].':'.$item['id']);

                if (! $hydrated) {
                    return null;
                }

                return [
                    ...$hydrated,
                    'addedAt' => $item['addedAt'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeFavoriteItem(array $item): ?array
    {
        if (! in_array($item['type'] ?? null, ['article', 'temple'], true) || empty($item['id'])) {
            return null;
        }

        return [
            'type' => $item['type'],
            'id' => (int) $item['id'],
            'addedAt' => $item['addedAt'] ?? null,
        ];
    }

    private function coverUrl(mixed $mediaUsages): ?string
    {
        $coverPath = $mediaUsages?->firstWhere('role_key', 'cover')?->media?->path;

        if (! $coverPath) {
            return null;
        }

        return filter_var($coverPath, FILTER_VALIDATE_URL)
            ? $coverPath
            : Storage::url($coverPath);
    }
}
