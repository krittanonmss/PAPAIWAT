<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\PublicComment;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'status' => $request->string('status')->toString(),
            'commentable_type' => $request->string('commentable_type')->toString(),
            'reported' => $request->string('reported')->toString(),
            'per_page' => (int) $request->query('per_page', 20),
        ];
        $filters['per_page'] = in_array($filters['per_page'], [10, 20, 50, 100], true)
            ? $filters['per_page']
            : 20;

        $comments = PublicComment::query()
            ->with('commentable')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%' . $filters['search'] . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('body', 'like', $like)
                        ->orWhere('display_name', 'like', $like)
                        ->orWhereHasMorph('commentable', [Article::class, Temple::class], function ($query, string $type) use ($like) {
                            $query->where(function ($query) use ($like, $type) {
                                $query->whereHas('content', function ($query) use ($like) {
                                    $query->where('title', 'like', $like)
                                        ->orWhere('slug', 'like', $like);
                                });

                                if ($type === Article::class) {
                                    $query->orWhere('title_en', 'like', $like);
                                }
                            });
                        });
                });
            })
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['commentable_type'] !== '', fn ($query) => $query->where('commentable_type', $filters['commentable_type']))
            ->when($filters['reported'] === 'yes', fn ($query) => $query->where('report_count', '>', 0))
            ->when($filters['reported'] === 'no', fn ($query) => $query->where('report_count', 0))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        $commentableTypes = PublicComment::query()
            ->whereNotNull('commentable_type')
            ->distinct()
            ->orderBy('commentable_type')
            ->pluck('commentable_type');

        return view('admin.interactions.comments.index', compact('comments', 'filters', 'commentableTypes'));
    }

    public function approve(PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $interactionService->approveComment($comment);

        return back()->with('success', 'อนุมัติความคิดเห็นแล้ว');
    }

    public function reject(PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $interactionService->rejectComment($comment);

        return back()->with('success', 'ปฏิเสธความคิดเห็นแล้ว');
    }

    public function destroy(PublicComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'ลบความคิดเห็นแล้ว');
    }

}
