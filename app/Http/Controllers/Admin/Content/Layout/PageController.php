<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
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

        $page = Page::create($validated);

        return redirect()
            ->route('admin.content.pages.show', $page)
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
}