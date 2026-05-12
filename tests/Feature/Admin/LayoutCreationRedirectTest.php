<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class LayoutCreationRedirectTest extends TestCase
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

    public function test_template_create_redirects_to_template_index(): void
    {
        $this->post(route('admin.content.templates.store'), [
            'name' => 'Detail Template',
            'key' => 'detail-template',
            'view_path' => 'frontend.templates.pages.default',
            'status' => 'active',
        ])->assertRedirect(route('admin.content.templates.index'));
    }

    public function test_menu_create_redirects_to_menu_index(): void
    {
        $this->post(route('admin.content.menus.store'), [
            'name' => 'Main Menu',
            'slug' => 'main-menu',
            'location_key' => 'main',
            'status' => 'active',
        ])->assertRedirect(route('admin.content.menus.index'));
    }

    public function test_page_create_redirects_to_page_index(): void
    {
        $this->post(route('admin.content.pages.store'), [
            'title' => 'About Page',
            'slug' => 'about-page',
            'page_type' => 'default',
            'status' => 'draft',
        ])->assertRedirect(route('admin.content.pages.index'));
    }

    public function test_footer_menu_item_can_be_created_as_non_link_heading(): void
    {
        $menu = Menu::query()->create([
            'name' => 'Footer Menu',
            'slug' => 'footer-menu',
            'location_key' => 'footer',
            'status' => 'active',
        ]);

        $this->post(route('admin.content.menu-items.store', $menu), [
            'label' => 'ข้อมูลเว็บไซต์',
            'menu_item_type' => 'heading',
            'target' => '_blank',
            'external_url' => 'https://example.com/should-be-ignored',
            'is_enabled' => '1',
        ])->assertRedirect(route('admin.content.menus.show', $menu));

        $item = MenuItem::query()->where('menu_id', $menu->id)->firstOrFail();

        $this->assertSame('heading', $item->menu_item_type);
        $this->assertNull($item->external_url);
        $this->assertSame('_self', $item->target);
    }

    public function test_footer_settings_can_be_updated(): void
    {
        $this->put(route('admin.content.footer.update'), [
            'brand_title' => 'Custom Footer',
            'brand_description' => 'ข้อความ footer ที่แก้เองได้',
            'footer_note' => 'เปิดทุกวัน',
            'copyright_text' => '© {year} {brand}',
            'show_brand' => '1',
            'show_menu' => '1',
            'show_bottom_bar' => '1',
            'show_border' => '1',
            'background_style' => 'solid',
            'column_count' => '5',
        ])->assertRedirect(route('admin.content.footer.edit'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'footer',
            'group_key' => 'layout',
        ]);
    }
}
