<?php

namespace App\Http\Requests\Admin\Content\Category;

use App\Models\Content\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
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
                $parentId = $this->integer('parent_id') ?: null;

                if (! $parentId) {
                    return;
                }

                $parent = Category::query()->find($parentId);

                if ($parent && $parent->type_key !== $this->input('type_key')) {
                    $validator->errors()->add('parent_id', 'หมวดหมู่แม่ต้องเป็นประเภทเดียวกัน');
                }
            },
        ];
    }
}
