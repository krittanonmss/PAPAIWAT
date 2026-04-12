<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $allPermissionIds = Permission::pluck('id')->all();

        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($allPermissionIds);
        }

        if ($adminRole) {
            $adminPermissionIds = Permission::whereIn('key', [
                'users.view',
                'users.create',
                'users.update',
            ])->pluck('id')->all();

            $adminRole->permissions()->sync($adminPermissionIds);
        }
    }
}