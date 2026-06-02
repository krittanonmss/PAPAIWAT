<?php

namespace App\Services\Interaction;

use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleStat;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\InteractionReport;
use App\Models\Interaction\PublicComment;
use App\Models\Interaction\TempleReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Support\SiteSettings;

class PublicInteractionService
{
    public function submitTempleReview(
        Temple $temple,
        AnonymousVisitor $visitor,
        array $validated,
        ?string $ipHash,
        ?string $userAgentHash
    ): TempleReview {
        $review = TempleReview::withTrashed()
            ->where('temple_id', $temple->id)
            ->where('anonymous_visitor_id', $visitor->id)
            ->first();

        $values = [
            'rating' => (int) $validated['rating'],
            'display_name' => $validated['display_name'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'status' => 'pending',
            'ip_hash' => $ipHash,
            'user_agent_hash' => $userAgentHash,
            'approved_at' => null,
            'deleted_at' => null,
        ];

        if ($review) {
            $review->restore();
            $review->update($values);
        } else {
            $review = TempleReview::query()->create([
                'temple_id' => $temple->id,
                'anonymous_visitor_id' => $visitor->id,
            ] + $values);
        }

        $this->syncTempleReviewStats($temple);

        return $review->refresh();
    }

    public function submitComment(
        Model $commentable,
        AnonymousVisitor $visitor,
        array $validated,
        ?string $ipHash,
        ?string $userAgentHash
    ): PublicComment {
        return PublicComment::query()->create([
            'anonymous_visitor_id' => $visitor->id,
            'commentable_type' => $commentable::class,
            'commentable_id' => $commentable->getKey(),
            'parent_id' => $validated['parent_id'] ?? null,
            'display_name' => $validated['display_name'] ?? null,
            'body' => $validated['body'],
            'status' => 'pending',
            'ip_hash' => $ipHash,
            'user_agent_hash' => $userAgentHash,
            'approved_at' => null,
        ]);
    }

    public function approveReview(TempleReview $review, ?string $reason = null, ?string $note = null): TempleReview
    {
        $review->update([
            'status' => 'approved',
            'approved_at' => now(),
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        $this->syncTempleReviewStats($review->temple);

        return $review->refresh();
    }

    public function rejectReview(TempleReview $review, ?string $reason = null, ?string $note = null): TempleReview
    {
        $review->update([
            'status' => 'rejected',
            'approved_at' => null,
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        $this->syncTempleReviewStats($review->temple);

        return $review->refresh();
    }

    public function markReviewSpam(TempleReview $review, ?string $note = null): TempleReview
    {
        $review->update([
            'status' => 'spam',
            'approved_at' => null,
            'moderation_reason' => 'spam',
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        $this->syncTempleReviewStats($review->temple);

        return $review->refresh();
    }

    public function approveComment(PublicComment $comment, ?string $reason = null, ?string $note = null): PublicComment
    {
        $comment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        return $comment->refresh();
    }

    public function rejectComment(PublicComment $comment, ?string $reason = null, ?string $note = null): PublicComment
    {
        $comment->update([
            'status' => 'rejected',
            'approved_at' => null,
            'moderation_reason' => $reason,
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        return $comment->refresh();
    }

    public function markCommentSpam(PublicComment $comment, ?string $note = null): PublicComment
    {
        $comment->update([
            'status' => 'spam',
            'approved_at' => null,
            'moderation_reason' => 'spam',
            'moderation_note' => $note,
            'moderated_by_admin_id' => auth('admin')->id(),
            'moderated_at' => now(),
        ]);

        return $comment->refresh();
    }

    public function report(Model $reportable, AnonymousVisitor $visitor, ?string $reason, ?string $ipHash, ?string $userAgentHash): void
    {
        DB::transaction(function () use ($reportable, $visitor, $reason, $ipHash, $userAgentHash) {
            InteractionReport::query()->firstOrCreate(
                [
                    'anonymous_visitor_id' => $visitor->id,
                    'reportable_type' => $reportable::class,
                    'reportable_id' => $reportable->getKey(),
                ],
                [
                    'reason' => $reason,
                    'ip_hash' => $ipHash,
                    'user_agent_hash' => $userAgentHash,
                ]
            );

            $reportCount = $this->syncReportCount($reportable);

            $updates = [];

            $autoHideThreshold = max(1, (int) SiteSettings::get('moderation', 'auto_hide_report_threshold', 3));

            if ($reportCount >= $autoHideThreshold) {
                $updates['status'] = 'rejected';
                $updates['approved_at'] = null;
                $updates['moderation_reason'] = 'auto_reported';
                $updates['moderation_note'] = 'Auto-hidden after '.$reportCount.' reports.';
                $updates['moderated_at'] = now();
            }

            $reportable->update($updates);

            if ($reportable instanceof TempleReview && $reportCount >= $autoHideThreshold) {
                $this->syncTempleReviewStats($reportable->temple);
            }
        });
    }

    public function syncReportCount(Model $reportable): int
    {
        $reportCount = InteractionReport::query()
            ->where('reportable_type', $reportable::class)
            ->where('reportable_id', $reportable->getKey())
            ->count();

        $reportable->update(['report_count' => $reportCount]);

        return $reportCount;
    }

    public function syncTempleReviewStats(Temple $temple): void
    {
        $approvedReviews = TempleReview::query()
            ->where('temple_id', $temple->id)
            ->where('status', 'approved');

        TempleStat::query()->updateOrCreate(
            ['temple_id' => $temple->id],
            [
                'review_count' => (clone $approvedReviews)->count(),
                'average_rating' => round((float) (clone $approvedReviews)->avg('rating'), 2),
                'updated_at' => now(),
            ]
        );
    }

}
