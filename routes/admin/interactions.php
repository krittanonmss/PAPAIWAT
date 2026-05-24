<?php

use App\Http\Controllers\Admin\Interaction\InteractionBanController;
use App\Http\Controllers\Admin\Interaction\InteractionReportModerationController;
use App\Http\Controllers\Admin\Interaction\PublicCommentModerationController;
use App\Http\Controllers\Admin\Interaction\TempleReviewModerationController;
use Illuminate\Support\Facades\Route;

Route::prefix('interactions')->name('interactions.')->group(function () {
    Route::get('/reviews', [TempleReviewModerationController::class, 'index'])
        ->middleware('admin.permission:interactions.view')
        ->name('reviews.index');

    Route::patch('/reviews/bulk', [TempleReviewModerationController::class, 'bulk'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('reviews.bulk');

    Route::get('/reviews/{review}', [TempleReviewModerationController::class, 'show'])
        ->middleware('admin.permission:interactions.view')
        ->name('reviews.show');

    Route::patch('/reviews/{review}/approve', [TempleReviewModerationController::class, 'approve'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('reviews.approve');

    Route::patch('/reviews/{review}/reject', [TempleReviewModerationController::class, 'reject'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('reviews.reject');

    Route::patch('/reviews/{review}/spam', [TempleReviewModerationController::class, 'spam'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('reviews.spam');

    Route::patch('/reviews/{review}/ban-visitor', [TempleReviewModerationController::class, 'banVisitor'])
        ->middleware('admin.permission:interactions.ban')
        ->name('reviews.ban-visitor');

    Route::delete('/reviews/{review}', [TempleReviewModerationController::class, 'destroy'])
        ->middleware('admin.permission:interactions.delete')
        ->name('reviews.destroy');

    Route::get('/comments', [PublicCommentModerationController::class, 'index'])
        ->middleware('admin.permission:interactions.view')
        ->name('comments.index');

    Route::patch('/comments/bulk', [PublicCommentModerationController::class, 'bulk'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('comments.bulk');

    Route::get('/comments/{comment}', [PublicCommentModerationController::class, 'show'])
        ->middleware('admin.permission:interactions.view')
        ->name('comments.show');

    Route::patch('/comments/{comment}/approve', [PublicCommentModerationController::class, 'approve'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('comments.approve');

    Route::patch('/comments/{comment}/reject', [PublicCommentModerationController::class, 'reject'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('comments.reject');

    Route::patch('/comments/{comment}/spam', [PublicCommentModerationController::class, 'spam'])
        ->middleware('admin.permission:interactions.moderate')
        ->name('comments.spam');

    Route::patch('/comments/{comment}/ban-visitor', [PublicCommentModerationController::class, 'banVisitor'])
        ->middleware('admin.permission:interactions.ban')
        ->name('comments.ban-visitor');

    Route::delete('/comments/{comment}', [PublicCommentModerationController::class, 'destroy'])
        ->middleware('admin.permission:interactions.delete')
        ->name('comments.destroy');

    Route::get('/reports', [InteractionReportModerationController::class, 'index'])
        ->middleware('admin.permission:interactions.view')
        ->name('reports.index');

    Route::delete('/reports/{report}', [InteractionReportModerationController::class, 'destroy'])
        ->middleware('admin.permission:interactions.delete')
        ->name('reports.destroy');

    Route::get('/bans', [InteractionBanController::class, 'index'])
        ->middleware('admin.permission:interactions.ban')
        ->name('bans.index');

    Route::delete('/bans/{ban}', [InteractionBanController::class, 'destroy'])
        ->middleware('admin.permission:interactions.ban')
        ->name('bans.destroy');

});
