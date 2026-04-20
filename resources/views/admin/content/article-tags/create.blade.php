<x-layouts.admin title="Create Article Tag" header="Create Article Tag">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Article Tag</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Add a new tag for article classification.
                </p>
            </div>

            <a
                href="{{ route('admin.content.article-tags.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.content.article-tags.store') }}" class="space-y-6">
            @csrf

            @include('admin.content.article-tags._form')

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
                    Create Tag
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>