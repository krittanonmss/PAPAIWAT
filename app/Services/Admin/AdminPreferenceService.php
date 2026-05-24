<?php

namespace App\Services\Admin;

use App\Models\Admin\Admin;
use App\Models\Admin\AdminPreference;

class AdminPreferenceService
{
    public const PER_PAGE_OPTIONS = [5, 10, 12, 15, 20, 24, 25, 48, 50, 96, 100];

    public const DEFAULTS = [
        'display.theme' => 'dark',
        'display.density' => 'comfortable',
        'display.sidebar_collapsed' => false,
        'display.scale' => 80,
        'tables.default_per_page' => 10,
        'tables.remember_filters' => true,
        'tables.open_detail_in_new_tab' => false,
        'editor.autosave_drafts' => true,
        'editor.preview_panel_open' => true,
        'media.default_view_mode' => 'grid',
        'media.upload_duplicate_behavior' => 'reject',
        'notifications.in_app' => true,
        'notifications.email' => false,
        'notifications.moderation_alerts' => true,
        'accessibility.reduced_motion' => false,
        'accessibility.high_contrast' => false,
    ];

    public function forAdmin(?Admin $admin): array
    {
        if (! $admin) {
            return self::DEFAULTS;
        }

        $stored = AdminPreference::query()
            ->where('admin_id', $admin->id)
            ->pluck('value', 'key')
            ->map(fn ($value) => $value['value'] ?? null)
            ->filter(fn ($value, string $key) => array_key_exists($key, self::DEFAULTS))
            ->all();

        return array_replace(self::DEFAULTS, $stored);
    }

    public function update(Admin $admin, array $preferences): array
    {
        $normalized = $this->normalize($preferences);

        AdminPreference::query()
            ->where('admin_id', $admin->id)
            ->whereIn('key', [
                'editor.default_body_format',
                'localization.language',
                'localization.timezone',
                'media.default_visibility',
            ])
            ->delete();

        foreach ($normalized as $key => $value) {
            AdminPreference::query()->updateOrCreate(
                ['admin_id' => $admin->id, 'key' => $key],
                ['value' => ['value' => $value]]
            );
        }

        return $this->forAdmin($admin);
    }

    public function preferredPerPage(?Admin $admin, array $allowed, int $fallback): int
    {
        if (! $admin) {
            return $fallback;
        }

        $stored = AdminPreference::query()
            ->where('admin_id', $admin->id)
            ->where('key', 'tables.default_per_page')
            ->value('value');

        $perPage = (int) ($stored['value'] ?? $fallback);

        return in_array($perPage, $allowed, true) ? $perPage : $fallback;
    }

    private function normalize(array $preferences): array
    {
        return [
            'display.theme' => $preferences['display']['theme'] ?? self::DEFAULTS['display.theme'],
            'display.density' => $preferences['display']['density'] ?? self::DEFAULTS['display.density'],
            'display.sidebar_collapsed' => (bool) ($preferences['display']['sidebar_collapsed'] ?? false),
            'display.scale' => (int) ($preferences['display']['scale'] ?? self::DEFAULTS['display.scale']),
            'tables.default_per_page' => (int) ($preferences['tables']['default_per_page'] ?? self::DEFAULTS['tables.default_per_page']),
            'tables.remember_filters' => (bool) ($preferences['tables']['remember_filters'] ?? false),
            'tables.open_detail_in_new_tab' => (bool) ($preferences['tables']['open_detail_in_new_tab'] ?? false),
            'editor.autosave_drafts' => (bool) ($preferences['editor']['autosave_drafts'] ?? false),
            'editor.preview_panel_open' => (bool) ($preferences['editor']['preview_panel_open'] ?? false),
            'media.default_view_mode' => $preferences['media']['default_view_mode'] ?? self::DEFAULTS['media.default_view_mode'],
            'media.upload_duplicate_behavior' => 'reject',
            'notifications.in_app' => (bool) ($preferences['notifications']['in_app'] ?? false),
            'notifications.email' => (bool) ($preferences['notifications']['email'] ?? false),
            'notifications.moderation_alerts' => (bool) ($preferences['notifications']['moderation_alerts'] ?? false),
            'accessibility.reduced_motion' => (bool) ($preferences['accessibility']['reduced_motion'] ?? false),
            'accessibility.high_contrast' => (bool) ($preferences['accessibility']['high_contrast'] ?? false),
        ];
    }
}
