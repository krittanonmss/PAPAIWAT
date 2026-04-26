<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Page;
use Illuminate\View\View;

class FrontendPageController extends Controller
{
    public function home(): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query
                    ->visible()
                    ->orderBy('sort_order'),
            ])
            ->published()
            ->where('is_homepage', true)
            ->firstOrFail();

        return $this->renderPage($page);
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->with([
                'template',
                'sections' => fn ($query) => $query
                    ->visible()
                    ->orderBy('sort_order'),
            ])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderPage($page);
    }

    private function renderPage(Page $page): View
    {
        $viewPath = $page->template?->view_path ?? 'frontend.pages.default';

        if (! view()->exists($viewPath)) {
            $viewPath = 'frontend.pages.default';
        }

        return view($viewPath, compact('page'));
    }
}