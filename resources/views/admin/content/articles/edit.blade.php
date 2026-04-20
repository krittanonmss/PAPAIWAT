<x-layouts.admin title="Edit Article" header="Edit Article">
    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Article</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Update article content, publishing settings, SEO, categories, tags, and related articles.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.articles.show', $article) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    View
                </a>

                <a
                    href="{{ route('admin.articles.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.articles.update', $article) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.articles._form', ['article' => $article])

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.articles.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Update Article
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>