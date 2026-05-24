<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAccessSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->seedPermissions();
        $roles = $this->seedRoles();

        $this->syncRolePermissions($roles, $permissions);
        $this->seedDefaultAdmin($roles['super_admin']);
    }

    private function seedPermissions(): array
    {
        $definitions = [
            'dashboard' => [
                'dashboard.view' => 'ดู Dashboard',
            ],
            'users' => [
                'users.view' => 'ดูผู้ดูแลระบบ',
                'users.create' => 'สร้างผู้ดูแลระบบ',
                'users.update' => 'แก้ไขผู้ดูแลระบบ',
                'users.delete' => 'ลบผู้ดูแลระบบ',
            ],
            'roles' => [
                'roles.view' => 'ดูบทบาท',
                'roles.create' => 'สร้างบทบาท',
                'roles.update' => 'แก้ไขบทบาท',
                'roles.delete' => 'ลบบทบาท',
                'roles.permissions' => 'จัดการสิทธิ์ของบทบาท',
            ],
            'permissions' => [
                'permissions.view' => 'ดูสิทธิ์',
                'permissions.create' => 'สร้างสิทธิ์',
                'permissions.update' => 'แก้ไขสิทธิ์',
                'permissions.delete' => 'ลบสิทธิ์',
            ],
            'temples' => [
                'temples.view' => 'ดูข้อมูลวัด',
                'temples.create' => 'สร้างข้อมูลวัด',
                'temples.update' => 'แก้ไขข้อมูลวัด',
                'temples.publish' => 'เผยแพร่ข้อมูลวัด',
                'temples.delete' => 'ลบข้อมูลวัด',
            ],
            'articles' => [
                'articles.view' => 'ดูบทความ',
                'articles.create' => 'สร้างบทความ',
                'articles.update' => 'แก้ไขบทความ',
                'articles.publish' => 'เผยแพร่บทความ',
                'articles.delete' => 'ลบบทความ',
            ],
            'categories' => [
                'categories.view' => 'ดูหมวดหมู่',
                'categories.create' => 'สร้างหมวดหมู่',
                'categories.update' => 'แก้ไขหมวดหมู่',
                'categories.delete' => 'ลบหมวดหมู่',
            ],
            'media' => [
                'media.view' => 'ดู Media Library',
                'media.create' => 'อัปโหลด Media',
                'media.update' => 'แก้ไข Media',
                'media.delete' => 'ลบ Media',
            ],
            'menus' => [
                'menus.view' => 'ดูเมนู',
                'menus.create' => 'สร้างเมนู',
                'menus.update' => 'แก้ไขเมนู',
                'menus.delete' => 'ลบเมนู',
            ],
            'menu-items' => [
                'menu-items.view' => 'ดูรายการเมนู',
                'menu-items.create' => 'สร้างรายการเมนู',
                'menu-items.update' => 'แก้ไขรายการเมนู',
                'menu-items.delete' => 'ลบรายการเมนู',
            ],
            'pages' => [
                'pages.view' => 'ดูหน้าเว็บ',
                'pages.create' => 'สร้างหน้าเว็บ',
                'pages.update' => 'แก้ไขหน้าเว็บ',
                'pages.delete' => 'ลบหน้าเว็บ',
            ],
            'sections' => [
                'sections.view' => 'ดู Page Section',
                'sections.create' => 'สร้าง Page Section',
                'sections.update' => 'แก้ไข Page Section',
                'sections.delete' => 'ลบ Page Section',
            ],
            'templates' => [
                'templates.view' => 'ดู Template',
                'templates.create' => 'สร้าง Template',
                'templates.update' => 'แก้ไข Template',
                'templates.delete' => 'ลบ Template',
            ],
            'preview' => [
                'preview.view' => 'ดู Preview',
            ],
            'interactions' => [
                'interactions.view' => 'ดูรีวิวและความคิดเห็น',
                'interactions.moderate' => 'อนุมัติ ปฏิเสธ หรือทำเครื่องหมายสแปม',
                'interactions.delete' => 'ลบรีวิว ความคิดเห็น หรือรายงาน',
                'interactions.ban' => 'บล็อกหรือปลดบล็อกผู้เยี่ยมชม',
            ],
            'settings' => [
                'settings.view' => 'ดูการตั้งค่าเว็บไซต์',
                'settings.update' => 'แก้ไขการตั้งค่าเว็บไซต์',
                'settings.maintenance' => 'เรียกใช้งาน maintenance ของเว็บไซต์',
            ],
        ];

        $permissions = [];

        foreach ($definitions as $groupKey => $items) {
            foreach ($items as $key => $name) {
                $permissions[$key] = Permission::updateOrCreate(
                    ['key' => $key],
                    [
                        'name' => $name,
                        'group_key' => $groupKey,
                        'description' => $name,
                    ]
                );
            }
        }

        $this->deleteObsoleteSystemPermissions([
            'interactions.manage',
        ]);

        return $permissions;
    }

    private function seedRoles(): array
    {
        $definitions = [
            'super_admin' => [
                'name' => 'Super Admin',
                'role_key' => 'super_admin',
                'description' => 'เข้าถึงและจัดการได้ทุกส่วนของระบบ',
                'level' => 100,
                'is_system' => true,
            ],
            'admin' => [
                'name' => 'Admin',
                'role_key' => 'admin',
                'description' => 'จัดการเนื้อหา หน้าเว็บ เมนู และผู้ดูแลระบบทั่วไป',
                'level' => 80,
                'is_system' => true,
            ],
            'editor' => [
                'name' => 'Editor',
                'role_key' => 'editor',
                'description' => 'จัดการเนื้อหา บทความ วัด หน้าเว็บ และ Media',
                'level' => 50,
                'is_system' => true,
            ],
            'viewer' => [
                'name' => 'Viewer',
                'role_key' => 'viewer',
                'description' => 'ดูข้อมูลในระบบได้อย่างเดียว',
                'level' => 10,
                'is_system' => true,
            ],
        ];

        $roles = [];

        foreach ($definitions as $key => $definition) {
            $role = Role::query()
                ->where('role_key', $definition['role_key'])
                ->orWhere('name', $definition['name'])
                ->first();

            if ($role) {
                $role->update($definition);
            } else {
                $role = Role::query()->create($definition);
            }

            $roles[$key] = $role;
        }

        return $roles;
    }

    private function syncRolePermissions(array $roles, array $permissions): void
    {
        $allPermissionIds = collect($permissions)->pluck('id')->all();

        $roles['super_admin']->permissions()->sync($allPermissionIds);

        $roles['admin']->permissions()->sync($this->permissionIds($permissions, [
            'dashboard.*',
            'users.view',
            'users.create',
            'users.update',
            'roles.view',
            'permissions.view',
            'temples.*',
            'articles.*',
            'categories.*',
            'media.*',
            'menus.*',
            'menu-items.*',
            'pages.*',
            'templates.*',
            'sections.*',
            'preview.view',
            'interactions.*',
            'settings.*',
        ]));

        $roles['editor']->permissions()->sync($this->permissionIds($permissions, [
            'dashboard.view',
            'temples.view',
            'temples.create',
            'temples.update',
            'articles.view',
            'articles.create',
            'articles.update',
            'categories.view',
            'media.view',
            'media.create',
            'media.update',
            'menus.view',
            'menu-items.create',
            'menu-items.update',
            'menu-items.delete',
            'pages.view',
            'pages.create',
            'pages.update',
            'sections.view',
            'sections.create',
            'sections.update',
            'templates.view',
            'preview.view',
            'interactions.view',
            'interactions.moderate',
        ]));

        $roles['viewer']->permissions()->sync($this->permissionIds($permissions, [
            'dashboard.view',
            '*.view',
        ]));
    }

    private function permissionIds(array $permissions, array $patterns): array
    {
        return collect($permissions)
            ->filter(function (Permission $permission) use ($patterns) {
                foreach ($patterns as $pattern) {
                    if (fnmatch($pattern, $permission->key)) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('id')
            ->values()
            ->all();
    }

    private function deleteObsoleteSystemPermissions(array $keys): void
    {
        Permission::query()
            ->whereIn('key', $keys)
            ->get()
            ->each(function (Permission $permission) {
                $permission->roles()->detach();
                $permission->delete();
            });
    }

    private function seedDefaultAdmin(Role $role): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! $password && app()->isProduction()) {
            throw new \RuntimeException('ADMIN_PASSWORD is required for seeding the default admin account in production.');
        }

        Admin::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'krittanon.mss@gmail.com')],
            [
                'username' => env('ADMIN_USERNAME', 'superadmin'),
                'password_hash' => Hash::make($password ?: 'ChangeMe12345'),
                'role_id' => $role->id,
                'status' => 'active',
            ]
        );
    }
}
