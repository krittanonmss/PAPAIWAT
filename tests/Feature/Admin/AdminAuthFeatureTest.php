<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminSession;
use App\Models\Admin\LoginLog;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class AdminAuthFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateSystemTables();
        $this->migrateAdminTables();
        $this->seed(SystemAccessSeeder::class);
    }

    public function test_admin_can_login_with_remember_me_and_session_is_tracked(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ChangeMe12345',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotNull($admin->fresh()->remember_token);
        $this->assertDatabaseHas('admin_sessions', [
            'admin_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('login_logs', [
            'admin_id' => $admin->id,
            'status' => 'success',
        ]);
    }

    public function test_admin_login_response_has_security_headers(): void
    {
        $response = $this->get(route('admin.login'));

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'same-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    public function test_failed_login_attempts_are_rate_limited(): void
    {
        RateLimiter::clear('admin-login:admin@example.com|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.store'), [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertSame(5, LoginLog::query()->where('reason', 'invalid_password')->count());
        $this->assertDatabaseHas('login_logs', [
            'email' => 'admin@example.com',
            'status' => 'failed',
            'reason' => 'rate_limited',
        ]);
    }

    public function test_login_normalizes_email_before_authentication_and_rate_limiting(): void
    {
        $response = $this->post(route('admin.login.store'), [
            'email' => ' ADMIN@EXAMPLE.COM ',
            'password' => 'ChangeMe12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated('admin');
        $this->assertDatabaseHas('login_logs', [
            'email' => 'admin@example.com',
            'status' => 'success',
        ]);
    }

    public function test_login_rehashes_legacy_password_hashes(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();

        config(['hashing.bcrypt.rounds' => 4]);
        $legacyHash = Hash::make('ChangeMe12345', ['rounds' => 4]);
        config(['hashing.bcrypt.rounds' => 12]);

        $admin->forceFill([
            'password_hash' => $legacyHash,
        ])->save();

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.dashboard'));

        $admin->refresh();

        $this->assertTrue(Hash::check('ChangeMe12345', $admin->password_hash));
        $this->assertNotSame($legacyHash, $admin->password_hash);
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $admin->update(['status' => 'inactive']);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ChangeMe12345',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
        $this->assertDatabaseHas('login_logs', [
            'admin_id' => $admin->id,
            'status' => 'failed',
            'reason' => 'inactive_or_suspended',
        ]);
    }

    public function test_protected_admin_pages_require_a_tracked_session(): void
    {
        $this->authenticateAsDefaultAdminWithTrackedSession();

        AdminSession::query()->delete();

        $this->get(route('admin.profile.edit'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_tracked_admin_session_is_bound_to_user_agent(): void
    {
        $this->authenticateAsDefaultAdminWithTrackedSession();

        $this->withHeader('User-Agent', 'Different Browser')
            ->get(route('admin.profile.edit'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_remembered_admin_without_tracked_session_must_login_again(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin, 'admin');

        AdminSession::query()->delete();

        $this->get(route('admin.profile.edit'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_login_prunes_expired_and_old_active_sessions(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();

        AdminSession::query()->create([
            'admin_id' => $admin->id,
            'session_token_hash' => hash('sha256', 'expired-session'),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'last_seen_at' => now()->subDays(2),
            'expires_at' => now()->subMinute(),
            'created_at' => now()->subDays(2),
        ]);

        for ($index = 1; $index <= 5; $index++) {
            AdminSession::query()->create([
                'admin_id' => $admin->id,
                'session_token_hash' => hash('sha256', "old-session-{$index}"),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Symfony',
                'last_seen_at' => now()->subMinutes(10 - $index),
                'expires_at' => now()->addHour(),
                'created_at' => now()->subMinutes(10 - $index),
            ]);
        }

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertSame(5, AdminSession::query()->where('admin_id', $admin->id)->count());
        $this->assertDatabaseMissing('admin_sessions', [
            'session_token_hash' => hash('sha256', 'expired-session'),
        ]);
        $this->assertDatabaseMissing('admin_sessions', [
            'session_token_hash' => hash('sha256', 'old-session-1'),
        ]);
    }

    public function test_profile_owner_can_update_own_password_after_confirming_current_password(): void
    {
        $this->withoutMiddleware(AdminAuthenticate::class);

        $admin = $this->authenticateAsDefaultAdminWithTrackedSession();

        AdminSession::query()->create([
            'admin_id' => $admin->id,
            'session_token_hash' => hash('sha256', 'another-session'),
            'ip_address' => '127.0.0.2',
            'user_agent' => 'PHPUnit',
            'last_seen_at' => now(),
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        $this->put(route('admin.profile.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'NewPassword12345',
            'password_confirmation' => 'NewPassword12345',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('admin.profile.password.update'), [
            'current_password' => 'ChangeMe12345',
            'password' => 'NewPassword12345',
            'password_confirmation' => 'NewPassword12345',
        ])->assertRedirect(route('admin.profile.edit'));

        $admin->refresh();

        $this->assertTrue(Hash::check('NewPassword12345', $admin->password_hash));
        $this->assertSame(0, AdminSession::query()
            ->where('admin_id', $admin->id)
            ->where('session_token_hash', hash('sha256', 'another-session'))
            ->count());
    }

    public function test_profile_owner_can_update_basic_profile_without_user_update_permission(): void
    {
        $this->withoutMiddleware(AdminAuthenticate::class);

        $this->authenticateAsDefaultAdminWithTrackedSession();

        $this->put(route('admin.profile.update'), [
            'username' => 'profile-owner',
            'email' => 'profile-owner@example.com',
            'phone' => '0812345678',
        ])->assertRedirect(route('admin.profile.edit'));

        $this->assertDatabaseHas('admins', [
            'username' => 'profile-owner',
            'email' => 'profile-owner@example.com',
            'phone' => '0812345678',
        ]);
    }

    private function loginAsDefaultAdmin(): void
    {
        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.dashboard'));
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
