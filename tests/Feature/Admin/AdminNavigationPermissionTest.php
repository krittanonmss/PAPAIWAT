<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class AdminNavigationPermissionTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);
    }

    public function test_side_menu_only_shows_links_allowed_by_admin_permissions(): void
    {
        $admin = $this->createAdminWithPermissions([
            'dashboard.view',
            'temples.view',
            'media.view',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('แดชบอร์ด')
            ->assertSee('จัดการเนื้อหา')
            ->assertSee('จัดการวัด')
            ->assertSee('รายการวัด')
            ->assertSee('จัดการคลังสื่อ')
            ->assertSee('คลังสื่อ')
            ->assertDontSee('จัดการบทความ')
            ->assertDontSee('แท็กบทความ')
            ->assertDontSee('จัดการหมวดหมู่')
            ->assertDontSee('จัดการโครงหน้าเว็บ')
            ->assertDontSee('จัดการสิทธิ์ผู้ใช้งาน');
    }

    public function test_admin_route_without_permission_returns_forbidden(): void
    {
        $admin = $this->createAdminWithPermissions([
            'dashboard.view',
            'temples.view',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('admin.content.articles.index'))
            ->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionKeys): Admin
    {
        $role = Role::query()->create([
            'name' => 'Limited Navigation Role',
            'role_key' => 'limited_navigation',
            'description' => 'Role for navigation permission tests',
            'level' => 20,
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::query()
                ->whereIn('key', $permissionKeys)
                ->pluck('id')
                ->all()
        );

        return Admin::query()->create([
            'username' => 'limited-nav-admin',
            'email' => 'limited-nav@example.com',
            'password_hash' => Hash::make('OriginalPassword123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }
}
