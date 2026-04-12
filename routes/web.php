<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\Content\CategoryController;
use App\Http\Controllers\Admin\Content\Media\MediaFolderController;
use App\Http\Controllers\Admin\Content\Media\MediaController;

Route::get('/', function () {
    return view('admin.auth.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['admin.auth', 'admin.active', 'admin.activity'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::patch('/users/{admin}/status', [UserManagementController::class, 'updateStatus'])
                ->name('users.status.update');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])
                ->middleware('admin.permission:users.view')
                ->name('index');

            Route::get('/create', [UserManagementController::class, 'create'])
                ->middleware('admin.permission:users.create')
                ->name('create');

            Route::post('/', [UserManagementController::class, 'store'])
                ->middleware('admin.permission:users.create')
                ->name('store');

            Route::get('/{admin}', [UserManagementController::class, 'show'])
                ->middleware('admin.permission:users.view')
                ->name('show');

            Route::get('/{admin}/edit', [UserManagementController::class, 'edit'])
                ->middleware('admin.permission:users.update')
                ->name('edit');

            Route::put('/{admin}', [UserManagementController::class, 'update'])
                ->middleware('admin.permission:users.update')
                ->name('update');

            Route::delete('/{admin}', [UserManagementController::class, 'destroy'])
                ->middleware('admin.permission:users.delete')
                ->name('destroy');

        });


        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->middleware('admin.permission:roles.view')
                ->name('index');

            Route::get('/create', [RoleController::class, 'create'])
                ->middleware('admin.permission:roles.create')
                ->name('create');

            Route::post('/', [RoleController::class, 'store'])
                ->middleware('admin.permission:roles.create')
                ->name('store');

            Route::get('/{role}/edit', [RoleController::class, 'edit'])
                ->middleware('admin.permission:roles.update')
                ->name('edit');

            Route::put('/{role}', [RoleController::class, 'update'])
                ->middleware('admin.permission:roles.update')
                ->name('update');

            Route::delete('/{role}', [RoleController::class, 'destroy'])
                ->middleware('admin.permission:roles.delete')
                ->name('destroy');

            Route::get('/{role}/permissions', [RolePermissionController::class, 'edit'])
                ->middleware('admin.permission:roles.permissions')
                ->name('permissions.edit');

            Route::put('/{role}/permissions', [RolePermissionController::class, 'update'])
                ->middleware('admin.permission:roles.permissions')
                ->name('permissions.update');
        });

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])
                ->middleware('admin.permission:permissions.view')
                ->name('index');

            Route::get('/create', [PermissionController::class, 'create'])
                ->middleware('admin.permission:permissions.create')
                ->name('create');

            Route::post('/', [PermissionController::class, 'store'])
                ->middleware('admin.permission:permissions.create')
                ->name('store');

            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])
                ->middleware('admin.permission:permissions.update')
                ->name('edit');

            Route::put('/{permission}', [PermissionController::class, 'update'])
                ->middleware('admin.permission:permissions.update')
                ->name('update');

            Route::delete('/{permission}', [PermissionController::class, 'destroy'])
                ->middleware('admin.permission:permissions.delete')
                ->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])
                ->name('index');

            Route::get('/create', [CategoryController::class, 'create'])
                ->name('create');

            Route::post('/', [CategoryController::class, 'store'])
                ->name('store');

            Route::get('/{category}/edit', [CategoryController::class, 'edit'])
                ->name('edit');

            Route::put('/{category}', [CategoryController::class, 'update'])
                ->name('update');

            Route::delete('/{category}', [CategoryController::class, 'destroy'])
                ->name('destroy');
        });

        Route::prefix('media-folders')->name('media-folders.')->group(function () {
            Route::get('/', [MediaFolderController::class, 'index'])->name('index');
            Route::get('/create', [MediaFolderController::class, 'create'])->name('create');
            Route::post('/', [MediaFolderController::class, 'store'])->name('store');
            Route::get('/{mediaFolder}/edit', [MediaFolderController::class, 'edit'])->name('edit');
            Route::put('/{mediaFolder}', [MediaFolderController::class, 'update'])->name('update');
            Route::delete('/{mediaFolder}', [MediaFolderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::get('/create', [MediaController::class, 'create'])->name('create');
            Route::post('/', [MediaController::class, 'store'])->name('store');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
        });
    });
});