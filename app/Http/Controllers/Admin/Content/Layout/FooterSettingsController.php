<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Support\FooterSettings;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FooterSettingsController extends Controller
{
    public function edit(): View
    {
        $footerMenus = Menu::query()
            ->with([
                'items' => fn ($query) => $query
                    ->with(['page', 'content'])
                    ->orderBy('sort_order')
                    ->orderBy('label'),
            ])
            ->withCount(['items', 'rootItems'])
            ->where('location_key', 'footer')
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $activeFooterMenu = $footerMenus->firstWhere('is_default', true)
            ?? $footerMenus->firstWhere('status', 'active')
            ?? $footerMenus->first();
        $childrenByParent = $activeFooterMenu
            ? $activeFooterMenu->items->whereNotNull('parent_id')->groupBy('parent_id')
            : collect();
        $rootItems = $activeFooterMenu
            ? $activeFooterMenu->items->whereNull('parent_id')->values()
            : collect();

        return view('admin.content.layout.footer.edit', [
            'settings' => FooterSettings::get(),
            'footerMenus' => $footerMenus,
            'activeFooterMenu' => $activeFooterMenu,
            'childrenByParent' => $childrenByParent,
            'rootItems' => $rootItems,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_description' => ['nullable', 'string', 'max:500'],
            'footer_note' => ['nullable', 'string', 'max:500'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'show_brand' => ['nullable', 'boolean'],
            'show_menu' => ['nullable', 'boolean'],
            'show_bottom_bar' => ['nullable', 'boolean'],
            'show_border' => ['nullable', 'boolean'],
            'background_style' => ['required', 'string', 'in:glass,solid,minimal'],
            'column_count' => ['required', 'string', 'in:3,4,5'],
        ]);

        foreach (['show_brand', 'show_menu', 'show_bottom_bar', 'show_border'] as $key) {
            $validated[$key] = $request->boolean($key);
        }

        FooterSettings::save($validated);

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'อัปเดต Footer เรียบร้อยแล้ว');
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $name = trim((string) ($validated['name'] ?? '')) ?: 'Footer Menu';

        DB::transaction(function () use ($name): void {
            Menu::query()
                ->where('location_key', 'footer')
                ->update(['is_default' => false]);

            Menu::create([
                'name' => $name,
                'slug' => $this->uniqueMenuSlug($name),
                'location_key' => 'footer',
                'description' => 'จัดการโครงสร้างลิงก์ใน Footer',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 0,
                'created_by_admin_id' => auth('admin')->id(),
                'updated_by_admin_id' => auth('admin')->id(),
            ]);
        });

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'สร้าง Footer Menu เรียบร้อยแล้ว');
    }

    public function setDefaultMenu(Menu $menu): RedirectResponse
    {
        $this->ensureFooterMenu($menu);

        DB::transaction(function () use ($menu): void {
            Menu::query()
                ->where('location_key', 'footer')
                ->whereKeyNot($menu->id)
                ->update(['is_default' => false]);

            $menu->update([
                'status' => 'active',
                'is_default' => true,
                'updated_by_admin_id' => auth('admin')->id(),
            ]);
        });

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'ตั้ง Footer Menu เริ่มต้นเรียบร้อยแล้ว');
    }

    public function storeColumn(Request $request, Menu $menu): RedirectResponse
    {
        $this->ensureFooterMenu($menu);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'label' => $validated['label'],
            'slug' => SlugGenerator::make($validated['label'], 'footer-column'),
            'menu_item_type' => 'heading',
            'target' => '_self',
            'is_enabled' => true,
            'sort_order' => $validated['sort_order'] ?? $this->nextSortOrder($menu),
            'created_by_admin_id' => auth('admin')->id(),
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'เพิ่มคอลัมน์ Footer เรียบร้อยแล้ว');
    }

    public function storeLink(Request $request, Menu $menu): RedirectResponse
    {
        $this->ensureFooterMenu($menu);

        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query
                    ->where('menu_id', $menu->id)
                    ->whereNull('parent_id')),
            ],
            'label' => ['required', 'string', 'max:255'],
            'external_url' => ['required', 'string', 'max:255'],
            'target' => ['required', 'string', 'in:_self,_blank'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (! $this->externalUrlIsValid($validated['external_url'])) {
            throw ValidationException::withMessages([
                'external_url' => 'URL ต้องเป็น path ที่ขึ้นต้นด้วย / หรือ URL http/https',
            ]);
        }

        $parentId = empty($validated['parent_id']) ? null : (int) $validated['parent_id'];

        MenuItem::create([
            'menu_id' => $menu->id,
            'parent_id' => $parentId,
            'label' => $validated['label'],
            'slug' => SlugGenerator::make($validated['label'], 'footer-link'),
            'menu_item_type' => 'external_url',
            'url' => $validated['external_url'],
            'external_url' => $validated['external_url'],
            'target' => $validated['target'],
            'rel' => $validated['target'] === '_blank' ? 'noopener noreferrer' : null,
            'is_enabled' => true,
            'sort_order' => $validated['sort_order'] ?? $this->nextSortOrder($menu, $parentId),
            'created_by_admin_id' => auth('admin')->id(),
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.content.footer.edit')
            ->with('success', 'เพิ่มลิงก์ Footer เรียบร้อยแล้ว');
    }

    private function ensureFooterMenu(Menu $menu): void
    {
        abort_unless($menu->location_key === 'footer', 404);
    }

    private function uniqueMenuSlug(string $name): string
    {
        $baseSlug = SlugGenerator::make($name, 'footer-menu');
        $slug = $baseSlug;
        $suffix = 2;

        while (Menu::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function nextSortOrder(Menu $menu, ?int $parentId = null): int
    {
        return ((int) MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parentId)
            ->max('sort_order')) + 10;
    }

    private function externalUrlIsValid(string $value): bool
    {
        $value = trim($value);

        if ($value === '') {
            return false;
        }

        if (str_starts_with($value, '/')) {
            return ! str_starts_with($value, '//');
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
