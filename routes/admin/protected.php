<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Content\ContentTemplatePreviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin.auth', 'admin.active', 'admin.activity', 'admin.remember_filters'])
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

        Route::get('/preferences', [PreferenceController::class, 'edit'])
            ->name('preferences.edit');

        Route::put('/preferences', [PreferenceController::class, 'update'])
            ->name('preferences.update');

        Route::get('/settings', [SettingsController::class, 'edit'])
            ->middleware('admin.permission:settings.view')
            ->name('settings.edit');

        Route::put('/settings/{group}', [SettingsController::class, 'update'])
            ->whereIn('group', \App\Support\SiteSettings::GROUPS)
            ->middleware('admin.permission:settings.update')
            ->name('settings.update');

        Route::post('/settings/maintenance/cache', [SettingsController::class, 'clearCache'])
            ->middleware('admin.permission:settings.maintenance')
            ->name('settings.maintenance.cache');

        Route::post('/settings/maintenance/sitemap', [SettingsController::class, 'generateSitemap'])
            ->middleware('admin.permission:settings.maintenance')
            ->name('settings.maintenance.sitemap');

        Route::get('/content/{type}/template-preview', [ContentTemplatePreviewController::class, 'sample'])
            ->middleware('admin.permission:preview.view')
            ->name('content.template-preview.sample');

        Route::post('/content/{type}/template-preview/live', [ContentTemplatePreviewController::class, 'live'])
            ->middleware('admin.permission:preview.view')
            ->name('content.template-preview.live');

        Route::get('/content/{type}/{content}/template-preview', ContentTemplatePreviewController::class)
            ->middleware('admin.permission:preview.view')
            ->name('content.template-preview');

        require __DIR__.'/access.php';
        require __DIR__.'/content.php';
        require __DIR__.'/interactions.php';
        require __DIR__.'/layout.php';
    });
