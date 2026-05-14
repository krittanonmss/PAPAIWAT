<?php

namespace App\Http\Requests\Admin\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cover_media_id' => $this->integerOrNull($this->input('cover_media_id')),
        ]);
    }

    private function integerOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) && preg_match('/^\d+$/', (string) $value)
            ? (int) $value
            : null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('status', 'active')
                        ->where('view_path', 'like', 'frontend.templates.details.%')
                        ->where(function ($query) {
                            $query->where('key', 'article-detail')
                                ->orWhere('view_path', 'like', 'frontend.templates.details.article-%');
                        });
                }),
            ],
            'excerpt' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],

            'title_en' => ['nullable', 'string', 'max:255'],
            'excerpt_en' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'body_format' => ['required', 'in:markdown,html,editorjs'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1'],
            'seo_keywords' => ['nullable', 'string'],
            'allow_comments' => ['nullable', 'boolean'],
            'show_on_homepage' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date', 'after_or_equal:scheduled_at'],

            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:article_tags,id'],

            'related_article_ids' => ['nullable', 'array'],
            'related_article_ids.*' => ['integer', 'exists:articles,id'],

            'cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
        ];
    }
}
