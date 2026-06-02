<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\FrontendPageController;
use App\Models\Content\Layout\MenuItem;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Admin\Content\Layout\LayoutVersionService;
use App\Support\SlugGenerator;
use App\Support\TemplateRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly TemplateRegistry $templateRegistry,
        private readonly LayoutVersionService $versionService,
    ) {
    }

    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 15);
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'page_type' => (string) $request->query('page_type', ''),
            'template_id' => (string) $request->query('template_id', ''),
            'is_homepage' => (string) $request->query('is_homepage', ''),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true)
            ? $filters['per_page']
            : $defaultPerPage;

        $pagesQuery = Page::query()
            ->with(['template', 'ogImage'])
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%' . $filters['search'] . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('excerpt', 'like', $like);
                });
            })
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['page_type'] !== '', fn ($query) => $query->where('page_type', $filters['page_type']))
            ->when($filters['template_id'] !== '', fn ($query) => $query->where('template_id', $filters['template_id']))
            ->when($filters['is_homepage'] === 'yes', fn ($query) => $query->where('is_homepage', true))
            ->when($filters['is_homepage'] === 'no', fn ($query) => $query->where('is_homepage', false));

        $pageTypes = Page::query()
            ->whereNotNull('page_type')
            ->distinct()
            ->orderBy('page_type')
            ->pluck('page_type');

        $templates = Template::query()
            ->whereIn('template_type', ['page', 'list'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'key']);

        $pages = $pagesQuery
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate($filters['per_page'])
            ->withQueryString();

        return view('admin.content.layout.pages.index', compact('pages', 'filters', 'pageTypes', 'templates'));
    }

    public function create(Request $request): View
    {
        $templates = Template::query()
            ->active()
            ->whereIn('template_type', ['page', 'list'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $ogImageMediaItems = $this->ogImageMediaItems([
            $request->old('og_image_media_id'),
        ]);

        return view('admin.content.layout.pages.create', compact('templates', 'ogImageMediaItems'));
    }

    public function previewCreate(Request $request): JsonResponse
    {
        return $this->previewResponse($request);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => ['nullable', 'integer', 'exists:templates,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'is_homepage' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'published_at' => ['nullable', 'date'],
            'unpublished_at' => ['nullable', 'date', 'after_or_equal:published_at'],
        ]);

        $validated['slug'] = $this->makeUniqueSlug(($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['title']);
        $validated['is_homepage'] = $request->boolean('is_homepage');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['published_at'] = $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null);
        $validated['created_by_admin_id'] = auth('admin')->id();
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if (($validated['template_id'] ?? null) && ! $this->templateIsAllowedForPage((int) $validated['template_id'])) {
            return back()
                ->withInput()
                ->withErrors(['template_id' => 'Template นี้ไม่รองรับหน้า Page/List']);
        }

        if (($validated['og_image_media_id'] ?? null) && ! $this->mediaIsAllowedForOgImage((int) $validated['og_image_media_id'])) {
            return back()
                ->withInput()
                ->withErrors(['og_image_media_id' => 'OG Image ต้องเป็นรูปภาพที่อัปโหลดสำเร็จแล้ว']);
        }

        DB::transaction(function () use ($validated) {
            if ($validated['is_homepage']) {
                Page::query()->update(['is_homepage' => false]);
            }

            $page = Page::create($validated);
            $this->versionService->snapshotPage($page, 'created');
        });

        return redirect()
            ->route('admin.content.pages.index')
            ->with('success', 'สร้างหน้าเว็บเรียบร้อยแล้ว');
    }

    public function show(Page $page): View
    {
        $page->load([
            'template',
            'sections' => fn ($query) => $query->orderBy('sort_order'),
            'ogImage',
            'versions' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('admin.content.layout.pages.show', compact('page'));
    }

    public function edit(Request $request, Page $page): View
    {
        $templates = Template::query()
            ->active()
            ->whereIn('template_type', ['page', 'list'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $ogImageMediaItems = $this->ogImageMediaItems([
            $request->old('og_image_media_id', $page->og_image_media_id),
        ]);

        return view('admin.content.layout.pages.edit', compact('page', 'templates', 'ogImageMediaItems'));
    }

    public function ogImageMediaPicker(Request $request): View
    {
        return view('admin.content.layout.pages.partials._og_image_media_grid', [
            'mediaItems' => $this->ogImageMediaItems(
                selectedMediaIds: [$request->input('selected')],
                search: $request->string('q')->toString()
            ),
        ]);
    }

    public function preview(Request $request, Page $page): JsonResponse
    {
        return $this->previewResponse($request, $page);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => ['nullable', 'integer', 'exists:templates,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'is_homepage' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'published_at' => ['nullable', 'date'],
            'unpublished_at' => ['nullable', 'date', 'after_or_equal:published_at'],
        ]);

        $validated['slug'] = $this->makeUniqueSlug(($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['title'], $page);
        $validated['is_homepage'] = $request->boolean('is_homepage');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['published_at'] = $validated['published_at'] ?? ($validated['status'] === 'published' ? ($page->published_at ?? now()) : null);
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if (($validated['template_id'] ?? null) && ! $this->templateIsAllowedForPage((int) $validated['template_id'])) {
            return back()
                ->withInput()
                ->withErrors(['template_id' => 'Template นี้ไม่รองรับหน้า Page/List']);
        }

        if (($validated['og_image_media_id'] ?? null) && ! $this->mediaIsAllowedForOgImage((int) $validated['og_image_media_id'])) {
            return back()
                ->withInput()
                ->withErrors(['og_image_media_id' => 'OG Image ต้องเป็นรูปภาพที่อัปโหลดสำเร็จแล้ว']);
        }

        DB::transaction(function () use ($page, $validated) {
            $this->versionService->snapshotPage($page, 'before_update');

            if ($validated['is_homepage']) {
                Page::query()
                    ->whereKeyNot($page->id)
                    ->update(['is_homepage' => false]);
            }

            $page->update($validated);
            $this->versionService->snapshotPage($page, 'updated');
        });

        return redirect()
            ->route('admin.content.pages.index')
            ->with('success', 'อัปเดตหน้าเว็บเรียบร้อยแล้ว');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->is_homepage) {
            return back()->withErrors(['page' => 'ไม่สามารถลบหน้า homepage ได้']);
        }

        if ($this->pageIsReferencedBySections($page)) {
            return back()->withErrors(['page' => 'ไม่สามารถลบหน้าที่ถูกใช้งานใน section อื่น']);
        }

        if ($this->pageIsReferencedByMenus($page)) {
            return back()->withErrors(['page' => 'ไม่สามารถลบหน้าที่ถูกใช้งานในเมนู']);
        }

        DB::transaction(function () use ($page) {
            $this->versionService->snapshotPage($page, 'before_delete');
            $page->delete();
        });

        return redirect()
            ->route('admin.content.pages.index')
            ->with('success', 'ลบหน้าเว็บเรียบร้อยแล้ว');
    }

    public function rollback(Page $page, int $version): RedirectResponse
    {
        $this->versionService->rollbackPage($page, $version);

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'ย้อนกลับเวอร์ชันหน้าเว็บเรียบร้อยแล้ว');
    }

    private function previewResponse(Request $request, ?Page $existingPage = null): JsonResponse
    {
        $page = $this->previewPage($request, $existingPage);
        $viewPath = $this->previewViewPath($page);
        $sections = $this->previewSections($existingPage);
        $sectionData = [];
        $items = collect();

        try {
            $html = view($viewPath, compact('page', 'sections', 'sectionData', 'items'))->render();
        } catch (\Throwable $exception) {
            report($exception);

            $html = view('frontend.templates.previews.admin-iframe', [
                'previewTitle' => 'Preview error',
                'previewMessage' => $exception->getMessage(),
            ])->render();
        }

        return response()->json([
            'html' => $html,
        ]);
    }

    private function ogImageMediaItems(array $selectedMediaIds = [], string $search = '')
    {
        $mediaItems = Media::query()
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('original_filename', 'like', '%' . $search . '%')
                        ->orWhere('filename', 'like', '%' . $search . '%')
                        ->orWhere('id', $search);
                });
            })
            ->orderByDesc('id')
            ->paginate(
                perPage: 7,
                columns: ['id', 'title', 'original_filename', 'media_type', 'path'],
                pageName: 'page_og_image_media_page'
            )
            ->withPath(route('admin.content.pages.media-picker.og-image'))
            ->appends(array_filter(['q' => $search]));

        $selectedMediaIds = collect($selectedMediaIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedMediaIds->isEmpty()) {
            return $mediaItems;
        }

        $visibleIds = $mediaItems->getCollection()->pluck('id')->map(fn ($id) => (int) $id);
        $missingSelectedIds = $selectedMediaIds->diff($visibleIds)->values();

        if ($missingSelectedIds->isEmpty()) {
            return $mediaItems;
        }

        $selectedItems = Media::query()
            ->whereIn('id', $missingSelectedIds)
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->get(['id', 'title', 'original_filename', 'media_type', 'path'])
            ->sortBy(fn (Media $media) => $selectedMediaIds->search((int) $media->id))
            ->values();

        $mediaItems->setCollection(
            $selectedItems
                ->concat($mediaItems->getCollection())
                ->unique('id')
                ->take(7)
                ->values()
        );

        return $mediaItems;
    }

    private function previewPage(Request $request, ?Page $existingPage = null): Page
    {
        $page = new Page([
            'template_id' => $request->integer('template_id') ?: null,
            'title' => $request->input('title') ?: $existingPage?->title ?: 'Untitled page',
            'slug' => SlugGenerator::make($request->input('slug') ?: $existingPage?->slug ?: $request->input('title'), 'preview'),
            'page_type' => $request->input('page_type') ?: $existingPage?->page_type ?: 'custom',
            'status' => $request->input('status') ?: $existingPage?->status ?: 'draft',
            'is_homepage' => $request->boolean('is_homepage'),
            'sort_order' => (int) ($request->input('sort_order') ?? $existingPage?->sort_order ?? 0),
            'excerpt' => $request->input('excerpt') ?: null,
            'description' => $request->input('description') ?: null,
            'meta_title' => $request->input('meta_title') ?: null,
            'meta_description' => $request->input('meta_description') ?: null,
            'meta_keywords' => $request->input('meta_keywords') ?: null,
            'canonical_url' => $request->input('canonical_url') ?: null,
            'og_title' => $request->input('og_title') ?: null,
            'og_description' => $request->input('og_description') ?: null,
            'og_image_media_id' => $request->integer('og_image_media_id') ?: null,
        ]);

        if ($existingPage) {
            $page->id = $existingPage->id;
            $page->exists = true;
        }

        if ($page->template_id) {
            $template = Template::query()
                ->active()
                ->find($page->template_id);

            if ($template) {
                $page->setRelation('template', $template);
            }
        } elseif ($existingPage?->template) {
            $page->setRelation('template', $existingPage->template);
        }

        return $page;
    }

    private function previewViewPath(Page $page): string
    {
        $viewPath = $page->template?->view_path;

        if ($viewPath && view()->exists($viewPath)) {
            return $viewPath;
        }

        return view()->exists('frontend.templates.pages.builder')
            ? 'frontend.templates.pages.builder'
            : 'frontend.templates.previews.admin-iframe';
    }

    private function previewSections(?Page $page)
    {
        if (! $page) {
            return collect();
        }

        $sections = $page->sections()
            ->where('status', 'active')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        $page->setRelation('sections', $sections);

        return app(FrontendPageController::class)->buildPageSections($page);
    }

    private function pageIsReferencedBySections(Page $page): bool
    {
        $needle = (string) $page->id;

        return \App\Models\Content\Layout\PageSection::query()
            ->where('page_id', '!=', $page->id)
            ->where(function ($query) use ($needle) {
                foreach (['primary_page_id', 'secondary_page_id', 'all_button_page_id'] as $key) {
                    $query->orWhere('content->' . $key, $needle);
                }
            })
            ->exists();
    }

    private function pageIsReferencedByMenus(Page $page): bool
    {
        return MenuItem::query()
            ->where('menu_item_type', 'page')
            ->where('page_id', $page->id)
            ->exists();
    }

    private function makeUniqueSlug(string $value, ?Page $ignorePage = null): string
    {
        $baseSlug = SlugGenerator::make($value, 'page');
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Page::withTrashed()
                ->where('slug', $slug)
                ->when($ignorePage, fn ($query) => $query->whereKeyNot($ignorePage->id))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function templateIsAllowedForPage(int $templateId): bool
    {
        return Template::query()
            ->whereKey($templateId)
            ->active()
            ->whereIn('template_type', ['page', 'list'])
            ->exists();
    }

    private function mediaIsAllowedForOgImage(int $mediaId): bool
    {
        return Media::query()
            ->whereKey($mediaId)
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->exists();
    }
}
