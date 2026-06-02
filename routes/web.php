<?php

use App\Http\Controllers\Frontend\FavoriteListController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Frontend\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\Auth\VerifyEmailController;
use App\Http\Controllers\Frontend\FrontendArticleController;
use App\Http\Controllers\Frontend\FrontendPageController;
use App\Http\Controllers\Frontend\FrontendTempleController;
use App\Http\Controllers\Frontend\InteractionReportController;
use App\Http\Controllers\Frontend\NearbyPlacePhotoController;
use App\Http\Controllers\Frontend\PublicCommentController;
use App\Http\Controllers\Frontend\PublicInteractionCounterController;
use App\Http\Controllers\Frontend\TempleReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendPageController::class, 'home'])->name('home');
Route::get('/search', [FrontendPageController::class, 'search'])->name('search');
Route::get('/favorites', FavoriteListController::class)->name('favorites.index');
Route::post('/favorites/items', [FavoriteListController::class, 'items'])->name('favorites.items');
Route::post('/favorites/sync', [FavoriteListController::class, 'sync'])
    ->middleware(['auth', 'verified', 'throttle:30,1'])
    ->name('favorites.sync');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1')->name('register.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/email/verify', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

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
require __DIR__.'/admin/auth.php';
require __DIR__.'/admin/protected.php';
require __DIR__.'/admin/media.php';

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/temples/{temple}', [FrontendTempleController::class, 'show'])
    ->name('temples.show');

Route::get('/nearby-place-recommendations/{recommendation}/photos/{index}', [NearbyPlacePhotoController::class, 'show'])
    ->whereNumber('index')
    ->middleware('throttle:120,1')
    ->name('nearby-place-recommendations.photos.show');

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
