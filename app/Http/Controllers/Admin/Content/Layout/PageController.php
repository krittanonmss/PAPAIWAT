<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()
            ->with('template')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);

        return view('admin.content.layout.pages.index', compact('pages'));
    }

    public function create(): View
    {
        $templates = Template::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $media = Media::query()
            ->where('media_type', 'image')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin.content.layout.pages.create', compact('templates', 'media'));
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
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

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_homepage'] = $request->boolean('is_homepage');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['created_by_admin_id'] = auth('admin')->id();
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if ($validated['is_homepage']) {
            Page::query()->update(['is_homepage' => false]);
        }

        Page::create($validated);

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
        ]);

        return view('admin.content.layout.pages.show', compact('page'));
    }

    public function edit(Page $page): View
    {
        $templates = Template::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $media = Media::query()
            ->where('media_type', 'image')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin.content.layout.pages.edit', compact('page', 'templates', 'media'));
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,' . $page->id],
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

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_homepage'] = $request->boolean('is_homepage');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if ($validated['is_homepage']) {
            Page::query()
                ->whereKeyNot($page->id)
                ->update(['is_homepage' => false]);
        }

        $page->update($validated);

        return redirect()
            ->route('admin.content.pages.show', $page)
            ->with('success', 'อัปเดตหน้าเว็บเรียบร้อยแล้ว');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.content.pages.index')
            ->with('success', 'ลบหน้าเว็บเรียบร้อยแล้ว');
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

    private function previewPage(Request $request, ?Page $existingPage = null): Page
    {
        $page = new Page([
            'template_id' => $request->integer('template_id') ?: null,
            'title' => $request->input('title') ?: $existingPage?->title ?: 'Untitled page',
            'slug' => $request->input('slug') ?: $existingPage?->slug ?: 'preview',
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

        return $page->sections()
            ->where('status', 'active')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();
    }
}
