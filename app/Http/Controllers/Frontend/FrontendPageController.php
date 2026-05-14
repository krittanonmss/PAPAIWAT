<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Article\ArticleStat;
use App\Models\Content\Article\ArticleTag;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Models\Content\Temple\TempleStat;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FrontendPageController extends Controller
{
    public function home(): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query->visible()->orderBy('sort_order'),
            ])
            ->where('is_homepage', true)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->renderPage($page);
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query->visible()->orderBy('sort_order'),
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->renderPage($page);
    }

    public function search(Request $request): View
    {
        $queryText = trim((string) $request->query('q', $request->query('search', '')));

        $results = Content::query()
            ->whereIn('content_type', ['temple', 'article'])
            ->where('status', 'published')
            ->with([
                'categories',
                'mediaUsages.media',
                'article.tags',
                'article.stat',
                'temple.address',
                'temple.stat',
            ])
            ->when($queryText !== '', function ($query) use ($queryText) {
                $query->where(function ($query) use ($queryText) {
                    $like = '%' . $queryText . '%';

                    $query->where('title', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('article', function ($query) use ($like) {
                            $query->where('title_en', 'like', $like)
                                ->orWhere('excerpt_en', 'like', $like)
                                ->orWhere('body', 'like', $like)
                                ->orWhere('author_name', 'like', $like)
                                ->orWhere('seo_keywords', 'like', $like);
                        })
                        ->orWhereHas('article.tags', function ($query) use ($like) {
                            $query->where('name', 'like', $like)
                                ->orWhere('slug', 'like', $like);
                        })
                        ->orWhereHas('temple', function ($query) use ($like) {
                            $query->where('temple_type', 'like', $like)
                                ->orWhere('sect', 'like', $like)
                                ->orWhere('architecture_style', 'like', $like)
                                ->orWhere('history', 'like', $like)
                                ->orWhereHas('address', function ($query) use ($like) {
                                    $query->where('address_line', 'like', $like)
                                        ->orWhere('province', 'like', $like)
                                        ->orWhere('district', 'like', $like)
                                        ->orWhere('subdistrict', 'like', $like);
                                })
                                ->orWhereHas('highlights', function ($query) use ($like) {
                                    $query->where('title', 'like', $like)
                                        ->orWhere('description', 'like', $like);
                                });
                        });
                });
            })
            ->when($queryText === '', fn ($query) => $query->whereRaw('1 = 0'))
            ->latest('published_at')
            ->latest('id')
            ->paginate(16)
            ->withQueryString();

        return view('frontend.search.index', compact('queryText', 'results'));
    }

    private function renderPage(Page $page): View
    {
        $viewPath = $page->template?->view_path ?? 'frontend.templates.pages.builder';

        if (! view()->exists($viewPath)) {
            $viewPath = view()->exists('frontend.templates.pages.builder')
                ? 'frontend.templates.pages.builder'
                : 'frontend.templates.pages.default';
        }

        $items = collect();
        $filters = [];
        $homeArticles = collect();
        $homeTemples = collect();
        $sections = $this->buildPageSections($page);

        if (str_starts_with($viewPath, 'frontend.templates.lists.')) {
            $isTempleList = str_contains($viewPath, 'temple');
            $isArticleList = str_contains($viewPath, 'article');

            if ($isTempleList) {
                $items = $this->getTempleListData(['source' => 'all'], true);
            } elseif ($isArticleList) {
                $items = $this->getArticleListData(['source' => 'all'], true);
            }

            if ($isTempleList) {
                $filters = $this->getTempleFilterData();
            } elseif ($isArticleList) {
                $filters = $this->getArticleFilterData();
            }
        }

        if (str_starts_with($viewPath, 'frontend.templates.pages.home-')) {
            $homeArticles = $this->getHomeArticleData();
            $homeTemples = $this->getHomeTempleData();
        }

        return view($viewPath, compact('page', 'items', 'filters', 'homeArticles', 'homeTemples', 'sections'));
    }

    public function buildPageSections(Page $page): Collection
    {
        $sections = $page->relationLoaded('sections')
            ? $page->sections
            : $page->sections()->visible()->orderBy('sort_order')->get();

        return $sections
            ->map(function (PageSection $section) {
                $contentData = $section->content ?? [];
                $contentData['primary_url'] = $this->resolveSectionPageUrl($contentData, 'primary_page_id', 'primary_url');
                $contentData['secondary_url'] = $this->resolveSectionPageUrl($contentData, 'secondary_page_id', 'secondary_url');

                $section->content_data = $contentData;
                $section->settings_data = $section->settings ?? [];
                $section->image_data = $this->resolveSectionImageData($section->content_data, $section->component_key);
                $section->image_url = $section->image_data['url'] ?? null;
                $section->gallery_items = $section->component_key === 'gallery'
                    ? $this->resolveSectionGalleryItems($section->content_data)
                    : collect();
                $section->all_button_url = $this->resolveSectionPageUrl($section->content_data);
                $section->bento_items = $section->component_key === 'travel_discovery_bento'
                    ? $this->getBentoContentItems($section->content_data, $section->settings_data)
                    : collect();
                $section->summary_stats = in_array($section->component_key, ['hero', 'banner'], true)
                    ? $this->getSummaryStats()
                    : [];
                $section->items = match ($section->component_key) {
                    'article_grid' => $this->getArticleListData($section->settings_data),
                    'temple_grid' => $this->getTempleListData($section->settings_data),
                    'article_list_full' => $this->getArticleListData(array_merge(['source' => 'all'], $section->settings_data), true),
                    'temple_list_full' => $this->getTempleListData(array_merge(['source' => 'all'], $section->settings_data), true),
                    default => collect(),
                };
                $section->filters = match ($section->component_key) {
                    'article_list_full' => $this->getArticleFilterData(),
                    'temple_list_full' => $this->getTempleFilterData(),
                    'travel_discovery_bento' => ($section->settings_data['bento_variant'] ?? 'travel') === 'article_filter'
                        ? $this->getBentoFilterData($section->settings_data)
                        : [],
                    default => [],
                };

                return $section;
            })
            ->values();
    }

    private function resolveSectionImageData(array $content, string $componentKey): array
    {
        if (! empty($content['image_media_id'])) {
            $media = Media::query()
                ->with(['variants' => fn ($query) => $query->where('processing_status', 'completed')])
                ->find((int) $content['image_media_id']);
            $path = $media?->path;

            if ($path) {
                $originalUrl = filter_var($path, FILTER_VALIDATE_URL)
                    ? $path
                    : Storage::url($path);

                $sources = collect();
                $originalWidth = (int) ($media->width ?? 0);

                if ($media->relationLoaded('variants')) {
                    $sources = $media->variants
                        ->filter(fn ($variant) => $variant->path && (int) ($variant->width ?? 0) > 0)
                        ->filter(fn ($variant) => $originalWidth <= 0 || (int) $variant->width <= $originalWidth)
                        ->map(fn ($variant) => [
                            'url' => filter_var($variant->path, FILTER_VALIDATE_URL)
                                ? $variant->path
                                : Storage::url($variant->path),
                            'width' => (int) $variant->width,
                        ]);
                }

                if ($originalWidth > 0) {
                    $sources->push([
                        'url' => $originalUrl,
                        'width' => $originalWidth,
                    ]);
                }

                $srcset = $sources
                    ->unique('width')
                    ->sortBy('width')
                    ->map(fn ($source) => $source['url'] . ' ' . $source['width'] . 'w')
                    ->implode(', ');

                return [
                    'url' => $originalUrl,
                    'srcset' => $srcset !== '' ? $srcset : null,
                    'sizes' => in_array($componentKey, ['hero', 'banner'], true)
                        ? '100vw'
                        : '(min-width: 1024px) 50vw, 100vw',
                ];
            }
        }

        return [
            'url' => $content['image_url'] ?? null,
            'srcset' => null,
            'sizes' => null,
        ];
    }

    private function resolveSectionPageUrl(array $content, string $pageKey = 'all_button_page_id', string $urlKey = 'all_button_url'): ?string
    {
        if (! empty($content[$pageKey])) {
            $page = Page::query()->find((int) $content[$pageKey]);

            if ($page) {
                return $page->is_homepage
                    ? route('home')
                    : route('pages.show', $page->slug);
            }
        }

        return $content[$urlKey] ?? null;
    }

    private function resolveSectionGalleryItems(array $content): Collection
    {
        $ids = collect($content['gallery_media_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $mediaItems = Media::query()
            ->whereIn('id', $ids)
            ->where('media_type', 'image')
            ->get()
            ->keyBy('id');

        return $ids
            ->map(function (int $id) use ($mediaItems) {
                $media = $mediaItems->get($id);
                $path = $media?->path;

                if (! $path) {
                    return null;
                }

                return [
                    'url' => filter_var($path, FILTER_VALIDATE_URL) ? $path : Storage::url($path),
                    'caption' => $media->title ?: $media->original_filename,
                ];
            })
            ->filter()
            ->values();
    }

    private function getBentoContentItems(array $content, array $settings): Collection
    {
        if (($settings['bento_variant'] ?? 'travel') === 'article_filter') {
            return $this->getFilteredBentoItems($settings);
        }

        $slots = collect($content['bento_slots'] ?? [])
            ->map(fn ($slot) => [
                'content_id' => (int) ($slot['content_id'] ?? 0),
                'size' => in_array(($slot['size'] ?? 'small'), ['large', 'wide', 'tall', 'small'], true) ? $slot['size'] : 'small',
            ])
            ->filter(fn ($slot) => $slot['content_id'] > 0)
            ->unique('content_id')
            ->take(9)
            ->values();

        if ($slots->isEmpty()) {
            $layoutSizes = $this->bentoLayoutSizes($settings['bento_layout'] ?? 'mosaic_5');
            $slots = collect($content['bento_content_ids'] ?? [])
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->take(9)
                ->values()
                ->map(fn ($id, $index) => [
                    'content_id' => $id,
                    'size' => $layoutSizes[$index] ?? 'small',
                ]);
        }

        if ($slots->isEmpty()) {
            return collect();
        }

        $contents = Content::query()
            ->whereIn('id', $slots->pluck('content_id'))
            ->whereIn('content_type', ['temple', 'article'])
            ->where('status', 'published')
            ->with([
                'categories',
                'mediaUsages.media',
                'article',
                'temple.address',
            ])
            ->get()
            ->keyBy('id');

        return $slots
            ->map(function (array $slot) use ($contents) {
                $id = $slot['content_id'];
                $content = $contents->get($id);

                if (! $content) {
                    return null;
                }

                $mediaUsages = $content->relationLoaded('mediaUsages') ? $content->mediaUsages : collect();
                $cover = $mediaUsages->firstWhere('role_key', 'cover');
                $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                $path = $coverMedia?->path;
                $imageUrl = $path
                    ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : Storage::url($path))
                    : null;

                $category = $content->relationLoaded('categories') ? $content->categories->first() : null;
                $temple = $content->relationLoaded('temple') ? $content->temple : null;
                $label = $category?->name
                    ?: ($content->content_type === 'temple' ? ($temple?->address?->province ?: 'วัด') : 'บทความ');

                return [
                    'title' => $content->title,
                    'description' => $content->excerpt ?: str($content->description ?? '')->stripTags()->limit(140)->toString(),
                    'label' => $label,
                    'url' => $this->contentPublicUrl($content),
                    'image' => $imageUrl,
                    'size' => $slot['size'],
                    'kicker' => $content->content_type === 'temple' ? 'Temple' : 'Article',
                ];
            })
            ->filter()
            ->values();
    }

    private function getFilteredBentoItems(array $settings): Collection
    {
        $limit = max(1, min((int) ($settings['limit'] ?? 6), 12));
        $layoutSizes = $this->bentoLayoutSizes($settings['bento_layout'] ?? 'editorial_6');
        $contentType = ($settings['bento_content_type'] ?? 'article') === 'temple' ? 'temple' : 'article';

        $querySettings = array_merge($settings, [
            'limit' => $limit,
            'source' => 'all',
            'sort' => 'random',
        ]);

        $items = $contentType === 'temple'
            ? $this->getTempleListData($querySettings)
            : $this->getArticleListData($querySettings);

        return collect($items)
            ->values()
            ->map(function ($item, int $index) use ($layoutSizes, $contentType) {
                return $contentType === 'temple'
                    ? $this->mapTempleBentoItem($item, $layoutSizes[$index % count($layoutSizes)] ?? 'small')
                    : $this->mapArticleBentoItem($item, $layoutSizes[$index % count($layoutSizes)] ?? 'small');
            })
            ->filter()
            ->values();
    }

    private function mapArticleBentoItem(Content $content, string $size): array
    {
        $mediaUsages = $content->relationLoaded('mediaUsages') ? $content->mediaUsages : collect();
        $cover = $mediaUsages->firstWhere('role_key', 'cover');
        $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
        $path = $coverMedia?->path;
        $imageUrl = $path
            ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : Storage::url($path))
            : null;
        $category = $content->relationLoaded('categories') ? $content->categories->first() : null;
        $article = $content->relationLoaded('article') ? $content->article : null;

        return [
            'title' => $content->title,
            'description' => $content->excerpt ?: str($content->description ?? '')->stripTags()->limit(140)->toString(),
            'label' => $category?->name ?: 'บทความ',
            'url' => $this->contentPublicUrl($content),
            'image' => $imageUrl,
            'size' => $size,
            'kicker' => $article?->author_name ?: ($content->published_at?->format('d M Y') ?? 'Article'),
        ];
    }

    private function mapTempleBentoItem(Temple $temple, string $size): ?array
    {
        $content = $temple->relationLoaded('content') ? $temple->content : null;

        if (! $content) {
            return null;
        }

        $mediaUsages = $content->relationLoaded('mediaUsages') ? $content->mediaUsages : collect();
        $cover = $mediaUsages->firstWhere('role_key', 'cover');
        $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
        $path = $coverMedia?->path;
        $imageUrl = $path
            ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : Storage::url($path))
            : null;
        $category = $content->relationLoaded('categories') ? $content->categories->first() : null;

        return [
            'title' => $content->title,
            'description' => $content->excerpt ?: str($content->description ?? $temple->history ?? '')->stripTags()->limit(140)->toString(),
            'label' => $category?->name ?: ($temple->address?->province ?: 'วัด'),
            'url' => route('temples.show', $temple),
            'image' => $imageUrl,
            'size' => $size,
            'kicker' => $temple->temple_type ?: ($temple->address?->province ?: 'Temple'),
        ];
    }

    private function getBentoFilterData(array $settings): array
    {
        if (($settings['bento_content_type'] ?? 'article') === 'temple') {
            return $this->getTempleFilterData();
        }

        return $this->getArticleFilterData();
    }

    private function bentoLayoutSizes(string $layout): array
    {
        return match ($layout) {
            'feature_3' => ['large', 'wide', 'wide'],
            'balanced_4' => ['large', 'small', 'small', 'wide'],
            'editorial_6' => ['large', 'small', 'small', 'wide', 'small', 'small'],
            'compact_7' => ['wide', 'small', 'small', 'tall', 'small', 'small', 'wide'],
            'full_9' => ['large', 'small', 'small', 'wide', 'small', 'tall', 'small', 'small', 'wide'],
            default => ['large', 'small', 'small', 'wide', 'small'],
        };
    }

    private function contentPublicUrl(Content $content): string
    {
        if ($content->content_type === 'temple' && $content->relationLoaded('temple') && $content->temple) {
            return route('temples.show', $content->temple);
        }

        if ($content->content_type === 'article' && $content->slug) {
            return route('articles.show', $content->slug);
        }

        return '#';
    }

    private function getSummaryStats(): array
    {
        $templeCount = Temple::query()
            ->whereHas('content', fn ($query) => $query->where('status', 'published'))
            ->count();

        $articleCount = Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->count();

        $totalViews = (int) TempleStat::query()->sum('view_count')
            + (int) ArticleStat::query()->sum('view_count');

        return [
            'temples' => $templeCount,
            'articles' => $articleCount,
            'views' => $totalViews,
        ];
    }

    private function getTempleListData(array $settings, bool $paginate = false)
    {
        $limit = min((int) ($settings['limit'] ?? 12), 48);
        $perPage = $this->fullListPerPage($settings);
        $source = $settings['source'] ?? 'all';

        $query = Temple::query()
            ->with([
                'content.categories',
                'content.mediaUsages.media',
                'address',
                'stat',
                'openingHours',
                'fees' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'highlights',
                'facilityItems.facility',
                'travelInfos' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'visitRules',
            ])
            ->whereHas('content', function ($query) use ($source) {
                $query->where('status', 'published');

                if ($source === 'featured') {
                    $query->where('is_featured', true);
                }

                if ($source === 'popular') {
                    $query->where('is_popular', true);
                }

            });

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('content', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('excerpt', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                })
                    ->orWhere('temple_type', 'like', '%' . $search . '%')
                    ->orWhere('sect', 'like', '%' . $search . '%')
                    ->orWhere('architecture_style', 'like', '%' . $search . '%')
                    ->orWhere('history', 'like', '%' . $search . '%')
                    ->orWhereHas('address', function ($query) use ($search) {
                        $query->where('address_line', 'like', '%' . $search . '%')
                            ->orWhere('province', 'like', '%' . $search . '%')
                            ->orWhere('district', 'like', '%' . $search . '%')
                            ->orWhere('subdistrict', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('highlights', function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($province = request('province', $settings['province'] ?? null)) {
            $query->whereHas('address', function ($query) use ($province) {
                $query->where('province', $province);
            });
        }

        if ($templeType = request('temple_type', $settings['temple_type'] ?? null)) {
            $query->where('temple_type', $templeType);
        }

        if ($category = request('category', $settings['category'] ?? null)) {
            $query->whereHas('content.categories', function ($query) use ($category) {
                $query->where('categories.slug', $category)
                    ->when(is_numeric($category), function ($query) use ($category) {
                        $query->orWhere('categories.id', (int) $category);
                    });
            });
        }

        $sort = request('sort', $settings['sort'] ?? 'popular');

        match ($sort) {
            'random' => $query->inRandomOrder(),
            'rating' => $query->orderByDesc(
                TempleStat::query()
                    ->select('average_rating')
                    ->whereColumn('temple_stats.temple_id', 'temples.id')
                    ->limit(1)
            ),
            'popular' => $query->orderByDesc(
                TempleStat::query()
                    ->select('score')
                    ->whereColumn('temple_stats.temple_id', 'temples.id')
                    ->limit(1)
            ),
            'latest' => $query->latest('temples.id'),
            default => $query->orderByDesc(
                TempleStat::query()
                    ->select('score')
                    ->whereColumn('temple_stats.temple_id', 'temples.id')
                    ->limit(1)
            )->orderBy('temples.id'),
        };

        if ($paginate) {
            return $query
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query->limit($limit)->get();
    }

    private function getTempleFilterData(): array
    {
        $templeTypes = Temple::query()
            ->whereHas('content', fn ($query) => $query->where('status', 'published'))
            ->whereNotNull('temple_type')
            ->distinct()
            ->orderBy('temple_type')
            ->pluck('temple_type')
            ->filter()
            ->values();

        $provinces = TempleAddress::query()
            ->whereHas('temple.content', fn ($query) => $query->where('status', 'published'))
            ->whereNotNull('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province')
            ->filter()
            ->values();

        $categories = Category::query()
            ->where('type_key', 'temple')
            ->where('status', 'active')
            ->whereHas('contents', function ($query) {
                $query->where('content_type', 'temple')
                    ->where('status', 'published');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return compact('templeTypes', 'provinces', 'categories');
    }

    private function getArticleListData(array $settings, bool $paginate = false)
    {
        $limit = min((int) ($settings['limit'] ?? 12), 48);
        $perPage = $this->fullListPerPage($settings);
        $source = $settings['source'] ?? 'all';

        $query = Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->with([
                'article.tags',
                'article.stat',
                'categories',
                'mediaUsages.media',
            ]);

        if ($source === 'featured') {
            $query->where('is_featured', true);
        }

        if ($source === 'popular') {
            $query->where('is_popular', true);
        }

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('article', function ($query) use ($search) {
                        $query->where('title_en', 'like', '%' . $search . '%')
                            ->orWhere('excerpt_en', 'like', '%' . $search . '%')
                            ->orWhere('body', 'like', '%' . $search . '%')
                            ->orWhere('author_name', 'like', '%' . $search . '%')
                            ->orWhere('seo_keywords', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('article.tags', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($category = request('category', $settings['category'] ?? null)) {
            $query->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.slug', $category)
                    ->when(is_numeric($category), function ($query) use ($category) {
                        $query->orWhere('categories.id', (int) $category);
                    });
            });
        }

        if ($tag = request('tag', $settings['tag'] ?? null)) {
            $query->whereHas('article.tags', function ($query) use ($tag) {
                $query->where('article_tags.slug', $tag)
                    ->when(is_numeric($tag), function ($query) use ($tag) {
                        $query->orWhere('article_tags.id', (int) $tag);
                    });
            });
        }

        if ($author = request('author', $settings['author'] ?? null)) {
            $query->whereHas('article', function ($query) use ($author) {
                $query->where('author_name', $author);
            });
        }

        $sort = request('sort', $settings['sort'] ?? 'latest');

        match ($sort) {
            'random' => $query->inRandomOrder(),
            'popular' => $query->orderByDesc(
                ArticleStat::query()
                    ->select('view_count')
                    ->join('articles', 'articles.id', '=', 'article_stats.article_id')
                    ->whereColumn('articles.content_id', 'contents.id')
                    ->limit(1)
            ),
            'likes' => $query->orderByDesc(
                ArticleStat::query()
                    ->select('like_count')
                    ->join('articles', 'articles.id', '=', 'article_stats.article_id')
                    ->whereColumn('articles.content_id', 'contents.id')
                    ->limit(1)
            ),
            'bookmarks' => $query->orderByDesc(
                ArticleStat::query()
                    ->select('bookmark_count')
                    ->join('articles', 'articles.id', '=', 'article_stats.article_id')
                    ->whereColumn('articles.content_id', 'contents.id')
                    ->limit(1)
            ),
            'shares' => $query->orderByDesc(
                ArticleStat::query()
                    ->select('share_count')
                    ->join('articles', 'articles.id', '=', 'article_stats.article_id')
                    ->whereColumn('articles.content_id', 'contents.id')
                    ->limit(1)
            ),
            'reading_time' => $query->orderByDesc(
                Article::query()
                    ->select('reading_time_minutes')
                    ->whereColumn('articles.content_id', 'contents.id')
                    ->limit(1)
            ),
            'oldest' => $query->oldest('published_at')->oldest('id'),
            default => $query->latest('published_at')->latest('id'),
        };

        if ($paginate) {
            return $query
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query->limit($limit)->get();
    }

    private function fullListPerPage(array $settings): int
    {
        $rows = max(1, min((int) ($settings['list_rows'] ?? 4), 12));
        $columns = max(1, min((int) ($settings['list_columns'] ?? 4), 6));

        return $rows * $columns;
    }

    private function getArticleFilterData(): array
    {
        $categories = Category::query()
            ->where('type_key', 'article')
            ->where('status', 'active')
            ->whereHas('contents', function ($query) {
                $query->where('content_type', 'article')
                    ->where('status', 'published');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $tags = ArticleTag::query()
            ->where('status', 'active')
            ->whereHas('articles.content', function ($query) {
                $query->where('content_type', 'article')
                    ->where('status', 'published');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $authors = Article::query()
            ->whereHas('content', fn ($query) => $query->where('content_type', 'article')->where('status', 'published'))
            ->whereNotNull('author_name')
            ->distinct()
            ->orderBy('author_name')
            ->pluck('author_name')
            ->filter()
            ->values();

        return compact('categories', 'tags', 'authors');
    }

    private function getHomeArticleData(): Collection
    {
        return Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->with([
                'article.tags',
                'article.stat',
                'categories',
                'mediaUsages.media',
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_popular')
            ->latest('published_at')
            ->latest('id')
            ->limit(4)
            ->get();
    }

    private function getHomeTempleData(): Collection
    {
        return Temple::query()
            ->with([
                'content.categories',
                'content.mediaUsages.media',
                'address',
                'stat',
                'openingHours',
            ])
            ->whereHas('content', function ($query) {
                $query->where('status', 'published')
                    ->where(function ($query) {
                        $query->where('is_featured', true)
                            ->orWhere('is_popular', true);
                    });
            })
            ->latest('temples.id')
            ->limit(4)
            ->get();
    }
}
