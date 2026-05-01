<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use Illuminate\View\View;

class FrontendArticleController extends Controller
{
    public function show(string $slug): View
    {
        $articleContent = Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with([
                'article',
                'categories',
                'mediaUsages.media',
            ])
            ->firstOrFail();

        return view('frontend.articles.show', [
            'articleContent' => $articleContent,
            'article' => $articleContent->article,
        ]);
    }
}