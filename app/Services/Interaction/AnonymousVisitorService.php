<?php

namespace App\Services\Interaction;

use App\Models\Interaction\AnonymousVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AnonymousVisitorService
{
    private const COOKIE_NAME = 'papaiwat_visitor_id';
    private const COOKIE_MINUTES = 60 * 24 * 365;

    public function resolve(Request $request): AnonymousVisitor
    {
        $visitorUuid = $request->cookie(self::COOKIE_NAME)
            ?: $request->session()->get(self::COOKIE_NAME);

        if (! is_string($visitorUuid) || ! Str::isUuid($visitorUuid)) {
            $visitorUuid = (string) Str::uuid();
        }

        $visitor = AnonymousVisitor::query()->firstOrCreate(
            ['visitor_uuid' => $visitorUuid],
            [
                'ip_hash' => $this->hashNullable($request->ip()),
                'user_agent_hash' => $this->hashNullable($request->userAgent()),
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        $visitor->update([
            'last_seen_at' => now(),
            'ip_hash' => $visitor->ip_hash ?: $this->hashNullable($request->ip()),
            'user_agent_hash' => $visitor->user_agent_hash ?: $this->hashNullable($request->userAgent()),
        ]);

        Cookie::queue(cookie(
            name: self::COOKIE_NAME,
            value: $visitor->visitor_uuid,
            minutes: self::COOKIE_MINUTES,
            path: '/',
            domain: null,
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax'
        ));
        $request->session()->put(self::COOKIE_NAME, $visitor->visitor_uuid);

        return $visitor->refresh();
    }

    public function findExisting(Request $request): ?AnonymousVisitor
    {
        $visitorUuid = $request->cookie(self::COOKIE_NAME)
            ?: $request->session()->get(self::COOKIE_NAME);

        if (! is_string($visitorUuid) || ! Str::isUuid($visitorUuid)) {
            return null;
        }

        return AnonymousVisitor::query()
            ->where('visitor_uuid', $visitorUuid)
            ->first();
    }

    public function hashNullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : hash('sha256', $value.'|'.config('app.key'));
    }
}
