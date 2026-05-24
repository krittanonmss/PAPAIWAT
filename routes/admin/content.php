<?php

use App\Http\Controllers\Admin\Content\Article\ArticleTagController;
use App\Http\Controllers\Admin\Content\CategoryController;
use App\Http\Controllers\Admin\Content\ContentLookupController;
use App\Http\Controllers\Admin\Content\Temple\TempleController;
use App\Http\Controllers\Admin\Content\Article\ArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('lookups')->name('lookups.')->group(function () {
    Route::get('/categories', [ContentLookupController::class, 'categories'])
        ->middleware('admin.permission:categories.view')
        ->name('categories');

    Route::get('/article-tags', [ContentLookupController::class, 'articleTags'])
        ->middleware('admin.permission:articles.view')
        ->name('article-tags');

    Route::get('/articles', [ContentLookupController::class, 'articles'])
        ->middleware('admin.permission:articles.view')
        ->name('articles');

    Route::get('/temples', [ContentLookupController::class, 'temples'])
        ->middleware('admin.permission:temples.view')
        ->name('temples');

    Route::get('/facilities', [ContentLookupController::class, 'facilities'])
        ->middleware('admin.permission:temples.view')
        ->name('facilities');

    Route::get('/media-folders', [ContentLookupController::class, 'mediaFolders'])
        ->middleware('admin.permission:media.view')
        ->name('media-folders');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->middleware('admin.permission:categories.view')->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->middleware('admin.permission:categories.create')->name('create');
    Route::post('/', [CategoryController::class, 'store'])->middleware('admin.permission:categories.create')->name('store');
    Route::patch('/bulk-move', [CategoryController::class, 'bulkMove'])->middleware('admin.permission:categories.update')->name('bulk-move');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->middleware('admin.permission:categories.update')->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->middleware('admin.permission:categories.update')->name('update');
    Route::patch('/{category}/restore', [CategoryController::class, 'restore'])->middleware('admin.permission:categories.update')->name('restore');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('admin.permission:categories.delete')->name('destroy');
});

Route::prefix('temples')->name('temples.')->group(function () {
    Route::get('/', [TempleController::class, 'index'])->middleware('admin.permission:temples.view')->name('index');
    Route::get('/create', [TempleController::class, 'create'])->middleware('admin.permission:temples.create')->name('create');
    Route::post('/', [TempleController::class, 'store'])->middleware('admin.permission:temples.create')->name('store');
    Route::patch('/bulk-category', [TempleController::class, 'bulkAssignCategory'])->middleware('admin.permission:temples.update')->name('bulk-category');
    Route::get('/{temple}', [TempleController::class, 'show'])->middleware('admin.permission:temples.view')->name('show');
    Route::get('/{temple}/edit', [TempleController::class, 'edit'])->middleware('admin.permission:temples.update')->name('edit');
    Route::put('/{temple}', [TempleController::class, 'update'])->middleware('admin.permission:temples.update')->name('update');
    Route::patch('/{temple}/publish', [TempleController::class, 'publish'])->middleware('admin.permission:temples.publish')->name('publish');
    Route::delete('/{temple}', [TempleController::class, 'destroy'])->middleware('admin.permission:temples.delete')->name('destroy');
});

Route::prefix('articles')->name('content.articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])
        ->middleware('admin.permission:articles.view')
        ->name('index');

    Route::get('/media-picker/cover', [ArticleController::class, 'coverMediaPicker'])
        ->middleware('admin.permission:articles.view')
        ->name('media-picker.cover');

    Route::get('/create', [ArticleController::class, 'create'])
        ->middleware('admin.permission:articles.create')
        ->name('create');

    Route::post('/', [ArticleController::class, 'store'])
        ->middleware('admin.permission:articles.create')
        ->name('store');

    Route::patch('/bulk-category', [ArticleController::class, 'bulkAssignCategory'])
        ->middleware('admin.permission:articles.update')
        ->name('bulk-category');

    Route::get('/{article}', [ArticleController::class, 'show'])
        ->middleware('admin.permission:articles.view')
        ->name('show');

    Route::get('/{article}/edit', [ArticleController::class, 'edit'])
        ->middleware('admin.permission:articles.update')
        ->name('edit');

    Route::put('/{article}', [ArticleController::class, 'update'])
        ->middleware('admin.permission:articles.update')
        ->name('update');

    Route::patch('/{article}/publish', [ArticleController::class, 'publish'])
        ->middleware('admin.permission:articles.publish')
        ->name('publish');

    Route::patch('/{article}/unpublish', [ArticleController::class, 'unpublish'])
        ->middleware('admin.permission:articles.publish')
        ->name('unpublish');

    Route::delete('/{article}', [ArticleController::class, 'destroy'])
        ->middleware('admin.permission:articles.delete')
        ->name('destroy');
});

Route::prefix('content/article-tags')->name('content.article-tags.')->group(function () {
    Route::get('/', [ArticleTagController::class, 'index'])->middleware('admin.permission:articles.view')->name('index');
    Route::get('/create', [ArticleTagController::class, 'create'])->middleware('admin.permission:articles.create')->name('create');
    Route::post('/', [ArticleTagController::class, 'store'])->middleware('admin.permission:articles.create')->name('store');
    Route::get('/{articleTag}/edit', [ArticleTagController::class, 'edit'])->middleware('admin.permission:articles.update')->name('edit');
    Route::put('/{articleTag}', [ArticleTagController::class, 'update'])->middleware('admin.permission:articles.update')->name('update');
    Route::delete('/{articleTag}', [ArticleTagController::class, 'destroy'])->middleware('admin.permission:articles.delete')->name('destroy');
});
