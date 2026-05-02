<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Template;
use App\Models\Content\Temple\Temple;
use Illuminate\View\View;

class FrontendTempleController extends Controller
{
    public function show(Temple $temple): View
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

        $viewPath = Template::query()
            ->where('key', 'temple-detail')
            ->where('status', 'active')
            ->value('view_path') ?? 'frontend.templates.details.temple-default';

        if (! view()->exists($viewPath)) {
            $viewPath = 'frontend.templates.details.temple-default';
        }

        return view($viewPath, compact('temple'));
    }
}