<?php

use App\Http\Controllers\Admin\Content\Layout\MenuController;
use App\Http\Controllers\Admin\Content\Layout\MenuItemController;
use App\Http\Controllers\Admin\Content\Layout\PageController;
use App\Http\Controllers\Admin\Content\Layout\PageSectionController;
use App\Http\Controllers\Admin\Content\Layout\TemplateController;
use App\Http\Controllers\Admin\Content\Layout\FooterSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('content/menus')->name('content.menus.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->middleware('admin.permission:menus.view')->name('index');
    Route::get('/create', [MenuController::class, 'create'])->middleware('admin.permission:menus.create')->name('create');
    Route::post('/', [MenuController::class, 'store'])->middleware('admin.permission:menus.create')->name('store');
    Route::get('/{menu}', [MenuController::class, 'show'])->middleware('admin.permission:menus.view')->name('show');
    Route::get('/{menu}/edit', [MenuController::class, 'edit'])->middleware('admin.permission:menus.update')->name('edit');
    Route::put('/{menu}', [MenuController::class, 'update'])->middleware('admin.permission:menus.update')->name('update');
    Route::delete('/{menu}', [MenuController::class, 'destroy'])->middleware('admin.permission:menus.delete')->name('destroy');

    Route::get('/{menu}/items/create', [MenuItemController::class, 'create'])->middleware('admin.permission:menus.create')->name('items.create');
    Route::post('/{menu}/items', [MenuItemController::class, 'store'])->middleware('admin.permission:menus.create')->name('items.store');
    Route::get('/{menu}/items/{menuItem}/edit', [MenuItemController::class, 'edit'])->middleware('admin.permission:menus.update')->name('items.edit');
    Route::put('/{menu}/items/{menuItem}', [MenuItemController::class, 'update'])->middleware('admin.permission:menus.update')->name('items.update');
    Route::delete('/{menu}/items/{menuItem}', [MenuItemController::class, 'destroy'])->middleware('admin.permission:menus.delete')->name('items.destroy');
});

Route::get('content/footer', [FooterSettingsController::class, 'edit'])
    ->middleware('admin.permission:menus.view')
    ->name('content.footer.edit');

Route::put('content/footer', [FooterSettingsController::class, 'update'])
    ->middleware('admin.permission:menus.update')
    ->name('content.footer.update');

Route::prefix('content/pages')->name('content.pages.')->group(function () {
    Route::get('/', [PageController::class, 'index'])->middleware('admin.permission:pages.view')->name('index');
    Route::get('/create', [PageController::class, 'create'])->middleware('admin.permission:pages.create')->name('create');
    Route::post('/preview', [PageController::class, 'previewCreate'])->middleware('admin.permission:pages.create')->name('preview-create');
    Route::post('/', [PageController::class, 'store'])->middleware('admin.permission:pages.create')->name('store');
    Route::get('/sections/media-picker/images', [PageSectionController::class, 'mediaPicker'])->middleware('admin.permission:pages.view')->name('sections.media-picker');
    Route::post('/{page}/preview', [PageController::class, 'preview'])->middleware('admin.permission:pages.update')->name('preview');
    Route::get('/{page}', [PageController::class, 'show'])->middleware('admin.permission:pages.view')->name('show');
    Route::get('/{page}/edit', [PageController::class, 'edit'])->middleware('admin.permission:pages.update')->name('edit');
    Route::put('/{page}', [PageController::class, 'update'])->middleware('admin.permission:pages.update')->name('update');
    Route::delete('/{page}', [PageController::class, 'destroy'])->middleware('admin.permission:pages.delete')->name('destroy');

    Route::get('/{page}/sections/create', [PageSectionController::class, 'create'])->middleware('admin.permission:pages.create')->name('sections.create');
    Route::post('/{page}/sections/preview', [PageSectionController::class, 'preview'])->middleware('admin.permission:pages.view')->name('sections.preview');
    Route::post('/{page}/sections', [PageSectionController::class, 'store'])->middleware('admin.permission:pages.create')->name('sections.store');
    Route::get('/{page}/sections/{section}/edit', [PageSectionController::class, 'edit'])->middleware('admin.permission:pages.update')->name('sections.edit');
    Route::put('/{page}/sections/{section}', [PageSectionController::class, 'update'])->middleware('admin.permission:pages.update')->name('sections.update');
    Route::delete('/{page}/sections/{section}', [PageSectionController::class, 'destroy'])->middleware('admin.permission:pages.delete')->name('sections.destroy');
});

Route::prefix('content/menus/{menu}/items')
    ->name('content.menu-items.')
    ->group(function () {
        Route::get('/create', [MenuItemController::class, 'create'])->name('create');
        Route::post('/', [MenuItemController::class, 'store'])->name('store');
        Route::get('/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('edit');
        Route::put('/{menuItem}', [MenuItemController::class, 'update'])->name('update');
        Route::delete('/{menuItem}', [MenuItemController::class, 'destroy'])->name('destroy');
    });

Route::prefix('content/templates')
    ->name('content.templates.')
    ->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
        Route::get('/{template}', [TemplateController::class, 'show'])->name('show');
        Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [TemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('destroy');
    });
