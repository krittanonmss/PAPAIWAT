<?php

namespace Database\Seeders;

use App\Models\Content\Layout\Template;
use Illuminate\Database\Seeder;

class SystemTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = collect(config('template_registry.templates', []))
            ->map(fn (array $template, string $key) => array_merge($template, [
                'key' => $key,
                'status' => $key === 'admin-preview-safe' ? 'inactive' : 'active',
            ]));

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
