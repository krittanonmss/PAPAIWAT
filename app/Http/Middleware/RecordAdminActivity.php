<?php

namespace App\Http\Middleware;

use App\Models\Admin\AdminActivityLog;
use App\Models\Admin\AdminSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RecordAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return $response;
        }

        AdminActivityLog::query()->create([
            'admin_id' => $admin->id,
            'action' => 'request_access',
            'target' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $sessionId = $request->session()->getId();
        $sessionTokenHash = hash('sha256', $sessionId);

        AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where('session_token_hash', $sessionTokenHash)
            ->update([
                'last_seen_at' => now(),
                'expires_at' => now()->addMinutes((int) config('session.lifetime')),
            ]);

        return $response;
    }
}