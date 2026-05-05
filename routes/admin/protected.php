<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Content\ContentTemplatePreviewController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin.auth', 'admin.active', 'admin.activity'])
    ->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('admin.permission:dashboard.view')
            ->name('dashboard');

        Route::get('/content/{type}/template-preview', [ContentTemplatePreviewController::class, 'sample'])
            ->name('content.template-preview.sample');

        Route::get('/content/{type}/{content}/template-preview', ContentTemplatePreviewController::class)
            ->name('content.template-preview');

        require __DIR__ . '/access.php';
        require __DIR__ . '/content.php';
        require __DIR__ . '/layout.php';
    });
