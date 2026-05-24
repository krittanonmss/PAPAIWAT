<?php

namespace App\Services\Frontend;

use App\Models\Content\Article\Article;
use App\Models\Content\Layout\Page;
use App\Models\Content\Temple\Temple;
use Illuminate\Support\Facades\Storage;

class SitemapService
{
    public function generate(): int
    {
        $urls = collect([route('home')]);

        Page::query()
            ->published()
            ->where('is_homepage', false)
            ->pluck('slug')
            ->each(fn (string $slug) => $urls->push(route('pages.show', $slug)));

        Temple::query()
            ->whereHas('content', fn ($query) => $query->where('status', 'published'))
            ->get()
            ->each(fn (Temple $temple) => $urls->push(route('temples.show', $temple)));

        Article::query()
            ->with('content:id,slug,status')
            ->whereHas('content', fn ($query) => $query->where('status', 'published'))
            ->get()
            ->each(fn (Article $article) => $urls->push(route('articles.show', $article->content->slug)));

        $xmlUrls = $urls->unique()
            ->map(fn (string $url) => '  <url><loc>'.htmlspecialchars($url, ENT_XML1).'</loc></url>')
            ->implode("\n");

        Storage::disk('public')->put(
            'sitemap.xml',
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            ."<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            .$xmlUrls."\n</urlset>\n"
        );

        return $urls->unique()->count();
    }
}
