<?php

namespace App\Services\Admin\Content\Article;

use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Content;
use App\Models\Content\ContentVersion;
use App\Support\SafeRichText;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ArticleDataSyncService
{
    public function create(array $validated): Article
    {
        return DB::transaction(function () use ($validated): Article {
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
                'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null),
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

            $this->syncRelations($article, $content, $validated);
            $this->createVersion($article, 'created');

            return $article;
        });
    }

    public function update(Article $article, array $validated): Article
    {
        $article->loadMissing('content', 'stat');

        DB::transaction(function () use ($validated, $article): void {
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
                'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? ($article->content->published_at ?? now()) : null),
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

            $this->touchOrCreateStats($article);
            $this->syncRelations($article, $article->content, $validated);
            $this->createVersion($article, 'updated');
        });

        return $article;
    }

    public function delete(Article $article): void
    {
        $article->loadMissing('content');

        DB::transaction(function () use ($article): void {
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
    }

    public function createVersion(Article $article, string $versionName): void
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

    public function generateUniqueSlug(string $value, ?int $ignoreContentId = null): string
    {
        $baseSlug = SlugGenerator::make($value, 'article');
        $slug = $baseSlug;
        $counter = 1;

        while (
            Content::query()
                ->withTrashed()
                ->where('content_type', 'article')
                ->where('slug', $slug)
                ->when($ignoreContentId, function (Builder $query) use ($ignoreContentId): void {
                    $query->where('id', '!=', $ignoreContentId);
                })
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function touchOrCreateStats(Article $article): void
    {
        if ($article->stat) {
            $article->stat->update(['updated_at' => now()]);

            return;
        }

        ArticleStat::query()->create([
            'article_id' => $article->id,
            'view_count' => 0,
            'like_count' => 0,
            'bookmark_count' => 0,
            'share_count' => 0,
            'updated_at' => now(),
        ]);
    }

    private function syncRelations(Article $article, Content $content, array $validated): void
    {
        $this->syncCategories($content, $validated['category_ids'] ?? []);
        $this->syncTags($article, $validated['tag_ids'] ?? []);
        $this->syncCoverMedia($content, $validated['cover_media_id'] ?? null);
        $this->syncRelatedArticles($article, $validated['related_article_ids'] ?? []);
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
            $syncData[$tagId] = ['created_at' => now()];
        }

        $article->tags()->sync($syncData);
    }

    private function syncCoverMedia(Content $content, int|string|null $coverMediaId): void
    {
        $content->mediaUsages()
            ->where('role_key', 'cover')
            ->delete();

        if (! $coverMediaId) {
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

    private function sanitizeRichText(?string $value): ?string
    {
        return SafeRichText::clean($value);
    }
}
