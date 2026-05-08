<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Interaction\PublicComment;
use App\Services\Frontend\ContentViewTrackingService;
use App\Support\ContentTemplateResolver;
use Illuminate\View\View;

class FrontendArticleController extends Controller
{
    public function show(
        string $slug,
        ContentTemplateResolver $templateResolver,
        ContentViewTrackingService $viewTrackingService
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

        if ($articleContent->article) {
            $articleContent->article->setRelation(
                'stat',
                $viewTrackingService->trackArticle($articleContent->article)
            );
        }

        $article = $articleContent->article;
        $approvedComments = $article
            ? PublicComment::query()
                ->approved()
                ->where('commentable_type', $article::class)
                ->where('commentable_id', $article->id)
                ->oldest()
                ->get()
            : collect();

        $viewData = [
            'content' => $articleContent,
            'articleContent' => $articleContent,
            'article' => $article,
            'relatedArticles' => $article?->relatedArticles ?? collect(),
            'approvedComments' => $approvedComments,
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
