<?php

namespace App\Http\Requests\Admin\Content\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'max:5120'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:5120'],
            'media_folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'visibility' => ['nullable', 'in:public,private'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->hasFile('file') && ! $this->hasFile('files')) {
                $validator->errors()->add('file', 'กรุณาเลือกไฟล์ก่อนอัปโหลด');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'file' => 'ไฟล์',
            'files' => 'ไฟล์',
            'files.*' => 'ไฟล์',
        ];
    }
}
