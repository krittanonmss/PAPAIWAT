<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\TempleReview;
use App\Services\Frontend\ContentViewTrackingService;
use App\Services\Interaction\AnonymousVisitorService;
use App\Support\ContentTemplateResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendTempleController extends Controller
{
    public function show(
        Request $request,
        Temple $temple,
        ContentTemplateResolver $templateResolver,
        ContentViewTrackingService $viewTrackingService,
        AnonymousVisitorService $visitorService
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

        $temple->setRelation('stat', $viewTrackingService->trackTemple($temple));

        $visitor = $visitorService->findExisting($request);
        $approvedReviews = TempleReview::query()
            ->approved()
            ->where('temple_id', $temple->id)
            ->latest('approved_at')
            ->latest('id')
            ->get();
        $visitorPendingReviews = $visitor
            ? TempleReview::query()
                ->where('anonymous_visitor_id', $visitor->id)
                ->where('temple_id', $temple->id)
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        $viewData = [
            'content' => $temple->content,
            'contentModel' => $temple->content,
            'temple' => $temple,
            'approvedReviews' => $approvedReviews,
            'visitorPendingReviews' => $visitorPendingReviews,
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
