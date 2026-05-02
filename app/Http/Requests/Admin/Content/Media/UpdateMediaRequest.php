<?php

namespace App\Http\Requests\Admin\Content\Media;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'max:10240'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'media_folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
            'visibility' => ['required', Rule::in(['public', 'private'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'ไฟล์',
            'title' => 'ชื่อไฟล์',
            'alt_text' => 'Alt text',
            'caption' => 'คำบรรยาย',
            'description' => 'รายละเอียด',
            'media_folder_id' => 'โฟลเดอร์',
            'visibility' => 'การมองเห็น',
        ];
    }
}