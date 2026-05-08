<?php

use App\Http\Controllers\Frontend\FrontendArticleController;
use App\Http\Controllers\Frontend\FrontendPageController;
use App\Http\Controllers\Frontend\FrontendTempleController;
use App\Http\Controllers\Frontend\FavoriteListController;
use App\Http\Controllers\Frontend\InteractionReportController;
use App\Http\Controllers\Frontend\PublicCommentController;
use App\Http\Controllers\Frontend\TempleReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendPageController::class, 'home'])->name('home');
Route::get('/favorites', FavoriteListController::class)->name('favorites.index');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin/auth.php';
require __DIR__ . '/admin/protected.php';
require __DIR__ . '/admin/users.php';
require __DIR__ . '/admin/media.php';

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/temples/{temple}', [FrontendTempleController::class, 'show'])
    ->name('temples.show');

Route::post('/temples/{temple}/reviews', [TempleReviewController::class, 'store'])
    ->name('temples.reviews.store');

Route::post('/temples/{temple}/comments', [PublicCommentController::class, 'storeTemple'])
    ->name('temples.comments.store');

Route::post('/reviews/{review}/report', [InteractionReportController::class, 'review'])
    ->name('reviews.report');

Route::get('/articles/{slug}', [FrontendArticleController::class, 'show'])
    ->name('articles.show');

Route::post('/articles/{article}/comments', [PublicCommentController::class, 'storeArticle'])
    ->name('articles.comments.store');

Route::post('/comments/{comment}/report', [InteractionReportController::class, 'comment'])
    ->name('comments.report');

Route::get('/{slug}', [FrontendPageController::class, 'show'])
    ->name('pages.show');
