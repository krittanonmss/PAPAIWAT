<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleTag;
use App\Models\Content\Category;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaFolder;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentLookupController extends Controller
{
    public function categories(Request $request): JsonResponse
    {
        $type = $request->string('type')->toString();
        $excludeIds = $this->excludedCategoryIds($request);

        $items = Category::query()
            ->when($type !== '', fn (Builder $query) => $query->where('type_key', $type))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->when($excludeIds !== [], fn (Builder $query) => $query->whereNotIn('id', $excludeIds))
            ->when($request->filled('max_level'), fn (Builder $query) => $query->where('level', '<=', $request->integer('max_level')))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = $request->string('q')->toString();

                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                        ->orWhere('slug', 'like', '%' . $term . '%')
                        ->orWhere('id', $term);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($this->limit($request))
            ->get(['id', 'name', 'slug', 'type_key', 'level'])
            ->map(fn (Category $category) => [
                'id' => (string) $category->id,
                'label' => $category->name,
                'meta' => trim($category->type_key . ' | Level ' . $category->level . ' | #' . $category->id),
            ]);

        return response()->json(['items' => $items]);
    }

    public function articleTags(Request $request): JsonResponse
    {
        $items = ArticleTag::query()
            ->where('status', 'active')
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = $request->string('q')->toString();

                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                        ->orWhere('slug', 'like', '%' . $term . '%')
                        ->orWhere('id', $term);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($this->limit($request))
            ->get(['id', 'name', 'slug'])
            ->map(fn (ArticleTag $tag) => [
                'id' => (string) $tag->id,
                'label' => $tag->name,
                'meta' => $tag->slug,
            ]);

        return response()->json(['items' => $items]);
    }

    public function articles(Request $request): JsonResponse
    {
        $items = Article::query()
            ->with('content:id,title,slug,content_type,deleted_at')
            ->when($request->filled('exclude_id'), fn (Builder $query) => $query->where('id', '!=', $request->integer('exclude_id')))
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->whereHas('content', function (Builder $query) use ($request) {
                $query->where('content_type', 'article')
                    ->whereNull('deleted_at')
                    ->when($request->filled('q'), function (Builder $subQuery) use ($request) {
                        $term = $request->string('q')->toString();

                        $subQuery->where(function (Builder $nested) use ($term) {
                            $nested->where('title', 'like', '%' . $term . '%')
                                ->orWhere('slug', 'like', '%' . $term . '%')
                                ->orWhere('contents.id', $term);
                        });
                    });
            })
            ->latest('id')
            ->limit($this->limit($request))
            ->get()
            ->map(fn (Article $article) => [
                'id' => (string) $article->id,
                'label' => $article->content?->title ?? 'Article #' . $article->id,
                'meta' => '#' . $article->id . ' | ' . ($article->content?->slug ?? '-'),
            ]);

        return response()->json(['items' => $items]);
    }

    public function temples(Request $request): JsonResponse
    {
        $items = Temple::query()
            ->with('content:id,title,slug,content_type,deleted_at')
            ->when($request->filled('exclude_id'), fn (Builder $query) => $query->where('id', '!=', $request->integer('exclude_id')))
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->whereHas('content', function (Builder $query) use ($request) {
                $query->where('content_type', 'temple')
                    ->whereNull('deleted_at')
                    ->when($request->filled('q'), function (Builder $subQuery) use ($request) {
                        $term = $request->string('q')->toString();

                        $subQuery->where(function (Builder $nested) use ($term) {
                            $nested->where('title', 'like', '%' . $term . '%')
                                ->orWhere('slug', 'like', '%' . $term . '%')
                                ->orWhere('contents.id', $term);
                        });
                    });
            })
            ->latest('id')
            ->limit($this->limit($request))
            ->get()
            ->map(fn (Temple $temple) => [
                'id' => (string) $temple->id,
                'label' => $temple->content?->title ?? 'Temple #' . $temple->id,
                'meta' => '#' . $temple->id . ' | ' . ($temple->content?->slug ?? '-'),
            ]);

        return response()->json(['items' => $items]);
    }

    public function facilities(Request $request): JsonResponse
    {
        $items = Facility::query()
            ->where('status', 'active')
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = $request->string('q')->toString();

                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                        ->orWhere('id', $term);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($this->limit($request))
            ->get(['id', 'name'])
            ->map(fn (Facility $facility) => [
                'id' => (string) $facility->id,
                'label' => $facility->name,
                'meta' => 'Facility #' . $facility->id,
            ]);

        return response()->json(['items' => $items]);
    }

    public function mediaFolders(Request $request): JsonResponse
    {
        $items = MediaFolder::query()
            ->where('status', 'active')
            ->when($request->filled('ids'), fn (Builder $query) => $query->whereIn('id', $this->ids($request)))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = $request->string('q')->toString();

                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                        ->orWhere('slug', 'like', '%' . $term . '%')
                        ->orWhere('id', $term);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($this->limit($request))
            ->get(['id', 'name', 'slug'])
            ->map(fn (MediaFolder $folder) => [
                'id' => (string) $folder->id,
                'label' => $folder->name,
                'meta' => trim(($folder->slug ?: 'folder') . ' #' . $folder->id),
            ]);

        return response()->json(['items' => $items]);
    }

    private function ids(Request $request): array
    {
        return collect($request->input('ids', []))
            ->filter(fn ($id) => is_scalar($id) && preg_match('/^\d+$/', (string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function limit(Request $request): int
    {
        return min(max($request->integer('limit', 30), 1), 80);
    }

    private function excludedCategoryIds(Request $request): array
    {
        $categoryId = $request->integer('exclude_id') ?: null;

        if (! $categoryId) {
            return [];
        }

        $category = Category::query()->find($categoryId);

        if (! $category) {
            return [$categoryId];
        }

        return collect([$category->id])
            ->merge($this->categoryDescendantIds($category))
            ->unique()
            ->values()
            ->all();
    }

    private function categoryDescendantIds(Category $category)
    {
        $ids = collect();

        foreach ($category->children()->get(['id']) as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->categoryDescendantIds($child));
        }

        return $ids;
    }
}
