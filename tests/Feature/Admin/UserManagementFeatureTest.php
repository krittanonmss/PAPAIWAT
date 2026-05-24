<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminSession;
use App\Models\Admin\AuditLog;
use App\Models\Admin\Role;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class UserManagementFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateSystemTables();
        $this->migrateAdminTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $this->admin = Admin::query()->where('status', 'active')->firstOrFail();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_create_user_with_strong_password(): void
    {
        $role = Role::query()->where('name', 'Editor')->firstOrFail();

        $this->post(route('admin.users.store'), [
            'username' => 'editor-user',
            'email' => 'editor@example.com',
            'password' => 'StrongPassword123',
            'password_confirmation' => 'StrongPassword123',
            'role_id' => $role->id,
            'status' => 'active',
            'phone' => '0812345678',
        ])->assertRedirect(route('admin.users.index'));

        $created = Admin::query()->where('email', 'editor@example.com')->firstOrFail();

        $this->assertSame('editor-user', $created->username);
        $this->assertSame($role->id, $created->role_id);
        $this->assertTrue(Hash::check('StrongPassword123', $created->password_hash));
    }

    public function test_user_management_pages_render_searchable_dropdowns(): void
    {
        $target = $this->createAdmin('render-target', 'render-target@example.com', Role::query()->where('name', 'Editor')->firstOrFail());

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('พิมพ์ชื่อบทบาท...')
            ->assertSee('พิมพ์สถานะ...')
            ->assertSee('name="role_id"', false)
            ->assertSee('name="status"', false);

        $this->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('พิมพ์ชื่อบทบาท...')
            ->assertSee('พิมพ์สถานะ...');

        $this->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertSee('พิมพ์ชื่อบทบาท...')
            ->assertSee('พิมพ์สถานะ...');
    }

    public function test_admin_can_update_user_without_replacing_password_then_change_password(): void
    {
        $role = Role::query()->where('name', 'Editor')->firstOrFail();
        $newRole = Role::query()->where('name', 'Viewer')->firstOrFail();
        $target = $this->createAdmin('target-user', 'target@example.com', $role);
        $originalHash = $target->password_hash;

        $this->put(route('admin.users.update', $target), [
            'username' => 'target-renamed',
            'email' => 'target-renamed@example.com',
            'role_id' => $newRole->id,
            'status' => 'active',
            'phone' => '0899999999',
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame('target-renamed', $target->username);
        $this->assertSame($newRole->id, $target->role_id);
        $this->assertSame($originalHash, $target->password_hash);

        $this->put(route('admin.users.update', $target), [
            'username' => 'target-renamed',
            'email' => 'target-renamed@example.com',
            'role_id' => $newRole->id,
            'status' => 'active',
            'phone' => '0899999999',
            'password' => 'ChangedPassword123',
            'password_confirmation' => 'ChangedPassword123',
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('ChangedPassword123', $target->fresh()->password_hash));
    }

    public function test_admin_cannot_delete_or_deactivate_own_account(): void
    {
        $this->patch(route('admin.users.status.update', $this->admin), [
            'status' => 'inactive',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('active', $this->admin->fresh()->status);

        $this->delete(route('admin.users.destroy', $this->admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertNotSoftDeleted('admins', ['id' => $this->admin->id]);
    }

    public function test_bulk_updates_skip_current_admin(): void
    {
        $editorRole = Role::query()->where('name', 'Editor')->firstOrFail();
        $viewerRole = Role::query()->where('name', 'Viewer')->firstOrFail();
        $target = $this->createAdmin('bulk-user', 'bulk@example.com', $editorRole);

        $this->patch(route('admin.users.bulk-role'), [
            'admin_ids' => [$this->admin->id, $target->id],
            'role_id' => $viewerRole->id,
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame($viewerRole->id, $target->fresh()->role_id);
        $this->assertNotSame($viewerRole->id, $this->admin->fresh()->role_id);

        $this->patch(route('admin.users.bulk-status'), [
            'admin_ids' => [$this->admin->id, $target->id],
            'status' => 'inactive',
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('inactive', $target->fresh()->status);
        $this->assertSame('active', $this->admin->fresh()->status);
    }

    public function test_sensitive_user_changes_require_current_password(): void
    {
        $editorRole = Role::query()->where('role_key', 'editor')->firstOrFail();
        $viewerRole = Role::query()->where('role_key', 'viewer')->firstOrFail();
        $target = $this->createAdmin('sensitive-user', 'sensitive@example.com', $editorRole);

        $this->put(route('admin.users.update', $target), [
            'username' => 'sensitive-user',
            'email' => 'sensitive@example.com',
            'role_id' => $viewerRole->id,
            'status' => 'active',
            'phone' => null,
        ])->assertSessionHasErrors('current_password');

        $this->assertSame($editorRole->id, $target->fresh()->role_id);
    }

    public function test_role_hierarchy_blocks_non_super_admin_from_managing_equal_or_higher_users(): void
    {
        $adminRole = Role::query()->where('role_key', 'admin')->firstOrFail();
        $editorRole = Role::query()->where('role_key', 'editor')->firstOrFail();
        $viewerRole = Role::query()->where('role_key', 'viewer')->firstOrFail();
        $actor = $this->createAdmin('role-admin', 'role-admin@example.com', $adminRole);
        $peer = $this->createAdmin('peer-admin', 'peer-admin@example.com', $adminRole);

        $this->actingAs($actor, 'admin');

        $this->put(route('admin.users.update', $peer), [
            'username' => 'peer-admin',
            'email' => 'peer-admin@example.com',
            'role_id' => $viewerRole->id,
            'status' => 'active',
            'phone' => null,
            'current_password' => 'OriginalPassword123',
        ])->assertSessionHasErrors('user');

        $editable = $this->createAdmin('editor-target', 'editor-target@example.com', $editorRole);

        $this->put(route('admin.users.update', $editable), [
            'username' => 'editor-target',
            'email' => 'editor-target@example.com',
            'role_id' => $adminRole->id,
            'status' => 'active',
            'phone' => null,
            'current_password' => 'OriginalPassword123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame($editorRole->id, $editable->fresh()->role_id);
        $this->assertTrue(session()->has('error'));
    }

    public function test_last_active_super_admin_cannot_be_downgraded_deactivated_or_deleted(): void
    {
        $viewerRole = Role::query()->where('role_key', 'viewer')->firstOrFail();

        $this->put(route('admin.users.update', $this->admin), [
            'username' => $this->admin->username,
            'email' => $this->admin->email,
            'role_id' => $viewerRole->id,
            'status' => 'active',
            'phone' => $this->admin->phone,
            'current_password' => 'ChangeMe12345',
        ])->assertSessionHasErrors('user');

        $this->patch(route('admin.users.status.update', $this->admin), [
            'status' => 'inactive',
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('active', $this->admin->fresh()->status);
        $this->assertSame('super_admin', $this->admin->fresh()->role->role_key);
    }

    public function test_changing_role_status_or_password_revokes_target_sessions_and_writes_audit_logs(): void
    {
        $editorRole = Role::query()->where('role_key', 'editor')->firstOrFail();
        $viewerRole = Role::query()->where('role_key', 'viewer')->firstOrFail();
        $target = $this->createAdmin('session-target', 'session-target@example.com', $editorRole);

        AdminSession::query()->create([
            'admin_id' => $target->id,
            'session_token_hash' => hash('sha256', 'target-session'),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'last_seen_at' => now(),
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        $this->put(route('admin.users.update', $target), [
            'username' => 'session-target',
            'email' => 'session-target@example.com',
            'role_id' => $viewerRole->id,
            'status' => 'inactive',
            'phone' => null,
            'password' => 'ChangedPassword123',
            'password_confirmation' => 'ChangedPassword123',
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame(0, AdminSession::query()->where('admin_id', $target->id)->count());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.role_changed',
            'table_name' => 'admins',
            'record_id' => $target->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.status_changed',
            'table_name' => 'admins',
            'record_id' => $target->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.password_changed',
            'table_name' => 'admins',
            'record_id' => $target->id,
        ]);
    }

    public function test_deleted_admin_can_be_restored_with_current_password(): void
    {
        $editorRole = Role::query()->where('role_key', 'editor')->firstOrFail();
        $target = $this->createAdmin('restore-user', 'restore@example.com', $editorRole);
        $target->delete();

        $this->patch(route('admin.users.restore', $target->id), [
            'current_password' => 'ChangeMe12345',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertNotSoftDeleted('admins', ['id' => $target->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.restored',
            'table_name' => 'admins',
            'record_id' => $target->id,
        ]);
    }

    private function createAdmin(string $username, string $email, Role $role): Admin
    {
        return Admin::query()->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => Hash::make('OriginalPassword123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }
}
