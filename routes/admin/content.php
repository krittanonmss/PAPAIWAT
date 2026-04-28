<?php

use App\Http\Controllers\Admin\Content\Article\ArticleController;
use App\Http\Controllers\Admin\Content\Article\ArticleTagController;
use App\Http\Controllers\Admin\Content\CategoryController;
use App\Http\Controllers\Admin\Content\Media\MediaController;
use App\Http\Controllers\Admin\Content\Media\MediaFolderController;
use App\Http\Controllers\Admin\Content\Temple\TempleController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->middleware('admin.permission:categories.view')->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->middleware('admin.permission:categories.create')->name('create');
    Route::post('/', [CategoryController::class, 'store'])->middleware('admin.permission:categories.create')->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->middleware('admin.permission:categories.update')->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->middleware('admin.permission:categories.update')->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('admin.permission:categories.delete')->name('destroy');
});

Route::prefix('media-folders')->name('media-folders.')->group(function () {
    Route::get('/', [MediaFolderController::class, 'index'])->middleware('admin.permission:media.view')->name('index');
    Route::get('/create', [MediaFolderController::class, 'create'])->middleware('admin.permission:media.create')->name('create');
    Route::post('/', [MediaFolderController::class, 'store'])->middleware('admin.permission:media.create')->name('store');
    Route::get('/{mediaFolder}/edit', [MediaFolderController::class, 'edit'])->middleware('admin.permission:media.update')->name('edit');
    Route::put('/{mediaFolder}', [MediaFolderController::class, 'update'])->middleware('admin.permission:media.update')->name('update');
    Route::delete('/{mediaFolder}', [MediaFolderController::class, 'destroy'])->middleware('admin.permission:media.delete')->name('destroy');
});

Route::prefix('media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->middleware('admin.permission:media.view')->name('index');
    Route::get('/create', [MediaController::class, 'create'])->middleware('admin.permission:media.create')->name('create');
    Route::post('/', [MediaController::class, 'store'])->middleware('admin.permission:media.create')->name('store');
    Route::get('/{media}/edit', [MediaController::class, 'edit'])->middleware('admin.permission:media.update')->name('edit');
    Route::put('/{media}', [MediaController::class, 'update'])->middleware('admin.permission:media.update')->name('update');
    Route::delete('/{media}', [MediaController::class, 'destroy'])->middleware('admin.permission:media.delete')->name('destroy');
});

Route::prefix('temples')->name('temples.')->group(function () {
    Route::get('/', [TempleController::class, 'index'])->middleware('admin.permission:temples.view')->name('index');
    Route::get('/create', [TempleController::class, 'create'])->middleware('admin.permission:temples.create')->name('create');
    Route::post('/', [TempleController::class, 'store'])->middleware('admin.permission:temples.create')->name('store');
    Route::get('/{temple}', [TempleController::class, 'show'])->middleware('admin.permission:temples.view')->name('show');
    Route::get('/{temple}/edit', [TempleController::class, 'edit'])->middleware('admin.permission:temples.update')->name('edit');
    Route::put('/{temple}', [TempleController::class, 'update'])->middleware('admin.permission:temples.update')->name('update');
    Route::delete('/{temple}', [TempleController::class, 'destroy'])->middleware('admin.permission:temples.delete')->name('destroy');
});

Route::prefix('articles')->name('content.articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->middleware('admin.permission:articles.view')->name('index');
    Route::get('/create', [ArticleController::class, 'create'])->middleware('admin.permission:articles.create')->name('create');
    Route::post('/', [ArticleController::class, 'store'])->middleware('admin.permission:articles.create')->name('store');
    Route::get('/{article}', [ArticleController::class, 'show'])->middleware('admin.permission:articles.view')->name('show');
    Route::get('/{article}/edit', [ArticleController::class, 'edit'])->middleware('admin.permission:articles.update')->name('edit');
    Route::put('/{article}', [ArticleController::class, 'update'])->middleware('admin.permission:articles.update')->name('update');
    Route::delete('/{article}', [ArticleController::class, 'destroy'])->middleware('admin.permission:articles.delete')->name('destroy');
});

Route::prefix('content/article-tags')->name('content.article-tags.')->group(function () {
    Route::get('/', [ArticleTagController::class, 'index'])->middleware('admin.permission:articles.view')->name('index');
    Route::get('/create', [ArticleTagController::class, 'create'])->middleware('admin.permission:articles.create')->name('create');
    Route::post('/', [ArticleTagController::class, 'store'])->middleware('admin.permission:articles.create')->name('store');
    Route::get('/{articleTag}/edit', [ArticleTagController::class, 'edit'])->middleware('admin.permission:articles.update')->name('edit');
    Route::put('/{articleTag}', [ArticleTagController::class, 'update'])->middleware('admin.permission:articles.update')->name('update');
    Route::delete('/{articleTag}', [ArticleTagController::class, 'destroy'])->middleware('admin.permission:articles.delete')->name('destroy');
});