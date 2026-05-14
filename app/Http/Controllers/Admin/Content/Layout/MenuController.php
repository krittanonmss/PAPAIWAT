<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()
            ->withCount('items')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.content.layout.menus.index', compact('menus'));
    }

    public function create(): View
    {
        return view('admin.content.layout.menus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:menus,slug'],
            'location_key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['created_by_admin_id'] = auth('admin')->id();
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if ($validated['is_default']) {
            Menu::query()->update(['is_default' => false]);
        }

        Menu::create($validated);

        return redirect()
            ->route('admin.content.menus.index')
            ->with('success', 'สร้างเมนูเรียบร้อยแล้ว');
    }

    public function show(Menu $menu): View
    {
        $menu->load([
            'items' => fn ($query) => $query
                ->with(['page', 'content'])
                ->orderBy('sort_order')
                ->orderBy('label'),
        ]);

        return view('admin.content.layout.menus.show', compact('menu'));
    }

    public function edit(Menu $menu): View
    {
        return view('admin.content.layout.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:menus,slug,' . $menu->id],
            'location_key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['updated_by_admin_id'] = auth('admin')->id();

        if ($validated['is_default']) {
            Menu::query()
                ->whereKeyNot($menu->id)
                ->update(['is_default' => false]);
        }

        $menu->update($validated);

        return redirect()
            ->route('admin.content.menus.index')
            ->with('success', 'อัปเดตเมนูเรียบร้อยแล้ว');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('admin.content.menus.index')
            ->with('success', 'ลบเมนูเรียบร้อยแล้ว');
    }
}
