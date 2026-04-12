<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'View Roles',
                'key' => 'roles.view',
                'group_key' => 'roles',
                'description' => 'View role list and detail',
            ],
            [
                'name' => 'Create Roles',
                'key' => 'roles.create',
                'group_key' => 'roles',
                'description' => 'Create new roles',
            ],
            [
                'name' => 'Update Roles',
                'key' => 'roles.update',
                'group_key' => 'roles',
                'description' => 'Update existing roles',
            ],
            [
                'name' => 'Delete Roles',
                'key' => 'roles.delete',
                'group_key' => 'roles',
                'description' => 'Delete roles',
            ],
            [
                'name' => 'Manage Role Permissions',
                'key' => 'roles.permissions',
                'group_key' => 'roles',
                'description' => 'Assign permissions to roles',
            ],
            [
                'name' => 'View Users',
                'key' => 'users.view',
                'group_key' => 'users',
                'description' => 'View user list and detail',
            ],
            [
                'name' => 'Create Users',
                'key' => 'users.create',
                'group_key' => 'users',
                'description' => 'Create new users',
            ],
            [
                'name' => 'Update Users',
                'key' => 'users.update',
                'group_key' => 'users',
                'description' => 'Update existing users',
            ],
            [
                'name' => 'Delete Users',
                'key' => 'users.delete',
                'group_key' => 'users',
                'description' => 'Delete users',
            ],

            [
                'name' => 'View Permissions',
                'key' => 'permissions.view',
                'group_key' => 'permissions',
                'description' => 'View permission list and detail',
            ],
            [
                'name' => 'Create Permissions',
                'key' => 'permissions.create',
                'group_key' => 'permissions',
                'description' => 'Create new permissions',
            ],
            [
                'name' => 'Update Permissions',
                'key' => 'permissions.update',
                'group_key' => 'permissions',
                'description' => 'Update existing permissions',
            ],
            [
                'name' => 'Delete Permissions',
                'key' => 'permissions.delete',
                'group_key' => 'permissions',
                'description' => 'Delete permissions',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }
    }
}