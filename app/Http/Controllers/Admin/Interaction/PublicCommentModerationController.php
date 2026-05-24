<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\PublicComment;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicCommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 20);
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'status' => $request->string('status')->toString(),
            'commentable_type' => $request->string('commentable_type')->toString(),
            'reported' => $request->string('reported')->toString(),
            'queue' => $request->string('queue')->toString(),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true)
            ? $filters['per_page']
            : $defaultPerPage;

        $comments = PublicComment::query()
            ->with(['commentable.content', 'visitor', 'parent', 'reports.visitor', 'moderator'])
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
            ->when($filters['queue'] === 'pending', fn ($query) => $query->where('status', 'pending'))
            ->when($filters['queue'] === 'reported', fn ($query) => $query->where('report_count', '>', 0))
            ->when($filters['queue'] === 'auto_hidden', fn ($query) => $query->where('status', 'rejected')->where('moderation_reason', 'auto_reported'))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        $commentableTypes = PublicComment::query()
            ->whereNotNull('commentable_type')
            ->distinct()
            ->orderBy('commentable_type')
            ->pluck('commentable_type');

        $summary = $this->summary();

        return view('admin.interactions.comments.index', compact('comments', 'filters', 'commentableTypes', 'summary'));
    }

    public function show(PublicComment $comment): View
    {
        $comment->load(['commentable.content', 'visitor', 'parent', 'replies', 'reports.visitor', 'moderator']);

        return view('admin.interactions.comments.show', compact('comment'));
    }

    public function approve(Request $request, PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'approved');
        $interactionService->approveComment($comment, $data['reason'], $data['note']);
        $this->notifyModeration('อนุมัติความคิดเห็นแล้ว', 'ความคิดเห็น #'.$comment->id.' ถูกอนุมัติโดยผู้ดูแล');

        return back()->with('success', 'อนุมัติความคิดเห็นแล้ว');
    }

    public function reject(Request $request, PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'rejected');
        $interactionService->rejectComment($comment, $data['reason'], $data['note']);
        $this->notifyModeration('ปฏิเสธความคิดเห็นแล้ว', 'ความคิดเห็น #'.$comment->id.' ถูกปฏิเสธโดยผู้ดูแล');

        return back()->with('success', 'ปฏิเสธความคิดเห็นแล้ว');
    }

    public function spam(Request $request, PublicComment $comment, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'spam');
        $interactionService->markCommentSpam($comment, $data['note']);
        $this->notifyModeration('ทำเครื่องหมายสแปมแล้ว', 'ความคิดเห็น #'.$comment->id.' ถูกทำเครื่องหมายเป็นสแปม');

        return back()->with('success', 'ทำเครื่องหมายสแปมแล้ว');
    }

    public function bulk(Request $request, PublicInteractionService $interactionService): RedirectResponse
    {
        $validated = $request->validate([
            'comment_ids' => ['required', 'array', 'min:1'],
            'comment_ids.*' => ['integer', 'exists:public_comments,id'],
            'action' => ['required', 'string', 'in:approve,reject,spam,delete'],
            'moderation_reason' => ['nullable', 'string', 'max:40'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $comments = PublicComment::query()->whereIn('id', $validated['comment_ids'])->get();

        foreach ($comments as $comment) {
            match ($validated['action']) {
                'approve' => $interactionService->approveComment($comment, $validated['moderation_reason'] ?? null, $validated['moderation_note'] ?? null),
                'reject' => $interactionService->rejectComment($comment, $validated['moderation_reason'] ?? 'off_topic', $validated['moderation_note'] ?? null),
                'spam' => $interactionService->markCommentSpam($comment, $validated['moderation_note'] ?? null),
                'delete' => $this->deleteComment($comment, $validated['moderation_reason'] ?? 'other', $validated['moderation_note'] ?? null),
            };
        }
        $this->notifyModeration('จัดการความคิดเห็นแบบกลุ่มแล้ว', 'จัดการความคิดเห็น '.count($comments).' รายการด้วย action '.$validated['action']);

        return back()->with('success', 'จัดการความคิดเห็นที่เลือกเรียบร้อยแล้ว');
    }

    public function banVisitor(Request $request, PublicComment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $this->banVisitorModel($comment->visitor, $validated['reason'] ?? 'moderation', $validated['expires_at'] ?? null);
        $this->notifyModeration('บล็อก visitor แล้ว', 'Visitor จากความคิดเห็น #'.$comment->id.' ถูกบล็อก');

        return back()->with('success', 'บล็อก visitor แล้ว');
    }

    public function destroy(Request $request, PublicComment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'moderation_reason' => ['nullable', 'string', 'max:40'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->deleteComment($comment, $validated['moderation_reason'] ?? 'other', $validated['moderation_note'] ?? null);
        $this->notifyModeration('ลบความคิดเห็นแล้ว', 'ความคิดเห็น #'.$comment->id.' ถูกลบโดยผู้ดูแล');

        return back()->with('success', 'ลบความคิดเห็นแล้ว');
    }

    private function notifyModeration(string $title, string $message): void
    {
        app(AdminNotificationService::class)->notifyAdmins('moderation', $title, $message);
    }

    private function moderationData(Request $request, string $action): array
    {
        $validated = $request->validate([
            'moderation_reason' => ['nullable', 'string', 'max:40'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        return [
            'reason' => $validated['moderation_reason'] ?? ($action === 'approved' ? null : 'off_topic'),
            'note' => $validated['moderation_note'] ?? null,
        ];
    }

    private function deleteComment(PublicComment $comment, ?string $reason, ?string $note): void
    {
        $comment->forceFill([
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ])->save();
        $comment->delete();
    }

    private function banVisitorModel(?AnonymousVisitor $visitor, string $reason, ?string $expiresAt): void
    {
        if (! $visitor) {
            return;
        }

        DB::table('interaction_bans')->updateOrInsert(
            ['ban_type' => 'visitor', 'value_hash' => hash('sha256', $visitor->visitor_uuid)],
            [
                'reason' => $reason,
                'created_by_admin_id' => auth('admin')->id(),
                'expires_at' => $expiresAt,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $visitor->update(['status' => 'banned', 'banned_at' => now()]);
    }

    private function summary(): array
    {
        return [
            'pending' => PublicComment::query()->where('status', 'pending')->count(),
            'reported' => PublicComment::query()->where('report_count', '>', 0)->count(),
            'auto_hidden' => PublicComment::query()->where('status', 'rejected')->where('moderation_reason', 'auto_reported')->count(),
            'approved_today' => PublicComment::query()->where('status', 'approved')->whereDate('moderated_at', today())->count(),
            'rejected_today' => PublicComment::query()->whereIn('status', ['rejected', 'spam'])->whereDate('moderated_at', today())->count(),
        ];
    }
}
