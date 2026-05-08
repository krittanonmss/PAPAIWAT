<?php

namespace Tests\Concerns;

trait MigratesAppDatabase
{
    protected function migrateSystemTables(): void
    {
        $this->artisan('migrate', ['--path' => 'database/migrations/system']);
    }

    protected function migrateAdminTables(): void
    {
        $this->artisan('migrate', ['--path' => 'database/migrations/admin']);
    }

    protected function migrateContentTables(): void
    {
        foreach ([
            'database/migrations/content',
            'database/migrations/content/categories',
            'database/migrations/content/media',
            'database/migrations/content/layout',
            'database/migrations/content/temple',
            'database/migrations/content/article',
        ] as $path) {
            $this->artisan('migrate', ['--path' => $path]);
        }
    }

    protected function migrateAdminContentTables(): void
    {
        $this->migrateSystemTables();
        $this->migrateAdminTables();
        $this->migrateContentTables();
    }
}
