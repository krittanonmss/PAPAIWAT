<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Media\Media;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function create(Page $page): View
    {
        $sectionMediaItems = $this->sectionMediaItems();

        return view('admin.content.layout.page-sections.create', compact('page', 'sectionMediaItems'));
    }

    public function mediaPicker(Request $request): View|Response
    {
        $mediaItems = $this->sectionMediaItems();

        return response()->view('admin.content.layout.page-sections.partials._media_grid', compact('mediaItems'));
    }

    public function store(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'section_key' => ['nullable', 'string', 'max:255'],
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
        $validated['name'] = $validated['name'] ?: $this->defaultSectionName($validated['component_key']);
        $generatedKey = Str::slug($validated['name'] . '-' . now()->format('His'));
        $validated['section_key'] = $validated['section_key'] ?: ($generatedKey ?: $validated['component_key'] . '-' . now()->format('His'));
        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PageSection::create($validated);

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'เพิ่ม section เรียบร้อยแล้ว');
    }

    public function edit(Page $page, PageSection $section): View
    {
        abort_if($section->page_id !== $page->id, 404);

        $sectionMediaItems = $this->sectionMediaItems();

        return view('admin.content.layout.page-sections.edit', compact('page', 'section', 'sectionMediaItems'));
    }

    public function update(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_if($section->page_id !== $page->id, 404);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'section_key' => ['nullable', 'string', 'max:255'],
            'component_key' => ['required', 'string', 'max:255'],
            'settings' => ['nullable', 'json'],
            'content' => ['nullable', 'json'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['settings'] = $this->decodeJson($validated['settings'] ?? null);
        $validated['content'] = $this->decodeJson($validated['content'] ?? null);
        $validated['name'] = $validated['name'] ?: $this->defaultSectionName($validated['component_key']);
        $generatedKey = Str::slug($validated['name'] . '-' . $section->id);
        $validated['section_key'] = $validated['section_key'] ?: ($section->section_key ?: ($generatedKey ?: $validated['component_key'] . '-' . $section->id));
        $validated['is_visible'] = $request->boolean('is_visible');
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
        if (! $value) {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function defaultSectionName(string $componentKey): string
    {
        return match ($componentKey) {
            'hero' => 'Hero',
            'rich_text' => 'ข้อความ',
            'image_text' => 'รูปภาพพร้อมข้อความ',
            'cta' => 'Call to Action',
            'article_grid' => 'รายการบทความ',
            'temple_grid' => 'รายการวัด',
            'article_list_full' => 'หน้ารวมบทความ',
            'temple_list_full' => 'หน้ารวมวัด',
            'gallery' => 'แกลเลอรี',
            'faq' => 'FAQ',
            'stats' => 'ตัวเลขสำคัญ',
            'contact' => 'ข้อมูลติดต่อ',
            default => Str::headline($componentKey),
        };
    }

    private function sectionMediaItems()
    {
        return Media::query()
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->orderByDesc('id')
            ->paginate(
                perPage: 7,
                columns: ['id', 'title', 'original_filename', 'media_type', 'path'],
                pageName: 'section_media_page'
            )
            ->withPath(route('admin.content.pages.sections.media-picker'));
    }
}
