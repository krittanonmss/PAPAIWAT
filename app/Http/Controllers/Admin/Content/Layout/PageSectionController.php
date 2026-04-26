<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function create(Page $page): View
    {
        return view('admin.content.layout.page-sections.create', compact('page'));
    }

    public function store(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'section_key' => ['required', 'string', 'max:255'],
            'component_key' => ['required', 'string', 'max:255'],
            'settings' => ['nullable', 'json'],
            'content' => ['nullable', 'json'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['page_id'] = $page->id;
        $validated['settings'] = $this->decodeJson($validated['settings'] ?? null);
        $validated['content'] = $this->decodeJson($validated['content'] ?? null);
        $validated['is_visible'] = $request->boolean('is_visible', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PageSection::create($validated);

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'เพิ่ม section เรียบร้อยแล้ว');
    }

    public function edit(Page $page, PageSection $section): View
    {
        abort_if($section->page_id !== $page->id, 404);

        return view('admin.content.layout.page-sections.edit', compact('page', 'section'));
    }

    public function update(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_if($section->page_id !== $page->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'section_key' => ['required', 'string', 'max:255'],
            'component_key' => ['required', 'string', 'max:255'],
            'settings' => ['nullable', 'json'],
            'content' => ['nullable', 'json'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['settings'] = $this->decodeJson($validated['settings'] ?? null);
        $validated['content'] = $this->decodeJson($validated['content'] ?? null);
        $validated['is_visible'] = $request->boolean('is_visible', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $section->update($validated);

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'อัปเดต section เรียบร้อยแล้ว');
    }

    public function destroy(Page $page, PageSection $section): RedirectResponse
    {
        abort_if($section->page_id !== $page->id, 404);

        $section->delete();

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'ลบ section เรียบร้อยแล้ว');
    }

    private function decodeJson(?string $value): ?array
    {
        if (!$value) {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }
}