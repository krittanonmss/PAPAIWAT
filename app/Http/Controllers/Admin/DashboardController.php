<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\LoginLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $adminCount = Admin::query()->count();

        $loginLogs = LoginLog::query()
            ->with('admin')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard.dashboard', [
            'adminCount' => $adminCount,
            'loginLogs' => $loginLogs,
        ]);
    }
}