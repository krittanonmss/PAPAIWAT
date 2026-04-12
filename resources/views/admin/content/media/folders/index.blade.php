<x-layouts.admin title="Media Folder Management" header="Media Folder Management">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Media Folder Management</h1>
                <p class="text-sm text-slate-500">จัดการโฟลเดอร์สำหรับ media library</p>
            </div>

            <a
                href="{{ route('admin.media-folders.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                + Create Media Folder
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.media-folders.index') }}" class="border-b border-slate-200 p-4">
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
                            placeholder="Search folder name or slug"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="parent_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Parent
                        </label>
                        <select
                            id="parent_id"
                            name="parent_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All</option>
                            <option value="root" @selected(request('parent_id') === 'root')>Root Folder</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) request('parent_id') === (string) $parent->id)>
                                    {{ $parent->name }}
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
                        href="{{ route('admin.media-folders.index') }}"
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
                                Slug
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
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($folders as $folder)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 align-top">
                                    <div class="font-medium text-slate-900">
                                        {{ $folder->name }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        {{ $folder->description ?: '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $folder->slug }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $folder->parent?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($folder->status === 'active')
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
                                    {{ $folder->sort_order }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.media-folders.edit', $folder->id) }}"
                                            class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.media-folders.destroy', $folder->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this folder?');"
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
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No media folders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($folders->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $folders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>