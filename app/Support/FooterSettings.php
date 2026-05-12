<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FooterSettings
{
    public static function defaults(): array
    {
        return [
            'brand_title' => 'PAPAIWAT',
            'brand_description' => 'แพลตฟอร์มรวบรวมข้อมูลวัด สถานที่ศักดิ์สิทธิ์ และวัฒนธรรมไทย',
            'footer_note' => null,
            'copyright_text' => '© {year} {brand}. All rights reserved.',
            'show_brand' => true,
            'show_menu' => true,
            'show_bottom_bar' => true,
            'show_border' => true,
            'background_style' => 'glass',
            'column_count' => '4',
        ];
    }

    public static function get(): array
    {
        if (! Schema::hasTable('site_settings')) {
            return self::defaults();
        }

        $value = DB::table('site_settings')
            ->where('key', 'footer')
            ->value('value');

        $settings = is_string($value) ? json_decode($value, true) : null;

        return array_merge(self::defaults(), is_array($settings) ? $settings : []);
    }

    public static function save(array $settings): void
    {
        $settings = self::normalize($settings);

        $exists = DB::table('site_settings')
            ->where('key', 'footer')
            ->exists();

        if ($exists) {
            DB::table('site_settings')
                ->where('key', 'footer')
                ->update([
                    'group_key' => 'layout',
                    'value' => json_encode($settings, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('site_settings')->insert([
                'group_key' => 'layout',
                'key' => 'footer',
                'value' => json_encode($settings, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }

    public static function normalize(array $settings): array
    {
        $settings = array_merge(self::defaults(), $settings);

        foreach (['show_brand', 'show_menu', 'show_bottom_bar', 'show_border'] as $key) {
            $settings[$key] = filter_var($settings[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        if (! in_array($settings['background_style'], ['glass', 'solid', 'minimal'], true)) {
            $settings['background_style'] = 'glass';
        }

        if (! in_array((string) $settings['column_count'], ['3', '4', '5'], true)) {
            $settings['column_count'] = '4';
        }

        return $settings;
    }

    public static function copyright(array $settings): string
    {
        return strtr($settings['copyright_text'] ?: self::defaults()['copyright_text'], [
            '{year}' => date('Y'),
            '{brand}' => $settings['brand_title'] ?: self::defaults()['brand_title'],
        ]);
    }
}
