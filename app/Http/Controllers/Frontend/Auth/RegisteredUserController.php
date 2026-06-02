<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $this->rememberIntendedUrl(request());

        return view('frontend.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);

        $user = User::query()->create($validated);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('favorites.index'))
            ->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว กรุณายืนยันอีเมลเพื่อเปิดการ sync รายการโปรด');
    }

    private function rememberIntendedUrl(Request $request): void
    {
        $redirect = (string) $request->query('redirect', '');

        if ($redirect === '' || ! str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return;
        }

        $request->session()->put('url.intended', url($redirect));
    }
}
