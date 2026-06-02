<?php

namespace App\Http\Requests\Admin\Content\Media;

use App\Models\Content\Media\MediaFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('media_folders', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $folder = $this->route('mediaFolder');
            $parentId = $this->input('parent_id');

            if (! $folder instanceof MediaFolder || blank($parentId)) {
                return;
            }

            $parentId = (int) $parentId;

            if ($parentId === (int) $folder->id) {
                $validator->errors()->add('parent_id', 'ไม่สามารถเลือกโฟลเดอร์ตัวเองเป็นโฟลเดอร์แม่ได้');

                return;
            }

            if ($this->isDescendant($folder, $parentId)) {
                $validator->errors()->add('parent_id', 'ไม่สามารถเลือกโฟลเดอร์ย่อยของตัวเองเป็นโฟลเดอร์แม่ได้');
            }
        });
    }

    private function isDescendant(MediaFolder $folder, int $candidateParentId): bool
    {
        $childrenByParent = MediaFolder::query()
            ->get(['id', 'parent_id'])
            ->groupBy('parent_id');

        $stack = $childrenByParent->get($folder->id, collect())
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
