<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdateSiteSettingsRequest;
use App\Models\Admin\AuditLog;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Services\Frontend\SitemapService;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const TABS = [
        'general' => 'General',
        'seo' => 'SEO',
        'content' => 'Content',
        'moderation' => 'Moderation',
        'media' => 'Media',
        'navigation' => 'Navigation',
        'integrations' => 'Integrations',
        'maintenance' => 'Maintenance',
        'audit' => 'Audit History',
    ];

    public function edit(Request $request): View
    {
        $activeTab = array_key_exists($request->string('tab')->toString(), self::TABS)
            ? $request->string('tab')->toString()
            : 'general';

        $auditLogs = $activeTab === 'audit' && Schema::hasTable('audit_logs')
            ? AuditLog::query()
                ->with('performer')
                ->where('table_name', 'site_settings')
                ->latest('created_at')
                ->paginate(20)
                ->withQueryString()
            : null;

        return view('admin.settings.edit', [
            'tabs' => self::TABS,
            'activeTab' => $activeTab,
            'settings' => SiteSettings::all(),
            'mediaImages' => Schema::hasTable('media')
                ? Media::query()->where('media_type', 'image')->latest('id')->limit(100)->get(['id', 'title', 'original_filename'])
                : collect(),
            'templates' => Schema::hasTable('templates')
                ? Template::query()->active()->where('template_type', 'detail')->orderBy('content_type')->orderBy('name')->get()
                : collect(),
            'menus' => Schema::hasTable('menus')
                ? Menu::query()->active()->orderBy('location_key')->orderBy('name')->get()
                : collect(),
            'auditLogs' => $auditLogs,
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request, string $group): RedirectResponse
    {
        $oldSettings = SiteSettings::group($group);
        $newSettings = SiteSettings::saveGroup($group, $request->settings());

        $this->writeAuditLog($request, 'settings.'.$group.'.updated', $oldSettings, $newSettings);

        return redirect()
            ->route('admin.settings.edit', ['tab' => $group])
            ->with('success', 'บันทึกการตั้งค่า '.self::TABS[$group].' เรียบร้อยแล้ว');
    }

    public function clearCache(Request $request): RedirectResponse
    {
        SiteSettings::forgetCache();
        Cache::forget('frontend_menu.header');
        Cache::forget('frontend_menu.footer');

        $this->writeAuditLog($request, 'settings.maintenance.cache_cleared', null, [
            'cache' => 'site_settings_and_frontend_menu',
        ]);

        return redirect()
            ->route('admin.settings.edit', ['tab' => 'maintenance'])
            ->with('success', 'ล้าง cache ของ Settings และเมนูหน้าเว็บเรียบร้อยแล้ว');
    }

    public function generateSitemap(Request $request, SitemapService $sitemapService): RedirectResponse
    {
        abort_unless((bool) SiteSettings::get('maintenance', 'sitemap_enabled', true), 409);

        $urlCount = $sitemapService->generate();
        $oldSettings = SiteSettings::group('maintenance');
        $newSettings = SiteSettings::saveGroup('maintenance', [
            'sitemap_last_generated_at' => now()->toIso8601String(),
        ]);

        $this->writeAuditLog($request, 'settings.maintenance.sitemap_generated', $oldSettings, $newSettings + [
            'url_count' => $urlCount,
        ]);

        return redirect()
            ->route('admin.settings.edit', ['tab' => 'maintenance'])
            ->with('success', 'สร้าง sitemap.xml เรียบร้อยแล้ว จำนวน '.$urlCount.' URL');
    }

    private function writeAuditLog(Request $request, string $action, ?array $oldData, array $newData): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        AuditLog::query()->create([
            'action' => $action,
            'table_name' => 'site_settings',
            'record_id' => null,
            'old_data' => $oldData,
            'new_data' => $newData,
            'performed_by' => $request->user('admin')?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
}
