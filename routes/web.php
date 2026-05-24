<?php

use App\Http\Controllers\Frontend\FrontendArticleController;
use App\Http\Controllers\Frontend\FrontendPageController;
use App\Http\Controllers\Frontend\FrontendTempleController;
use App\Http\Controllers\Frontend\FavoriteListController;
use App\Http\Controllers\Frontend\InteractionReportController;
use App\Http\Controllers\Frontend\PublicInteractionCounterController;
use App\Http\Controllers\Frontend\PublicCommentController;
use App\Http\Controllers\Frontend\TempleReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendPageController::class, 'home'])->name('home');
Route::get('/search', [FrontendPageController::class, 'search'])->name('search');
Route::get('/favorites', FavoriteListController::class)->name('favorites.index');
Route::post('/favorites/items', [FavoriteListController::class, 'items'])->name('favorites.items');

Route::post('/interactions/favorite', [PublicInteractionCounterController::class, 'favorite'])
    ->middleware('throttle:60,1')
    ->name('interactions.favorite');

Route::post('/interactions/share', [PublicInteractionCounterController::class, 'share'])
    ->middleware('throttle:60,1')
    ->name('interactions.share');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin/auth.php';
require __DIR__ . '/admin/protected.php';
require __DIR__ . '/admin/media.php';

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/temples/{temple}', [FrontendTempleController::class, 'show'])
    ->name('temples.show');

Route::post('/temples/{temple}/reviews', [TempleReviewController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('temples.reviews.store');

Route::post('/temples/{temple}/comments', [PublicCommentController::class, 'storeTemple'])
    ->middleware('throttle:10,1')
    ->name('temples.comments.store');

Route::post('/reviews/{review}/report', [InteractionReportController::class, 'review'])
    ->middleware('throttle:10,1')
    ->name('reviews.report');

Route::get('/articles/{slug}', [FrontendArticleController::class, 'show'])
    ->name('articles.show');

Route::post('/articles/{article}/comments', [PublicCommentController::class, 'storeArticle'])
    ->middleware('throttle:10,1')
    ->name('articles.comments.store');

Route::post('/comments/{comment}/report', [InteractionReportController::class, 'comment'])
    ->middleware('throttle:10,1')
    ->name('comments.report');

Route::get('/{slug}', [FrontendPageController::class, 'show'])
    ->name('pages.show');
