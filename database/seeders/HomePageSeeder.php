<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Page;
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

    }
}
