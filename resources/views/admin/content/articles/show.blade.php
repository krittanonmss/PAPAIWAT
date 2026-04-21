<x-layouts.admin title="Article Detail" header="Article Detail">
    @php
        $content = $article->content;
        $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
        $coverMedia = $coverUsage?->media;
    @endphp

    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $content?->title ?? 'Article Detail' }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    View complete article details, relationships, SEO data, and statistics.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.content.articles.edit', $article) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </a>

                <a
                    href="{{ route('admin.content.articles.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Content Information</h2>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Title</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $content?->title ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">English Title</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $article->title_en ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Slug</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $content?->slug ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</div>
                            <div class="mt-1 text-sm text-slate-900">{{ ucfirst($content?->status ?? '-') }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Author</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $article->author_name ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Reading Time</div>
                            <div class="mt-1 text-sm text-slate-900">
                                {{ $article->reading_time_minutes ? $article->reading_time_minutes . ' minutes' : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Body Format</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $article->body_format }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Published At</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Scheduled At</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $article->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Expired At</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $article->expired_at?->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Excerpt</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $content?->excerpt ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">English Excerpt</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $article->excerpt_en ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Description</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $content?->description ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Body</div>
                            <div class="mt-1 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm text-slate-900">
                                {{ $article->body ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">SEO</h2>
                    </div>

                    <div class="grid gap-5">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Meta Title</div>
                            <div class="mt-1 text-sm text-slate-900">{{ $content?->meta_title ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Meta Description</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $content?->meta_description ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">SEO Keywords</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $article->seo_keywords ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Display Settings</h2>
                    </div>

                    <div class="space-y-3 text-sm text-slate-700">
                        <div class="flex items-center justify-between gap-4">
                            <span>Allow Comments</span>
                            <span>{{ $article->allow_comments ? 'Yes' : 'No' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <span>Show on Homepage</span>
                            <span>{{ $article->show_on_homepage ? 'Yes' : 'No' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <span>Featured</span>
                            <span>{{ $content?->is_featured ? 'Yes' : 'No' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <span>Popular</span>
                            <span>{{ $content?->is_popular ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Statistics</h2>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Views</div>
                            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $article->stat?->view_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Likes</div>
                            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $article->stat?->like_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Bookmarks</div>
                            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $article->stat?->bookmark_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Shares</div>
                            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $article->stat?->share_count ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Categories</h2>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @forelse ($content?->categories ?? [] as $category)
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $category->name }}
                            </span>
                        @empty
                            <span class="text-sm text-slate-500">No categories assigned.</span>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Tags</h2>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @forelse ($article->tags as $tag)
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-sm text-slate-500">No tags assigned.</span>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Related Articles</h2>
                    </div>

                    <div class="space-y-3">
                        @forelse ($article->relatedArticles as $relatedArticle)
                            <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                {{ $relatedArticle->content?->title ?? 'Untitled Article' }}
                            </div>
                        @empty
                            <span class="text-sm text-slate-500">No related articles assigned.</span>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Cover Media</h2>
                    </div>

                    @if ($coverMedia)
                        <div class="space-y-3">
                            <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-700">
                                <div><span class="font-medium">Title:</span> {{ $coverMedia->title ?: '-' }}</div>
                                <div><span class="font-medium">File:</span> {{ $coverMedia->original_filename }}</div>
                                <div><span class="font-medium">Type:</span> {{ $coverMedia->media_type }}</div>
                                <div><span class="font-medium">Path:</span> {{ $coverMedia->path }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-sm text-slate-500">No cover media assigned.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>