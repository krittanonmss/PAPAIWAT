<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::patch('/users/{admin}/status', [UserManagementController::class, 'updateStatus'])
    ->middleware('admin.permission:users.update')
    ->name('users.status.update');

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->middleware('admin.permission:users.view')->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])->middleware('admin.permission:users.create')->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->middleware('admin.permission:users.create')->name('store');
    Route::get('/{admin}', [UserManagementController::class, 'show'])->middleware('admin.permission:users.view')->name('show');
    Route::get('/{admin}/edit', [UserManagementController::class, 'edit'])->middleware('admin.permission:users.update')->name('edit');
    Route::put('/{admin}', [UserManagementController::class, 'update'])->middleware('admin.permission:users.update')->name('update');
    Route::delete('/{admin}', [UserManagementController::class, 'destroy'])->middleware('admin.permission:users.delete')->name('destroy');
});

Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->middleware('admin.permission:roles.view')->name('index');
    Route::get('/create', [RoleController::class, 'create'])->middleware('admin.permission:roles.create')->name('create');
    Route::post('/', [RoleController::class, 'store'])->middleware('admin.permission:roles.create')->name('store');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('admin.permission:roles.update')->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->middleware('admin.permission:roles.update')->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('admin.permission:roles.delete')->name('destroy');

    Route::get('/{role}/permissions', [RolePermissionController::class, 'edit'])
        ->middleware('admin.permission:roles.permissions')
        ->name('permissions.edit');

    Route::put('/{role}/permissions', [RolePermissionController::class, 'update'])
        ->middleware('admin.permission:roles.permissions')
        ->name('permissions.update');
});

Route::prefix('permissions')->name('permissions.')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->middleware('admin.permission:permissions.view')->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->middleware('admin.permission:permissions.create')->name('create');
    Route::post('/', [PermissionController::class, 'store'])->middleware('admin.permission:permissions.create')->name('store');
    Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->middleware('admin.permission:permissions.update')->name('edit');
    Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('admin.permission:permissions.update')->name('update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('admin.permission:permissions.delete')->name('destroy');
});