<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\InteractionReport;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
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
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true) ? $filters['per_page'] : $defaultPerPage;

        $reports = InteractionReport::query()
            ->with(['visitor', 'reportable'])
            ->when($filters['type'] !== '', fn ($query) => $query->where('reportable_type', $filters['type']))
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%'.$filters['search'].'%';

                $query->where(function ($query) use ($like) {
                    $query->where('reason', 'like', $like)
                        ->orWhere('ip_hash', 'like', $like)
                        ->orWhere('user_agent_hash', 'like', $like);
                });
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        $types = InteractionReport::query()
            ->distinct()
            ->orderBy('reportable_type')
            ->pluck('reportable_type');

        return view('admin.interactions.reports.index', compact('reports', 'filters', 'types'));
    }

    public function destroy(InteractionReport $report): RedirectResponse
    {
        $report->delete();
        app(AdminNotificationService::class)->notifyAdmins('moderation', 'ลบรายงานแล้ว', 'รายงาน #'.$report->id.' ถูกลบโดยผู้ดูแล');

        return back()->with('success', 'ลบรายงานแล้ว');
    }
}
