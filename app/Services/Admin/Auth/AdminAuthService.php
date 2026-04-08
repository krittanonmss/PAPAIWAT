<?php

namespace App\Services\Admin\Auth;

use App\Models\Admin;
use App\Models\AdminSession;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    public function login(string $email, string $password, Request $request): Admin
    {
        $admin = Admin::query()
            ->where('email', $email)
            ->first();

        if (! $admin) {
            $this->writeLoginLog(
                adminId: null,
                email: $email,
                ipAddress: $request->ip(),
                status: 'failed',
                reason: 'user_not_found',
                userAgent: $request->userAgent(),
            );

            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        if (! Hash::check($password, $admin->password_hash)) {
            $this->writeLoginLog(
                adminId: $admin->id,
                email: $email,
                ipAddress: $request->ip(),
                status: 'failed',
                reason: 'invalid_password',
                userAgent: $request->userAgent(),
            );

            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        if ($admin->status !== 'active') {
            $this->writeLoginLog(
                adminId: $admin->id,
                email: $email,
                ipAddress: $request->ip(),
                status: 'failed',
                reason: 'inactive_or_suspended',
                userAgent: $request->userAgent(),
            );

            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ไม่สามารถเข้าสู่ระบบได้',
            ]);
        }

        DB::transaction(function () use ($admin, $request): void {
            Auth::guard('admin')->login($admin);

            $request->session()->regenerate();

            $sessionId = $request->session()->getId();
            $sessionTokenHash = hash('sha256', $sessionId);

            AdminSession::query()->create([
                'admin_id' => $admin->id,
                'session_token_hash' => $sessionTokenHash,
                'ip_address' => $request->ip() ?? '',
                'user_agent' => $request->userAgent(),
                'last_seen_at' => now(),
                'expires_at' => now()->addMinutes((int) config('session.lifetime')),
                'created_at' => now(),
            ]);

            $admin->forceFill([
                'last_login_at' => now(),
            ])->save();

            $this->writeLoginLog(
                adminId: $admin->id,
                email: $admin->email,
                ipAddress: $request->ip(),
                status: 'success',
                reason: null,
                userAgent: $request->userAgent(),
            );
        });

        return $admin->fresh();
    }

    public function logout(Request $request): void
    {
        $admin = Auth::guard('admin')->user();
        $sessionId = $request->session()->getId();
        $sessionTokenHash = hash('sha256', $sessionId);

        if ($admin) {
            AdminSession::query()
                ->where('admin_id', $admin->id)
                ->where('session_token_hash', $sessionTokenHash)
                ->delete();
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    protected function writeLoginLog(
        ?int $adminId,
        string $email,
        ?string $ipAddress,
        string $status,
        ?string $reason,
        ?string $userAgent,
    ): void {
        LoginLog::query()->create([
            'admin_id' => $adminId,
            'email' => $email,
            'ip_address' => $ipAddress ?? '',
            'status' => $status,
            'reason' => $reason,
            'user_agent' => $userAgent,
            'created_at' => now(),
        ]);
    }
}