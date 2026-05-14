<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Layout\Page;
use App\Models\Content\Temple\Temple;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->map(fn (array $item) => [
                'type' => $item['type'],
                'id' => (int) $item['id'],
            ])
            ->unique(fn (array $item) => $item['type'].':'.$item['id'])
            ->values();

        $articleIds = $items->where('type', 'article')->pluck('id')->all();
        $templeIds = $items->where('type', 'temple')->pluck('id')->all();

        $articles = Article::query()
            ->with(['content.mediaUsages.media', 'stat'])
            ->whereIn('id', $articleIds)
            ->get()
            ->filter(fn (Article $article) => $article->content?->status === 'published')
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

        return response()->json([
            'items' => $items
                ->map(fn (array $item) => $lookup->get($item['type'].':'.$item['id']))
                ->filter()
                ->values(),
        ]);
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
