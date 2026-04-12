<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if (! $superAdminRole) {
            throw new RuntimeException('Role "Super Admin" not found.');
        }

        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'superadmin',
                'password_hash' => Hash::make('123456'),
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ]
        );
    }
}