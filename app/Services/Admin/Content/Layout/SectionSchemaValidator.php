<?php

namespace App\Services\Admin\Content\Layout;

use App\Support\TemplateRegistry;
use Illuminate\Validation\ValidationException;

class SectionSchemaValidator
{
    public function __construct(private readonly TemplateRegistry $registry)
    {
    }

    public function validate(string $componentKey, array $content, array $settings): void
    {
        $schema = $this->registry->section($componentKey);

        foreach ($schema['content_required'] ?? [] as $field) {
            if (trim((string) ($content[$field] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'content' => "Section {$componentKey} ต้องมีค่า {$field}",
                ]);
            }
        }

        if (! empty($schema['cta'])) {
            $primaryEnabled = (bool) ($content['primary_enabled'] ?? true);
            $secondaryEnabled = (bool) ($content['secondary_enabled'] ?? true);
            $hasPrimary = $primaryEnabled && ! empty($content['primary_label']) && ! empty($content['primary_url']);
            $hasPrimaryPage = $primaryEnabled && ! empty($content['primary_label']) && ! empty($content['primary_page_id']);
            $hasSecondary = $secondaryEnabled && ! empty($content['secondary_label']) && ! empty($content['secondary_url']);
            $hasSecondaryPage = $secondaryEnabled && ! empty($content['secondary_label']) && ! empty($content['secondary_page_id']);

            if (! $hasPrimary && ! $hasPrimaryPage && ! $hasSecondary && ! $hasSecondaryPage) {
                throw ValidationException::withMessages([
                    'content' => 'CTA section ต้องมีปุ่มอย่างน้อยหนึ่งปุ่ม',
                ]);
            }
        }

        if (isset($content['image_media_id']) && $content['image_media_id'] !== '' && ! ctype_digit((string) $content['image_media_id'])) {
            throw ValidationException::withMessages([
                'content' => 'image_media_id ต้องเป็นรหัส media ที่ถูกต้อง',
            ]);
        }

        if (isset($settings['limit']) && ((int) $settings['limit'] < 1 || (int) $settings['limit'] > 12)) {
            throw ValidationException::withMessages([
                'settings' => 'limit ต้องอยู่ระหว่าง 1 ถึง 12',
            ]);
        }
    }
}
