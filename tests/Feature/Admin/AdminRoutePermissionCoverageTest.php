<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\Permission;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class AdminRoutePermissionCoverageTest extends TestCase
{
    use MigratesAppDatabase;

    public function test_protected_admin_routes_require_admin_authentication(): void
    {
        $missingAuth = collect(Route::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'admin'))
            ->reject(fn ($route) => in_array($route->getName(), [
                'admin.login',
                'admin.login.store',
            ], true))
            ->reject(fn ($route) => $this->hasMiddleware($route->gatherMiddleware(), 'admin.auth'))
            ->map(fn ($route) => $this->routeLabel($route))
            ->values()
            ->all();

        $this->assertSame([], $missingAuth);
    }

    public function test_sensitive_admin_routes_require_explicit_permissions(): void
    {
        $requiredPermissions = [
            'admin.content.template-preview.sample' => 'preview.view',
            'admin.content.template-preview.live' => 'preview.view',
            'admin.content.template-preview' => 'preview.view',
            'admin.temples.publish' => 'temples.publish',
            'admin.content.articles.publish' => 'articles.publish',
            'admin.content.articles.unpublish' => 'articles.publish',
            'admin.categories.restore' => 'categories.update',
            'admin.media.file' => 'media.view',
            'admin.media.quick-upload' => 'media.create',
            'admin.media.bulk-folder' => 'media.update',
            'admin.media.regenerate-variants' => 'media.update',
            'admin.temples.media-picker.cover' => 'temples.view',
            'admin.temples.media-picker.gallery' => 'temples.view',
            'admin.users.bulk-role' => 'users.update',
            'admin.users.bulk-status' => 'users.update',
            'admin.content.footer.edit' => 'menus.view',
            'admin.content.footer.update' => 'menus.update',
            'admin.settings.edit' => 'settings.view',
            'admin.settings.update' => 'settings.update',
            'admin.settings.maintenance.cache' => 'settings.maintenance',
            'admin.settings.maintenance.sitemap' => 'settings.maintenance',
            'admin.content.menu-items.create' => 'menu-items.create',
            'admin.content.menu-items.store' => 'menu-items.create',
            'admin.content.menu-items.lookups.pages' => 'menu-items.view',
            'admin.content.menu-items.lookups.contents' => 'menu-items.view',
            'admin.content.menu-items.edit' => 'menu-items.update',
            'admin.content.menu-items.update' => 'menu-items.update',
            'admin.content.menu-items.destroy' => 'menu-items.delete',
            'admin.content.templates.index' => 'templates.view',
            'admin.content.templates.create' => 'templates.create',
            'admin.content.templates.store' => 'templates.create',
            'admin.content.templates.show' => 'templates.view',
            'admin.content.templates.edit' => 'templates.update',
            'admin.content.templates.update' => 'templates.update',
            'admin.content.templates.destroy' => 'templates.delete',
        ];

        foreach ($requiredPermissions as $routeName => $permission) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Route [{$routeName}] is not registered.");
            $this->assertTrue(
                $this->hasMiddleware($route->gatherMiddleware(), "admin.permission:{$permission}"),
                "Route [{$routeName}] is missing admin.permission:{$permission}."
            );
        }
    }

    public function test_admin_preview_routes_require_preview_permission(): void
    {
        $missingPermission = collect(Route::getRoutes())
            ->filter(fn ($route) => str_starts_with((string) $route->getName(), 'admin.'))
            ->filter(fn ($route) => str_contains((string) $route->getName(), 'preview'))
            ->reject(fn ($route) => $this->hasMiddleware($route->gatherMiddleware(), 'admin.permission:preview.view'))
            ->map(fn ($route) => $this->routeLabel($route))
            ->values()
            ->all();

        $this->assertSame([], $missingPermission);
    }

    public function test_seeded_permissions_cover_all_route_permission_middleware(): void
    {
        $this->migrateAdminTables();

        Permission::query()->create([
            'key' => 'interactions.manage',
            'name' => 'Legacy interaction management',
            'group_key' => 'interactions',
            'description' => 'Legacy permission no longer used by routes.',
        ]);

        $this->seed(SystemAccessSeeder::class);

        $routePermissions = collect(Route::getRoutes())
            ->flatMap(fn ($route) => $route->gatherMiddleware())
            ->filter(fn ($middleware) => str_starts_with($middleware, 'admin.permission:'))
            ->map(fn ($middleware) => substr($middleware, strlen('admin.permission:')))
            ->unique()
            ->sort()
            ->values();

        $seededPermissions = Permission::query()
            ->pluck('key')
            ->sort()
            ->values();

        $this->assertSame([], $routePermissions->diff($seededPermissions)->values()->all());
        $this->assertFalse(Permission::query()->where('key', 'interactions.manage')->exists());
    }

    private function hasMiddleware(array $middleware, string $expected): bool
    {
        return collect($middleware)->contains(fn ($name) => $name === $expected);
    }

    private function routeLabel($route): string
    {
        return trim(implode('|', $route->methods()).' '.$route->uri().' '.$route->getName());
    }
}
