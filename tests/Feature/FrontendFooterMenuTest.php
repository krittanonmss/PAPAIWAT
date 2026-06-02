<?php

namespace Tests\Feature;

use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Support\FooterSettings;
use App\Support\SiteSettings;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class FrontendFooterMenuTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
    }

    public function test_frontend_footer_uses_active_footer_menu_items(): void
    {
        $headerMenu = Menu::query()->create([
            'name' => 'Header Menu',
            'slug' => 'header-menu',
            'location_key' => 'header',
            'status' => 'active',
        ]);

        MenuItem::query()->create([
            'menu_id' => $headerMenu->id,
            'label' => 'Header Only',
            'menu_item_type' => 'external_url',
            'external_url' => '/header-only',
            'target' => '_self',
            'is_enabled' => true,
        ]);

        $footerMenu = Menu::query()->create([
            'name' => 'Footer Menu',
            'slug' => 'footer-menu',
            'location_key' => 'footer',
            'status' => 'active',
        ]);

        MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'label' => 'หน้าแรก Footer',
            'menu_item_type' => 'route',
            'route_name' => 'home',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $infoColumn = MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'label' => 'ข้อมูลเว็บไซต์',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 2,
        ]);

        MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'parent_id' => $infoColumn->id,
            'label' => 'ติดต่อเรา Footer',
            'menu_item_type' => 'external_url',
            'external_url' => '/contact',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $nestedFooterItem = MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'parent_id' => $infoColumn->id,
            'label' => 'คู่มือ Footer',
            'menu_item_type' => 'external_url',
            'external_url' => '/guide',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 2,
        ]);

        MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'parent_id' => $nestedFooterItem->id,
            'label' => 'คู่มือย่อย Footer',
            'menu_item_type' => 'external_url',
            'external_url' => '/guide/detail',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        MenuItem::query()->create([
            'menu_id' => $footerMenu->id,
            'label' => 'ซ่อนใน Footer',
            'menu_item_type' => 'external_url',
            'external_url' => '/hidden',
            'target' => '_self',
            'is_enabled' => false,
            'sort_order' => 3,
        ]);

        $footerHtml = view('frontend.partials.footer')->render();

        $this->assertStringContainsString('หน้าแรก Footer', $footerHtml);
        $this->assertStringContainsString('ข้อมูลเว็บไซต์', $footerHtml);
        $this->assertStringContainsString('ติดต่อเรา Footer', $footerHtml);
        $this->assertStringContainsString('คู่มือย่อย Footer', $footerHtml);
        $this->assertStringContainsString('href="/contact"', $footerHtml);
        $this->assertStringContainsString('href="/guide/detail"', $footerHtml);
        $this->assertStringNotContainsString('Header Only', $footerHtml);
        $this->assertStringNotContainsString('ซ่อนใน Footer', $footerHtml);
    }

    public function test_frontend_footer_uses_custom_footer_settings(): void
    {
        SiteSettings::saveGroup('general', [
            'site_name' => 'วัดไทย Directory',
            'tagline' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'contact_address' => null,
            'locale' => 'th',
            'timezone' => 'Asia/Bangkok',
        ]);

        FooterSettings::save([
            'brand_description' => 'ข้อความ footer ที่จัดการเอง',
            'footer_note' => 'หมายเหตุท้ายเว็บ',
            'copyright_text' => '© {year} โดย {brand}',
            'show_brand' => true,
            'show_menu' => false,
            'show_bottom_bar' => true,
            'show_border' => false,
            'background_style' => 'solid',
            'column_count' => '3',
        ]);

        $footerHtml = view('frontend.partials.footer')->render();

        $this->assertStringContainsString('วัดไทย Directory', $footerHtml);
        $this->assertStringContainsString('ข้อความ footer ที่จัดการเอง', $footerHtml);
        $this->assertStringContainsString('หมายเหตุท้ายเว็บ', $footerHtml);
        $this->assertStringContainsString('© '.date('Y').' โดย วัดไทย Directory', $footerHtml);
        $this->assertStringContainsString('grid h-11 w-11', $footerHtml);
        $this->assertStringNotContainsString('หน้าแรก</a>', $footerHtml);
    }

    public function test_site_name_setting_drives_header_and_footer_brand(): void
    {
        SiteSettings::saveGroup('general', [
            'site_name' => 'เที่ยววัดไทย',
            'tagline' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'contact_address' => null,
            'locale' => 'th',
            'timezone' => 'Asia/Bangkok',
        ]);

        FooterSettings::save([
            'brand_title' => 'ชื่อเก่าใน Footer',
            'brand_description' => 'ข้อความ footer',
            'copyright_text' => '© {year} {brand}',
            'show_brand' => true,
            'show_menu' => false,
            'show_bottom_bar' => true,
            'show_border' => true,
            'background_style' => 'glass',
            'column_count' => '4',
        ]);

        $headerHtml = view('frontend.partials.header')->render();
        $footerHtml = view('frontend.partials.footer')->render();

        $this->assertStringContainsString('เที่ยววัดไทย', $headerHtml);
        $this->assertStringContainsString('เที่ยววัดไทย', $footerHtml);
        $this->assertStringContainsString('© '.date('Y').' เที่ยววัดไทย', $footerHtml);
        $this->assertStringNotContainsString('ชื่อเก่าใน Footer', $footerHtml);
    }

    public function test_frontend_navigation_renders_nested_dropdown_menu_items(): void
    {
        $headerMenu = Menu::query()->create([
            'name' => 'Header Menu',
            'slug' => 'header-menu',
            'location_key' => 'header',
            'status' => 'active',
        ]);

        $parent = MenuItem::query()->create([
            'menu_id' => $headerMenu->id,
            'label' => 'สถานที่',
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $child = MenuItem::query()->create([
            'menu_id' => $headerMenu->id,
            'parent_id' => $parent->id,
            'label' => 'วัดภาคเหนือ',
            'menu_item_type' => 'external_url',
            'external_url' => '/north',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        MenuItem::query()->create([
            'menu_id' => $headerMenu->id,
            'parent_id' => $child->id,
            'label' => 'เชียงใหม่',
            'menu_item_type' => 'external_url',
            'external_url' => '/north/chiang-mai',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $navigationHtml = view('frontend.partials.navigation')->render();

        $this->assertStringContainsString('สถานที่', $navigationHtml);
        $this->assertStringContainsString('วัดภาคเหนือ', $navigationHtml);
        $this->assertStringContainsString('เชียงใหม่', $navigationHtml);
        $this->assertStringContainsString('left-full top-0', $navigationHtml);
        $this->assertStringContainsString('href="/north/chiang-mai"', $navigationHtml);
    }
}
