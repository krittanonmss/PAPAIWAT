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

        if (! $session && Auth::guard('admin')->viaRemember()) {
            AdminSession::query()->create([
                'admin_id' => $admin->id,
                'session_token_hash' => $sessionTokenHash,
                'ip_address' => $request->ip() ?? '',
                'user_agent' => $request->userAgent(),
                'last_seen_at' => now(),
                'expires_at' => now()->addMinutes((int) config('session.lifetime')),
                'created_at' => now(),
            ]);

            return $next($request);
        }

        if (! $session) {
            Auth::guard('admin')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่',
                ]);
        }

        return $next($request);
    }
}
