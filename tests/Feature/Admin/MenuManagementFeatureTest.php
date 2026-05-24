<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\Role;
use App\Models\Content\Content;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Models\Content\Layout\Page;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class MenuManagementFeatureTest extends TestCase
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

    public function test_menu_item_type_validation_and_blank_target_rel_are_enforced(): void
    {
        $menu = $this->menu();
        $page = Page::query()->create([
            'title' => 'About',
            'slug' => 'about',
            'page_type' => 'custom',
            'status' => 'published',
        ]);

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Page missing target',
            'menu_item_type' => 'page',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertSessionHasErrors('page_id');

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Bad external',
            'menu_item_type' => 'external_url',
            'external_url' => 'javascript:alert(1)',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertSessionHasErrors('external_url');

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Bad anchor',
            'menu_item_type' => 'anchor',
            'anchor' => 'contact',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertSessionHasErrors('anchor');

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Valid page',
            'menu_item_type' => 'page',
            'page_id' => $page->id,
            'target' => '_blank',
            'rel' => 'nofollow',
            'is_enabled' => '1',
        ])->assertRedirect(route('admin.content.menus.show', $menu));

        $this->assertDatabaseHas('menu_items', [
            'label' => 'Valid page',
            'page_id' => $page->id,
            'target' => '_blank',
            'rel' => 'nofollow noopener noreferrer',
        ]);
    }

    public function test_menu_item_parent_cannot_create_descendant_cycle(): void
    {
        $menu = $this->menu();
        $parent = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Parent',
            'slug' => 'parent',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
        ]);
        $child = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => 'Child',
            'slug' => 'child',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
        ]);

        $this->put(route('admin.content.menu-items.update', [$menu, $parent]), [
            'parent_id' => $child->id,
            'label' => 'Parent',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_default_menu_is_scoped_by_location_and_delete_is_guarded(): void
    {
        $header = $this->menu(['slug' => 'header-one', 'location_key' => 'header', 'is_default' => true]);
        $footer = $this->menu(['slug' => 'footer-one', 'location_key' => 'footer', 'is_default' => true]);

        $this->post(route('admin.content.menus.store'), [
            'name' => 'Header Two',
            'slug' => 'header-two',
            'location_key' => 'header',
            'status' => 'active',
            'is_default' => '1',
        ])->assertRedirect(route('admin.content.menus.index'));

        $this->assertFalse($header->fresh()->is_default);
        $this->assertTrue($footer->fresh()->is_default);

        $newHeader = Menu::query()->where('slug', 'header-two')->firstOrFail();
        $this->delete(route('admin.content.menus.destroy', $newHeader))
            ->assertSessionHasErrors('menu');

        MenuItem::query()->create([
            'menu_id' => $footer->id,
            'label' => 'Footer item',
            'slug' => 'footer-item',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
        ]);

        $this->delete(route('admin.content.menus.destroy', $footer))
            ->assertSessionHasErrors('menu');
    }

    public function test_menu_item_lookup_endpoints_search_pages_and_contents(): void
    {
        $menu = $this->menu();
        Page::query()->create([
            'title' => 'Lookup Page Target',
            'slug' => 'lookup-page-target',
            'page_type' => 'custom',
            'status' => 'published',
        ]);
        Content::query()->create([
            'content_type' => 'article',
            'title' => 'Lookup Content Target',
            'slug' => 'lookup-content-target',
            'status' => 'published',
        ]);

        $this->getJson(route('admin.content.menu-items.lookups.pages', [$menu, 'q' => 'Lookup Page']))
            ->assertOk()
            ->assertJsonPath('items.0.label', 'Lookup Page Target');

        $this->getJson(route('admin.content.menu-items.lookups.contents', [$menu, 'q' => 'Lookup Content']))
            ->assertOk()
            ->assertJsonPath('items.0.label', 'Lookup Content Target');
    }

    public function test_parameterless_route_ignores_stale_route_params_while_custom_route_validates_json(): void
    {
        $menu = $this->menu();
        $home = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'หน้าแรก',
            'slug' => 'home',
            'menu_item_type' => 'route',
            'route_name' => 'home',
            'route_params' => ['stale' => 'value'],
            'target' => '_self',
            'is_enabled' => true,
        ]);

        $this->put(route('admin.content.menu-items.update', [$menu, $home]), [
            'label' => 'หน้าแรก',
            'menu_item_type' => 'route',
            'route_name' => 'home',
            'route_params' => 'not-json-from-old-field',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertRedirect(route('admin.content.menus.show', $menu));

        $this->assertNull($home->fresh()->route_params);

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Article Route',
            'menu_item_type' => 'route',
            'route_name' => 'articles.show',
            'route_params' => 'not-json',
            'target' => '_self',
            'is_enabled' => '1',
        ])->assertSessionHasErrors('route_params');
    }

    public function test_editor_can_update_menu_structure_in_one_submission(): void
    {
        $menu = $this->menu();
        $first = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'First',
            'slug' => 'first',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);
        $second = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Second',
            'slug' => 'second',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 10,
        ]);

        $this->patch(route('admin.content.menus.structure.update', $menu), [
            'items' => [
                ['id' => $first->id, 'parent_id' => $second->id, 'sort_order' => 5],
                ['id' => $second->id, 'parent_id' => null, 'sort_order' => 0],
            ],
        ])->assertRedirect(route('admin.content.menus.show', $menu));

        $this->assertDatabaseHas('menu_items', [
            'id' => $first->id,
            'parent_id' => $second->id,
            'sort_order' => 5,
        ]);
    }

    public function test_menu_show_handles_route_items_missing_params_as_warning(): void
    {
        $menu = $this->menu();
        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Article Detail',
            'slug' => 'article-detail',
            'menu_item_type' => 'route',
            'route_name' => 'articles.show',
            'target' => '_self',
            'is_enabled' => true,
        ]);

        $this->get(route('admin.content.menus.show', $menu))
            ->assertOk()
            ->assertSee('ยังหา URL ปลายทางไม่ได้');
    }

    public function test_menu_structure_blocks_cycles_and_excessive_depth(): void
    {
        $menu = $this->menu();
        $root = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Root',
            'slug' => 'root',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);
        $child = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $root->id,
            'label' => 'Child',
            'slug' => 'child',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);
        $grandchild = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $child->id,
            'label' => 'Grandchild',
            'slug' => 'grandchild',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);
        $deep = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Deep',
            'slug' => 'deep',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        $this->patch(route('admin.content.menus.structure.update', $menu), [
            'items' => [
                ['id' => $root->id, 'parent_id' => $grandchild->id, 'sort_order' => 0],
                ['id' => $child->id, 'parent_id' => $root->id, 'sort_order' => 0],
                ['id' => $grandchild->id, 'parent_id' => $child->id, 'sort_order' => 0],
                ['id' => $deep->id, 'parent_id' => null, 'sort_order' => 0],
            ],
        ])->assertSessionHasErrors('items');

        $this->patch(route('admin.content.menus.structure.update', $menu), [
            'items' => [
                ['id' => $root->id, 'parent_id' => null, 'sort_order' => 0],
                ['id' => $child->id, 'parent_id' => $root->id, 'sort_order' => 0],
                ['id' => $grandchild->id, 'parent_id' => $child->id, 'sort_order' => 0],
                ['id' => $deep->id, 'parent_id' => $grandchild->id, 'sort_order' => 0],
            ],
        ])->assertSessionHasErrors('items');

        $this->patch(route('admin.content.menus.structure.update', $menu), [
            'items' => [
                ['id' => $root->id, 'parent_id' => null, 'sort_order' => 0],
                ['id' => $child->id, 'parent_id' => $root->id, 'sort_order' => 0],
                ['id' => $grandchild->id, 'parent_id' => $child->id, 'sort_order' => 0],
                ['id' => $deep->id, 'parent_id' => null, 'sort_order' => 10],
            ],
        ])->assertRedirect(route('admin.content.menus.show', $menu));
    }

    public function test_menu_item_create_permission_is_denied_without_specific_permission(): void
    {
        $viewerRole = Role::query()->where('role_key', 'viewer')->firstOrFail();
        $viewer = Admin::query()->create([
            'username' => 'viewer-menu',
            'email' => 'viewer-menu@example.com',
            'password_hash' => bcrypt('Password12345'),
            'role_id' => $viewerRole->id,
            'status' => 'active',
        ]);
        $menu = $this->menu();

        $this->actingAs($viewer, 'admin');

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'Denied',
            'menu_item_type' => 'heading',
            'target' => '_self',
        ])->assertForbidden();
    }

    private function menu(array $overrides = []): Menu
    {
        return Menu::query()->create(array_merge([
            'name' => 'Main Menu',
            'slug' => 'main-menu-' . Menu::query()->count(),
            'location_key' => 'header',
            'status' => 'active',
            'is_default' => false,
        ], $overrides));
    }
}
