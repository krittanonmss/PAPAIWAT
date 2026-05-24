@php
    /** @var \App\Models\Content\Article\Article|null $article */
    $article = $article ?? null;
    $content = $article->content ?? null;
    $adminPreferences = app(\App\Services\Admin\AdminPreferenceService::class)->forAdmin(auth('admin')->user());
    $autosaveDrafts = (bool) ($adminPreferences['editor.autosave_drafts'] ?? true);

    $selectedCategoryIds = old(
        'category_ids',
        isset($article) ? $content?->categories->pluck('id')->all() ?? [] : []
    );

    $selectedTagIds = old(
        'tag_ids',
        isset($article) ? $article->tags->pluck('id')->all() ?? [] : []
    );

    $selectedRelatedArticleIds = old(
        'related_article_ids',
        isset($article) ? $article->relatedArticles->pluck('id')->all() ?? [] : []
    );

    $selectedCoverMediaId = old(
        'cover_media_id',
        isset($article)
            ? optional($content?->mediaUsages?->firstWhere('role_key', 'cover'))->media_id
            : null
    );

    $detailTemplates = $detailTemplates ?? collect();
    $templatePreviewUrl = $templatePreviewUrl ?? null;
    $bodyEditorValue = $article->body ?? '';

    if (($article->body_format ?? 'html') === 'markdown' && $bodyEditorValue !== '') {
        $bodyEditorValue = (string) \Illuminate\Support\Str::markdown($bodyEditorValue);
    }

    $selectedBodyFormat = old(
        'body_format',
        isset($article) && $article?->body_format ? $article->body_format : 'html'
    );
    $rawBodyValue = old('body', $article->body ?? '');
@endphp

@include('admin.content.articles.partials.form._assets')

<input type="hidden" name="content_id" value="{{ $content?->id }}">
<input type="hidden" name="article_id" value="{{ $article?->id }}">

<div class="article-form-ui space-y-6">
    <div class="space-y-6">
        @include('admin.content.articles.partials.form._main_content')
        @include('admin.content.articles.partials.form._seo')
    </div>

    <div class="space-y-6">
        @include('admin.content.articles.partials.form._categories')
        @include('admin.content.articles.partials.form._tags')
        @include('admin.content.articles.partials.form._cover_media')
        @include('admin.content.articles.partials.form._related_articles')
        @include('admin.content.articles.partials.form._publishing')
    </div>
</div>

@if ($autosaveDrafts)
    @include('admin.content.articles.partials.form._draft_script')
@endif
