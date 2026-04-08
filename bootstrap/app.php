<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminGuestRedirect;
use App\Http\Middleware\EnsureAdminIsActive;
use App\Http\Middleware\RecordAdminActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticate::class,
            'admin.guest' => AdminGuestRedirect::class,
            'admin.active' => EnsureAdminIsActive::class,
            'admin.activity' => RecordAdminActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();