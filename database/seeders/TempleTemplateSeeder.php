<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class TempleTemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::updateOrCreate(
            ['key' => 'temple-detail'],
            [
                'name' => 'Temple Detail',
                'view_path' => 'frontend.templates.details.temple-default',
                'description' => 'Template หลักสำหรับหน้าแสดงรายละเอียดวัด',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 20,
            ]
        );
    }
}