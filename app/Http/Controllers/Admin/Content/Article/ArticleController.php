<?php

namespace App\Http\Controllers\Admin\Content\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleRequest;
use App\Http\Requests\Admin\Article\UpdateArticleRequest;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()
            ->with([
                'content',
                'tags',
                'stat',
                'content.categories',
                'content.media',
            ])
            ->whereHas('content', function (Builder $q) use ($request) {
                $q->where('content_type', 'article')
                    ->whereNull('deleted_at');

                if ($request->filled('search')) {
                    $search = $request->string('search')->toString();

                    $q->where(function (Builder $subQuery) use ($search) {
                        $subQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%')
                            ->orWhere('excerpt', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
                }

                if ($request->filled('status')) {
                    $q->where('status', $request->string('status')->toString());
                }

                if ($request->filled('is_featured')) {
                    $q->where('is_featured', (bool) $request->boolean('is_featured'));
                }

                if ($request->filled('is_popular')) {
                    $q->where('is_popular', (bool) $request->boolean('is_popular'));
                }
            });

        if ($request->filled('author_name')) {
            $query->where('author_name', 'like', '%' . $request->string('author_name')->toString() . '%');
        }

        if ($request->filled('body_format')) {
            $query->where('body_format', $request->string('body_format')->toString());
        }

        if ($request->filled('allow_comments')) {
            $query->where('allow_comments', (bool) $request->boolean('allow_comments'));
        }

        if ($request->filled('show_on_homepage')) {
            $query->where('show_on_homepage', (bool) $request->boolean('show_on_homepage'));
        }

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            $query->whereHas('content.categories', function (Builder $q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        if ($request->filled('tag_id')) {
            $tagId = (int) $request->input('tag_id');

            $query->whereHas('tags', function (Builder $q) use ($tagId) {
                $q->where('article_tags.id', $tagId);
            });
        }

        $articles = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->where('type_key', 'article')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tags = \App\Models\Content\Article\ArticleTag::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.content.articles.index', compact('articles', 'categories', 'tags'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('type_key', 'article')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tags = \App\Models\Content\Article\ArticleTag::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $mediaItems = Media::query()
            ->where('media_type', 'image')
            ->where('upload_status', 'completed')
            ->whereNull('deleted_at')
            ->latest('id')
            ->get();

        $relatedArticles = Article::query()
            ->with('content')
            ->whereHas('content', function (Builder $q) {
                $q->where('content_type', 'article')
                    ->whereNull('deleted_at');
            })
            ->latest('id')
            ->get();

        return view('admin.content.articles.create', compact(
            'categories',
            'tags',
            'mediaItems',
            'relatedArticles'
        ) + [
            'detailTemplates' => $this->detailTemplates('article'),
            'templatePreviewUrl' => route('admin.content.template-preview.sample', ['type' => 'article']),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => $validated['title'],
                'slug' => $this->generateUniqueSlug($validated['slug'] ?? $validated['title']),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $validated['excerpt'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => $validated['is_featured'] ?? false,
                'is_popular' => $validated['is_popular'] ?? false,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'created_by_admin_id' => auth('admin')->id(),
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $article = Article::query()->create([
                'content_id' => $content->id,
                'title_en' => $validated['title_en'] ?? null,
                'excerpt_en' => $validated['excerpt_en'] ?? null,
                'body' => $validated['body'] ?? null,
                'body_format' => $validated['body_format'],
                'author_name' => $validated['author_name'] ?? null,
                'reading_time_minutes' => $validated['reading_time_minutes'] ?? null,
                'seo_keywords' => $validated['seo_keywords'] ?? null,
                'allow_comments' => $validated['allow_comments'] ?? true,
                'show_on_homepage' => $validated['show_on_homepage'] ?? false,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'expired_at' => $validated['expired_at'] ?? null,
            ]);

            ArticleStat::query()->create([
                'article_id' => $article->id,
                'view_count' => 0,
                'like_count' => 0,
                'bookmark_count' => 0,
                'share_count' => 0,
                'updated_at' => now(),
            ]);

            $this->syncCategories($content, $validated['category_ids'] ?? []);
            $this->syncTags($article, $validated['tag_ids'] ?? []);
            $this->syncCoverMedia($content, $validated['cover_media_id'] ?? null);
            $this->syncRelatedArticles($article, $validated['related_article_ids'] ?? []);
        });

        return redirect()
            ->route('admin.content.articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function show(Article $article): View
    {
        $article->load([
            'content',
            'tags',
            'stat',
            'relatedArticles.content',
            'content.categories',
            'content.media',
            'content.mediaUsages.media',
        ]);

        return view('admin.content.articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        $article->load([
            'content',
            'tags',
            'relatedArticles',
            'content.categories',
            'content.mediaUsages',
        ]);

        $categories = Category::query()
            ->where('type_key', 'article')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tags = \App\Models\Content\Article\ArticleTag::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $mediaItems = Media::query()
            ->where('media_type', 'image')
            ->where('upload_status', 'completed')
            ->whereNull('deleted_at')
            ->latest('id')
            ->get();

        $relatedArticles = Article::query()
            ->with('content')
            ->where('id', '!=', $article->id)
            ->whereHas('content', function (Builder $q) {
                $q->where('content_type', 'article')
                    ->whereNull('deleted_at');
            })
            ->latest('id')
            ->get();

        return view('admin.content.articles.edit', compact(
            'article',
            'categories',
            'tags',
            'mediaItems',
            'relatedArticles'
        ) + [
            'detailTemplates' => $this->detailTemplates('article'),
            'templatePreviewUrl' => $article->content
                ? route('admin.content.template-preview', ['type' => 'article', 'content' => $article->content])
                : route('admin.content.template-preview.sample', ['type' => 'article']),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();

        $article->load('content', 'stat');

        DB::transaction(function () use ($validated, $article) {
            $article->content->update([
                'title' => $validated['title'],
                'slug' => $this->generateUniqueSlug(
                    $validated['slug'] ?? $validated['title'],
                    $article->content->id
                ),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $validated['excerpt'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => $validated['is_featured'] ?? false,
                'is_popular' => $validated['is_popular'] ?? false,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $article->update([
                'title_en' => $validated['title_en'] ?? null,
                'excerpt_en' => $validated['excerpt_en'] ?? null,
                'body' => $validated['body'] ?? null,
                'body_format' => $validated['body_format'],
                'author_name' => $validated['author_name'] ?? null,
                'reading_time_minutes' => $validated['reading_time_minutes'] ?? null,
                'seo_keywords' => $validated['seo_keywords'] ?? null,
                'allow_comments' => $validated['allow_comments'] ?? true,
                'show_on_homepage' => $validated['show_on_homepage'] ?? false,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'expired_at' => $validated['expired_at'] ?? null,
            ]);

            if ($article->stat) {
                $article->stat->update([
                    'updated_at' => now(),
                ]);
            } else {
                ArticleStat::query()->create([
                    'article_id' => $article->id,
                    'view_count' => 0,
                    'like_count' => 0,
                    'bookmark_count' => 0,
                    'share_count' => 0,
                    'updated_at' => now(),
                ]);
            }

            $this->syncCategories($article->content, $validated['category_ids'] ?? []);
            $this->syncTags($article, $validated['tag_ids'] ?? []);
            $this->syncCoverMedia($article->content, $validated['cover_media_id'] ?? null);
            $this->syncRelatedArticles($article, $validated['related_article_ids'] ?? []);
        });

        return redirect()
            ->route('admin.content.articles.edit', $article)
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->load('content');

        DB::transaction(function () use ($article) {
            $article->tags()->detach();
            $article->relatedArticles()->detach();
            $article->content->categories()->detach();
            $article->content->media()->detach();

            $article->content->update([
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $article->content->delete();
        });

        return redirect()
            ->route('admin.content.articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    private function syncCategories(Content $content, array $categoryIds): void
    {
        $syncData = [];

        foreach (array_values($categoryIds) as $index => $categoryId) {
            $syncData[$categoryId] = [
                'is_primary' => $index === 0,
                'sort_order' => $index,
                'created_at' => now(),
            ];
        }

        $content->categories()->sync($syncData);
    }

    private function syncTags(Article $article, array $tagIds): void
    {
        $syncData = [];

        foreach ($tagIds as $tagId) {
            $syncData[$tagId] = [
                'created_at' => now(),
            ];
        }

        $article->tags()->sync($syncData);
    }

    private function syncCoverMedia(Content $content, int|string|null $coverMediaId): void
    {
        $content->mediaUsages()
            ->where('role_key', 'cover')
            ->delete();

        if (!$coverMediaId) {
            return;
        }

        $content->mediaUsages()->create([
            'media_id' => (int) $coverMediaId,
            'entity_type' => Content::class,
            'entity_id' => $content->id,
            'role_key' => 'cover',
            'sort_order' => 0,
            'created_by_admin_id' => auth('admin')->id(),
        ]);
    }

    private function syncRelatedArticles(Article $article, array $relatedArticleIds): void
    {
        $syncData = [];

        foreach (array_values($relatedArticleIds) as $index => $relatedArticleId) {
            if ((int) $relatedArticleId === (int) $article->id) {
                continue;
            }

            $syncData[$relatedArticleId] = [
                'sort_order' => $index,
                'created_at' => now(),
            ];
        }

        $article->relatedArticles()->sync($syncData);
    }

    private function generateUniqueSlug(string $value, ?int $ignoreContentId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'article';
        $counter = 1;

        while (
            Content::query()
                ->where('content_type', 'article')
                ->where('slug', $slug)
                ->when($ignoreContentId, function (Builder $query) use ($ignoreContentId) {
                    $query->where('id', '!=', $ignoreContentId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function detailTemplates(string $contentType)
    {
        return Template::query()
            ->active()
            ->where('view_path', 'like', 'frontend.templates.details.%')
            ->where(function (Builder $query) use ($contentType) {
                $query->where('key', $contentType . '-detail')
                    ->orWhere('view_path', 'like', 'frontend.templates.details.' . $contentType . '-%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
