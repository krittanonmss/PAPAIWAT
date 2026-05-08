<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Interaction\PublicComment;
use App\Models\Interaction\TempleReview;
use App\Services\Interaction\AnonymousVisitorService;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InteractionReportController extends Controller
{
    public function review(
        Request $request,
        TempleReview $review,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:80'],
        ]);

        $visitor = $visitorService->resolve($request);

        $interactionService->report(
            $review,
            $visitor,
            $validated['reason'] ?? null,
            $visitorService->hashNullable($request->ip()),
            $visitorService->hashNullable($request->userAgent())
        );

        return back()->with('success', 'รับรายงานแล้ว');
    }

    public function comment(
        Request $request,
        PublicComment $comment,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:80'],
        ]);

        $visitor = $visitorService->resolve($request);

        $interactionService->report(
            $comment,
            $visitor,
            $validated['reason'] ?? null,
            $visitorService->hashNullable($request->ip()),
            $visitorService->hashNullable($request->userAgent())
        );

        return back()->with('success', 'รับรายงานแล้ว');
    }
}
