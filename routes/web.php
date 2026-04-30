<?php

use App\Http\Controllers\Frontend\FrontendPageController;
use App\Http\Controllers\Frontend\TempleController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin/auth.php';
require __DIR__ . '/admin/protected.php';
require __DIR__ . '/admin/access.php';
require __DIR__ . '/admin/users.php';
require __DIR__ . '/admin/content.php';
require __DIR__ . '/admin/layout.php';
require __DIR__ . '/admin/media.php';

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [FrontendPageController::class, 'show'])
    ->name('pages.show');

Route::get('/temples/{temple}', [TempleController::class, 'show'])
    ->name('temples.show');