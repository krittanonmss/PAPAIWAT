<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::where('key', 'home-default')->firstOrFail();

        $page = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'template_id' => $template->id,
                'title' => 'หน้าแรก',
                'page_type' => 'custom',
                'status' => 'published',
                'is_homepage' => true,
                'sort_order' => 0,
                'excerpt' => 'ค้นพบวัดและวัฒนธรรมไทย',
                'description' => 'หน้าแรกของระบบ PAPAIWAT',
                'meta_title' => 'PAPAIWAT - ค้นหาวัดทั่วไทย',
                'meta_description' => 'รวมข้อมูลวัดทั่วไทย สถานที่ท่องเที่ยว ศาสนา และวัฒนธรรม',
                'published_at' => now(),
            ]
        );

        // Hero
        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'hero',
            ],
            [
                'name' => 'Hero Section',
                'component_key' => 'home.hero',
                'settings' => [
                    'title' => 'ค้นหาวัดทั่วประเทศไทย',
                    'subtitle' => 'สำรวจวัดและวัฒนธรรมไทย',
                ],
                'content' => [],
                'status' => 'active',
                'is_visible' => true,
                'sort_order' => 1,
            ]
        );

        // Temple list
        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'featured_temples',
            ],
            [
                'name' => 'Featured Temples',
                'component_key' => 'temple_list',
                'settings' => [
                    'limit' => 8,
                    'source' => 'featured',
                ],
                'content' => [
                    'title' => 'วัดแนะนำ',
                    'subtitle' => 'สถานที่ยอดนิยม',
                ],
                'status' => 'active',
                'is_visible' => true,
                'sort_order' => 2,
            ]
        );
    }
}