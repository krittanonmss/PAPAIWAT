<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $this->rememberIntendedUrl($request);

        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->intended(route('favorites.index'));
        }

        return view('frontend.auth.verify-email');
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
