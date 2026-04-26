<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // dashboard
            [
                'name' => 'View Dashboard',
                'key' => 'dashboard.view',
                'group_key' => 'dashboard',
                'description' => 'View dashboard',
            ],

            // users
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

            // roles
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

            // permissions
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

            // temples
            [
                'name' => 'View Temples',
                'key' => 'temples.view',
                'group_key' => 'temples',
                'description' => 'View temple list and detail',
            ],
            [
                'name' => 'Create Temples',
                'key' => 'temples.create',
                'group_key' => 'temples',
                'description' => 'Create new temples',
            ],
            [
                'name' => 'Update Temples',
                'key' => 'temples.update',
                'group_key' => 'temples',
                'description' => 'Update existing temples',
            ],
            [
                'name' => 'Delete Temples',
                'key' => 'temples.delete',
                'group_key' => 'temples',
                'description' => 'Delete temples',
            ],
            [
                'name' => 'Approve Temples',
                'key' => 'temples.approve',
                'group_key' => 'temples',
                'description' => 'Approve temples',
            ],
            [
                'name' => 'Publish Temples',
                'key' => 'temples.publish',
                'group_key' => 'temples',
                'description' => 'Publish temples',
            ],

            // articles
            [
                'name' => 'View Articles',
                'key' => 'articles.view',
                'group_key' => 'articles',
                'description' => 'View article list and detail',
            ],
            [
                'name' => 'Create Articles',
                'key' => 'articles.create',
                'group_key' => 'articles',
                'description' => 'Create new articles',
            ],
            [
                'name' => 'Update Articles',
                'key' => 'articles.update',
                'group_key' => 'articles',
                'description' => 'Update existing articles',
            ],
            [
                'name' => 'Delete Articles',
                'key' => 'articles.delete',
                'group_key' => 'articles',
                'description' => 'Delete articles',
            ],
            [
                'name' => 'Approve Articles',
                'key' => 'articles.approve',
                'group_key' => 'articles',
                'description' => 'Approve articles',
            ],
            [
                'name' => 'Publish Articles',
                'key' => 'articles.publish',
                'group_key' => 'articles',
                'description' => 'Publish articles',
            ],

            // categories
            [
                'name' => 'View Categories',
                'key' => 'categories.view',
                'group_key' => 'categories',
                'description' => 'View category list and detail',
            ],
            [
                'name' => 'Create Categories',
                'key' => 'categories.create',
                'group_key' => 'categories',
                'description' => 'Create new categories',
            ],
            [
                'name' => 'Update Categories',
                'key' => 'categories.update',
                'group_key' => 'categories',
                'description' => 'Update existing categories',
            ],
            [
                'name' => 'Delete Categories',
                'key' => 'categories.delete',
                'group_key' => 'categories',
                'description' => 'Delete categories',
            ],

            // media
            [
                'name' => 'View Media',
                'key' => 'media.view',
                'group_key' => 'media',
                'description' => 'View media list and detail',
            ],
            [
                'name' => 'Create Media',
                'key' => 'media.create',
                'group_key' => 'media',
                'description' => 'Create new media',
            ],
            [
                'name' => 'Update Media',
                'key' => 'media.update',
                'group_key' => 'media',
                'description' => 'Update existing media',
            ],
            [
                'name' => 'Delete Media',
                'key' => 'media.delete',
                'group_key' => 'media',
                'description' => 'Delete media',
            ],

            // settings
            [
                'name' => 'View Settings',
                'key' => 'settings.view',
                'group_key' => 'settings',
                'description' => 'View system settings',
            ],
            [
                'name' => 'Manage Settings',
                'key' => 'settings.manage',
                'group_key' => 'settings',
                'description' => 'Manage system settings',
            ],
            [
                'name' => 'Update Settings',
                'key' => 'settings.update',
                'group_key' => 'settings',
                'description' => 'Update system settings',
            ],

            // menus
            [
                'name' => 'View Menus',
                'key' => 'menus.view',
                'group_key' => 'menus',
                'description' => 'View menu list and detail',
            ],
            [
                'name' => 'Create Menus',
                'key' => 'menus.create',
                'group_key' => 'menus',
                'description' => 'Create new menus',
            ],
            [
                'name' => 'Update Menus',
                'key' => 'menus.update',
                'group_key' => 'menus',
                'description' => 'Update existing menus',
            ],
            [
                'name' => 'Delete Menus',
                'key' => 'menus.delete',
                'group_key' => 'menus',
                'description' => 'Delete menus',
            ],
            
            // pages
            [
                'name' => 'View Pages',
                'key' => 'pages.view',
                'group_key' => 'pages',
                'description' => 'View page list and detail',
            ],
            [
                'name' => 'Create Pages',
                'key' => 'pages.create',
                'group_key' => 'pages',
                'description' => 'Create new pages',
            ],
            [
                'name' => 'Update Pages',
                'key' => 'pages.update',
                'group_key' => 'pages',
                'description' => 'Update existing pages',
            ],
            [
                'name' => 'Delete Pages',
                'key' => 'pages.delete',
                'group_key' => 'pages',
                'description' => 'Delete pages',
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