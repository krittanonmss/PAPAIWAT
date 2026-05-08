<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
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

        $this->admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
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
            'phone' => '0899999999',
        ])->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame('target-renamed', $target->username);
        $this->assertSame($newRole->id, $target->role_id);
        $this->assertSame($originalHash, $target->password_hash);

        $this->put(route('admin.users.update', $target), [
            'username' => 'target-renamed',
            'email' => 'target-renamed@example.com',
            'role_id' => $newRole->id,
            'phone' => '0899999999',
            'password' => 'ChangedPassword123',
            'password_confirmation' => 'ChangedPassword123',
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
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame($viewerRole->id, $target->fresh()->role_id);
        $this->assertNotSame($viewerRole->id, $this->admin->fresh()->role_id);

        $this->patch(route('admin.users.bulk-status'), [
            'admin_ids' => [$this->admin->id, $target->id],
            'status' => 'inactive',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('inactive', $target->fresh()->status);
        $this->assertSame('active', $this->admin->fresh()->status);
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
