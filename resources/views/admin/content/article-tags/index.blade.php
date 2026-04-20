<x-layouts.admin title="Article Tag Management" header="Article Tag Management">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Article Tags</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Manage tag master data for article classification and filtering.
                </p>
            </div>

            <a
                href="{{ route('admin.content.article-tags.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create Tag
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <form method="GET" action="{{ route('admin.content.article-tags.index') }}" class="grid gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Search
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name or slug"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none ring-0 placeholder:text-slate-400 focus:border-slate-400"
                    >
                </div>

                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none ring-0 focus:border-slate-400"
                    >
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="flex flex-wrap items-center gap-3 md:col-span-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.content.article-tags.index') }}"
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
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Name</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Slug</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Sort Order</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-700">Created At</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($articleTags as $articleTag)
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-900">{{ $articleTag->name }}</div>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $articleTag->slug }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="{{ $articleTag->status === 'active'
                                        ? 'inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700'
                                        : 'inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700' }}">
                                        {{ ucfirst($articleTag->status) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $articleTag->sort_order }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $articleTag->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.content.article-tags.edit', $articleTag) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.content.article-tags.destroy', $articleTag) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this article tag?');"
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
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">
                                    No article tags found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articleTags->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $articleTags->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>