<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\TempleReview;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TempleReviewModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $reviews = TempleReview::query()
            ->with(['temple.content'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.interactions.reviews.index', compact('reviews', 'status'));
    }

    public function approve(TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $interactionService->approveReview($review);

        return back()->with('success', 'อนุมัติรีวิวแล้ว');
    }

    public function reject(TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $interactionService->rejectReview($review);

        return back()->with('success', 'ปฏิเสธรีวิวแล้ว');
    }

    public function destroy(TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $temple = $review->temple;
        $review->delete();

        if ($temple) {
            $interactionService->syncTempleReviewStats($temple);
        }

        return back()->with('success', 'ลบรีวิวแล้ว');
    }

}
