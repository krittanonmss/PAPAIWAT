@php
    /** @var \App\Models\Content\Article\Article|null $article */
    $content = $article->content ?? null;

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
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Main Content</h2>
                <p class="text-sm text-slate-500">Primary content fields used by the article content record.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Title <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $content->title ?? '') }}"
                        class="@error('title') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Enter article title"
                    >
                    @error('title')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Slug
                    </label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $content->slug ?? '') }}"
                        class="@error('slug') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Leave blank to auto generate"
                    >
                    @error('slug')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="title_en" class="mb-1.5 block text-sm font-medium text-slate-700">
                        English Title
                    </label>
                    <input
                        type="text"
                        id="title_en"
                        name="title_en"
                        value="{{ old('title_en', $article->title_en ?? '') }}"
                        class="@error('title_en') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Enter English title"
                    >
                    @error('title_en')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Excerpt
                    </label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                        class="@error('excerpt') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Short article summary"
                    >{{ old('excerpt', $content->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt_en" class="mb-1.5 block text-sm font-medium text-slate-700">
                        English Excerpt
                    </label>
                    <textarea
                        id="excerpt_en"
                        name="excerpt_en"
                        rows="3"
                        class="@error('excerpt_en') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Short English summary"
                    >{{ old('excerpt_en', $article->excerpt_en ?? '') }}</textarea>
                    @error('excerpt_en')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="@error('description') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Additional content description"
                    >{{ old('description', $content->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body_format" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Body Format <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="body_format"
                        name="body_format"
                        class="@error('body_format') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="markdown" @selected(old('body_format', $article->body_format ?? 'markdown') === 'markdown')>Markdown</option>
                        <option value="html" @selected(old('body_format', $article->body_format ?? 'markdown') === 'html')>HTML</option>
                        <option value="editorjs" @selected(old('body_format', $article->body_format ?? 'markdown') === 'editorjs')>EditorJS</option>
                    </select>
                    @error('body_format')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Author Name
                    </label>
                    <input
                        type="text"
                        id="author_name"
                        name="author_name"
                        value="{{ old('author_name', $article->author_name ?? '') }}"
                        class="@error('author_name') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                        placeholder="Enter author name"
                    >
                    @error('author_name')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reading_time_minutes" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Reading Time (Minutes)
                    </label>
                    <input
                        type="number"
                        id="reading_time_minutes"
                        name="reading_time_minutes"
                        min="1"
                        value="{{ old('reading_time_minutes', $article->reading_time_minutes ?? '') }}"
                        class="@error('reading_time_minutes') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                    @error('reading_time_minutes')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Published At
                    </label>
                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        value="{{ old('published_at', isset($content?->published_at) ? $content->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('published_at') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                    @error('published_at')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scheduled_at" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Scheduled At
                    </label>
                    <input
                        type="datetime-local"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old('scheduled_at', isset($article?->scheduled_at) ? $article->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('scheduled_at') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                    @error('scheduled_at')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expired_at" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Expired At
                    </label>
                    <input
                        type="datetime-local"
                        id="expired_at"
                        name="expired_at"
                        value="{{ old('expired_at', isset($article?->expired_at) ? $article->expired_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('expired_at') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                    @error('expired_at')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="body" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Body
                    </label>
                    <textarea
                        id="body"
                        name="body"
                        rows="14"
                        class="@error('body') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 font-mono text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                        placeholder="Write article body content"
                    >{{ old('body', $article->body ?? '') }}</textarea>
                    @error('body')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">SEO</h2>
                <p class="text-sm text-slate-500">SEO fields from content and article records.</p>
            </div>

            <div class="grid gap-6">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Meta Title
                    </label>
                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $content->meta_title ?? '') }}"
                        class="@error('meta_title') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                        placeholder="SEO title"
                    >
                    @error('meta_title')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Meta Description
                    </label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="@error('meta_description') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                        placeholder="SEO description"
                    >{{ old('meta_description', $content->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seo_keywords" class="mb-1.5 block text-sm font-medium text-slate-700">
                        SEO Keywords
                    </label>
                    <textarea
                        id="seo_keywords"
                        name="seo_keywords"
                        rows="3"
                        class="@error('seo_keywords') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                        placeholder="Comma-separated keywords"
                    >{{ old('seo_keywords', $article->seo_keywords ?? '') }}</textarea>
                    @error('seo_keywords')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Publishing</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="@error('status') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="draft" @selected(old('status', $content->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $content->status ?? 'draft') === 'published')>Published</option>
                        <option value="archived" @selected(old('status', $content->status ?? 'draft') === 'archived')>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="flex items-start gap-3">
                        <input
                            type="hidden"
                            name="is_featured"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked(old('is_featured', $content->is_featured ?? false))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">Featured</div>
                            <div class="text-xs text-slate-500">Highlight this article in important sections.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3">
                        <input
                            type="hidden"
                            name="is_popular"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="is_popular"
                            value="1"
                            @checked(old('is_popular', $content->is_popular ?? false))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">Popular</div>
                            <div class="text-xs text-slate-500">Mark this article as popular content.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3">
                        <input
                            type="hidden"
                            name="allow_comments"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="allow_comments"
                            value="1"
                            @checked(old('allow_comments', $article->allow_comments ?? true))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">Allow Comments</div>
                            <div class="text-xs text-slate-500">Enable comments for this article.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3">
                        <input
                            type="hidden"
                            name="show_on_homepage"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="show_on_homepage"
                            value="1"
                            @checked(old('show_on_homepage', $article->show_on_homepage ?? false))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">Show on Homepage</div>
                            <div class="text-xs text-slate-500">Display this article in homepage sections.</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Categories</h2>
            </div>

            <div class="space-y-3">
                @forelse ($categories as $category)
                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="category_ids[]"
                            value="{{ $category->id }}"
                            @checked(in_array($category->id, $selectedCategoryIds))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">{{ $category->name }}</div>
                            <div class="text-xs text-slate-500">
                                Type: {{ $category->type_key }} | Sort: {{ $category->sort_order }}
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">No active article categories found.</p>
                @endforelse
            </div>

            @error('category_ids')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            @error('category_ids.*')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Tags</h2>
            </div>

            <div class="space-y-3">
                @forelse ($tags as $tag)
                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="tag_ids[]"
                            value="{{ $tag->id }}"
                            @checked(in_array($tag->id, $selectedTagIds))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">{{ $tag->name }}</div>
                            <div class="text-xs text-slate-500">
                                Slug: {{ $tag->slug }}
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">No active article tags found.</p>
                @endforelse
            </div>

            @error('tag_ids')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            @error('tag_ids.*')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Cover Media</h2>
            </div>

            <div>
                <label for="cover_media_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Cover Image
                </label>
                <select
                    id="cover_media_id"
                    name="cover_media_id"
                    class="@error('cover_media_id') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                >
                    <option value="">No Cover</option>
                    @foreach ($mediaItems as $mediaItem)
                        <option value="{{ $mediaItem->id }}" @selected((string) $selectedCoverMediaId === (string) $mediaItem->id)>
                            #{{ $mediaItem->id }} - {{ $mediaItem->title ?: $mediaItem->original_filename }}
                        </option>
                    @endforeach
                </select>
                @error('cover_media_id')
                    <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Related Articles</h2>
            </div>

            <div class="max-h-80 space-y-3 overflow-y-auto pr-1">
                @forelse ($relatedArticles as $relatedArticle)
                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="related_article_ids[]"
                            value="{{ $relatedArticle->id }}"
                            @checked(in_array($relatedArticle->id, $selectedRelatedArticleIds))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-700">
                                {{ $relatedArticle->content?->title ?? 'Untitled Article' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                #{{ $relatedArticle->id }} | {{ $relatedArticle->content?->slug ?? '-' }}
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">No related articles available.</p>
                @endforelse
            </div>

            @error('related_article_ids')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            @error('related_article_ids.*')
                <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>