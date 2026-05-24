<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Services\Admin\AdminPreferenceService;
use App\Support\MenuUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    private const MAX_EDITOR_DEPTH = 2;

    public function index(Request $request): View
    {
        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $perPageOptions, 15);
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'location_key' => (string) $request->query('location_key', ''),
            'is_default' => (string) $request->query('is_default', ''),
            'per_page' => (int) $request->query('per_page', $defaultPerPage),
        ];
        $filters['per_page'] = in_array($filters['per_page'], $perPageOptions, true)
            ? $filters['per_page']
            : $defaultPerPage;

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
            'slug' => ['nullable', 'string', 'max:255'],
            'location_key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->makeUniqueSlug(($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['name']);
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

        $items = $menu->items;
        $items->each(fn (MenuItem $item) => $item->setAttribute('depth', $this->depthFromItems($item, $items)));
        $childrenByParent = $items->whereNotNull('parent_id')->groupBy('parent_id');
        $menuPreviewItems = $this->previewTree($items);
        $menuWarnings = $this->menuWarnings($items, $childrenByParent);

        return view('admin.content.layout.menus.show', compact(
            'menu',
            'childrenByParent',
            'menuPreviewItems',
            'menuWarnings'
        ));
    }

    public function edit(Menu $menu): View
    {
        return view('admin.content.layout.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'location_key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->makeUniqueSlug(($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['name'], $menu);
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

    public function updateStructure(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $items = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->get(['id', 'parent_id']);

        $itemIds = $items->pluck('id')->map(fn ($id) => (int) $id)->all();
        $submittedIds = collect($validated['items'])->pluck('id')->map(fn ($id) => (int) $id);

        if ($submittedIds->diff($itemIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'มีรายการเมนูที่ไม่ได้อยู่ในเมนูนี้',
            ]);
        }

        $parentMap = collect($validated['items'])
            ->mapWithKeys(fn (array $item) => [
                (int) $item['id'] => empty($item['parent_id']) ? null : (int) $item['parent_id'],
            ]);

        foreach ($parentMap as $itemId => $parentId) {
            if ($parentId === $itemId) {
                throw ValidationException::withMessages(['items' => 'ไม่สามารถวางรายการไว้ใต้ตัวเองได้']);
            }

            if ($parentId !== null && ! in_array($parentId, $itemIds, true)) {
                throw ValidationException::withMessages(['items' => 'เมนูแม่ต้องอยู่ในเมนูเดียวกัน']);
            }

            if ($this->wouldCreateCycle($itemId, $parentId, $parentMap)) {
                throw ValidationException::withMessages(['items' => 'โครงสร้างเมนูมีวงวน กรุณาตรวจสอบรายการแม่/ลูก']);
            }

            if ($this->depthFor($itemId, $parentMap) > self::MAX_EDITOR_DEPTH) {
                throw ValidationException::withMessages([
                    'items' => 'โครงสร้างเมนูรองรับไม่เกิน 3 ชั้นเพื่อให้แสดงผลบนมือถือได้ดี',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $menu): void {
            foreach ($validated['items'] as $item) {
                MenuItem::query()
                    ->where('menu_id', $menu->id)
                    ->whereKey((int) $item['id'])
                    ->update([
                        'parent_id' => empty($item['parent_id']) ? null : (int) $item['parent_id'],
                        'sort_order' => (int) $item['sort_order'],
                        'updated_by_admin_id' => auth('admin')->id(),
                    ]);
            }

            $menu->update(['updated_by_admin_id' => auth('admin')->id()]);
        });

        return redirect()
            ->route('admin.content.menus.show', $menu)
            ->with('success', 'อัปเดตโครงสร้างเมนูเรียบร้อยแล้ว');
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

    private function previewTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->filter(fn (MenuItem $item) => (int) ($item->parent_id ?? 0) === (int) ($parentId ?? 0))
            ->filter(fn (MenuItem $item) => $item->is_enabled && $this->itemIsActive($item))
            ->sortBy([
                ['sort_order', 'asc'],
                ['label', 'asc'],
            ])
            ->map(function (MenuItem $item) use ($items) {
                $preview = (object) $item->toArray();
                $preview->url = MenuUrl::resolve($item);
                $preview->children = $this->previewTree($items, $item->id);

                return $preview;
            })
            ->values();
    }

    private function menuWarnings(Collection $items, Collection $childrenByParent): array
    {
        return $items
            ->flatMap(function (MenuItem $item) use ($items, $childrenByParent) {
                $warnings = [];
                $url = MenuUrl::resolve($item);
                $depth = $this->depthFromItems($item, $items);

                if ($depth > self::MAX_EDITOR_DEPTH) {
                    $warnings[] = "{$item->label}: อยู่ลึกเกิน 3 ชั้น อาจใช้งานยากบนมือถือ";
                }

                if ($item->menu_item_type !== 'heading' && $url === '#') {
                    $warnings[] = "{$item->label}: ยังหา URL ปลายทางไม่ได้";
                }

                if ($item->menu_item_type === 'page' && (! $item->page || $item->page->status !== 'published')) {
                    $warnings[] = "{$item->label}: Page ยังไม่ published";
                }

                if ($item->menu_item_type === 'content' && (! $item->content || $item->content->status !== 'published')) {
                    $warnings[] = "{$item->label}: Content ยังไม่ published";
                }

                if (! $item->is_enabled && $childrenByParent->get($item->id, collect())->isNotEmpty()) {
                    $warnings[] = "{$item->label}: ปิดใช้งานอยู่ เมนูลูกจะไม่แสดงใน frontend";
                }

                return $warnings;
            })
            ->values()
            ->all();
    }

    private function itemIsActive(MenuItem $item): bool
    {
        return (! $item->starts_at || $item->starts_at->lte(now()))
            && (! $item->ends_at || $item->ends_at->gte(now()));
    }

    private function depthFromItems(MenuItem $item, Collection $items): int
    {
        $depth = 0;
        $parentId = $item->parent_id;

        while ($parentId) {
            $parent = $items->firstWhere('id', $parentId);

            if (! $parent) {
                break;
            }

            $depth++;
            $parentId = $parent->parent_id;
        }

        return $depth;
    }

    private function wouldCreateCycle(int $itemId, ?int $parentId, Collection $parentMap): bool
    {
        while ($parentId !== null) {
            if ($parentId === $itemId) {
                return true;
            }

            $parentId = $parentMap->get($parentId);
        }

        return false;
    }

    private function depthFor(int $itemId, Collection $parentMap): int
    {
        $depth = 0;
        $parentId = $parentMap->get($itemId);

        while ($parentId !== null) {
            $depth++;
            $parentId = $parentMap->get($parentId);
        }

        return $depth;
    }

    private function makeUniqueSlug(string $value, ?Menu $ignoreMenu = null): string
    {
        $baseSlug = SlugGenerator::make($value, 'menu');
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Menu::query()
                ->where('slug', $slug)
                ->when($ignoreMenu, fn ($query) => $query->whereKeyNot($ignoreMenu->id))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
