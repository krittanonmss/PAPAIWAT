<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TemplateRegistry
{
    public function templates(): Collection
    {
        return collect(config('template_registry.templates', []))
            ->map(fn (array $template, string $key) => array_merge($template, ['key' => $key]));
    }

    public function sections(): Collection
    {
        return collect(config('template_registry.sections', []))
            ->map(fn (array $section, string $key) => array_merge($section, ['key' => $key]));
    }

    public function template(string $key): array
    {
        $template = $this->templates()->get($key);

        if (! $template) {
            throw ValidationException::withMessages([
                'key' => ['Template นี้ไม่มีอยู่ใน registry'],
            ]);
        }

        if (! view()->exists($template['view_path'])) {
            throw ValidationException::withMessages([
                'key' => ['Template registry ชี้ไปยัง view ที่ไม่มีอยู่จริง'],
            ]);
        }

        return $template;
    }

    public function section(string $componentKey): array
    {
        $section = $this->sections()->get($componentKey);

        if (! $section) {
            throw ValidationException::withMessages([
                'component_key' => ['Section นี้ไม่มีอยู่ใน registry'],
            ]);
        }

        return $section;
    }

    public function compatibleTemplateKeys(string $templateType, string $contentType): array
    {
        return $this->templates()
            ->filter(fn (array $template) => $template['template_type'] === $templateType)
            ->filter(fn (array $template) => in_array($template['content_type'], [$contentType, 'global'], true))
            ->keys()
            ->all();
    }
}
