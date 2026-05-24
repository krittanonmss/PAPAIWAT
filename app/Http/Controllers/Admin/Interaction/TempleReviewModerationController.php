<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\TempleReview;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TempleReviewModerationController extends Controller
{
    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 20);
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'status' => $request->string('status')->toString(),
            'rating' => $request->string('rating')->toString(),
            'reported' => $request->string('reported')->toString(),
            'queue' => $request->string('queue')->toString(),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true)
            ? $filters['per_page']
            : $defaultPerPage;

        $reviews = TempleReview::query()
            ->with(['temple.content', 'visitor', 'reports.visitor', 'moderator'])
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
            ->when($filters['queue'] === 'pending', fn ($query) => $query->where('status', 'pending'))
            ->when($filters['queue'] === 'reported', fn ($query) => $query->where('report_count', '>', 0))
            ->when($filters['queue'] === 'auto_hidden', fn ($query) => $query->where('status', 'rejected')->where('moderation_reason', 'auto_reported'))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        $summary = $this->summary();

        return view('admin.interactions.reviews.index', compact('reviews', 'filters', 'summary'));
    }

    public function show(TempleReview $review): View
    {
        $review->load(['temple.content', 'visitor', 'reports.visitor', 'moderator']);

        return view('admin.interactions.reviews.show', compact('review'));
    }

    public function approve(Request $request, TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'approved');
        $interactionService->approveReview($review, $data['reason'], $data['note']);
        $this->notifyModeration('อนุมัติรีวิวแล้ว', 'รีวิววัด #'.$review->id.' ถูกอนุมัติโดยผู้ดูแล');

        return back()->with('success', 'อนุมัติรีวิวแล้ว');
    }

    public function reject(Request $request, TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'rejected');
        $interactionService->rejectReview($review, $data['reason'], $data['note']);
        $this->notifyModeration('ปฏิเสธรีวิวแล้ว', 'รีวิววัด #'.$review->id.' ถูกปฏิเสธโดยผู้ดูแล');

        return back()->with('success', 'ปฏิเสธรีวิวแล้ว');
    }

    public function spam(Request $request, TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $data = $this->moderationData($request, 'spam');
        $interactionService->markReviewSpam($review, $data['note']);
        $this->notifyModeration('ทำเครื่องหมายสแปมแล้ว', 'รีวิววัด #'.$review->id.' ถูกทำเครื่องหมายเป็นสแปม');

        return back()->with('success', 'ทำเครื่องหมายสแปมแล้ว');
    }

    public function bulk(Request $request, PublicInteractionService $interactionService): RedirectResponse
    {
        $validated = $request->validate([
            'review_ids' => ['required', 'array', 'min:1'],
            'review_ids.*' => ['integer', 'exists:temple_reviews,id'],
            'action' => ['required', 'string', 'in:approve,reject,spam,delete'],
            'moderation_reason' => ['nullable', 'string', 'max:40'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $reviews = TempleReview::query()->whereIn('id', $validated['review_ids'])->get();

        foreach ($reviews as $review) {
            match ($validated['action']) {
                'approve' => $interactionService->approveReview($review, $validated['moderation_reason'] ?? null, $validated['moderation_note'] ?? null),
                'reject' => $interactionService->rejectReview($review, $validated['moderation_reason'] ?? 'off_topic', $validated['moderation_note'] ?? null),
                'spam' => $interactionService->markReviewSpam($review, $validated['moderation_note'] ?? null),
                'delete' => $this->deleteReview($review, $interactionService, $validated['moderation_reason'] ?? 'other', $validated['moderation_note'] ?? null),
            };
        }
        $this->notifyModeration('จัดการรีวิวแบบกลุ่มแล้ว', 'จัดการรีวิว '.count($reviews).' รายการด้วย action '.$validated['action']);

        return back()->with('success', 'จัดการรีวิวที่เลือกเรียบร้อยแล้ว');
    }

    public function banVisitor(Request $request, TempleReview $review): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $this->banVisitorModel($review->visitor, $validated['reason'] ?? 'moderation', $validated['expires_at'] ?? null);
        $this->notifyModeration('บล็อก visitor แล้ว', 'Visitor จากรีวิว #'.$review->id.' ถูกบล็อก');

        return back()->with('success', 'บล็อก visitor แล้ว');
    }

    public function destroy(Request $request, TempleReview $review, PublicInteractionService $interactionService): RedirectResponse
    {
        $validated = $request->validate([
            'moderation_reason' => ['nullable', 'string', 'max:40'],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->deleteReview($review, $interactionService, $validated['moderation_reason'] ?? 'other', $validated['moderation_note'] ?? null);
        $this->notifyModeration('ลบรีวิวแล้ว', 'รีวิววัด #'.$review->id.' ถูกลบโดยผู้ดูแล');

        return back()->with('success', 'ลบรีวิวแล้ว');
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

    private function deleteReview(TempleReview $review, PublicInteractionService $interactionService, ?string $reason, ?string $note): void
    {
        $temple = $review->temple;
        $review->forceFill([
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ])->save();
        $review->delete();

        if ($temple) {
            $interactionService->syncTempleReviewStats($temple);
        }
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
            'pending' => TempleReview::query()->where('status', 'pending')->count(),
            'reported' => TempleReview::query()->where('report_count', '>', 0)->count(),
            'auto_hidden' => TempleReview::query()->where('status', 'rejected')->where('moderation_reason', 'auto_reported')->count(),
            'approved_today' => TempleReview::query()->where('status', 'approved')->whereDate('moderated_at', today())->count(),
            'rejected_today' => TempleReview::query()->whereIn('status', ['rejected', 'spam'])->whereDate('moderated_at', today())->count(),
        ];
    }
}
