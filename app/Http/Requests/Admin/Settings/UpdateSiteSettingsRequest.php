<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return match ((string) $this->route('group')) {
            'general' => [
                'settings.site_name' => ['required', 'string', 'max:120'],
                'settings.tagline' => ['nullable', 'string', 'max:255'],
                'settings.contact_email' => ['nullable', 'email', 'max:255'],
                'settings.contact_phone' => ['nullable', 'string', 'max:50'],
                'settings.contact_address' => ['nullable', 'string', 'max:500'],
                'settings.locale' => ['required', Rule::in(['th', 'en'])],
                'settings.timezone' => ['required', 'timezone:all'],
            ],
            'seo' => [
                'settings.default_title' => ['required', 'string', 'max:120'],
                'settings.default_description' => ['nullable', 'string', 'max:320'],
                'settings.og_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
                'settings.canonical_base_url' => ['nullable', 'url', 'max:255'],
                'settings.indexing_enabled' => ['required', 'boolean'],
            ],
            'content' => [
                'settings.temple_default_template_id' => ['nullable', 'integer', 'exists:templates,id'],
                'settings.article_default_template_id' => ['nullable', 'integer', 'exists:templates,id'],
                'settings.default_status' => ['required', Rule::in(['draft', 'review'])],
                'settings.article_allow_comments_default' => ['required', 'boolean'],
                'settings.temple_reviews_enabled' => ['required', 'boolean'],
            ],
            'moderation' => [
                'settings.comments_enabled' => ['required', 'boolean'],
                'settings.reviews_enabled' => ['required', 'boolean'],
                'settings.reports_enabled' => ['required', 'boolean'],
                'settings.auto_hide_report_threshold' => ['required', 'integer', 'min:1', 'max:20'],
                'settings.notification_email' => ['nullable', 'email', 'max:255'],
            ],
            'media' => [
                'settings.max_upload_mb' => ['required', 'integer', 'min:1', 'max:20'],
                'settings.allowed_types' => ['required', 'array', 'min:1'],
                'settings.allowed_types.*' => ['required', Rule::in(['image', 'document'])],
                'settings.default_visibility' => ['required', Rule::in(['public', 'private'])],
                'settings.image_quality' => ['required', 'integer', 'min:40', 'max:100'],
                'settings.duplicate_policy' => ['required', Rule::in(['reject', 'allow'])],
            ],
            'navigation' => [
                'settings.header_menu_id' => ['nullable', 'integer', 'exists:menus,id'],
                'settings.footer_menu_id' => ['nullable', 'integer', 'exists:menus,id'],
                'settings.facebook_url' => ['nullable', 'url', 'max:255'],
                'settings.instagram_url' => ['nullable', 'url', 'max:255'],
                'settings.youtube_url' => ['nullable', 'url', 'max:255'],
                'settings.line_url' => ['nullable', 'url', 'max:255'],
            ],
            'integrations' => [
                'settings.analytics_measurement_id' => ['nullable', 'string', 'max:40', 'regex:/^G-[A-Z0-9]+$/'],
                'settings.tag_manager_container_id' => ['nullable', 'string', 'max:40', 'regex:/^GTM-[A-Z0-9]+$/'],
                'settings.maps_enabled' => ['required', 'boolean'],
                'settings.maps_public_browser_key' => ['nullable', 'string', 'max:255'],
            ],
            'maintenance' => [
                'settings.announcement_enabled' => ['required', 'boolean'],
                'settings.announcement_text' => ['nullable', 'string', 'max:255', 'required_if:settings.announcement_enabled,1'],
                'settings.announcement_level' => ['required', Rule::in(['info', 'warning', 'critical'])],
                'settings.sitemap_enabled' => ['required', 'boolean'],
            ],
            default => ['settings' => ['prohibited']],
        };
    }

    public function settings(): array
    {
        return $this->validated('settings') ?? [];
    }
}
