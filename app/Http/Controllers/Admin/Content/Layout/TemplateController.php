<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()
            ->withCount('pages')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.content.layout.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.content.layout.templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255', 'unique:templates,key'],
            'description' => ['nullable', 'string'],
            'view_path' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['key'] = $validated['key'] ?: Str::slug($validated['name']);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($validated['is_default']) {
            Template::query()->update(['is_default' => false]);
        }

        Template::create($validated);

        return redirect()
            ->route('admin.content.templates.index')
            ->with('success', 'สร้าง Template เรียบร้อยแล้ว');
    }

    public function show(Template $template): View
    {
        $template->loadCount('pages');

        return view('admin.content.layout.templates.show', compact('template'));
    }

    public function edit(Template $template): View
    {
        return view('admin.content.layout.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255', 'unique:templates,key,' . $template->id],
            'description' => ['nullable', 'string'],
            'view_path' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['key'] = $validated['key'] ?: Str::slug($validated['name']);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($validated['is_default']) {
            Template::query()
                ->whereKeyNot($template->id)
                ->update(['is_default' => false]);
        }

        $template->update($validated);

        return redirect()
            ->route('admin.content.templates.show', $template)
            ->with('success', 'อัปเดต Template เรียบร้อยแล้ว');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $template->delete();

        return redirect()
            ->route('admin.content.templates.index')
            ->with('success', 'ลบ Template เรียบร้อยแล้ว');
    }
}
