<?php

namespace Database\Seeders;

use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use Illuminate\Database\Seeder;

class DemoArticleContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = Content::updateOrCreate(
            ['content_type' => 'article', 'slug' => 'demo-article'],
            [
                'title' => 'บทความตัวอย่าง PAPAIWAT',
                'excerpt' => 'บทความตัวอย่างสำหรับทดสอบหน้า CMS-managed detail',
                'description' => 'ข้อมูลบทความตัวอย่างสำหรับตรวจสอบ template และ sections ของหน้ารายละเอียดบทความ',
                'status' => 'published',
                'is_featured' => true,
                'is_popular' => false,
                'meta_title' => 'บทความตัวอย่าง PAPAIWAT',
                'meta_description' => 'บทความตัวอย่างสำหรับทดสอบหน้ารายละเอียดบทความ',
                'published_at' => now(),
            ]
        );

        Article::updateOrCreate(
            ['content_id' => $content->id],
            [
                'title_en' => 'PAPAIWAT Demo Article',
                'excerpt_en' => 'Starter article for CMS-managed detail page testing.',
                'body' => "## หัวข้อบทความตัวอย่าง\n\nเนื้อหานี้ใช้สำหรับตรวจสอบการแสดงผลของ section `article_body` ในหน้า CMS-managed detail\n\nผู้ดูแลระบบสามารถแก้ไข section ของหน้า Article Detail ได้จาก CMS โดยไม่ต้องแก้ code",
                'body_format' => 'markdown',
                'author_name' => 'PAPAIWAT Editorial',
                'reading_time_minutes' => 3,
                'seo_keywords' => 'PAPAIWAT, CMS, Article',
                'allow_comments' => true,
                'show_on_homepage' => true,
            ]
        );
    }
}
