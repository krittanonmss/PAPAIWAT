<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Temple\Temple;
use App\Support\ContentTemplateResolver;
use Illuminate\View\View;

class FrontendTempleController extends Controller
{
    public function show(
        Temple $temple,
        ContentTemplateResolver $templateResolver
    ): View
    {
        $temple->load([
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

        abort_unless($temple->content?->status === 'published', 404);

        $viewData = [
            'content' => $temple->content,
            'contentModel' => $temple->content,
            'temple' => $temple,
            'page' => null,
        ];

        $viewPath = $templateResolver->resolveViewPath(
            $temple->content,
            $temple->content?->template_id,
            'frontend.templates.details.temple-default'
        );

        return view($viewPath, $viewData);
    }
}
