<x-layouts.admin title="Category Management" header="Category Management">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Category Management</h1>
                <p class="text-sm text-slate-500">จัดการหมวดหมู่เนื้อหา</p>
            </div>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                + Create Category
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="border-b border-slate-200 p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="search" class="mb-1 block text-sm font-medium text-slate-700">
                            Search
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name or slug"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="type_key" class="mb-1 block text-sm font-medium text-slate-700">
                            Type
                        </label>
                        <select
                            id="type_key"
                            name="type_key"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All Types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @selected(request('type_key') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-slate-700">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All Status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Name
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Parent
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sort
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Featured
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 align-top">
                                    <div class="font-medium text-slate-900">
                                        {{ $category->name }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        {{ $category->slug }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ ucfirst($category->type_key) }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $category->parent?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($category->status === 'active')
                                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $category->sort_order }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($category->is_featured)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                            Yes
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">No</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.categories.destroy', $category->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>