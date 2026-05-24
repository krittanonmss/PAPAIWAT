<?php

namespace App\Http\Middleware;

use App\Services\Admin\AdminPreferenceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberAdminFilters
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || ! $request->routeIs('admin.*.index', 'admin.*.*.index')) {
            return $next($request);
        }

        $admin = $request->user('admin');
        $preferences = app(AdminPreferenceService::class)->forAdmin($admin);

        if (! (bool) ($preferences['tables.remember_filters'] ?? false)) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();
        $sessionKey = 'admin.remembered_filters.'.$routeName;
        $query = collect($request->query())
            ->except(['page'])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        if ($query !== []) {
            $request->session()->put($sessionKey, $query);

            return $next($request);
        }

        $remembered = $request->session()->get($sessionKey, []);

        if ($remembered !== []) {
            return redirect()->route($routeName, $remembered);
        }

        return $next($request);
    }
}
