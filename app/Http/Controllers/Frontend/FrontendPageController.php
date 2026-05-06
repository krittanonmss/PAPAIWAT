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

    private function buildPageSections(Page $page): Collection
    {
        $sections = $page->relationLoaded('sections')
            ? $page->sections
            : $page->sections()->visible()->orderBy('sort_order')->get();

        return $sections
            ->map(function (PageSection $section) {
                $section->content_data = $section->content ?? [];
                $section->settings_data = $section->settings ?? [];
                $section->image_url = $this->resolveSectionImageUrl($section->content_data);
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
                    default => [],
                };

                return $section;
            })
            ->values();
    }

    private function resolveSectionImageUrl(array $content): ?string
    {
        if (! empty($content['image_media_id'])) {
            $media = Media::query()->find((int) $content['image_media_id']);
            $path = $media?->path;

            if ($path) {
                return filter_var($path, FILTER_VALIDATE_URL)
                    ? $path
                    : Storage::url($path);
            }
        }

        return $content['image_url'] ?? null;
    }

    private function getTempleListData(array $settings, bool $paginate = false)
    {
        $limit = min((int) ($settings['limit'] ?? 12), 48);
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
                ->paginate(16)
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
                ->paginate(16)
                ->withQueryString();
        }

        return $query->limit($limit)->get();
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
