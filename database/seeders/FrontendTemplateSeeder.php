<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class FrontendTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Home Default',
                'key' => 'home-default',
                'view_path' => 'frontend.templates.pages.home-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Page Default',
                'key' => 'page-default',
                'view_path' => 'frontend.templates.pages.default',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Temple List',
                'key' => 'temple-list',
                'view_path' => 'frontend.templates.lists.temple-list',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($templates as $data) {
            Template::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}