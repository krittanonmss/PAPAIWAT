<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Http\Controllers\Frontend\FrontendPageController;
use Illuminate\Http\JsonResponse;
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
        $linkPages = $this->linkPages();
        $bentoContents = $this->bentoContents();

        return view('admin.content.layout.page-sections.create', compact('page', 'sectionMediaItems', 'linkPages', 'bentoContents'));
    }

    public function mediaPicker(Request $request): View|Response
    {
        $mediaItems = $this->sectionMediaItems();
        $view = $request->string('mode')->toString() === 'gallery'
            ? 'admin.content.layout.page-sections.partials._gallery_media_grid'
            : 'admin.content.layout.page-sections.partials._media_grid';

        return response()->view($view, compact('mediaItems'));
    }

    public function preview(Request $request, Page $page): JsonResponse
    {
        $componentKey = (string) $request->input('component_key', 'hero');
        $section = new PageSection([
            'page_id' => $page->id,
            'name' => $request->input('name') ?: $this->defaultSectionName($componentKey),
            'section_key' => $request->input('section_key') ?: 'preview-section',
            'component_key' => $componentKey,
            'content' => $this->prepareSectionContent($request->input('content')) ?? [],
            'settings' => $this->decodeJson($request->input('settings')) ?? [],
            'status' => 'active',
            'is_visible' => true,
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);
        $section->id = (int) ($request->input('section_id') ?: 0);
        $section->exists = false;

        $page->setRelation('sections', collect([$section]));

        try {
            $sections = app(FrontendPageController::class)->buildPageSections($page);
            $html = view('frontend.templates.previews.section', compact('sections'))->render();
        } catch (\Throwable $exception) {
            report($exception);

            $html = view('frontend.templates.previews.admin-iframe', [
                'previewTitle' => 'Section preview error',
                'previewMessage' => $exception->getMessage(),
            ])->render();
        }

        return response()->json([
            'html' => $html,
        ]);
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
        $validated['content'] = $this->prepareSectionContent($validated['content'] ?? null);
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
        $linkPages = $this->linkPages();
        $bentoContents = $this->bentoContents();

        return view('admin.content.layout.page-sections.edit', compact('page', 'section', 'sectionMediaItems', 'linkPages', 'bentoContents'));
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
        $validated['content'] = $this->prepareSectionContent($validated['content'] ?? null);
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

    private function prepareSectionContent(?string $value): ?array
    {
        $content = $this->decodeJson($value);

        if (! is_array($content)) {
            return null;
        }

        if (array_key_exists('all_button_enabled', $content)) {
            $content['all_button_enabled'] = (bool) $content['all_button_enabled'];
        }

        foreach (['primary_page_id', 'secondary_page_id', 'all_button_page_id'] as $pageKey) {
            if (! empty($content[$pageKey])) {
                $pageId = (int) $content[$pageKey];
                $content[$pageKey] = Page::query()->whereKey($pageId)->exists()
                    ? (string) $pageId
                    : '';
            }
        }

        $content['all_button_url'] = $content['all_button_url'] ?? '';

        if (array_key_exists('gallery_media_ids', $content)) {
            $ids = collect($content['gallery_media_ids'])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->take(36)
                ->values();

            $existingIds = Media::query()
                ->whereIn('id', $ids)
                ->where('media_type', 'image')
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $content['gallery_media_ids'] = $ids
                ->map(fn ($id) => (string) $id)
                ->filter(fn ($id) => in_array($id, $existingIds, true))
                ->values()
                ->all();
        }

        if (array_key_exists('bento_slots', $content)) {
            $slots = collect($content['bento_slots'])
                ->map(fn ($slot) => [
                    'content_id' => (int) ($slot['content_id'] ?? 0),
                    'size' => in_array(($slot['size'] ?? 'small'), ['large', 'wide', 'tall', 'small'], true) ? $slot['size'] : 'small',
                ])
                ->filter(fn ($slot) => $slot['content_id'] > 0)
                ->unique('content_id')
                ->take(9)
                ->values();

            $existingIds = Content::query()
                ->whereIn('id', $slots->pluck('content_id'))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $content['bento_slots'] = $slots
                ->map(fn ($slot) => [
                    'content_id' => (string) $slot['content_id'],
                    'size' => $slot['size'],
                ])
                ->filter(fn ($slot) => in_array($slot['content_id'], $existingIds, true))
                ->values()
                ->all();

            $content['bento_content_ids'] = collect($content['bento_slots'])
                ->pluck('content_id')
                ->values()
                ->all();
        }

        return $content;
    }

    private function defaultSectionName(string $componentKey): string
    {
        return match ($componentKey) {
            'hero' => 'Hero',
            'banner' => 'Banner',
            'rich_text' => 'ข้อความ',
            'image_text' => 'รูปภาพพร้อมข้อความ',
            'cta' => 'Call to Action',
            'article_grid' => 'รายการบทความ',
            'temple_grid' => 'รายการวัด',
            'travel_discovery_bento' => 'Travel Discovery Bento',
            'favorites_list' => 'รายการโปรด',
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

    private function linkPages()
    {
        return Page::query()
            ->orderByDesc('is_homepage')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'status', 'is_homepage']);
    }

    private function bentoContents()
    {
        return Content::query()
            ->whereIn('content_type', ['temple', 'article'])
            ->where('status', 'published')
            ->orderBy('content_type')
            ->orderBy('title')
            ->get(['id', 'content_type', 'title', 'excerpt', 'slug']);
    }
}
