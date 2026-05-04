<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Support\ContentTemplateResolver;
use Illuminate\View\View;

class FrontendArticleController extends Controller
{
    public function show(
        string $slug,
        ContentTemplateResolver $templateResolver
    ): View
    {
        $articleContent = Content::query()
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with([
                'template',
                'article',
                'article.tags',
                'article.stat',
                'article.relatedItems',
                'article.relatedArticles.content.mediaUsages.media',
                'article.relatedArticles.content.categories',
                'article.relatedArticles.tags',
                'article.relatedArticles.stat',
                'categories',
                'mediaUsages.media',
            ])
            ->firstOrFail();

        $viewData = [
            'content' => $articleContent,
            'articleContent' => $articleContent,
            'article' => $articleContent->article,
            'relatedArticles' => $articleContent->article?->relatedArticles ?? collect(),
            'page' => null,
        ];

        $viewPath = $templateResolver->resolveViewPath(
            $articleContent,
            $articleContent->template_id,
            'frontend.templates.details.article-default'
        );

        return view($viewPath, $viewData);
    }
}
