<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminSession;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
    }

    public function test_dashboard_renders_admin_overview_metrics(): void
    {
        $this->withoutMiddleware(AdminAuthenticate::class);

        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'วัดทดสอบ Dashboard',
            'slug' => 'dashboard-temple',
            'status' => 'published',
        ]);

        Temple::query()->create([
            'content_id' => $content->id,
            'temple_type' => 'royal',
        ]);

        $this->authenticateAsDefaultAdminWithTrackedSession();

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('ภาพรวมระบบผู้ดูแล')
            ->assertSee('วัดทั้งหมด')
            ->assertSee('ความปลอดภัย 24 ชั่วโมง')
            ->assertSee('วัดทดสอบ Dashboard');
    }

    private function authenticateAsDefaultAdminWithTrackedSession(): Admin
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $sessionId = 'admin-test-session-'.$admin->id;

        $this->actingAs($admin, 'admin');
        $this->withCookie(config('session.cookie'), $sessionId);
        $this->withSession(['admin_test_session' => true]);

        AdminSession::query()->create([
            'admin_id' => $admin->id,
            'session_token_hash' => hash('sha256', $sessionId),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'last_seen_at' => now(),
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        return $admin;
    }
}
