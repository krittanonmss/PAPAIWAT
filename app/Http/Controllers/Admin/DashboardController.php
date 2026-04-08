<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\LoginLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $loginLogs = LoginLog::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        $activityLogs = AdminActivityLog::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard.dashboard', [
            'loginLogs' => $loginLogs,
            'activityLogs' => $activityLogs,
        ]);
    }
}