<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\Content\Category;
use App\Models\Content\Content;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class CategoryManagementFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_create_duplicate_thai_named_child_categories_without_empty_slug_collision(): void
    {
        $parent = Category::query()->create([
            'name' => 'หมวดหลักวัด',
            'slug' => 'temple-root',
            'type_key' => 'temple',
            'status' => 'active',
        ]);

        $payload = [
            'parent_id' => $parent->id,
            'name' => 'วัดประวัติศาสตร์',
            'type_key' => 'temple',
            'status' => 'active',
        ];

        $this->post(route('admin.categories.store'), $payload)
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();
        $this->post(route('admin.categories.store'), $payload)
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $categories = Category::query()
            ->where('parent_id', $parent->id)
            ->where('name', 'วัดประวัติศาสตร์')
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $categories);
        $this->assertNotSame('', $categories[0]->slug);
        $this->assertNotSame('', $categories[1]->slug);
        $this->assertNotSame($categories[0]->slug, $categories[1]->slug);
    }

    public function test_category_parent_must_use_same_type(): void
    {
        $templeParent = Category::query()->create([
            'name' => 'หมวดวัด',
            'slug' => 'temple-category',
            'type_key' => 'temple',
            'status' => 'active',
        ]);

        $this->post(route('admin.categories.store'), [
            'parent_id' => $templeParent->id,
            'name' => 'บทความธรรมะ',
            'type_key' => 'article',
            'status' => 'active',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_category_cannot_use_its_descendant_as_parent(): void
    {
        $root = Category::query()->create([
            'name' => 'หมวดหลัก',
            'slug' => 'root-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $child = Category::query()->create([
            'parent_id' => $root->id,
            'name' => 'หมวดย่อย',
            'slug' => 'child-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $this->put(route('admin.categories.update', $root), [
            'parent_id' => $child->id,
            'name' => $root->name,
            'type_key' => 'temple',
            'status' => 'active',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_category_edit_does_not_offer_descendants_as_parent_options(): void
    {
        $root = Category::query()->create([
            'name' => 'หมวดหลัก',
            'slug' => 'root-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $child = Category::query()->create([
            'parent_id' => $root->id,
            'name' => 'หมวดย่อยที่ห้ามเลือก',
            'slug' => 'blocked-child-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $this->get(route('admin.categories.edit', $root))
            ->assertOk()
            ->assertDontSee('value="'.$child->id.'"', false);
    }

    public function test_category_delete_is_blocked_when_content_uses_it(): void
    {
        $category = Category::query()->create([
            'name' => 'หมวดที่ถูกใช้งาน',
            'slug' => 'used-category',
            'type_key' => 'article',
            'status' => 'active',
        ]);

        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'บทความทดสอบ',
            'slug' => 'article-category-delete-test',
            'status' => 'draft',
        ]);

        $content->categories()->attach($category->id, [
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    public function test_moving_category_recalculates_descendant_levels_and_writes_audit_log(): void
    {
        $newRoot = Category::query()->create([
            'name' => 'หมวดหลักใหม่',
            'slug' => 'new-root-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $root = Category::query()->create([
            'name' => 'หมวดหลักเดิม',
            'slug' => 'old-root-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $child = Category::query()->create([
            'parent_id' => $root->id,
            'name' => 'หมวดย่อย',
            'slug' => 'child-to-recalculate',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $grandchild = Category::query()->create([
            'parent_id' => $child->id,
            'name' => 'หมวดชั้นสาม',
            'slug' => 'grandchild-to-recalculate',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 2,
        ]);

        $this->put(route('admin.categories.update', $root), [
            'parent_id' => $newRoot->id,
            'name' => $root->name,
            'type_key' => 'temple',
            'status' => 'active',
        ])->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $root->fresh()->level);
        $this->assertSame(2, $child->fresh()->level);
        $this->assertSame(3, $grandchild->fresh()->level);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'category.moved',
            'table_name' => 'categories',
            'record_id' => $root->id,
        ]);
    }

    public function test_admin_can_bulk_move_categories_under_new_parent(): void
    {
        $newParent = Category::query()->create([
            'name' => 'หมวดหลักใหม่',
            'slug' => 'bulk-new-parent',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $oldParent = Category::query()->create([
            'name' => 'หมวดหลักเดิม',
            'slug' => 'bulk-old-parent',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $first = Category::query()->create([
            'parent_id' => $oldParent->id,
            'name' => 'หมวดย่อยแรก',
            'slug' => 'bulk-first-child',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $second = Category::query()->create([
            'parent_id' => $oldParent->id,
            'name' => 'หมวดย่อยสอง',
            'slug' => 'bulk-second-child',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $grandchild = Category::query()->create([
            'parent_id' => $first->id,
            'name' => 'หมวดชั้นสาม',
            'slug' => 'bulk-grandchild',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 2,
        ]);

        $this->patch(route('admin.categories.bulk-move'), [
            'category_ids' => [$first->id, $second->id],
            'parent_id' => $newParent->id,
        ])->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertSame($newParent->id, $first->fresh()->parent_id);
        $this->assertSame(1, $first->fresh()->level);
        $this->assertSame($newParent->id, $second->fresh()->parent_id);
        $this->assertSame(1, $second->fresh()->level);
        $this->assertSame(2, $grandchild->fresh()->level);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'category.moved',
            'table_name' => 'categories',
            'record_id' => $first->id,
        ]);
    }

    public function test_category_max_depth_is_enforced(): void
    {
        $parent = null;

        for ($level = 0; $level <= Category::MAX_LEVEL; $level++) {
            $parent = Category::query()->create([
                'parent_id' => $parent?->id,
                'name' => 'Level '.$level,
                'slug' => 'level-'.$level,
                'type_key' => 'temple',
                'status' => 'active',
                'level' => $level,
            ]);
        }

        $this->post(route('admin.categories.store'), [
            'parent_id' => $parent->id,
            'name' => 'ลึกเกินกำหนด',
            'type_key' => 'temple',
            'status' => 'active',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_deleted_category_can_be_restored_from_index_flow(): void
    {
        $category = Category::query()->create([
            'name' => 'หมวดที่กู้คืนได้',
            'slug' => 'restorable-category',
            'type_key' => 'article',
            'status' => 'active',
        ]);

        $category->delete();

        $this->get(route('admin.categories.index', ['deleted' => 'only']))
            ->assertOk()
            ->assertSee('กู้คืน');

        $this->patch(route('admin.categories.restore', $category->id))
            ->assertRedirect(route('admin.categories.index', ['deleted' => 'with']));

        $this->assertFalse($category->fresh()->trashed());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'category.restored',
            'table_name' => 'categories',
            'record_id' => $category->id,
        ]);
    }

    public function test_category_parent_lookup_excludes_current_subtree(): void
    {
        $root = Category::query()->create([
            'name' => 'หมวดหลัก',
            'slug' => 'lookup-root-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 0,
        ]);

        $child = Category::query()->create([
            'parent_id' => $root->id,
            'name' => 'หมวดย่อยที่ต้องถูกซ่อน',
            'slug' => 'lookup-child-category',
            'type_key' => 'temple',
            'status' => 'active',
            'level' => 1,
        ]);

        $this->getJson(route('admin.lookups.categories', [
            'exclude_id' => $root->id,
            'q' => 'หมวด',
        ]))->assertOk()
            ->assertJsonMissing(['id' => (string) $root->id])
            ->assertJsonMissing(['id' => (string) $child->id]);
    }

    public function test_category_delete_permission_is_enforced(): void
    {
        $category = Category::query()->create([
            'name' => 'หมวดห้ามลบ',
            'slug' => 'permission-denied-category',
            'type_key' => 'article',
            'status' => 'active',
        ]);

        $this->actingAs($this->createAdminWithPermissions(['categories.view']), 'admin');

        $this->delete(route('admin.categories.destroy', $category))
            ->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionKeys): Admin
    {
        $role = Role::query()->create([
            'name' => 'Category Limited '.uniqid(),
            'role_key' => 'category_limited_'.uniqid(),
            'description' => 'Category limited role',
            'level' => 10,
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::query()
                ->whereIn('key', $permissionKeys)
                ->pluck('id')
                ->all()
        );

        return Admin::query()->create([
            'username' => 'category_limited_'.uniqid(),
            'email' => uniqid('category_limited_', true).'@example.com',
            'password_hash' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);
    }
}
