<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\Content\CategoryController;
use App\Http\Controllers\Admin\Content\Media\MediaFolderController;
use App\Http\Controllers\Admin\Content\Media\MediaController;
use App\Http\Controllers\Admin\Content\Temple\TempleController;
use App\Http\Controllers\Admin\Content\Article\ArticleController;
use App\Http\Controllers\Admin\Content\Article\ArticleTagController;
use App\Http\Controllers\Frontend\FrontendPageController;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Admin\Content\Layout\MenuController;
use App\Http\Controllers\Admin\Content\Layout\MenuItemController;
use App\Http\Controllers\Admin\Content\Layout\PageController;
use App\Http\Controllers\Admin\Content\Layout\PageSectionController;
use App\Http\Controllers\Admin\Content\Layout\TemplateController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{slug}', [FrontendPageController::class, 'show'])
    ->name('pages.show');

Route::get('/admin/login', function () {
    return view('admin.auth.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['admin.auth', 'admin.active', 'admin.activity'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('admin.permission:dashboard.view')
            ->name('dashboard');

        Route::patch('/users/{admin}/status', [UserManagementController::class, 'updateStatus'])
            ->middleware('admin.permission:users.update')
            ->name('users.status.update');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])
                ->middleware('admin.permission:users.view')
                ->name('index');

            Route::get('/create', [UserManagementController::class, 'create'])
                ->middleware('admin.permission:users.create')
                ->name('create');

            Route::post('/', [UserManagementController::class, 'store'])
                ->middleware('admin.permission:users.create')
                ->name('store');

            Route::get('/{admin}', [UserManagementController::class, 'show'])
                ->middleware('admin.permission:users.view')
                ->name('show');

            Route::get('/{admin}/edit', [UserManagementController::class, 'edit'])
                ->middleware('admin.permission:users.update')
                ->name('edit');

            Route::put('/{admin}', [UserManagementController::class, 'update'])
                ->middleware('admin.permission:users.update')
                ->name('update');

            Route::delete('/{admin}', [UserManagementController::class, 'destroy'])
                ->middleware('admin.permission:users.delete')
                ->name('destroy');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->middleware('admin.permission:roles.view')
                ->name('index');

            Route::get('/create', [RoleController::class, 'create'])
                ->middleware('admin.permission:roles.create')
                ->name('create');

            Route::post('/', [RoleController::class, 'store'])
                ->middleware('admin.permission:roles.create')
                ->name('store');

            Route::get('/{role}/edit', [RoleController::class, 'edit'])
                ->middleware('admin.permission:roles.update')
                ->name('edit');

            Route::put('/{role}', [RoleController::class, 'update'])
                ->middleware('admin.permission:roles.update')
                ->name('update');

            Route::delete('/{role}', [RoleController::class, 'destroy'])
                ->middleware('admin.permission:roles.delete')
                ->name('destroy');

            Route::get('/{role}/permissions', [RolePermissionController::class, 'edit'])
                ->middleware('admin.permission:roles.permissions')
                ->name('permissions.edit');

            Route::put('/{role}/permissions', [RolePermissionController::class, 'update'])
                ->middleware('admin.permission:roles.permissions')
                ->name('permissions.update');
        });

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])
                ->middleware('admin.permission:permissions.view')
                ->name('index');

            Route::get('/create', [PermissionController::class, 'create'])
                ->middleware('admin.permission:permissions.create')
                ->name('create');

            Route::post('/', [PermissionController::class, 'store'])
                ->middleware('admin.permission:permissions.create')
                ->name('store');

            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])
                ->middleware('admin.permission:permissions.update')
                ->name('edit');

            Route::put('/{permission}', [PermissionController::class, 'update'])
                ->middleware('admin.permission:permissions.update')
                ->name('update');

            Route::delete('/{permission}', [PermissionController::class, 'destroy'])
                ->middleware('admin.permission:permissions.delete')
                ->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])
                ->middleware('admin.permission:categories.view')
                ->name('index');

            Route::get('/create', [CategoryController::class, 'create'])
                ->middleware('admin.permission:categories.create')
                ->name('create');

            Route::post('/', [CategoryController::class, 'store'])
                ->middleware('admin.permission:categories.create')
                ->name('store');

            Route::get('/{category}/edit', [CategoryController::class, 'edit'])
                ->middleware('admin.permission:categories.update')
                ->name('edit');

            Route::put('/{category}', [CategoryController::class, 'update'])
                ->middleware('admin.permission:categories.update')
                ->name('update');

            Route::delete('/{category}', [CategoryController::class, 'destroy'])
                ->middleware('admin.permission:categories.delete')
                ->name('destroy');
        });

        Route::prefix('media-folders')->name('media-folders.')->group(function () {
            Route::get('/', [MediaFolderController::class, 'index'])
                ->middleware('admin.permission:media.view')
                ->name('index');

            Route::get('/create', [MediaFolderController::class, 'create'])
                ->middleware('admin.permission:media.create')
                ->name('create');

            Route::post('/', [MediaFolderController::class, 'store'])
                ->middleware('admin.permission:media.create')
                ->name('store');

            Route::get('/{mediaFolder}/edit', [MediaFolderController::class, 'edit'])
                ->middleware('admin.permission:media.update')
                ->name('edit');

            Route::put('/{mediaFolder}', [MediaFolderController::class, 'update'])
                ->middleware('admin.permission:media.update')
                ->name('update');

            Route::delete('/{mediaFolder}', [MediaFolderController::class, 'destroy'])
                ->middleware('admin.permission:media.delete')
                ->name('destroy');
        });

        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])
                ->middleware('admin.permission:media.view')
                ->name('index');

            Route::get('/create', [MediaController::class, 'create'])
                ->middleware('admin.permission:media.create')
                ->name('create');

            Route::post('/', [MediaController::class, 'store'])
                ->middleware('admin.permission:media.create')
                ->name('store');

            Route::get('/{media}/edit', [MediaController::class, 'edit'])
                ->middleware('admin.permission:media.update')
                ->name('edit');

            Route::put('/{media}', [MediaController::class, 'update'])
                ->middleware('admin.permission:media.update')
                ->name('update');

            Route::delete('/{media}', [MediaController::class, 'destroy'])
                ->middleware('admin.permission:media.delete')
                ->name('destroy');
        });

        Route::prefix('temples')->name('temples.')->group(function () {
            Route::get('/', [TempleController::class, 'index'])
                ->middleware('admin.permission:temples.view')
                ->name('index');

            Route::get('/create', [TempleController::class, 'create'])
                ->middleware('admin.permission:temples.create')
                ->name('create');

            Route::post('/', [TempleController::class, 'store'])
                ->middleware('admin.permission:temples.create')
                ->name('store');

            Route::get('/{temple}', [TempleController::class, 'show'])
                ->middleware('admin.permission:temples.view')
                ->name('show');

            Route::get('/{temple}/edit', [TempleController::class, 'edit'])
                ->middleware('admin.permission:temples.update')
                ->name('edit');

            Route::put('/{temple}', [TempleController::class, 'update'])
                ->middleware('admin.permission:temples.update')
                ->name('update');

            Route::delete('/{temple}', [TempleController::class, 'destroy'])
                ->middleware('admin.permission:temples.delete')
                ->name('destroy');
        });

        Route::prefix('articles')->name('content.articles.')->group(function () {
            Route::get('/', [ArticleController::class, 'index'])
                ->middleware('admin.permission:articles.view')
                ->name('index');

            Route::get('/create', [ArticleController::class, 'create'])
                ->middleware('admin.permission:articles.create')
                ->name('create');

            Route::post('/', [ArticleController::class, 'store'])
                ->middleware('admin.permission:articles.create')
                ->name('store');

            Route::get('/{article}', [ArticleController::class, 'show'])
                ->middleware('admin.permission:articles.view')
                ->name('show');

            Route::get('/{article}/edit', [ArticleController::class, 'edit'])
                ->middleware('admin.permission:articles.update')
                ->name('edit');

            Route::put('/{article}', [ArticleController::class, 'update'])
                ->middleware('admin.permission:articles.update')
                ->name('update');

            Route::delete('/{article}', [ArticleController::class, 'destroy'])
                ->middleware('admin.permission:articles.delete')
                ->name('destroy');
        });

        Route::prefix('content/article-tags')->name('content.article-tags.')->group(function () {
            Route::get('/', [ArticleTagController::class, 'index'])
                ->middleware('admin.permission:articles.view')
                ->name('index');

            Route::get('/create', [ArticleTagController::class, 'create'])
                ->middleware('admin.permission:articles.create')
                ->name('create');

            Route::post('/', [ArticleTagController::class, 'store'])
                ->middleware('admin.permission:articles.create')
                ->name('store');

            Route::get('/{articleTag}/edit', [ArticleTagController::class, 'edit'])
                ->middleware('admin.permission:articles.update')
                ->name('edit');

            Route::put('/{articleTag}', [ArticleTagController::class, 'update'])
                ->middleware('admin.permission:articles.update')
                ->name('update');

            Route::delete('/{articleTag}', [ArticleTagController::class, 'destroy'])
                ->middleware('admin.permission:articles.delete')
                ->name('destroy');
        });

        Route::prefix('content/menus')->name('content.menus.')->group(function () {
            Route::get('/', [MenuController::class, 'index'])
                ->middleware('admin.permission:menus.view')
                ->name('index');

            Route::get('/create', [MenuController::class, 'create'])
                ->middleware('admin.permission:menus.create')
                ->name('create');

            Route::post('/', [MenuController::class, 'store'])
                ->middleware('admin.permission:menus.create')
                ->name('store');

            Route::get('/{menu}', [MenuController::class, 'show'])
                ->middleware('admin.permission:menus.view')
                ->name('show');

            Route::get('/{menu}/edit', [MenuController::class, 'edit'])
                ->middleware('admin.permission:menus.update')
                ->name('edit');

            Route::put('/{menu}', [MenuController::class, 'update'])
                ->middleware('admin.permission:menus.update')
                ->name('update');

            Route::delete('/{menu}', [MenuController::class, 'destroy'])
                ->middleware('admin.permission:menus.delete')
                ->name('destroy');

            Route::get('/{menu}/items/create', [MenuItemController::class, 'create'])
                ->middleware('admin.permission:menus.create')
                ->name('items.create');

            Route::post('/{menu}/items', [MenuItemController::class, 'store'])
                ->middleware('admin.permission:menus.create')
                ->name('items.store');

            Route::get('/{menu}/items/{menuItem}/edit', [MenuItemController::class, 'edit'])
                ->middleware('admin.permission:menus.update')
                ->name('items.edit');

            Route::put('/{menu}/items/{menuItem}', [MenuItemController::class, 'update'])
                ->middleware('admin.permission:menus.update')
                ->name('items.update');

            Route::delete('/{menu}/items/{menuItem}', [MenuItemController::class, 'destroy'])
                ->middleware('admin.permission:menus.delete')
                ->name('items.destroy');
        });

        Route::prefix('content/pages')->name('content.pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])
                ->middleware('admin.permission:pages.view')
                ->name('index');

            Route::get('/create', [PageController::class, 'create'])
                ->middleware('admin.permission:pages.create')
                ->name('create');

            Route::post('/', [PageController::class, 'store'])
                ->middleware('admin.permission:pages.create')
                ->name('store');

            Route::get('/{page}', [PageController::class, 'show'])
                ->middleware('admin.permission:pages.view')
                ->name('show');

            Route::get('/{page}/edit', [PageController::class, 'edit'])
                ->middleware('admin.permission:pages.update')
                ->name('edit');

            Route::put('/{page}', [PageController::class, 'update'])
                ->middleware('admin.permission:pages.update')
                ->name('update');

            Route::delete('/{page}', [PageController::class, 'destroy'])
                ->middleware('admin.permission:pages.delete')
                ->name('destroy');

            Route::get('/{page}/sections/create', [PageSectionController::class, 'create'])
                ->middleware('admin.permission:pages.create')
                ->name('sections.create');

            Route::post('/{page}/sections', [PageSectionController::class, 'store'])
                ->middleware('admin.permission:pages.create')
                ->name('sections.store');

            Route::get('/{page}/sections/{section}/edit', [PageSectionController::class, 'edit'])
                ->middleware('admin.permission:pages.update')
                ->name('sections.edit');

            Route::put('/{page}/sections/{section}', [PageSectionController::class, 'update'])
                ->middleware('admin.permission:pages.update')
                ->name('sections.update');

            Route::delete('/{page}/sections/{section}', [PageSectionController::class, 'destroy'])
                ->middleware('admin.permission:pages.delete')
                ->name('sections.destroy');
        });

        Route::prefix('content/menus/{menu}/items')
            ->name('content.menu-items.')
            ->group(function () {
                Route::get('/create', [MenuItemController::class, 'create'])
                    ->name('create');

                Route::post('/', [MenuItemController::class, 'store'])
                    ->name('store');

                Route::get('/{menuItem}/edit', [MenuItemController::class, 'edit'])
                    ->name('edit');

                Route::put('/{menuItem}', [MenuItemController::class, 'update'])
                    ->name('update');

                Route::delete('/{menuItem}', [MenuItemController::class, 'destroy'])
                    ->name('destroy');
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
    });
});