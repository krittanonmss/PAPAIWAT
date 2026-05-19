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
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'status' => $request->string('status')->toString(),
            'rating' => $request->string('rating')->toString(),
            'reported' => $request->string('reported')->toString(),
            'per_page' => (int) $request->query('per_page', 20),
        ];
        $filters['per_page'] = in_array($filters['per_page'], [10, 20, 50, 100], true)
            ? $filters['per_page']
            : 20;

        $reviews = TempleReview::query()
            ->with(['temple.content'])
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%' . $filters['search'] . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('comment', 'like', $like)
                        ->orWhere('display_name', 'like', $like)
                        ->orWhereHas('temple.content', function ($query) use ($like) {
                            $query->where('title', 'like', $like)
                                ->orWhere('slug', 'like', $like);
                        });
                });
            })
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['rating'] !== '', fn ($query) => $query->where('rating', (int) $filters['rating']))
            ->when($filters['reported'] === 'yes', fn ($query) => $query->where('report_count', '>', 0))
            ->when($filters['reported'] === 'no', fn ($query) => $query->where('report_count', 0))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        return view('admin.interactions.reviews.index', compact('reviews', 'filters'));
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
