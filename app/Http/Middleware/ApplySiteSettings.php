<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApplySiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $general = SiteSettings::group('general');

        $locale = (string) ($general['locale'] ?? config('app.locale'));
        if (in_array($locale, ['th', 'en'], true)) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
        }

        $timezone = (string) ($general['timezone'] ?? config('app.timezone'));
        if ($timezone !== '') {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
