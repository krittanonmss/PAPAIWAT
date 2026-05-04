<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class CmsDetailPageSeeder extends Seeder
{
    public function run(): void
    {
        Template::updateOrCreate(
            ['key' => 'temple-detail'],
            [
                'name' => 'Temple Detail Default',
                'description' => 'Default hardcoded fallback template for temple detail pages.',
                'view_path' => 'frontend.templates.details.temple-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 40,
            ]
        );

        Template::updateOrCreate(
            ['key' => 'article-detail'],
            [
                'name' => 'Article Detail Default',
                'description' => 'Default hardcoded fallback template for article detail pages.',
                'view_path' => 'frontend.templates.details.article-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 60,
            ]
        );

        $articleListTemplate = Template::updateOrCreate(
            ['key' => 'article-list'],
            [
                'name' => 'Article List Default',
                'description' => 'Default article listing template.',
                'view_path' => 'frontend.templates.lists.article-list',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 50,
            ]
        );

        $this->seedDetailPage($articleListTemplate, [
            'title' => 'บทความ',
            'slug' => 'articles',
            'page_type' => 'article_list',
            'excerpt' => 'อ่านเรื่องราว วัฒนธรรม และข้อมูลที่เกี่ยวข้อง',
            'description' => 'หน้าแสดงรายการบทความทั้งหมด',
            'meta_title' => 'บทความ - PAPAIWAT',
            'meta_description' => 'รายการบทความจาก PAPAIWAT',
        ]);

    }

    private function seedDetailPage(Template $template, array $definition): void
    {
        $page = Page::updateOrCreate(
            ['slug' => $definition['slug']],
            [
                'template_id' => $template->id,
                'title' => $definition['title'],
                'page_type' => $definition['page_type'],
                'status' => 'published',
                'is_homepage' => false,
                'sort_order' => 0,
                'excerpt' => $definition['excerpt'],
                'description' => $definition['description'],
                'meta_title' => $definition['meta_title'],
                'meta_description' => $definition['meta_description'],
                'published_at' => now(),
            ]
        );
    }
}
