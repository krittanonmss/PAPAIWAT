<x-layouts.admin title="Edit Article Tag" header="Edit Article Tag">
    <div class="mx-auto max-w-3xl space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Article Tag</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Update the selected article tag information.
                </p>
            </div>

            <a
                href="{{ route('admin.content.article-tags.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.content.article-tags.update', $articleTag) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.content.article-tags._form', ['articleTag' => $articleTag])

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.content.article-tags.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Update Tag
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>