<?php

namespace App\Services\Admin\Auth;

use App\Models\Admin\Admin;
use App\Models\Admin\AdminSession;
use App\Models\Admin\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 300;

    private const MAX_ACTIVE_SESSIONS_PER_ADMIN = 5;

    public function login(string $email, string $password, bool $remember, Request $request): Admin
    {
        $email = $this->normalizeEmail($email);

        $this->ensureLoginIsNotRateLimited($email, $request);
        $this->purgeExpiredSessions();

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

            $this->hitLoginRateLimit($email, $request);

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

            $this->hitLoginRateLimit($email, $request);

            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        if (Hash::needsRehash($admin->password_hash, $this->hashOptions())) {
            $admin->forceFill([
                'password_hash' => Hash::make($password, $this->hashOptions()),
                'remember_token' => Str::random(60),
            ])->save();
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

            $this->hitLoginRateLimit($email, $request);

            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ไม่สามารถเข้าสู่ระบบได้',
            ]);
        }

        DB::transaction(function () use ($admin, $remember, $request): void {
            Auth::guard('admin')->login($admin, $remember);

            $request->session()->regenerate();

            $sessionId = $request->session()->getId();
            $sessionTokenHash = hash('sha256', $sessionId);

            AdminSession::query()
                ->where('session_token_hash', $sessionTokenHash)
                ->delete();

            AdminSession::query()->create([
                'admin_id' => $admin->id,
                'session_token_hash' => $sessionTokenHash,
                'ip_address' => $request->ip() ?? '',
                'user_agent' => $request->userAgent(),
                'last_seen_at' => now(),
                'expires_at' => now()->addMinutes((int) config('session.lifetime')),
                'created_at' => now(),
            ]);

            $this->pruneOldActiveSessions($admin, $sessionTokenHash);

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

        RateLimiter::clear($this->loginRateLimitKey($email, $request));

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

    private function ensureLoginIsNotRateLimited(string $email, Request $request): void
    {
        $key = $this->loginRateLimitKey($email, $request);

        if (! RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        $adminId = Admin::query()
            ->where('email', $email)
            ->value('id');

        $this->writeLoginLog(
            adminId: $adminId,
            email: $email,
            ipAddress: $request->ip(),
            status: 'failed',
            reason: 'rate_limited',
            userAgent: $request->userAgent(),
        );

        throw ValidationException::withMessages([
            'email' => "พยายามเข้าสู่ระบบผิดหลายครั้ง กรุณารอ {$seconds} วินาทีแล้วลองใหม่",
        ]);
    }

    private function hitLoginRateLimit(string $email, Request $request): void
    {
        RateLimiter::hit(
            $this->loginRateLimitKey($email, $request),
            self::LOGIN_DECAY_SECONDS
        );
    }

    private function loginRateLimitKey(string $email, Request $request): string
    {
        return 'admin-login:'.$this->normalizeEmail($email).'|'.$request->ip();
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function hashOptions(): array
    {
        if (config('hashing.driver') !== 'bcrypt') {
            return [];
        }

        return [
            'rounds' => (int) config('hashing.bcrypt.rounds', 12),
        ];
    }

    private function purgeExpiredSessions(): void
    {
        AdminSession::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();
    }

    private function pruneOldActiveSessions(Admin $admin, string $currentSessionTokenHash): void
    {
        $sessionIdsToKeep = AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('last_seen_at')
            ->orderByDesc('id')
            ->limit(self::MAX_ACTIVE_SESSIONS_PER_ADMIN)
            ->pluck('id');

        AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where('session_token_hash', '!=', $currentSessionTokenHash)
            ->whereNotIn('id', $sessionIdsToKeep)
            ->delete();
    }
}
