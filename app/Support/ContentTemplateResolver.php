<?php

namespace App\Support;

use App\Models\Content\Content;
use App\Models\Content\Layout\Template;

class ContentTemplateResolver
{
    public function resolveViewPath(
        Content $content,
        ?int $templateId,
        string $fallbackView,
        bool $useContentTemplate = true
    ): string
    {
        $templates = collect([
            $this->selectedTemplate($templateId, $content->content_type),
            $useContentTemplate ? $this->selectedTemplate($content->template_id, $content->content_type) : null,
            $this->defaultTemplateFor($content->content_type),
            $this->globalDefaultTemplate($content->content_type),
        ])->filter();

        foreach ($templates as $template) {
            if ($this->templateCanRender($template)) {
                return $template->view_path;
            }
        }

        return view()->exists($fallbackView)
            ? $fallbackView
            : 'frontend.templates.pages.default';
    }

    private function selectedTemplate(?int $templateId, ?string $contentType): ?Template
    {
        if (! $templateId) {
            return null;
        }

        return Template::query()
            ->active()
            ->where('view_path', 'like', 'frontend.templates.details.%')
            ->when($contentType, function ($query) use ($contentType) {
                $query->where(function ($query) use ($contentType) {
                    $query->where('key', $contentType . '-detail')
                        ->orWhere('view_path', 'like', 'frontend.templates.details.' . $contentType . '-%');
                });
            })
            ->find($templateId);
    }

    private function defaultTemplateFor(?string $contentType): ?Template
    {
        if (! $contentType) {
            return null;
        }

        return Template::query()
            ->active()
            ->where('key', $contentType . '-detail')
            ->first();
    }

    private function globalDefaultTemplate(?string $contentType): ?Template
    {
        return Template::query()
            ->active()
            ->where('is_default', true)
            ->where('view_path', 'like', 'frontend.templates.details.%')
            ->when($contentType, function ($query) use ($contentType) {
                $query->where(function ($query) use ($contentType) {
                    $query->where('key', $contentType . '-detail')
                        ->orWhere('view_path', 'like', 'frontend.templates.details.' . $contentType . '-%');
                });
            })
            ->first();
    }

    private function templateCanRender(Template $template): bool
    {
        return $template->view_path && view()->exists($template->view_path);
    }
}
