<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class SystemTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'page-builder',
                'name' => 'Page Builder / Blank Canvas',
                'description' => 'หน้าเปล่าสำหรับประกอบด้วย block จาก Page Builder',
                'view_path' => 'frontend.templates.pages.builder',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'page-default',
                'name' => 'Generic Page Default',
                'description' => 'หน้า default แบบ fallback สำหรับข้อมูลหน้าแบบเดิม',
                'view_path' => 'frontend.templates.pages.default',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 10,
            ],
            [
                'key' => 'home-default',
                'name' => 'Home Default',
                'description' => 'Homepage template เดิม รองรับ Page Builder เมื่อมี block',
                'view_path' => 'frontend.templates.pages.home-default',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 20,
            ],
            [
                'key' => 'home-focus',
                'name' => 'Home Focus',
                'description' => 'Homepage focus template เดิม รองรับ Page Builder เมื่อมี block',
                'view_path' => 'frontend.templates.pages.home-focus',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 21,
            ],
            [
                'key' => 'home-split',
                'name' => 'Home Split',
                'description' => 'Homepage split template เดิม รองรับ Page Builder เมื่อมี block',
                'view_path' => 'frontend.templates.pages.home-split',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 22,
            ],
            [
                'key' => 'listing-page',
                'name' => 'Generic Listing Page',
                'description' => 'Template รายการทั่วไป',
                'view_path' => 'frontend.templates.pages.listing',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 30,
            ],
            [
                'key' => 'temple-list',
                'name' => 'Temple List',
                'description' => 'หน้า list วัด รองรับ block หน้ารวมวัด',
                'view_path' => 'frontend.templates.lists.temple-list',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 40,
            ],
            [
                'key' => 'article-list',
                'name' => 'Article List',
                'description' => 'หน้า list บทความ รองรับ block หน้ารวมบทความ',
                'view_path' => 'frontend.templates.lists.article-list',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 41,
            ],
            [
                'key' => 'temple-detail',
                'name' => 'Temple Detail Default',
                'description' => 'Template หลักสำหรับหน้าแสดงรายละเอียดวัด',
                'view_path' => 'frontend.templates.details.temple-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 50,
            ],
            [
                'key' => 'temple-compact-detail',
                'name' => 'Temple Detail Compact',
                'description' => 'Template รายละเอียดวัดแบบ compact',
                'view_path' => 'frontend.templates.details.temple-compact',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 51,
            ],
            [
                'key' => 'article-detail',
                'name' => 'Article Detail Default',
                'description' => 'Template หลักสำหรับหน้าแสดงรายละเอียดบทความ',
                'view_path' => 'frontend.templates.details.article-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 60,
            ],
            [
                'key' => 'article-editorial-detail',
                'name' => 'Article Detail Editorial',
                'description' => 'Template บทความแบบ editorial',
                'view_path' => 'frontend.templates.details.article-editorial',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 61,
            ],
            [
                'key' => 'admin-preview-safe',
                'name' => 'Admin Preview Safe',
                'description' => 'Template สำหรับ fallback preview ใน iframe ของ admin',
                'view_path' => 'frontend.templates.previews.admin-iframe',
                'status' => 'inactive',
                'is_default' => false,
                'sort_order' => 90,
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
