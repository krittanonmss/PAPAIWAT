<?php

namespace App\Http\Middleware;

use App\Models\Admin\AdminSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        AdminSession::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();

        $admin = Auth::guard('admin')->user();
        $sessionTokenHash = hash('sha256', $request->session()->getId());
        $session = AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where('session_token_hash', $sessionTokenHash)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $session || ! $this->requestMatchesTrackedSession($session, $request)) {
            Auth::guard('admin')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => 'Session ไม่ปลอดภัยหรือหมดอายุ กรุณาเข้าสู่ระบบใหม่',
                ]);
        }

        return $next($request);
    }

    private function requestMatchesTrackedSession(AdminSession $session, Request $request): bool
    {
        return hash_equals($session->ip_address, $request->ip() ?? '')
            && hash_equals((string) $session->user_agent, (string) $request->userAgent());
    }
}
