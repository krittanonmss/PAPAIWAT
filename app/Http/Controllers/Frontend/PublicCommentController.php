<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Article\Article;
use App\Models\Content\Temple\Temple;
use App\Services\Interaction\AnonymousVisitorService;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicCommentController extends Controller
{
    public function storeTemple(
        Request $request,
        Temple $temple,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        abort_unless($temple->content?->status === 'published', 404);

        return $this->store($request, $temple, $visitorService, $interactionService);
    }

    public function storeArticle(
        Request $request,
        Article $article,
        AnonymousVisitorService $visitorService,
        PublicInteractionService $interactionService
    ): RedirectResponse {
        abort_unless($article->content?->status === 'published', 404);

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

        $visitor = $visitorService->resolve($request);

        $interactionService->submitComment(
            $commentable,
            $visitor,
            $validated,
            $visitorService->hashNullable($request->ip()),
            $visitorService->hashNullable($request->userAgent())
        );

        return back()->with('success', 'ความคิดเห็นของท่านกำลังรอการตรวจสอบก่อนเผยแพร่');
    }
}
