<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Support\ContentTemplateResolver;
use Illuminate\Http\Request;
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
}
