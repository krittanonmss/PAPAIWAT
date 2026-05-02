<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Temple\Temple;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FrontendPageController extends Controller
{
    public function home(): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('is_visible', true)
                    ->orderBy('sort_order'),
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
                'sections' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('is_visible', true)
                    ->orderBy('sort_order'),
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->renderPage($page);
    }

    private function renderPage(Page $page): View
    {
        $viewPath = $page->template?->view_path ?? 'frontend.templates.pages.default';

        if (! view()->exists($viewPath)) {
            $viewPath = 'frontend.templates.pages.default';
        }

        $sections = $page->sections;
        $sectionData = $this->buildSectionData($sections);

        $items = collect();

        if (str_starts_with($viewPath, 'frontend.templates.lists.')) {
            $listSection = $sections->firstWhere('component_key', 'temple_list');

            if ($listSection) {
                $items = $sectionData[$listSection->id] ?? collect();
            }
        }

        return view($viewPath, compact('page', 'sections', 'sectionData', 'items'));
    }

    private function buildSectionData(Collection $sections): array
    {
        $sectionData = [];

        foreach ($sections as $section) {
            $settings = $section->settings ?? [];

            $sectionData[$section->id] = match ($section->component_key) {
                'temple_list' => $this->getTempleListData($settings),
                'article_list' => $this->getArticleListData($settings),
                default => null,
            };
        }

        return $sectionData;
    }

    private function getTempleListData(array $settings): Collection
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
                'fees',
                'highlights',
            ])
            ->whereHas('content', function ($query) use ($source) {
                $query->where('status', 'published');

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
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
                }
            });

        if ($province = request('province', $settings['province'] ?? null)) {
            $query->whereHas('address', function ($query) use ($province) {
                $query->where('province', $province);
            });
        }

        if ($templeType = request('temple_type', $settings['temple_type'] ?? null)) {
            $query->where('temple_type', $templeType);
        }

        if (($settings['sort'] ?? null) === 'latest') {
            $query->latest();
        }

        return $query
            ->limit($limit)
            ->get();
    }

    private function getArticleListData(array $settings): Collection
    {
        $limit = min((int) ($settings['limit'] ?? 12), 48);

        return Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->with([
                'article',
                'categories',
                'mediaUsages.media',
            ])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}