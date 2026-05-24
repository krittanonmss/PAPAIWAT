<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Admin\Auth\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function __construct(
        protected AdminAuthService $adminAuthService,
    ) {}

    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $admin = $this->adminAuthService->login(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            remember: $request->boolean('remember'),
            request: $request,
        );

        $theme = app(AdminPreferenceService::class)->forAdmin($admin)['display.theme'] ?? 'dark';
        $theme = in_array($theme, ['dark', 'light', 'system'], true) ? $theme : 'dark';

        return redirect()
            ->route('admin.dashboard')
            ->cookie('papaiwat_admin_theme', $theme, 60 * 24 * 365, null, null, false, false, false, 'lax');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->adminAuthService->logout($request);

        return redirect()
            ->route('admin.login')
            ->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
