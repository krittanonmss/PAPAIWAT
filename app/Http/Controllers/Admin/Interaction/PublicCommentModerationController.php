<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\PublicComment;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $comments = PublicComment::query()
            ->with('commentable')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.interactions.comments.index', compact('comments', 'status'));
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

    public function banVisitor(PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        if ($comment->visitor) {
            $interactionService->banVisitor($comment->visitor, 'Banned from comment moderation');
        }

        return back()->with('success', 'ban visitor แล้ว');
    }

    public function banIp(PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $interactionService->banIpHash($comment->ip_hash, 'Banned from comment moderation');

        return back()->with('success', 'ban IP แล้ว');
    }
}
