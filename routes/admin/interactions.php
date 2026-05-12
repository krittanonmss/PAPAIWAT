<?php

use App\Http\Controllers\Admin\Interaction\PublicCommentModerationController;
use App\Http\Controllers\Admin\Interaction\TempleReviewModerationController;
use Illuminate\Support\Facades\Route;

Route::prefix('interactions')->name('interactions.')->group(function () {
    Route::get('/reviews', [TempleReviewModerationController::class, 'index'])
        ->middleware('admin.permission:interactions.view')
        ->name('reviews.index');

    Route::patch('/reviews/{review}/approve', [TempleReviewModerationController::class, 'approve'])
        ->middleware('admin.permission:interactions.manage')
        ->name('reviews.approve');

    Route::patch('/reviews/{review}/reject', [TempleReviewModerationController::class, 'reject'])
        ->middleware('admin.permission:interactions.manage')
        ->name('reviews.reject');

    Route::delete('/reviews/{review}', [TempleReviewModerationController::class, 'destroy'])
        ->middleware('admin.permission:interactions.manage')
        ->name('reviews.destroy');

    Route::get('/comments', [PublicCommentModerationController::class, 'index'])
        ->middleware('admin.permission:interactions.view')
        ->name('comments.index');

    Route::patch('/comments/{comment}/approve', [PublicCommentModerationController::class, 'approve'])
        ->middleware('admin.permission:interactions.manage')
        ->name('comments.approve');

    Route::patch('/comments/{comment}/reject', [PublicCommentModerationController::class, 'reject'])
        ->middleware('admin.permission:interactions.manage')
        ->name('comments.reject');

    Route::delete('/comments/{comment}', [PublicCommentModerationController::class, 'destroy'])
        ->middleware('admin.permission:interactions.manage')
        ->name('comments.destroy');

});
