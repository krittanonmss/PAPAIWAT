<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class CmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'home-default',
                'name' => 'Home Default',
                'description' => 'Default homepage template for PAPAIWAT.',
                'view_path' => 'frontend.templates.pages.home-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 10,
            ],
            [
                'key' => 'home-focus',
                'name' => 'Home Focus',
                'description' => 'Homepage template with a compact hero and focused editorial sections.',
                'view_path' => 'frontend.templates.pages.home-focus',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 11,
            ],
            [
                'key' => 'home-split',
                'name' => 'Home Split',
                'description' => 'Homepage template with split temple/article discovery panels.',
                'view_path' => 'frontend.templates.pages.home-split',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 12,
            ],
            [
                'key' => 'page-default',
                'name' => 'Generic Page Default',
                'description' => 'Default template for generic CMS pages.',
                'view_path' => 'frontend.templates.pages.default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 20,
            ],
            [
                'key' => 'listing-page',
                'name' => 'Listing Page',
                'description' => 'Default generic listing page template.',
                'view_path' => 'frontend.templates.pages.listing',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 22,
            ],
            [
                'key' => 'temple-list',
                'name' => 'Temple List Default',
                'description' => 'Default temple listing template.',
                'view_path' => 'frontend.templates.lists.temple-list',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 30,
            ],
            [
                'key' => 'temple-detail',
                'name' => 'Temple Detail Default',
                'description' => 'Default hardcoded fallback template for temple detail pages.',
                'view_path' => 'frontend.templates.details.temple-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 40,
            ],
            [
                'key' => 'temple-compact-detail',
                'name' => 'Temple Detail Compact',
                'description' => 'Compact split layout for temple detail pages.',
                'view_path' => 'frontend.templates.details.temple-compact',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 45,
            ],
            [
                'key' => 'article-list',
                'name' => 'Article List Default',
                'description' => 'Default article listing template.',
                'view_path' => 'frontend.templates.lists.article-list',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 50,
            ],
            [
                'key' => 'article-detail',
                'name' => 'Article Detail Default',
                'description' => 'Default hardcoded fallback template for article detail pages.',
                'view_path' => 'frontend.templates.details.article-default',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 60,
            ],
            [
                'key' => 'article-editorial-detail',
                'name' => 'Article Detail Editorial',
                'description' => 'Editorial reading layout for article detail pages.',
                'view_path' => 'frontend.templates.details.article-editorial',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 65,
            ],
            [
                'key' => 'admin-preview-safe',
                'name' => 'Admin Preview Safe',
                'description' => 'Preview-safe template for admin iframe preview states.',
                'view_path' => 'frontend.templates.previews.admin-iframe',
                'status' => 'inactive',
                'is_default' => false,
                'sort_order' => 90,
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['key' => $template['key']],
                [
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'view_path' => $template['view_path'],
                    'status' => $template['status'],
                    'is_default' => $template['is_default'],
                    'sort_order' => $template['sort_order'],
                ]
            );
        }
    }
}
