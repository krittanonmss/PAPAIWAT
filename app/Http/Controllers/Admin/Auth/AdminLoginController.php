<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Services\Admin\Auth\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function __construct(
        protected AdminAuthService $adminAuthService,
    ) {
    }

    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $this->adminAuthService->login(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            request: $request,
        );

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->adminAuthService->logout($request);

        return redirect()
            ->route('admin.login')
            ->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}