<?php

namespace Database\Seeders;

use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Models\Content\Temple\TempleOpeningHour;
use Illuminate\Database\Seeder;

class DemoTempleContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = Content::updateOrCreate(
            ['content_type' => 'temple', 'slug' => 'demo-temple'],
            [
                'title' => 'วัดตัวอย่าง PAPAIWAT',
                'excerpt' => 'ข้อมูลวัดตัวอย่างสำหรับทดสอบหน้า CMS-managed detail',
                'description' => 'วัดตัวอย่างนี้ใช้สำหรับตรวจสอบการแสดงผลของ sections ในหน้ารายละเอียดวัด โดยไม่กระทบข้อมูลจริง',
                'status' => 'published',
                'is_featured' => true,
                'is_popular' => false,
                'meta_title' => 'วัดตัวอย่าง PAPAIWAT',
                'meta_description' => 'ข้อมูลตัวอย่างสำหรับทดสอบหน้ารายละเอียดวัด',
                'published_at' => now(),
            ]
        );

        $temple = Temple::updateOrCreate(
            ['content_id' => $content->id],
            [
                'temple_type' => 'วัดราษฎร์',
                'sect' => 'มหานิกาย',
                'architecture_style' => 'ไทยประยุกต์',
                'founded_year' => '2567',
                'history' => 'ประวัติตัวอย่างสำหรับแสดงใน section เนื้อหาวัด',
                'dress_code' => 'แต่งกายสุภาพเมื่อเข้าพื้นที่วัด',
                'recommended_visit_start_time' => '08:00',
                'recommended_visit_end_time' => '17:00',
            ]
        );

        TempleAddress::updateOrCreate(
            ['temple_id' => $temple->id],
            [
                'address_line' => '123 ถนนตัวอย่าง',
                'province' => 'กรุงเทพมหานคร',
                'district' => 'พระนคร',
                'subdistrict' => 'พระบรมมหาราชวัง',
                'postal_code' => '10200',
            ]
        );

        foreach (range(0, 6) as $dayOfWeek) {
            TempleOpeningHour::updateOrCreate(
                ['temple_id' => $temple->id, 'day_of_week' => $dayOfWeek],
                [
                    'open_time' => '08:00:00',
                    'close_time' => '17:00:00',
                    'is_closed' => false,
                    'note' => null,
                ]
            );
        }
    }
}
