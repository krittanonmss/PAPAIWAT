<?php

namespace App\Http\Requests\Admin\Content\Category;

use App\Models\Content\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type_key' => ['required', 'in:temple,article'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $category = $this->route('category');

                if (! $category instanceof Category) {
                    return;
                }

                $parentId = $this->integer('parent_id') ?: null;

                if ($parentId === $category->id) {
                    $validator->errors()->add('parent_id', 'ไม่สามารถเลือกหมวดหมู่นี้เป็นหมวดหมู่แม่ของตัวเองได้');
                    return;
                }

                if ($parentId && $this->descendantIds($category)->contains($parentId)) {
                    $validator->errors()->add('parent_id', 'ไม่สามารถเลือกหมวดหมู่ย่อยของตัวเองเป็นหมวดหมู่แม่ได้');
                }

                if ($parentId) {
                    $parent = Category::query()->find($parentId);

                    if ($parent && $parent->type_key !== $this->input('type_key')) {
                        $validator->errors()->add('parent_id', 'หมวดหมู่แม่ต้องเป็นประเภทเดียวกัน');
                    }
                }

                if ($category->type_key !== $this->input('type_key') && $category->children()->exists()) {
                    $validator->errors()->add('type_key', 'ไม่สามารถเปลี่ยนประเภทของหมวดหมู่ที่มีหมวดหมู่ย่อยอยู่');
                }
            },
        ];
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
}
