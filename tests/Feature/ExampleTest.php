<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        foreach ($this->migrationPaths() as $path) {
            $this->artisan('migrate', ['--path' => $path]);
        }

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
        $this->assertDatabaseHas('admins', ['email' => 'admin@example.com']);
        $this->assertDatabaseHas('permissions', ['key' => 'dashboard.view']);
        $this->assertDatabaseHas('templates', ['key' => 'page-builder']);
    }

    private function migrationPaths(): array
    {
        return [
            'database/migrations/system',
            'database/migrations/admin',
            'database/migrations/content/categories',
            'database/migrations/content/media',
            'database/migrations/content',
            'database/migrations/content/temple',
            'database/migrations/content/article',
            'database/migrations/content/layout',
        ];
    }
}
