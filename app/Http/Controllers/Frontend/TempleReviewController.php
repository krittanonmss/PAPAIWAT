<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Temple\Temple;
use App\Services\Admin\AdminNotificationService;
use App\Services\Interaction\AnonymousVisitorService;
use App\Services\Interaction\PublicInteractionService;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TempleReviewController extends Controller
{
    public function store(
        Request $request,
        Temple $temple,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        abort_unless($temple->content?->status === 'published', 404);
        abort_unless((bool) SiteSettings::get('content', 'temple_reviews_enabled', true), 403);
        abort_unless((bool) SiteSettings::get('moderation', 'reviews_enabled', true), 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'display_name' => ['nullable', 'string', 'max:80'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $visitor = $visitorService->resolve($request);
        abort_if($visitor->isBanned(), 403);

        $review = $interactionService->submitTempleReview(
            $temple,
            $visitor,
            $validated,
            $visitorService->hashNullable($request->ip()),
            $visitorService->hashNullable($request->userAgent())
        );

        app(AdminNotificationService::class)->notifyAdminsWithPermission(
            'interactions.moderate',
            'moderation',
            'มีรีวิววัดรอตรวจสอบ',
            'รีวิว #'.$review->id.' ของวัด "'.($temple->content?->title ?? '#'.$temple->id).'" กำลังรอการตรวจสอบ'
        );

        return back()->with('success', 'รีวิวของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');
    }
}
