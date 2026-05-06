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
                'temples.delete' => 'ลบข้อมูลวัด',
            ],
            'articles' => [
                'articles.view' => 'ดูบทความ',
                'articles.create' => 'สร้างบทความ',
                'articles.update' => 'แก้ไขบทความ',
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
            'pages' => [
                'pages.view' => 'ดูหน้าเว็บ',
                'pages.create' => 'สร้างหน้าเว็บ',
                'pages.update' => 'แก้ไขหน้าเว็บ',
                'pages.delete' => 'ลบหน้าเว็บ',
            ],
            'templates' => [
                'templates.view' => 'ดู Template',
                'templates.create' => 'สร้าง Template',
                'templates.update' => 'แก้ไข Template',
                'templates.delete' => 'ลบ Template',
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

        return $permissions;
    }

    private function seedRoles(): array
    {
        $definitions = [
            'super_admin' => [
                'name' => 'Super Admin',
                'description' => 'เข้าถึงและจัดการได้ทุกส่วนของระบบ',
                'is_system' => true,
            ],
            'admin' => [
                'name' => 'Admin',
                'description' => 'จัดการเนื้อหา หน้าเว็บ เมนู และผู้ดูแลระบบทั่วไป',
                'is_system' => true,
            ],
            'editor' => [
                'name' => 'Editor',
                'description' => 'จัดการเนื้อหา บทความ วัด หน้าเว็บ และ Media',
                'is_system' => true,
            ],
            'viewer' => [
                'name' => 'Viewer',
                'description' => 'ดูข้อมูลในระบบได้อย่างเดียว',
                'is_system' => true,
            ],
        ];

        $roles = [];

        foreach ($definitions as $key => $definition) {
            $roles[$key] = Role::updateOrCreate(
                ['name' => $definition['name']],
                $definition
            );
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
            'pages.*',
            'templates.view',
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
            'pages.view',
            'pages.create',
            'pages.update',
            'templates.view',
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

    private function seedDefaultAdmin(Role $role): void
    {
        Admin::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'username' => env('ADMIN_USERNAME', 'superadmin'),
                'password_hash' => Hash::make(env('ADMIN_PASSWORD', '12345678')),
                'role_id' => $role->id,
                'status' => 'active',
            ]
        );
    }
}
