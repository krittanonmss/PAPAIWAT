<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Support\FooterSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.content.layout.footer.edit', [
            'settings' => FooterSettings::get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_title' => ['nullable', 'string', 'max:120'],
            'brand_description' => ['nullable', 'string', 'max:500'],
            'footer_note' => ['nullable', 'string', 'max:500'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'show_brand' => ['nullable', 'boolean'],
            'show_menu' => ['nullable', 'boolean'],
            'show_bottom_bar' => ['nullable', 'boolean'],
            'show_border' => ['nullable', 'boolean'],
            'background_style' => ['required', 'string', 'in:glass,solid,minimal'],
            'column_count' => ['required', 'string', 'in:3,4,5'],
        ]);

        foreach (['show_brand', 'show_menu', 'show_bottom_bar', 'show_border'] as $key) {
            $validated[$key] = $request->boolean($key);
        }

        FooterSettings::save($validated);

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'อัปเดต Footer เรียบร้อยแล้ว');
    }
}
