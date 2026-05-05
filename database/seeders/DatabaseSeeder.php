<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminSeeder::class,
            FrontendTemplateSeeder::class,
            HomePageSeeder::class,
            TempleListPageSeeder::class,
            CmsDetailPageSeeder::class,
            TempleTemplateSeeder::class,

        ]);
    }
}
