<?php

namespace App\Http\Controllers\Admin\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Interaction\AnonymousVisitor;
use App\Models\Interaction\InteractionBan;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteractionBanController extends Controller
{
    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 20);
        $filters = [
            'search' => trim($request->string('search')->toString()),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true) ? $filters['per_page'] : $defaultPerPage;

        $bans = InteractionBan::query()
            ->with('creator')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%'.$filters['search'].'%';

                $query->where(function ($query) use ($like) {
                    $query->where('reason', 'like', $like)
                        ->orWhere('value_hash', 'like', $like)
                        ->orWhere('ban_type', 'like', $like);
                });
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        return view('admin.interactions.bans.index', compact('bans', 'filters'));
    }

    public function destroy(InteractionBan $ban): RedirectResponse
    {
        if ($ban->ban_type === 'visitor') {
            AnonymousVisitor::query()
                ->get()
                ->first(fn (AnonymousVisitor $visitor) => hash('sha256', $visitor->visitor_uuid) === $ban->value_hash)
                ?->update(['status' => 'active', 'banned_at' => null]);
        }

        $ban->delete();
        app(AdminNotificationService::class)->notifyAdmins('moderation', 'ปลดบล็อกแล้ว', 'ปลดบล็อก '.$ban->ban_type.' โดยผู้ดูแล');

        return back()->with('success', 'ปลดบล็อกแล้ว');
    }
}
