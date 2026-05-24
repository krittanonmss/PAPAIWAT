<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Content\Category\UpdateCategoryRequest;
use App\Models\Admin\AuditLog;
use App\Models\Content\Category;
use App\Services\Admin\AdminPreferenceService;
use App\Support\SlugGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    private const TYPE_OPTIONS = [
        'temple',
        'article',
    ];

    public function index(Request $request): View
    {
        $query = Category::query()
            ->with(['parent'])
            ->orderBy('type_key')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->string('deleted')->toString() === 'only') {
            $query->onlyTrashed();
        } elseif ($request->string('deleted')->toString() === 'with') {
            $query->withTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('type_key', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type_key')) {
            $query->where('type_key', $request->string('type_key')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('parent_id')) {
            if ($request->string('parent_id')->toString() === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', (int) $request->input('parent_id'));
            }
        }

        $categories = $query->paginate($this->perPage($request, 15))->withQueryString();

        $selectedParent = null;

        if ($request->filled('parent_id') && $request->string('parent_id')->toString() !== 'root') {
            $selectedParent = Category::query()->find((int) $request->input('parent_id'));
        }

        return view('admin.content.categories.index', [
            'title' => 'Category Management',
            'categories' => $categories,
            'selectedParent' => $selectedParent,
            'parentOptions' => $this->categoryParentOptions(),
            'types' => self::TYPE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.content.categories.create', [
            'title' => 'Create Category',
            'types' => self::TYPE_OPTIONS,
        ]);
    }

    private function perPage(Request $request, int $default): int
    {
        $allowed = AdminPreferenceService::PER_PAGE_OPTIONS;
        $default = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $allowed, $default);
        $perPage = (int) $request->input('per_page', $default);

        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Category::query()->find($validated['parent_id']);
        }

        $category = DB::transaction(function () use ($request, $validated, $parent): Category {
            $category = Category::query()->create([
                'parent_id' => $validated['parent_id'] ?? null,
                'name' => $validated['name'],
                'slug' => $this->makeUniqueSlug(
                    ($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['name'],
                    $validated['parent_id'] ?? null,
                    $validated['type_key']
                ),
                'description' => $validated['description'] ?? null,
                'type_key' => $validated['type_key'],
                'level' => $parent ? ($parent->level + 1) : 0,
                'sort_order' => $validated['sort_order'] ?? 0,
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'created_by_admin_id' => auth('admin')->id(),
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $this->writeAuditLog($request, 'category.created', $category, null, $this->categoryAuditData($category));

            return $category;
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'สร้างหมวดหมู่เรียบร้อยแล้ว');
    }

    public function edit(Category $category): View
    {
        return view('admin.content.categories.edit', [
            'title' => 'Edit Category',
            'category' => $category,
            'types' => self::TYPE_OPTIONS,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Category::query()->find($validated['parent_id']);
        }

        DB::transaction(function () use ($request, $category, $validated, $parent): void {
            $oldData = $this->categoryAuditData($category);
            $oldParentId = $category->parent_id;
            $oldLevel = $category->level;

            $category->update([
                'parent_id' => $validated['parent_id'] ?? null,
                'name' => $validated['name'],
                'slug' => $this->makeUniqueSlug(
                    ($validated['slug'] ?? '') !== '' ? $validated['slug'] : $validated['name'],
                    $validated['parent_id'] ?? null,
                    $validated['type_key'],
                    $category
                ),
                'description' => $validated['description'] ?? null,
                'type_key' => $validated['type_key'],
                'level' => $parent ? ($parent->level + 1) : 0,
                'sort_order' => $validated['sort_order'] ?? 0,
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $this->recalculateSubtreeLevels($category);
            $category->refresh();

            $newData = $this->categoryAuditData($category);
            $this->writeAuditLog($request, 'category.updated', $category, $oldData, $newData);

            if ($oldParentId !== $category->parent_id || $oldLevel !== $category->level) {
                $this->writeAuditLog($request, 'category.moved', $category, [
                    'parent_id' => $oldParentId,
                    'level' => $oldLevel,
                ], [
                    'parent_id' => $category->parent_id,
                    'level' => $category->level,
                ]);
            }
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'อัปเดตหมวดหมู่เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีหมวดหมู่ย่อยผูกอยู่');
        }

        if ($category->contents()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีเนื้อหาใช้งานอยู่');
        }

        DB::transaction(function () use ($request, $category): void {
            $oldData = $this->categoryAuditData($category);
            $category->delete();

            $this->writeAuditLog($request, 'category.deleted', $category, $oldData, ['deleted' => true]);
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }

    public function restore(Request $request, int $category): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($category);

        if (! $category->trashed()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'หมวดหมู่นี้ยังไม่ได้ถูกลบ');
        }

        DB::transaction(function () use ($request, $category): void {
            $category->restore();
            $category->update(['updated_by_admin_id' => auth('admin')->id()]);

            $this->writeAuditLog($request, 'category.restored', $category, ['deleted' => true], $this->categoryAuditData($category));
        });

        return redirect()
            ->route('admin.categories.index', ['deleted' => 'with'])
            ->with('success', 'กู้คืนหมวดหมู่เรียบร้อยแล้ว');
    }

    public function bulkMove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'parent_id' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $parent = Category::query()->findOrFail((int) $validated['parent_id']);
        $categories = Category::query()
            ->whereIn('id', $validated['category_ids'])
            ->orderBy('level')
            ->get();

        if ($categories->isEmpty()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'กรุณาเลือกหมวดหมู่อย่างน้อย 1 รายการ');
        }

        if ($categories->contains(fn (Category $category) => (int) $category->id === (int) $parent->id)) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'ไม่สามารถย้ายหมวดหมู่เข้าไปใต้ตัวเองได้');
        }

        foreach ($categories as $category) {
            if ($category->type_key !== $parent->type_key) {
                return redirect()
                    ->route('admin.categories.index')
                    ->with('error', 'หมวดหมู่ที่เลือกต้องเป็นประเภทเดียวกับหมวดหมู่ปลายทาง');
            }

            if ($this->descendantIds($category)->contains((int) $parent->id)) {
                return redirect()
                    ->route('admin.categories.index')
                    ->with('error', 'ไม่สามารถย้ายหมวดหมู่เข้าไปใต้หมวดหมู่ย่อยของตัวเองได้');
            }

            if (($parent->level + 1 + $this->subtreeHeight($category)) > Category::MAX_LEVEL) {
                return redirect()
                    ->route('admin.categories.index')
                    ->with('error', 'หมวดหมู่มีลำดับชั้นได้สูงสุด '.(Category::MAX_LEVEL + 1).' ชั้น');
            }
        }

        $movedCount = 0;

        DB::transaction(function () use ($request, $categories, $parent, &$movedCount): void {
            foreach ($categories as $category) {
                if ((int) $category->parent_id === (int) $parent->id) {
                    continue;
                }

                $oldData = $this->categoryAuditData($category);

                $category->update([
                    'parent_id' => $parent->id,
                    'level' => $parent->level + 1,
                    'updated_by_admin_id' => auth('admin')->id(),
                ]);

                $this->recalculateSubtreeLevels($category);
                $category->refresh();

                $this->writeAuditLog($request, 'category.moved', $category, $oldData, $this->categoryAuditData($category) + [
                    'bulk_parent_id' => $parent->id,
                ]);

                $movedCount++;
            }
        });

        if ($movedCount === 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'ไม่มีหมวดหมู่ที่ต้องย้ายไปยังหมวดหมู่ปลายทางนี้');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'ย้ายหมวดหมู่ที่เลือกเรียบร้อยแล้ว');
    }

    private function makeUniqueSlug(
        string $name,
        ?int $parentId,
        string $typeKey,
        ?Category $ignoreCategory = null
    ): string {
        $baseSlug = SlugGenerator::make(
            $name,
            'category-'.substr(sha1($typeKey.'|'.($parentId ?? 'root').'|'.$name), 0, 10)
        );

        $slug = $baseSlug;
        $suffix = 2;

        while ($this->categorySlugExists($slug, $parentId, $typeKey, $ignoreCategory)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function categorySlugExists(
        string $slug,
        ?int $parentId,
        string $typeKey,
        ?Category $ignoreCategory = null
    ): bool {
        return Category::withTrashed()
            ->where('type_key', $typeKey)
            ->where('slug', $slug)
            ->when(
                $parentId === null,
                fn ($query) => $query->whereNull('parent_id'),
                fn ($query) => $query->where('parent_id', $parentId)
            )
            ->when($ignoreCategory, fn ($query) => $query->whereKeyNot($ignoreCategory->getKey()))
            ->exists();
    }

    private function recalculateSubtreeLevels(Category $category): void
    {
        $category->children()->each(function (Category $child) use ($category): void {
            $child->forceFill([
                'level' => $category->level + 1,
                'updated_by_admin_id' => auth('admin')->id(),
            ])->save();

            $this->recalculateSubtreeLevels($child);
        });
    }

    private function categoryParentOptions()
    {
        return Category::query()
            ->where('status', 'active')
            ->orderBy('type_key')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'type_key', 'level', 'parent_id']);
    }

    private function descendantIds(Category $category)
    {
        $ids = collect();
        $children = $category->children()->get(['id']);

        foreach ($children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->descendantIds($child));
        }

        return $ids;
    }

    private function subtreeHeight(Category $category): int
    {
        $maxHeight = 0;

        foreach ($category->children()->get(['id']) as $child) {
            $maxHeight = max($maxHeight, 1 + $this->subtreeHeight($child));
        }

        return $maxHeight;
    }

    private function categoryAuditData(Category $category): array
    {
        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'type_key' => $category->type_key,
            'level' => $category->level,
            'status' => $category->status,
            'sort_order' => $category->sort_order,
            'is_featured' => $category->is_featured,
        ];
    }

    private function writeAuditLog(Request $request, string $action, Category $category, ?array $oldData, ?array $newData): void
    {
        AuditLog::query()->create([
            'action' => $action,
            'table_name' => 'categories',
            'record_id' => $category->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'performed_by' => auth('admin')->id(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }
}
