<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Content\Media\MediaFolderController;
use App\Http\Controllers\Admin\Content\Media\MediaController;

Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'admin.active', 'admin.activity'])->group(function () {

    Route::prefix('media-folders')->name('media-folders.')->group(function () {
        Route::get('/', [MediaFolderController::class, 'index'])
            ->middleware('admin.permission:media.view')->name('index');
        Route::get('/create', [MediaFolderController::class, 'create'])
            ->middleware('admin.permission:media.create')->name('create');
        Route::post('/', [MediaFolderController::class, 'store'])
            ->middleware('admin.permission:media.create')->name('store');
        Route::get('/{mediaFolder}/edit', [MediaFolderController::class, 'edit'])
            ->middleware('admin.permission:media.update')->name('edit');
        Route::put('/{mediaFolder}', [MediaFolderController::class, 'update'])
            ->middleware('admin.permission:media.update')->name('update');
        Route::delete('/{mediaFolder}', [MediaFolderController::class, 'destroy'])
            ->middleware('admin.permission:media.delete')->name('destroy');
    });

    Route::prefix('media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])
        ->middleware('admin.permission:media.view')->name('index');

    Route::get('/create', [MediaController::class, 'create'])
        ->middleware('admin.permission:media.create')->name('create');

    Route::post('/', [MediaController::class, 'store'])
        ->middleware('admin.permission:media.create')->name('store');

    Route::post('/{media}/regenerate-variants', [MediaController::class, 'regenerateVariants'])
        ->middleware('admin.permission:media.update')
        ->name('regenerate-variants');

    Route::get('/{media}/edit', [MediaController::class, 'edit'])
        ->middleware('admin.permission:media.update')->name('edit');

    Route::put('/{media}', [MediaController::class, 'update'])
        ->middleware('admin.permission:media.update')->name('update');

    Route::delete('/{media}', [MediaController::class, 'destroy'])
        ->middleware('admin.permission:media.delete')->name('destroy');
    });
});