<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $admin = $request->user('admin')->loadMissing('role');
        $currentSessionHash = hash('sha256', $request->session()->getId());
        $activeSessions = $admin->sessions()
            ->where('expires_at', '>', now())
            ->orderByDesc('last_seen_at')
            ->limit(4)
            ->get();

        return view('admin.profile.edit', [
            'admin' => $admin,
            'activeSessions' => $activeSessions,
            'activeSessionsCount' => $admin->sessions()
                ->where('expires_at', '>', now())
                ->count(),
            'currentSessionHash' => $currentSessionHash,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user('admin');

        $validated = $request->validateWithBag('profile', [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('admins', 'username')->ignore($admin->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $admin->update($validated);

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $request->user('admin');

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                Password::min(12)->letters()->mixedCase()->numbers(),
                'confirmed',
            ],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password_hash)) {
            return back()
                ->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'], 'password')
                ->onlyInput();
        }

        $admin->forceFill([
            'password_hash' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        $currentSessionHash = hash('sha256', $request->session()->getId());

        AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where('session_token_hash', '!=', $currentSessionHash)
            ->delete();

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว ระบบได้ยกเลิก session อื่นของบัญชีนี้แล้ว');
    }

}
