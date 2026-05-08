<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Content\ContentTemplatePreviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin.auth', 'admin.active', 'admin.activity'])
    ->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('admin.permission:dashboard.view')
            ->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        Route::get('/content/{type}/template-preview', [ContentTemplatePreviewController::class, 'sample'])
            ->name('content.template-preview.sample');

        Route::get('/content/{type}/{content}/template-preview', ContentTemplatePreviewController::class)
            ->name('content.template-preview');

        require __DIR__.'/access.php';
        require __DIR__.'/content.php';
        require __DIR__.'/layout.php';
    });
