<?php

namespace App\Http\Controllers\Admin\Content\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleRequest;
use App\Http\Requests\Admin\Article\UpdateArticleRequest;
use App\Models\Admin\AuditLog;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\ContentVersion;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Services\Admin\Content\Article\ArticleValidationService;
use App\Support\SafeRichText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    private ArticleValidationService $articleValidationService;

    public function __construct(?ArticleValidationService $articleValidationService = null)
    {
        $this->articleValidationService = $articleValidationService ?? new ArticleValidationService;
    }

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
            ->paginate(5)
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
        $categories = collect();
        $tags = collect();
        $mediaItems = collect();
        $relatedArticles = collect();

        return view('admin.content.articles.create', compact(
            'categories',
            'tags',
            'mediaItems',
            'relatedArticles'
        ) + [
            'coverMediaItems' => $this->coverMediaItems(),
            'detailTemplates' => $this->detailTemplates('article'),
            'templatePreviewUrl' => route('admin.content.template-preview.sample', ['type' => 'article']),
            'templatePreviewLiveUrl' => route('admin.content.template-preview.live', ['type' => 'article']),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->articleValidationService->validateForSave($validated);

        $article = DB::transaction(function () use ($validated) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => $validated['title'],
                'slug' => $this->generateUniqueSlug($validated['slug'] ?? $validated['title']),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $this->sanitizeRichText($validated['excerpt'] ?? null),
                'description' => $this->sanitizeRichText($validated['description'] ?? null),
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
                'excerpt_en' => $this->sanitizeRichText($validated['excerpt_en'] ?? null),
                'body' => $this->sanitizeRichText($validated['body'] ?? null),
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

            $this->createVersion($article, 'created');

            return $article;
        });

        $article->load('content.mediaUsages');
        $this->writeAuditLog($request, 'article.created', $article, null, $this->articleAuditData($article));

        return redirect()
            ->route('admin.content.articles.index')
            ->with('success', 'สร้างบทความเรียบร้อยแล้ว');
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

        $categories = $article->content?->categories ?? collect();
        $tags = $article->tags ?? collect();
        $mediaItems = collect();
        $relatedArticles = $article->relatedArticles ?? collect();

        return view('admin.content.articles.edit', compact(
            'article',
            'categories',
            'tags',
            'mediaItems',
            'relatedArticles'
        ) + [
            'coverMediaItems' => $this->coverMediaItems([
                $article->content?->mediaUsages?->firstWhere('role_key', 'cover')?->media_id,
            ]),
            'detailTemplates' => $this->detailTemplates('article'),
            'templatePreviewUrl' => $article->content
                ? route('admin.content.template-preview', ['type' => 'article', 'content' => $article->content])
                : route('admin.content.template-preview.sample', ['type' => 'article']),
            'templatePreviewLiveUrl' => route('admin.content.template-preview.live', ['type' => 'article']),
        ]);
    }

    public function coverMediaPicker(Request $request): View
    {
        return view('admin.content.articles.partials._cover_media_grid', [
            'mediaItems' => $this->coverMediaItems(search: $request->string('q')->toString()),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();

        $article->load('content.mediaUsages', 'stat');
        $oldData = $this->articleAuditData($article);

        $this->articleValidationService->validateForSave($validated, $article);

        DB::transaction(function () use ($validated, $article) {
            $article->content->update([
                'title' => $validated['title'],
                'slug' => $this->generateUniqueSlug(
                    $validated['slug'] ?? $validated['title'],
                    $article->content->id
                ),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $this->sanitizeRichText($validated['excerpt'] ?? null),
                'description' => $this->sanitizeRichText($validated['description'] ?? null),
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
                'excerpt_en' => $this->sanitizeRichText($validated['excerpt_en'] ?? null),
                'body' => $this->sanitizeRichText($validated['body'] ?? null),
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

            $this->createVersion($article, 'updated');
        });

        $article->refresh()->load('content.mediaUsages');
        $newData = $this->articleAuditData($article);

        $this->writeAuditLog($request, 'article.updated', $article, $oldData, $newData);

        if (($oldData['status'] ?? null) !== ($newData['status'] ?? null)) {
            $this->writeAuditLog($request, 'article.status_changed', $article, ['status' => $oldData['status']], ['status' => $newData['status']]);
        }

        if (($oldData['template_id'] ?? null) !== ($newData['template_id'] ?? null)) {
            $this->writeAuditLog($request, 'article.template_changed', $article, ['template_id' => $oldData['template_id']], ['template_id' => $newData['template_id']]);
        }

        if (($oldData['cover_media_id'] ?? null) !== ($newData['cover_media_id'] ?? null)) {
            $this->writeAuditLog($request, 'article.media_changed', $article, ['cover_media_id' => $oldData['cover_media_id']], ['cover_media_id' => $newData['cover_media_id']]);
        }

        return redirect()
            ->route('admin.content.articles.edit', $article)
            ->with('success', 'อัปเดตบทความเรียบร้อยแล้ว');
    }

    public function publish(Request $request, Article $article): RedirectResponse
    {
        $article->load('content.mediaUsages', 'content.categories');
        $oldData = $this->articleAuditData($article);

        $this->articleValidationService->validateForPublish($article);

        $article->content->forceFill([
            'status' => 'published',
            'published_at' => $article->content->published_at ?? now(),
            'updated_by_admin_id' => auth('admin')->id(),
        ])->save();

        $this->createVersion($article, 'published');

        $article->refresh()->load('content.mediaUsages');
        $this->writeAuditLog($request, 'article.published', $article, $oldData, $this->articleAuditData($article));

        return redirect()
            ->route('admin.content.articles.edit', $article)
            ->with('success', 'เผยแพร่บทความเรียบร้อยแล้ว');
    }

    public function unpublish(Request $request, Article $article): RedirectResponse
    {
        $article->load('content.mediaUsages');
        $oldData = $this->articleAuditData($article);

        $article->content->forceFill([
            'status' => 'review',
            'updated_by_admin_id' => auth('admin')->id(),
        ])->save();

        $this->createVersion($article, 'unpublished');

        $article->refresh()->load('content.mediaUsages');
        $this->writeAuditLog($request, 'article.unpublished', $article, $oldData, $this->articleAuditData($article));

        return redirect()
            ->route('admin.content.articles.edit', $article)
            ->with('success', 'ยกเลิกการเผยแพร่บทความเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Article $article): RedirectResponse
    {
        $article->load('content.mediaUsages');
        $oldData = $this->articleAuditData($article);

        DB::transaction(function () use ($article) {
            $this->createVersion($article, 'deleted');

            $article->tags()->detach();
            $article->relatedArticles()->detach();
            $article->content->categories()->detach();
            $article->content->media()->detach();

            $article->content->update([
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $article->content->delete();
        });

        $this->writeAuditLog($request, 'article.deleted', $article, $oldData, ['deleted' => true]);

        return redirect()
            ->route('admin.content.articles.index')
            ->with('success', 'ลบบทความเรียบร้อยแล้ว');
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
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'article';
        $slug = $baseSlug;
        $counter = 1;

        while (
            Content::query()
                ->withTrashed()
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
            ->where('template_type', 'detail')
            ->where('content_type', $contentType)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function coverMediaItems(array $selectedMediaIds = [], string $search = '')
    {
        $mediaItems = Media::query()
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('original_filename', 'like', '%' . $search . '%')
                        ->orWhere('filename', 'like', '%' . $search . '%')
                        ->orWhere('id', $search);
                });
            })
            ->orderByDesc('id')
            ->paginate(
                perPage: 7,
                columns: ['id', 'title', 'original_filename', 'media_type', 'path'],
                pageName: 'article_cover_media_page'
            )
            ->withPath(route('admin.content.articles.media-picker.cover'))
            ->appends(array_filter(['q' => $search]));

        $selectedMediaIds = collect($selectedMediaIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedMediaIds->isEmpty()) {
            return $mediaItems;
        }

        $visibleIds = $mediaItems->getCollection()->pluck('id')->map(fn ($id) => (int) $id);
        $missingSelectedIds = $selectedMediaIds->diff($visibleIds)->values();

        if ($missingSelectedIds->isEmpty()) {
            return $mediaItems;
        }

        $selectedItems = Media::query()
            ->whereIn('id', $missingSelectedIds)
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->get(['id', 'title', 'original_filename', 'media_type', 'path'])
            ->sortBy(fn (Media $media) => $selectedMediaIds->search((int) $media->id))
            ->values();

        $mediaItems->setCollection(
            $selectedItems
                ->concat($mediaItems->getCollection())
                ->unique('id')
                ->values()
        );

        return $mediaItems;
    }

    private function sanitizeRichText(?string $value): ?string
    {
        return SafeRichText::clean($value);
    }

    private function createVersion(Article $article, string $versionName): void
    {
        $article->loadMissing([
            'content.categories',
            'content.mediaUsages',
            'tags',
            'relatedItems',
        ]);

        if (! $article->content) {
            return;
        }

        ContentVersion::query()->create([
            'content_id' => $article->content->id,
            'content_type' => 'article',
            'version_name' => $versionName,
            'snapshot' => [
                'content' => $article->content->only([
                    'id',
                    'content_type',
                    'title',
                    'slug',
                    'template_id',
                    'excerpt',
                    'description',
                    'status',
                    'is_featured',
                    'is_popular',
                    'meta_title',
                    'meta_description',
                    'published_at',
                ]),
                'article' => $article->only([
                    'id',
                    'content_id',
                    'title_en',
                    'excerpt_en',
                    'body',
                    'body_format',
                    'author_name',
                    'reading_time_minutes',
                    'seo_keywords',
                    'allow_comments',
                    'show_on_homepage',
                    'scheduled_at',
                    'expired_at',
                ]),
                'category_ids' => $article->content->categories->pluck('id')->values()->all(),
                'tag_ids' => $article->tags->pluck('id')->values()->all(),
                'media' => $article->content->mediaUsages->map(fn ($usage) => [
                    'media_id' => $usage->media_id,
                    'role_key' => $usage->role_key,
                    'sort_order' => $usage->sort_order,
                ])->values()->all(),
                'related_article_ids' => $article->relatedItems->pluck('related_article_id')->values()->all(),
            ],
            'created_by_admin_id' => auth('admin')->id(),
        ]);
    }

    private function articleAuditData(Article $article): array
    {
        $content = $article->content;

        return [
            'article_id' => $article->id,
            'content_id' => $article->content_id,
            'title' => $content?->title,
            'slug' => $content?->slug,
            'template_id' => $content?->template_id,
            'status' => $content?->status,
            'published_at' => $content?->published_at?->toDateTimeString(),
            'scheduled_at' => $article->scheduled_at?->toDateTimeString(),
            'expired_at' => $article->expired_at?->toDateTimeString(),
            'cover_media_id' => $content?->mediaUsages?->firstWhere('role_key', 'cover')?->media_id,
            'allow_comments' => $article->allow_comments,
            'show_on_homepage' => $article->show_on_homepage,
        ];
    }

    private function writeAuditLog(Request $request, string $action, Article $article, ?array $oldData, ?array $newData): void
    {
        AuditLog::query()->create([
            'action' => $action,
            'table_name' => 'articles',
            'record_id' => $article->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'performed_by' => auth('admin')->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
}
