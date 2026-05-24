<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    public const CACHE_KEY = 'site_settings.groups';

    public const GROUPS = [
        'general',
        'seo',
        'content',
        'moderation',
        'media',
        'navigation',
        'integrations',
        'maintenance',
    ];

    public static function defaults(): array
    {
        return [
            'general' => [
                'site_name' => 'PAPAIWAT',
                'tagline' => null,
                'contact_email' => null,
                'contact_phone' => null,
                'contact_address' => null,
                'locale' => 'th',
                'timezone' => 'Asia/Bangkok',
            ],
            'seo' => [
                'default_title' => 'PAPAIWAT',
                'default_description' => 'PAPAIWAT Platform',
                'og_image_media_id' => null,
                'canonical_base_url' => null,
                'indexing_enabled' => true,
            ],
            'content' => [
                'temple_default_template_id' => null,
                'article_default_template_id' => null,
                'default_status' => 'draft',
                'article_allow_comments_default' => true,
                'temple_reviews_enabled' => true,
            ],
            'moderation' => [
                'comments_enabled' => true,
                'reviews_enabled' => true,
                'reports_enabled' => true,
                'auto_hide_report_threshold' => 3,
                'notification_email' => null,
            ],
            'media' => [
                'max_upload_mb' => 5,
                'allowed_types' => ['image', 'document'],
                'default_visibility' => 'public',
                'image_quality' => 82,
                'duplicate_policy' => 'reject',
            ],
            'navigation' => [
                'header_menu_id' => null,
                'footer_menu_id' => null,
                'facebook_url' => null,
                'instagram_url' => null,
                'youtube_url' => null,
                'line_url' => null,
            ],
            'integrations' => [
                'analytics_measurement_id' => null,
                'tag_manager_container_id' => null,
                'maps_enabled' => false,
                'maps_public_browser_key' => null,
            ],
            'maintenance' => [
                'announcement_enabled' => false,
                'announcement_text' => null,
                'announcement_level' => 'info',
                'sitemap_enabled' => true,
                'sitemap_last_generated_at' => null,
            ],
        ];
    }

    public static function all(): array
    {
        if (! Schema::hasTable('site_settings')) {
            return self::defaults();
        }

        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $settings = self::defaults();

            DB::table('site_settings')
                ->whereIn('group_key', self::GROUPS)
                ->get()
                ->each(function (object $record) use (&$settings): void {
                    if (! array_key_exists($record->group_key, $settings)
                        || ! array_key_exists($record->key, $settings[$record->group_key])) {
                        return;
                    }

                    $settings[$record->group_key][$record->key] = self::decode($record->value);
                });

            return $settings;
        });
    }

    public static function group(string $group): array
    {
        return self::all()[$group] ?? [];
    }

    public static function get(string $group, string $key, mixed $fallback = null): mixed
    {
        return self::group($group)[$key] ?? $fallback;
    }

    public static function saveGroup(string $group, array $values): array
    {
        $defaults = self::defaults()[$group] ?? null;

        if ($defaults === null) {
            return [];
        }

        $values = array_intersect_key($values, $defaults);
        $saved = self::normalize($group, array_replace($defaults, self::group($group), $values));

        foreach ($saved as $key => $value) {
            $query = DB::table('site_settings')->where('key', $key);
            $payload = [
                    'value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
            ];

            if ($query->exists()) {
                $query->update(['group_key' => $group] + $payload);
            } else {
                DB::table('site_settings')->insert([
                    'group_key' => $group,
                    'key' => $key,
                    'created_at' => now(),
                ] + $payload);
            }
        }

        Cache::forget(self::CACHE_KEY);

        return $saved;
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function decode(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    private static function normalize(string $group, array $values): array
    {
        $defaults = self::defaults()[$group] ?? [];

        foreach ($values as $key => $value) {
            if (($defaults[$key] ?? null) === null && $value === '') {
                $values[$key] = null;
                continue;
            }

            if (is_bool($defaults[$key] ?? null)) {
                $values[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                continue;
            }

            if (is_int($defaults[$key] ?? null)) {
                $values[$key] = (int) $value;
                continue;
            }

            if (is_array($defaults[$key] ?? null)) {
                $values[$key] = array_values(array_unique((array) $value));
                continue;
            }

            if (str_ends_with($key, '_id') && $value !== null && $value !== '') {
                $values[$key] = (int) $value;
            }
        }

        return $values;
    }
}
