<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\InteractionReport;
use App\Models\Interaction\PublicComment;
use App\Models\Interaction\TempleReview;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Interaction\PublicInteractionService;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteractionReportModerationController extends Controller
{
    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 20);
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'type' => $request->string('type')->toString(),
            'visitor_id' => trim($request->string('visitor_id')->toString()),
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true) ? $filters['per_page'] : $defaultPerPage;

        $reports = InteractionReport::query()
            ->with([
                'visitor',
                'reportable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        PublicComment::class => ['commentable.content'],
                        TempleReview::class => ['temple.content'],
                    ]);
                },
            ])
            ->when($filters['type'] !== '', fn ($query) => $query->where('reportable_type', $filters['type']))
            ->when($filters['visitor_id'] !== '', fn ($query) => $query->where('anonymous_visitor_id', $filters['visitor_id']))
            ->when($filters['date_from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%'.$filters['search'].'%';

                $query->where(function ($query) use ($like) {
                    $query->where('reason', 'like', $like)
                        ->orWhere('ip_hash', 'like', $like)
                        ->orWhere('user_agent_hash', 'like', $like)
                        ->orWhere('reportable_id', 'like', $like);
                });
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        $types = InteractionReport::query()
            ->distinct()
            ->orderBy('reportable_type')
            ->pluck('reportable_type');

        $visitors = AnonymousVisitor::query()
            ->whereHas('reports')
            ->latest('last_seen_at')
            ->limit(100)
            ->get();

        return view('admin.interactions.reports.index', compact('reports', 'filters', 'types', 'visitors'));
    }

    public function destroy(InteractionReport $report, PublicInteractionService $interactionService): RedirectResponse
    {
        $reportable = $report->reportable;
        $report->delete();

        if ($reportable) {
            $interactionService->syncReportCount($reportable);
        }

        app(AdminNotificationService::class)->notifyAdmins('moderation', 'ลบรายงานแล้ว', 'รายงาน #'.$report->id.' ถูกลบโดยผู้ดูแล');

        return back()->with('success', 'ลบรายงานแล้ว');
    }
}
