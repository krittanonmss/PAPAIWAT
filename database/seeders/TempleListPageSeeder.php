<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class TempleListPageSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::where('key', 'temple-list')->firstOrFail();

        $page = Page::updateOrCreate(
            ['slug' => 'temple-list'],
            [
                'template_id' => $template->id,
                'title' => 'รายการวัด',
                'page_type' => 'custom',
                'status' => 'published',
                'is_homepage' => false,
                'sort_order' => 0,
                'excerpt' => 'ค้นพบวัดทั่วประเทศไทย',
                'description' => 'รวมข้อมูลวัดทั่วไทย',
                'meta_title' => 'รายการวัด - PAPAIWAT',
                'meta_description' => 'รวมวัดทั่วประเทศไทย',
                'published_at' => now(),
            ]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'temple_list_main',
            ],
            [
                'name' => 'Temple List',
                'component_key' => 'temple_list',
                'settings' => [
                    'limit' => 20,
                    'source' => 'all',
                ],
                'content' => [
                    'title' => 'รายการวัดทั้งหมด',
                ],
                'status' => 'active',
                'is_visible' => true,
                'sort_order' => 1,
            ]
        );
    }
}