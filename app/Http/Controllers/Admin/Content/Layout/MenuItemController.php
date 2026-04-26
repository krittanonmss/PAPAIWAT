<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Models\Content\Layout\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function create(Menu $menu): View
    {
        $parentItems = $menu->items()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $pages = Page::query()
            ->orderBy('title')
            ->get();

        $contents = Content::query()
            ->orderBy('title')
            ->get();

        return view('admin.content.layout.menu-items.create', compact(
            'menu',
            'parentItems',
            'pages',
            'contents'
        ));
    }

    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'label' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'menu_item_type' => ['required', 'string', 'in:route,page,content,external_url,anchor'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'route_params' => ['nullable', 'json'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'content_id' => ['nullable', 'integer', 'exists:contents,id'],
            'url' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'anchor' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'string', 'in:_self,_blank'],
            'rel' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['label']);
        $validated['route_params'] = $this->decodeJson($validated['route_params'] ?? null);
        $validated['is_enabled'] = $request->boolean('is_enabled', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['created_by_admin_id'] = auth('admin')->id();
        $validated['updated_by_admin_id'] = auth('admin')->id();

        MenuItem::create($validated);

        return redirect()
            ->route('admin.content.menus.show', $menu)
            ->with('success', 'เพิ่มรายการเมนูเรียบร้อยแล้ว');
    }

    public function edit(Menu $menu, MenuItem $menuItem): View
    {
        abort_if($menuItem->menu_id !== $menu->id, 404);

        $parentItems = $menu->items()
            ->whereNull('parent_id')
            ->whereKeyNot($menuItem->id)
            ->orderBy('sort_order')
            ->get();

        $pages = Page::query()
            ->orderBy('title')
            ->get();

        $contents = Content::query()
            ->orderBy('title')
            ->get();

        return view('admin.content.layout.menu-items.edit', compact(
            'menu',
            'menuItem',
            'parentItems',
            'pages',
            'contents'
        ));
    }

    public function update(Request $request, Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_if($menuItem->menu_id !== $menu->id, 404);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'label' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'menu_item_type' => ['required', 'string', 'in:route,page,content,external_url,anchor'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'route_params' => ['nullable', 'json'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'content_id' => ['nullable', 'integer', 'exists:contents,id'],
            'url' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'anchor' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'string', 'in:_self,_blank'],
            'rel' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ]);

        if ((int) ($validated['parent_id'] ?? 0) === $menuItem->id) {
            return back()
                ->withErrors(['parent_id' => 'ไม่สามารถเลือกตัวเองเป็นเมนูหลักได้'])
                ->withInput();
        }

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['label']);
        $validated['route_params'] = $this->decodeJson($validated['route_params'] ?? null);
        $validated['is_enabled'] = $request->boolean('is_enabled', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['updated_by_admin_id'] = auth('admin')->id();

        $menuItem->update($validated);

        return redirect()
            ->route('admin.content.menus.show', $menu)
            ->with('success', 'อัปเดตรายการเมนูเรียบร้อยแล้ว');
    }

    public function destroy(Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_if($menuItem->menu_id !== $menu->id, 404);

        $menuItem->delete();

        return redirect()
            ->route('admin.content.menus.show', $menu)
            ->with('success', 'ลบรายการเมนูเรียบร้อยแล้ว');
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