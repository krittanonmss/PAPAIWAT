<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemAccessSeeder::class,
            SystemTemplateSeeder::class,
            DraftTempleAndDhammaSeeder::class,
            DraftCategorySeeder::class,
            ContentCategoryAssignmentSeeder::class,
        ]);
    }
}
