<?php

namespace Database\Seeders;

use App\Models\Permission;
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
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }
    }
}