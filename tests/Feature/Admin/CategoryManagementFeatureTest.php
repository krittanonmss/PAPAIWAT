<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Category;
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
}
