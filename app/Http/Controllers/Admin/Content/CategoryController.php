<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Content\Category\UpdateCategoryRequest;
use App\Models\Content\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            ->latest('id');

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

        $categories = $query->paginate(15)->withQueryString();

        $parents = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'type_key', 'level']);

        return view('admin.content.categories.index', [
            'title' => 'Category Management',
            'categories' => $categories,
            'parents' => $parents,
            'types' => self::TYPE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        $parents = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'type_key', 'level']);

        return view('admin.content.categories.create', [
            'title' => 'Create Category',
            'parents' => $parents,
            'types' => self::TYPE_OPTIONS,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Category::query()->find($validated['parent_id']);
        }

        $category = Category::query()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
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

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'สร้างหมวดหมู่เรียบร้อยแล้ว');
    }

    public function edit(Category $category): View
    {
        $parents = Category::query()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name', 'type_key', 'level']);

        return view('admin.content.categories.edit', [
            'title' => 'Edit Category',
            'category' => $category,
            'parents' => $parents,
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

        $category->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
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

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'อัปเดตหมวดหมู่เรียบร้อยแล้ว');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีหมวดหมู่ย่อยผูกอยู่');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }
}