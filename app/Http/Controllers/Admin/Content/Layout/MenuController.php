<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'location_key' => (string) $request->query('location_key', ''),
            'is_default' => (string) $request->query('is_default', ''),
            'per_page' => (int) $request->query('per_page', 15),
        ];
        $filters['per_page'] = in_array($filters['per_page'], [5, 10, 15, 25, 50], true)
            ? $filters['per_page']
            : 15;

        $menusQuery = Menu::query()
            ->withCount('items')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $like = '%' . $filters['search'] . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('location_key', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['location_key'] !== '', fn ($query) => $query->where('location_key', $filters['location_key']))
            ->when($filters['is_default'] === 'yes', fn ($query) => $query->where('is_default', true))
            ->when($filters['is_default'] === 'no', fn ($query) => $query->where('is_default', false));

        $locations = Menu::query()
            ->whereNotNull('location_key')
            ->where('location_key', '!=', '')
            ->distinct()
            ->orderBy('location_key')
            ->pluck('location_key');

        $menus = $menusQuery
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($filters['per_page'])
            ->withQueryString();

        return view('admin.content.layout.menus.index', compact('menus', 'filters', 'locations'));
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

        DB::transaction(function () use ($validated) {
            if ($validated['is_default']) {
                Menu::query()
                    ->where('location_key', $validated['location_key'])
                    ->update(['is_default' => false]);
            }

            Menu::create($validated);
        });

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

        DB::transaction(function () use ($menu, $validated) {
            if ($validated['is_default']) {
                Menu::query()
                    ->whereKeyNot($menu->id)
                    ->where('location_key', $validated['location_key'])
                    ->update(['is_default' => false]);
            }

            $menu->update($validated);
        });

        return redirect()
            ->route('admin.content.menus.index')
            ->with('success', 'อัปเดตเมนูเรียบร้อยแล้ว');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        if ($menu->is_default && ! request()->boolean('force')) {
            return back()->withErrors(['menu' => 'ไม่สามารถลบ default menu ได้ หากต้องการลบต้อง force อย่างชัดเจน']);
        }

        if ($menu->items()->exists() && ! request()->boolean('force')) {
            return back()->withErrors(['menu' => 'ไม่สามารถลบเมนูที่มีรายการอยู่ หากต้องการลบต้อง force อย่างชัดเจน']);
        }

        $menu->delete();

        return redirect()
            ->route('admin.content.menus.index')
            ->with('success', 'ลบเมนูเรียบร้อยแล้ว');
    }
}
