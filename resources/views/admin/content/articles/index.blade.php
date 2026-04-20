<x-layouts.admin title="Article Management" header="Article Management">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Articles</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Manage article content, categories, tags, SEO, and publishing status.
                </p>
            </div>

            <a
                href="{{ route('admin.articles.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create Article
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <form method="GET" action="{{ route('admin.articles.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="md:col-span-2 xl:col-span-2">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Search
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by title, slug, excerpt, or description"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                    >
                </div>

                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All Status</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    </select>
                </div>

                <div>
                    <label for="body_format" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Body Format
                    </label>
                    <select
                        id="body_format"
                        name="body_format"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All Formats</option>
                        <option value="markdown" @selected(request('body_format') === 'markdown')>Markdown</option>
                        <option value="html" @selected(request('body_format') === 'html')>HTML</option>
                        <option value="editorjs" @selected(request('body_format') === 'editorjs')>EditorJS</option>
                    </select>
                </div>

                <div>
                    <label for="author_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Author
                    </label>
                    <input
                        type="text"
                        id="author_name"
                        name="author_name"
                        value="{{ request('author_name') }}"
                        placeholder="Search author"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                    >
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Category
                    </label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tag_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Tag
                    </label>
                    <select
                        id="tag_id"
                        name="tag_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All Tags</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" @selected((string) request('tag_id') === (string) $tag->id)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="allow_comments" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Comments
                    </label>
                    <select
                        id="allow_comments"
                        name="allow_comments"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All</option>
                        <option value="1" @selected(request('allow_comments') === '1')>Allowed</option>
                        <option value="0" @selected(request('allow_comments') === '0')>Disabled</option>
                    </select>
                </div>

                <div>
                    <label for="show_on_homepage" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Homepage
                    </label>
                    <select
                        id="show_on_homepage"
                        name="show_on_homepage"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All</option>
                        <option value="1" @selected(request('show_on_homepage') === '1')>Shown</option>
                        <option value="0" @selected(request('show_on_homepage') === '0')>Hidden</option>
                    </select>
                </div>

                <div>
                    <label for="is_featured" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Featured
                    </label>
                    <select
                        id="is_featured"
                        name="is_featured"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All</option>
                        <option value="1" @selected(request('is_featured') === '1')>Featured</option>
                        <option value="0" @selected(request('is_featured') === '0')>Not Featured</option>
                    </select>
                </div>

                <div>
                    <label for="is_popular" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Popular
                    </label>
                    <select
                        id="is_popular"
                        name="is_popular"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
                    >
                        <option value="">All</option>
                        <option value="1" @selected(request('is_popular') === '1')>Popular</option>
                        <option value="0" @selected(request('is_popular') === '0')>Not Popular</option>
                    </select>
                </div>

                <div class="md:col-span-2 xl:col-span-4 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.articles.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Article</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Author</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Categories</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Tags</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Stats</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Published</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($articles as $article)
                            @php
                                $content = $article->content;
                            @endphp
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <div class="font-medium text-slate-900">
                                            {{ $content?->title ?? '-' }}
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            Slug: {{ $content?->slug ?? '-' }}
                                        </div>

                                        @if ($article->title_en)
                                            <div class="text-xs text-slate-500">
                                                EN: {{ $article->title_en }}
                                            </div>
                                        @endif

                                        <div class="flex flex-wrap gap-2 pt-1">
                                            @if ($content?->is_featured)
                                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                    Featured
                                                </span>
                                            @endif

                                            @if ($content?->is_popular)
                                                <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">
                                                    Popular
                                                </span>
                                            @endif

                                            @if ($article->show_on_homepage)
                                                <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-1 text-xs font-medium text-violet-700">
                                                    Homepage
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="@class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-slate-200 text-slate-700' => $content?->status === 'draft',
                                        'bg-emerald-100 text-emerald-700' => $content?->status === 'published',
                                        'bg-amber-100 text-amber-700' => $content?->status === 'archived',
                                    ])">
                                        {{ ucfirst($content?->status ?? 'unknown') }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div>{{ $article->author_name ?: '-' }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $article->reading_time_minutes ? $article->reading_time_minutes . ' min read' : '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        @forelse ($content?->categories ?? [] as $category)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <span>-</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        @forelse ($article->tags as $tag)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                {{ $tag->name }}
                                            </span>
                                        @empty
                                            <span>-</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <div class="space-y-1 text-xs">
                                        <div>Views: {{ $article->stat?->view_count ?? 0 }}</div>
                                        <div>Likes: {{ $article->stat?->like_count ?? 0 }}</div>
                                        <div>Bookmarks: {{ $article->stat?->bookmark_count ?? 0 }}</div>
                                        <div>Shares: {{ $article->stat?->share_count ?? 0 }}</div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.articles.show', $article) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route('admin.articles.edit', $article) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.articles.destroy', $article) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this article?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-lg border border-rose-300 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">
                                    No articles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>