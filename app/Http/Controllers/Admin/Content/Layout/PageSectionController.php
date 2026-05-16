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
            'settings' => $this->sanitizeSectionSettings($this->decodeJson($request->input('settings')) ?? []),
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
                'previewTitle' => 'ตัวอย่างเซกชันมีข้อผิดพลาด',
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
        $validated['settings'] = $this->sanitizeSectionSettings($this->decodeJson($validated['settings'] ?? null) ?? []);
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

        $validated['settings'] = $this->sanitizeSectionSettings($this->decodeJson($validated['settings'] ?? null) ?? []);
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

    private function sanitizeSectionSettings(array $settings): array
    {
        $safe = $settings;

        $safe['background_color'] = $this->sanitizeHexColor($settings['background_color'] ?? null, '#020617');
        $safe['background_color_end'] = $this->sanitizeHexColor($settings['background_color_end'] ?? null, $safe['background_color']);
        $safe['text_color'] = $this->sanitizeHexColor($settings['text_color'] ?? null, '#ffffff');
        $safe['background_gradient'] = (bool) ($settings['background_gradient'] ?? false);
        $safe['show_search_box'] = (bool) ($settings['show_search_box'] ?? false);
        $safe['show_summary_stats'] = (bool) ($settings['show_summary_stats'] ?? false);

        $safe['background_gradient_direction'] = $this->allowedValue($settings['background_gradient_direction'] ?? null, [
            'to bottom',
            'to top',
            'to right',
            'to left',
            '135deg',
        ], 'to bottom');

        $safe['align'] = $this->allowedValue($settings['align'] ?? null, ['left', 'center', 'right'], 'center');
        $safe['layout'] = $this->allowedValue($settings['layout'] ?? null, ['image_right', 'image_left'], 'image_right');
        $safe['source'] = $this->allowedValue($settings['source'] ?? null, ['featured', 'popular', 'all'], 'featured');
        $safe['bento_variant'] = $this->allowedValue($settings['bento_variant'] ?? null, ['travel', 'article_filter'], 'travel');
        $safe['bento_content_type'] = $this->allowedValue($settings['bento_content_type'] ?? null, ['article', 'temple'], 'article');
        $safe['bento_layout'] = $this->allowedValue($settings['bento_layout'] ?? null, ['feature_3', 'balanced_4', 'mosaic_5', 'editorial_6', 'compact_7', 'full_9'], 'mosaic_5');
        $safe['bento_content_align'] = $this->allowedValue($settings['bento_content_align'] ?? null, ['left', 'center', 'right'], 'left');
        $safe['image_fit'] = $this->allowedValue($settings['image_fit'] ?? null, ['contain', 'cover'], 'contain');
        $safe['image_position'] = $this->allowedValue($settings['image_position'] ?? null, ['center', 'top', 'bottom', 'left', 'right'], 'center');
        $safe['font_size'] = $this->allowedValue($settings['font_size'] ?? null, ['sm', 'base', 'lg', 'xl'], 'base');
        $safe['font_weight'] = $this->allowedValue($settings['font_weight'] ?? null, ['normal', 'medium', 'semibold', 'bold'], 'normal');
        $safe['text_align'] = $this->allowedValue($settings['text_align'] ?? null, ['inherit', 'left', 'center', 'right'], 'inherit');
        $safe['spacing_padding'] = $this->allowedValue($settings['spacing_padding'] ?? null, ['none', 'compact', 'default', 'spacious'], 'default');
        $safe['spacing_margin'] = $this->allowedValue($settings['spacing_margin'] ?? null, ['none', 'sm', 'md', 'lg'], 'none');
        $safe['button_style'] = $this->allowedValue($settings['button_style'] ?? null, ['solid', 'outline', 'ghost', 'glass'], 'solid');
        $safe['button_radius'] = $this->allowedValue($settings['button_radius'] ?? null, ['lg', '2xl', 'full'], '2xl');
        $safe['border_radius'] = $this->allowedValue($settings['border_radius'] ?? null, ['none', 'xl', '2xl', '3xl'], 'none');
        $safe['layout_width'] = $this->allowedValue($settings['layout_width'] ?? null, ['4xl', '5xl', '7xl', 'full'], '7xl');
        $safe['visibility'] = $this->allowedValue($settings['visibility'] ?? null, ['all', 'desktop', 'mobile', 'hidden'], 'all');
        $safe['animation_type'] = $this->allowedValue($settings['animation_type'] ?? null, ['none', 'fade', 'fade-up', 'fade-down', 'slide-left', 'slide-right', 'zoom-in', 'zoom-out'], 'none');
        $safe['animation_duration'] = max(100, min((int) ($settings['animation_duration'] ?? 500), 3000));
        $safe['animation_delay'] = max(0, min((int) ($settings['animation_delay'] ?? 0), 3000));
        $safe['animation_class'] = preg_match('/^[a-zA-Z0-9_-]{0,64}$/', (string) ($settings['animation_class'] ?? ''))
            ? (string) ($settings['animation_class'] ?? '')
            : '';
        $safe['custom_animation_css'] = $this->sanitizeCustomAnimationCss($settings['custom_animation_css'] ?? '');

        $safe['limit'] = max(1, min((int) ($settings['limit'] ?? 4), 12));
        $safe['slider_threshold'] = max(1, min((int) ($settings['slider_threshold'] ?? 4), 12));
        $safe['list_rows'] = max(1, min((int) ($settings['list_rows'] ?? 4), 12));
        $safe['list_columns'] = max(1, min((int) ($settings['list_columns'] ?? 4), 6));
        $safe['banner_height'] = in_array((int) ($settings['banner_height'] ?? 540), [540, 720], true)
            ? (int) ($settings['banner_height'] ?? 540)
            : 540;
        $safe['image_opacity'] = max(10, min((int) ($settings['image_opacity'] ?? 100), 100));

        return $safe;
    }

    private function sanitizeHexColor(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    }

    private function allowedValue(mixed $value, array $allowed, string $fallback): string
    {
        $value = (string) $value;

        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function sanitizeCustomAnimationCss(mixed $value): string
    {
        $css = str_replace("\0", '', (string) $value);
        $css = trim(mb_substr($css, 0, 8000));

        if ($css === '') {
            return '';
        }

        if (! str_contains($css, '{section}')) {
            return '';
        }

        $blockedPatterns = [
            '/<\/?\s*style\b/i',
            '/<\/?\s*script\b/i',
            '/@import\b/i',
            '/url\s*\(/i',
            '/expression\s*\(/i',
            '/javascript\s*:/i',
            '/behavior\s*:/i',
            '/-moz-binding\s*:/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $css)) {
                return '';
            }
        }

        return $css;
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
