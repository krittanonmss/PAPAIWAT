<?php

namespace Tests\Feature;

use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Support\FooterSettings;
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
        $this->assertStringContainsString('href="/contact"', $footerHtml);
        $this->assertStringNotContainsString('Header Only', $footerHtml);
        $this->assertStringNotContainsString('ซ่อนใน Footer', $footerHtml);
    }

    public function test_frontend_footer_uses_custom_footer_settings(): void
    {
        FooterSettings::save([
            'brand_title' => 'วัดไทย Custom',
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

        $this->assertStringContainsString('วัดไทย Custom', $footerHtml);
        $this->assertStringContainsString('ข้อความ footer ที่จัดการเอง', $footerHtml);
        $this->assertStringContainsString('หมายเหตุท้ายเว็บ', $footerHtml);
        $this->assertStringContainsString('© '.date('Y').' โดย วัดไทย Custom', $footerHtml);
        $this->assertStringNotContainsString('หน้าแรก</a>', $footerHtml);
    }
}
