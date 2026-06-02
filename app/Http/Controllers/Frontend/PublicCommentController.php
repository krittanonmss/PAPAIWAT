<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Temple\Temple;
use App\Models\Interaction\PublicComment;
use App\Services\Admin\AdminNotificationService;
use App\Services\Interaction\AnonymousVisitorService;
use App\Services\Interaction\PublicInteractionService;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PublicCommentController extends Controller
{
    public function storeTemple(
        Request $request,
        Temple $temple,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        abort_unless($temple->content?->status === 'published', 404);
        abort_unless((bool) SiteSettings::get('moderation', 'comments_enabled', true), 403);

        return $this->store($request, $temple, $visitorService, $interactionService);
    }

    public function storeArticle(
        Request $request,
        Article $article,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        abort_unless($article->content?->status === 'published', 404);
        abort_unless($article->allow_comments, 403);
        abort_unless((bool) SiteSettings::get('moderation', 'comments_enabled', true), 403);

        return $this->store($request, $article, $visitorService, $interactionService);
    }

    private function store(
        Request $request,
        object $commentable,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:public_comments,id'],
        ]);

        $this->validateParentComment($validated['parent_id'] ?? null, $commentable);

        $visitor = $visitorService->resolve($request);
        abort_if($visitor->isBanned(), 403);

        $comment = $interactionService->submitComment(
            $commentable,
            $visitor,
            $validated,
            $visitorService->hashNullable($request->ip()),
            $visitorService->hashNullable($request->userAgent())
        );

        app(AdminNotificationService::class)->notifyAdminsWithPermission(
            'interactions.moderate',
            'moderation',
            'มีความคิดเห็นรอตรวจสอบ',
            'ความคิดเห็น #'.$comment->id.' จากหน้าเว็บกำลังรอการตรวจสอบ'
        );

        return back()->with('success', 'ความคิดเห็นของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');
    }

    private function validateParentComment(mixed $parentId, object $commentable): void
    {
        if (blank($parentId)) {
            return;
        }

        $parentExists = PublicComment::query()
            ->whereKey((int) $parentId)
            ->where('status', 'approved')
            ->where('commentable_type', $commentable::class)
            ->where('commentable_id', $commentable->getKey())
            ->exists();

        if (! $parentExists) {
            throw ValidationException::withMessages([
                'parent_id' => 'ความคิดเห็นแม่ต้องเป็นความคิดเห็นที่อนุมัติแล้วในเนื้อหาเดียวกัน',
            ]);
        }
    }
}
