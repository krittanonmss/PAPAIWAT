<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Article\ArticleTag;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaUsage;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Models\Content\Temple\TempleFacility;
use App\Models\Content\Temple\TempleFee;
use App\Models\Content\Temple\TempleHighlight;
use App\Models\Content\Temple\TempleNearbyPlace;
use App\Models\Content\Temple\TempleOpeningHour;
use App\Models\Content\Temple\TempleStat;
use App\Models\Content\Temple\TempleTravelInfo;
use App\Models\Content\Temple\TempleVisitRule;
use App\Support\SafeRichText;
use App\Support\ContentTemplateResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContentTemplatePreviewController extends Controller
{
    public function __invoke(
        Request $request,
        string $type,
        Content $content,
        ContentTemplateResolver $templateResolver
    ): View
    {
        abort_unless(in_array($type, ['temple', 'article'], true), 404);
        abort_unless($content->content_type === $type, 404);

        return match ($type) {
            'temple' => $this->renderTemple($content, $request, $templateResolver),
            'article' => $this->renderArticle($content, $request, $templateResolver),
        };
    }

    public function sample(
        Request $request,
        string $type,
        ContentTemplateResolver $templateResolver
    ): View
    {
        abort_unless(in_array($type, ['temple', 'article'], true), 404);

        $content = Content::query()
            ->where('content_type', $type)
            ->latest('id')
            ->first();

        if ($content) {
            return match ($type) {
                'temple' => $this->renderTemple($content, $request, $templateResolver),
                'article' => $this->renderArticle($content, $request, $templateResolver),
            };
        }

        return view('admin.content.template-preview-empty', [
            'type' => $type,
        ]);
    }

    public function live(
        Request $request,
        string $type,
        ContentTemplateResolver $templateResolver
    ): JsonResponse {
        abort_unless(in_array($type, ['temple', 'article'], true), 404);

        try {
            if ($type === 'temple') {
                $temple = $this->liveTemple($request);
                $view = $this->renderTempleView($temple, $temple->content, $request, $templateResolver);
            } else {
                $view = $this->renderLiveArticle($request, $templateResolver);
            }

            return response()->json([
                'html' => $view->render(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            $html = view('frontend.templates.previews.admin-iframe', [
                'previewTitle' => 'Template preview error',
                'previewMessage' => $exception->getMessage(),
            ])->render();

            return response()->json([
                'html' => $html,
            ], 422);
        }
    }

    private function renderTemple(
        Content $content,
        Request $request,
        ContentTemplateResolver $templateResolver
    ): View
    {
        $temple = $content->temple()->firstOrFail();

        $temple->load([
            'content.template',
            'content.categories',
            'content.mediaUsages.media',
            'address',
            'stat',
            'openingHours',
            'fees',
            'highlights',
            'visitRules',
            'travelInfos',
            'facilityItems.facility',
            'nearbyPlaces.nearbyTemple.content',
        ]);

        return $this->renderTempleView($temple, $content, $request, $templateResolver);
    }

    private function renderTempleView(
        Temple $temple,
        Content $content,
        Request $request,
        ContentTemplateResolver $templateResolver
    ): View
    {
        $viewPath = $templateResolver->resolveViewPath(
            $content,
            $request->has('template_id') ? ($request->integer('template_id') ?: null) : $content->template_id,
            'frontend.templates.details.temple-default',
            ! $request->has('template_id')
        );

        return view($viewPath, compact('temple'));
    }

    private function renderArticle(
        Content $content,
        Request $request,
        ContentTemplateResolver $templateResolver
    ): View
    {
        $articleContent = $content->load([
            'template',
            'article.tags',
            'article.relatedArticles.content.mediaUsages.media',
            'article.relatedArticles.content.categories',
            'article.stat',
            'categories',
            'mediaUsages.media',
        ]);

        $viewData = [
            'content' => $articleContent,
            'articleContent' => $articleContent,
            'article' => $articleContent->article,
            'relatedArticles' => $articleContent->article?->relatedArticles ?? collect(),
            'page' => null,
        ];

        $viewPath = $templateResolver->resolveViewPath(
            $articleContent,
            $request->has('template_id') ? ($request->integer('template_id') ?: null) : $articleContent->template_id,
            'frontend.templates.details.article-default',
            ! $request->has('template_id')
        );

        return view($viewPath, $viewData);
    }

    private function renderLiveArticle(Request $request, ContentTemplateResolver $templateResolver): View
    {
        $articleContent = $this->liveContent($request, 'article');
        $article = new Article([
            'content_id' => $articleContent->id,
            'title_en' => $request->input('title_en'),
            'excerpt_en' => $request->input('excerpt_en'),
            'body' => $request->input('body'),
            'body_format' => $request->input('body_format', 'html'),
            'author_name' => $request->input('author_name'),
            'reading_time_minutes' => $request->input('reading_time_minutes'),
            'seo_keywords' => $request->input('seo_keywords'),
            'allow_comments' => $request->boolean('allow_comments', true),
            'show_on_homepage' => $request->boolean('show_on_homepage'),
            'scheduled_at' => $this->dateOrNull($request->input('scheduled_at')),
            'expired_at' => $this->dateOrNull($request->input('expired_at')),
        ]);
        $article->id = (int) ($request->input('article_id') ?: 0);
        $article->exists = true;

        $article->setRelation('content', $articleContent);
        $article->setRelation('tags', $this->articleTags($request->input('tag_ids', [])));
        $article->setRelation('relatedArticles', $this->relatedArticles($request->input('related_article_ids', [])));
        $article->setRelation('stat', new ArticleStat([
            'view_count' => 0,
            'like_count' => 0,
            'bookmark_count' => 0,
            'share_count' => 0,
        ]));

        $articleContent->setRelation('article', $article);

        $viewPath = $templateResolver->resolveViewPath(
            $articleContent,
            $request->has('template_id') ? ($request->integer('template_id') ?: null) : $articleContent->template_id,
            'frontend.templates.details.article-default',
            ! $request->has('template_id')
        );

        return view($viewPath, [
            'content' => $articleContent,
            'articleContent' => $articleContent,
            'article' => $article,
            'relatedArticles' => $article->relatedArticles,
            'page' => null,
        ]);
    }

    private function liveTemple(Request $request): Temple
    {
        $content = $this->liveContent($request, 'temple');
        $temple = new Temple([
            'content_id' => $content->id,
            'temple_type' => $request->input('temple_type'),
            'sect' => $request->input('sect'),
            'architecture_style' => $request->input('architecture_style'),
            'founded_year' => $request->input('founded_year'),
            'history' => SafeRichText::clean($request->input('history')) ?? $request->input('history'),
            'dress_code' => $request->input('dress_code'),
            'recommended_visit_start_time' => $request->input('recommended_visit_start_time'),
            'recommended_visit_end_time' => $request->input('recommended_visit_end_time'),
        ]);
        $temple->id = (int) ($request->input('temple_id') ?: 0);
        $temple->exists = true;

        $temple->setRelation('content', $content);
        $temple->setRelation('address', new TempleAddress($request->input('address', [])));
        $temple->setRelation('stat', new TempleStat([
            'view_count' => 0,
            'review_count' => 0,
            'average_rating' => 0,
            'favorite_count' => 0,
            'share_count' => 0,
            'score' => 0,
        ]));
        $temple->setRelation('openingHours', $this->templeOpeningHours($request->input('opening_hours', [])));
        $temple->setRelation('fees', $this->templeFees($request->input('fees', [])));
        $temple->setRelation('highlights', $this->templeHighlights($request->input('highlights', [])));
        $temple->setRelation('visitRules', $this->templeVisitRules($request->input('visit_rules', [])));
        $temple->setRelation('travelInfos', $this->templeTravelInfos($request->input('travel_infos', [])));
        $temple->setRelation('facilityItems', $this->templeFacilities($request->input('facility_items', [])));
        $temple->setRelation('nearbyPlaces', $this->nearbyPlaces($request->input('nearby_places', [])));

        return $temple;
    }

    private function liveContent(Request $request, string $type): Content
    {
        $content = new Content([
            'content_type' => $type,
            'title' => $request->input('title') ?: ($type === 'temple' ? 'รายละเอียดวัด' : 'รายละเอียดบทความ'),
            'slug' => $request->input('slug') ?: Str::slug($request->input('title') ?: $type . '-preview'),
            'template_id' => $request->integer('template_id') ?: null,
            'excerpt' => $request->input('excerpt'),
            'description' => SafeRichText::clean($request->input('description')) ?? $request->input('description'),
            'status' => $request->input('status', 'draft'),
            'is_featured' => $request->boolean('is_featured'),
            'is_popular' => $request->boolean('is_popular'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'published_at' => $this->dateOrNull($request->input('published_at')),
        ]);
        $content->id = (int) ($request->input('content_id') ?: 0);
        $content->exists = true;

        $categoryIds = $request->input('category_ids', []);
        $primaryCategoryId = $request->input('primary_category_id');
        $content->setRelation('categories', $this->categories($categoryIds, $primaryCategoryId));
        $content->setRelation('mediaUsages', $this->mediaUsages(
            $request->input('cover_media_id'),
            $request->input('gallery_media_ids', [])
        ));

        return $content;
    }

    private function mediaUsages(mixed $coverMediaId, array $galleryMediaIds = [])
    {
        $ids = collect([$coverMediaId])
            ->merge($galleryMediaIds)
            ->filter(fn ($id) => is_scalar($id) && preg_match('/^\d+$/', (string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $media = Media::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $usages = collect();
        $sortOrder = 0;

        if ($coverMediaId && $media->has((int) $coverMediaId)) {
            $usage = new MediaUsage([
                'media_id' => (int) $coverMediaId,
                'role_key' => 'cover',
                'sort_order' => $sortOrder++,
            ]);
            $usage->setRelation('media', $media->get((int) $coverMediaId));
            $usages->push($usage);
        }

        foreach ($galleryMediaIds as $galleryMediaId) {
            $galleryMediaId = (int) $galleryMediaId;

            if (! $media->has($galleryMediaId)) {
                continue;
            }

            $usage = new MediaUsage([
                'media_id' => $galleryMediaId,
                'role_key' => 'gallery',
                'sort_order' => $sortOrder++,
            ]);
            $usage->setRelation('media', $media->get($galleryMediaId));
            $usages->push($usage);
        }

        return $usages;
    }

    private function categories(array $categoryIds, mixed $primaryCategoryId)
    {
        $ids = collect($categoryIds)
            ->filter(fn ($id) => is_scalar($id) && preg_match('/^\d+$/', (string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        return Category::query()
            ->whereIn('id', $ids)
            ->get()
            ->each(function (Category $category) use ($primaryCategoryId) {
                $category->setRelation('pivot', new \Illuminate\Database\Eloquent\Relations\Pivot([
                    'is_primary' => (string) $category->id === (string) $primaryCategoryId,
                    'sort_order' => 0,
                ]));
            });
    }

    private function articleTags(array $tagIds)
    {
        return ArticleTag::query()
            ->whereIn('id', collect($tagIds)->map(fn ($id) => (int) $id)->filter())
            ->get();
    }

    private function relatedArticles(array $articleIds)
    {
        return Article::query()
            ->with(['content.mediaUsages.media', 'content.categories'])
            ->whereIn('id', collect($articleIds)->map(fn ($id) => (int) $id)->filter())
            ->get();
    }

    private function templeOpeningHours(array $rows)
    {
        return collect($rows)->map(fn ($row) => new TempleOpeningHour([
            'day_of_week' => $row['day_of_week'] ?? 0,
            'open_time' => $row['open_time'] ?? null,
            'close_time' => $row['close_time'] ?? null,
            'is_closed' => (bool) ($row['is_closed'] ?? false),
            'note' => $row['note'] ?? null,
        ]));
    }

    private function templeFees(array $rows)
    {
        return collect($rows)->map(fn ($row) => new TempleFee([
            'fee_type' => $row['fee_type'] ?? null,
            'label' => $row['label'] ?? null,
            'amount' => $row['amount'] ?? null,
            'currency' => $row['currency'] ?? 'THB',
            'note' => $row['note'] ?? null,
            'is_active' => (bool) ($row['is_active'] ?? true),
            'sort_order' => $row['sort_order'] ?? 0,
        ]));
    }

    private function templeHighlights(array $rows)
    {
        return collect($rows)
            ->filter(fn ($row) => trim((string) ($row['title'] ?? '')) !== '')
            ->map(fn ($row) => new TempleHighlight([
                'title' => $row['title'] ?? null,
                'description' => SafeRichText::clean($row['description'] ?? null),
                'sort_order' => $row['sort_order'] ?? 0,
            ]));
    }

    private function templeVisitRules(array $rows)
    {
        return collect($rows)
            ->filter(fn ($row) => trim(strip_tags((string) ($row['rule_text'] ?? ''))) !== '')
            ->map(fn ($row) => new TempleVisitRule([
                'rule_text' => SafeRichText::clean($row['rule_text'] ?? null),
                'sort_order' => $row['sort_order'] ?? 0,
            ]));
    }

    private function templeTravelInfos(array $rows)
    {
        return collect($rows)->map(fn ($row) => new TempleTravelInfo([
            'travel_type' => $row['travel_type'] ?? null,
            'start_place' => $row['start_place'] ?? null,
            'distance_km' => $row['distance_km'] ?? null,
            'duration_minutes' => $row['duration_minutes'] ?? null,
            'cost_estimate' => $row['cost_estimate'] ?? null,
            'note' => $row['note'] ?? null,
            'is_active' => (bool) ($row['is_active'] ?? true),
            'sort_order' => $row['sort_order'] ?? 0,
        ]));
    }

    private function templeFacilities(array $rows)
    {
        $facilityIds = collect($rows)->pluck('facility_id')->filter()->map(fn ($id) => (int) $id);
        $facilities = Facility::query()->whereIn('id', $facilityIds)->get()->keyBy('id');

        return collect($rows)->map(function ($row) use ($facilities) {
            $item = new TempleFacility([
                'facility_id' => $row['facility_id'] ?? null,
                'value' => $row['value'] ?? null,
                'note' => $row['note'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
            ]);

            if (! empty($row['facility_id']) && $facilities->has((int) $row['facility_id'])) {
                $item->setRelation('facility', $facilities->get((int) $row['facility_id']));
            } elseif (! empty($row['facility_name'])) {
                $item->setRelation('facility', new Facility(['name' => $row['facility_name']]));
            }

            return $item;
        });
    }

    private function nearbyPlaces(array $rows)
    {
        $templeIds = collect($rows)->pluck('nearby_temple_id')->filter()->map(fn ($id) => (int) $id);
        $temples = Temple::query()->with('content')->whereIn('id', $templeIds)->get()->keyBy('id');

        return collect($rows)->map(function ($row) use ($temples) {
            $item = new TempleNearbyPlace([
                'nearby_temple_id' => $row['nearby_temple_id'] ?? null,
                'relation_type' => $row['relation_type'] ?? null,
                'distance_km' => $row['distance_km'] ?? null,
                'duration_minutes' => $row['duration_minutes'] ?? null,
                'score' => $row['score'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
            ]);

            if (! empty($row['nearby_temple_id']) && $temples->has((int) $row['nearby_temple_id'])) {
                $item->setRelation('nearbyTemple', $temples->get((int) $row['nearby_temple_id']));
            }

            return $item;
        });
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

}
