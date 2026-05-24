<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function edit(Request $request, AdminPreferenceService $preferences): View
    {
        $admin = $request->user('admin');

        return view('admin.preferences.edit', [
            'admin' => $admin,
            'preferences' => $preferences->forAdmin($admin),
        ]);
    }

    public function update(Request $request, AdminPreferenceService $preferences): RedirectResponse
    {
        $admin = $request->user('admin');

        $validated = $request->validateWithBag('preferences', [
            'display.theme' => ['required', 'in:dark,light,system'],
            'display.density' => ['required', 'in:compact,comfortable'],
            'display.sidebar_collapsed' => ['nullable', 'boolean'],
            'display.scale' => ['required', 'integer', 'between:70,100'],
            'tables.default_per_page' => ['required', 'integer', 'in:' . implode(',', AdminPreferenceService::PER_PAGE_OPTIONS)],
            'tables.remember_filters' => ['nullable', 'boolean'],
            'tables.open_detail_in_new_tab' => ['nullable', 'boolean'],
            'editor.autosave_drafts' => ['nullable', 'boolean'],
            'editor.preview_panel_open' => ['nullable', 'boolean'],
            'media.default_view_mode' => ['required', 'in:grid,list'],
            'notifications.in_app' => ['nullable', 'boolean'],
            'notifications.email' => ['nullable', 'boolean'],
            'notifications.moderation_alerts' => ['nullable', 'boolean'],
            'accessibility.reduced_motion' => ['nullable', 'boolean'],
            'accessibility.high_contrast' => ['nullable', 'boolean'],
        ]);

        $updated = $preferences->update($admin, $validated);
        $theme = $updated['display.theme'] ?? 'dark';
        $theme = in_array($theme, ['dark', 'light', 'system'], true) ? $theme : 'dark';

        return redirect()
            ->route('admin.preferences.edit')
            ->with('success', 'บันทึกการตั้งค่าการใช้งานเรียบร้อยแล้ว')
            ->cookie('papaiwat_admin_theme', $theme, 60 * 24 * 365, null, null, false, false, false, 'lax');
    }
}
