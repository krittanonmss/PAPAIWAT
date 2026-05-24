<?php

namespace App\Http\Requests\Admin\Content\Media;

use App\Support\SiteSettings;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $maxKilobytes = max(1024, (int) SiteSettings::get('media', 'max_upload_mb', 5) * 1024);
        $mimeTypes = $this->allowedMimeTypes();

        return [
            'file' => ['nullable', 'file', 'max:'.$maxKilobytes, 'mimetypes:'.implode(',', $mimeTypes)],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:'.$maxKilobytes, 'mimetypes:'.implode(',', $mimeTypes)],
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

    private function allowedMimeTypes(): array
    {
        $configuredTypes = (array) SiteSettings::get('media', 'allowed_types', ['image', 'document']);
        $mimeTypes = [];

        if (in_array('image', $configuredTypes, true)) {
            $mimeTypes = array_merge($mimeTypes, ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
        }

        if (in_array('document', $configuredTypes, true)) {
            $mimeTypes[] = 'application/pdf';
        }

        return $mimeTypes ?: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    }
}
