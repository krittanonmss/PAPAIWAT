<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Content\Layout\Menu;
use App\Models\Content\Layout\MenuItem;
use App\Models\Content\Layout\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function create(Menu $menu): View
    {
        $parentItems = $this->parentItems($menu);
        $pages = $this->selectedPages(old('page_id'));
        $contents = $this->selectedContents(old('content_id'));

        return view('admin.content.layout.menu-items.create', compact(
            'menu',
            'parentItems',
            'pages',
            'contents'
        ));
    }

    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate($this->rules($menu));

        $this->validateTypeData($validated);

        $validated = $this->prepareMenuItemData($validated, $request, $menu);
        $validated['created_by_admin_id'] = auth('admin')->id();

        MenuItem::create($validated);

        return redirect()
            ->route('admin.content.menus.show', $menu)
            ->with('success', 'เพิ่มรายการเมนูเรียบร้อยแล้ว');
    }

    public function pageLookup(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $ids = collect((array) $request->query('ids', []))
            ->merge(explode(',', (string) $request->query('selected', '')))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $pages = Page::query()
            ->when($ids->isNotEmpty(), fn ($query) => $query->whereIn('id', $ids))
            ->when($ids->isEmpty() && $q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('title', 'like', '%' . $q . '%')
                        ->orWhere('slug', 'like', '%' . $q . '%');
                });
            })
            ->when($ids->isEmpty() && $q === '', fn ($query) => $query->latest('id'))
            ->orderBy('title')
            ->limit(20)
            ->get(['id', 'title', 'slug', 'status']);

        $items = $pages->map(fn (Page $page) => [
                'id' => $page->id,
                'label' => $page->title,
                'meta' => '/' . ltrim($page->slug, '/') . ' | ' . $page->status,
            ])->values();

        return response()->json([
            'items' => $items,
            'results' => $items,
        ]);
    }

    public function contentLookup(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $ids = collect((array) $request->query('ids', []))
            ->merge(explode(',', (string) $request->query('selected', '')))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $contents = Content::query()
            ->when($ids->isNotEmpty(), fn ($query) => $query->whereIn('id', $ids))
            ->when($ids->isEmpty() && $q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('title', 'like', '%' . $q . '%')
                        ->orWhere('slug', 'like', '%' . $q . '%')
                        ->orWhere('content_type', 'like', '%' . $q . '%');
                });
            })
            ->when($ids->isEmpty() && $q === '', fn ($query) => $query->latest('id'))
            ->orderBy('title')
            ->limit(20)
            ->get(['id', 'content_type', 'title', 'slug', 'status']);

        $items = $contents->map(fn (Content $content) => [
                'id' => $content->id,
                'label' => $content->title,
                'meta' => $content->content_type . ' | ' . $content->slug . ' | ' . $content->status,
            ])->values();

        return response()->json([
            'items' => $items,
            'results' => $items,
        ]);
    }

    private function rules(Menu $menu): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('menu_id', $menu->id)),
            ],
            'label' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'menu_item_type' => ['required', 'string', 'in:heading,route,page,content,external_url,anchor'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'route_params' => ['nullable', 'json'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'content_id' => ['nullable', 'integer', 'exists:contents,id'],
            'url' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'anchor' => ['nullable', 'string', 'max:255', 'regex:/^#[A-Za-z][A-Za-z0-9_-]*$/'],
            'target' => ['required', 'string', 'in:_self,_blank'],
            'rel' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'css_class' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function edit(Menu $menu, MenuItem $menuItem): View
    {
        abort_if($menuItem->menu_id !== $menu->id, 404);

        $parentItems = $this->parentItems($menu, $menuItem);
        $pages = $this->selectedPages(old('page_id', $menuItem->page_id));
        $contents = $this->selectedContents(old('content_id', $menuItem->content_id));

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

        $validated = $request->validate($this->rules($menu));

        if ((int) ($validated['parent_id'] ?? 0) === $menuItem->id) {
            return back()
                ->withErrors(['parent_id' => 'ไม่สามารถเลือกตัวเองเป็นเมนูหลักได้'])
                ->withInput();
        }

        if ($this->isDescendant($menuItem, (int) ($validated['parent_id'] ?? 0))) {
            return back()
                ->withErrors(['parent_id' => 'ไม่สามารถเลือกเมนูย่อยของตัวเองเป็นเมนูแม่ได้'])
                ->withInput();
        }

        $this->validateTypeData($validated);

        $validated = $this->prepareMenuItemData($validated, $request, $menu);

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

    private function prepareMenuItemData(array $validated, Request $request, Menu $menu): array
    {
        $type = $validated['menu_item_type'];

        if ($type === 'heading') {
            $validated['route_name'] = null;
            $validated['route_params'] = null;
            $validated['page_id'] = null;
            $validated['content_id'] = null;
            $validated['url'] = null;
            $validated['external_url'] = null;
            $validated['anchor'] = null;
            $validated['target'] = '_self';
            $validated['rel'] = null;
        }

        if ($type === 'anchor' && empty($validated['anchor'])) {
            $validated['anchor'] = $validated['external_url'] ?? $validated['url'] ?? null;
        }

        if ($type === 'external_url' && empty($validated['external_url'])) {
            $validated['external_url'] = $validated['url'] ?? null;
        }

        if ($type !== 'route') {
            $validated['route_name'] = null;
            $validated['route_params'] = null;
        }

        if ($type !== 'page') {
            $validated['page_id'] = null;
        }

        if ($type !== 'content') {
            $validated['content_id'] = null;
        }

        if ($type !== 'external_url') {
            $validated['external_url'] = null;
            $validated['url'] = null;
        }

        if ($type !== 'anchor') {
            $validated['anchor'] = null;
        }

        $validated['menu_id'] = $menu->id;
        $generatedSlug = Str::slug($validated['label']);
        $validated['slug'] = ($validated['slug'] ?? null) ?: ($generatedSlug !== '' ? $generatedSlug : 'menu-item');
        $validated['route_params'] = $this->decodeJson($validated['route_params'] ?? null);
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['updated_by_admin_id'] = auth('admin')->id();
        $validated['rel'] = $validated['target'] === '_blank'
            ? $this->mergeRel($validated['rel'] ?? null, ['noopener', 'noreferrer'])
            : ($validated['rel'] ?? null);

        return $validated;
    }

    private function validateTypeData(array $validated): void
    {
        $type = $validated['menu_item_type'];

        $messages = match ($type) {
            'page' => empty($validated['page_id']) ? ['page_id' => 'เมนูประเภท page ต้องเลือก page'] : [],
            'content' => empty($validated['content_id']) ? ['content_id' => 'เมนูประเภท content ต้องเลือก content'] : [],
            'route' => empty($validated['route_name']) || ! Route::has($validated['route_name'])
                ? ['route_name' => 'route_name ต้องเป็น route ที่มีอยู่จริง']
                : [],
            'external_url' => $this->externalUrlIsValid($validated['external_url'] ?? $validated['url'] ?? null)
                ? []
                : ['external_url' => 'external_url ต้องเป็น URL ที่ถูกต้อง หรือ path ที่ขึ้นต้นด้วย /'],
            'anchor' => empty($validated['anchor']) ? ['anchor' => 'anchor ต้องขึ้นต้นด้วย #'] : [],
            default => [],
        };

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function externalUrlIsValid(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        if (str_starts_with($value, '/')) {
            return ! str_starts_with($value, '//');
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    private function mergeRel(?string $rel, array $required): string
    {
        return collect(preg_split('/\s+/', trim((string) $rel)) ?: [])
            ->filter()
            ->merge($required)
            ->map(fn ($value) => strtolower($value))
            ->unique()
            ->implode(' ');
    }

    private function parentItems(Menu $menu, ?MenuItem $exclude = null)
    {
        $query = $menu->items()
            ->orderBy('sort_order')
            ->orderBy('label');

        if ($exclude) {
            $query->whereKeyNot($exclude->id);
        }

        return $query->get();
    }

    private function selectedPages(mixed $selectedId)
    {
        return Page::query()
            ->when($selectedId, fn ($query) => $query->whereKey((int) $selectedId))
            ->orderBy('title')
            ->limit(20)
            ->get();
    }

    private function selectedContents(mixed $selectedId)
    {
        return Content::query()
            ->when($selectedId, fn ($query) => $query->whereKey((int) $selectedId))
            ->orderBy('title')
            ->limit(20)
            ->get();
    }

    private function isDescendant(MenuItem $menuItem, int $candidateParentId): bool
    {
        if ($candidateParentId <= 0) {
            return false;
        }

        $childrenByParent = MenuItem::query()
            ->where('menu_id', $menuItem->menu_id)
            ->get(['id', 'parent_id'])
            ->groupBy('parent_id');

        $stack = $childrenByParent->get($menuItem->id, collect())
            ->pluck('id')
            ->all();

        while ($stack !== []) {
            $id = (int) array_pop($stack);

            if ($id === $candidateParentId) {
                return true;
            }

            foreach ($childrenByParent->get($id, collect()) as $child) {
                $stack[] = (int) $child->id;
            }
        }

        return false;
    }
}
