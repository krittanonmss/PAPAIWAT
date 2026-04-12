<x-layouts.admin title="Media Management" header="Media Management">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Media Management</h1>
                <p class="text-sm text-slate-500">จัดการไฟล์ใน media library</p>
            </div>

            <a
                href="{{ route('admin.media.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                + Upload Media
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
            <form method="GET" action="{{ route('admin.media.index') }}" class="border-b border-slate-200 p-4">
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
                            placeholder="Search title, filename, or mime type"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="media_type" class="mb-1 block text-sm font-medium text-slate-700">
                            Type
                        </label>
                        <select
                            id="media_type"
                            name="media_type"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All Types</option>
                            @foreach ($mediaTypes as $type)
                                <option value="{{ $type }}" @selected(request('media_type') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="visibility" class="mb-1 block text-sm font-medium text-slate-700">
                            Visibility
                        </label>
                        <select
                            id="visibility"
                            name="visibility"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All</option>
                            <option value="public" @selected(request('visibility') === 'public')>Public</option>
                            <option value="private" @selected(request('visibility') === 'private')>Private</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="media_folder_id" class="mb-1 block text-sm font-medium text-slate-700">
                            Folder
                        </label>
                        <select
                            id="media_folder_id"
                            name="media_folder_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All Folders</option>
                            <option value="none" @selected(request('media_folder_id') === 'none')>No Folder</option>
                            @foreach ($folders as $folder)
                                <option value="{{ $folder->id }}" @selected((string) request('media_folder_id') === (string) $folder->id)>
                                    {{ $folder->name }}
                                </option>
                            @endforeach
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
                        href="{{ route('admin.media.index') }}"
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
                                Preview
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                File
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Folder
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Size
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Uploaded By
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($mediaItems as $media)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 align-top">
                                    @if ($media->media_type === 'image')
                                        <img
                                            src="{{ asset('storage/' . $media->path) }}"
                                            alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}"
                                            class="h-16 w-16 rounded-lg border border-slate-200 object-cover"
                                        >
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-xs text-slate-500">
                                            {{ strtoupper($media->extension ?: 'FILE') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="font-medium text-slate-900">
                                        {{ $media->title ?: $media->original_filename }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        {{ $media->original_filename }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $media->mime_type }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ ucfirst($media->media_type) }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $media->folder?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ number_format($media->file_size / 1024, 2) }} KB
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $media->uploader?->username ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($media->visibility === 'public')
                                            <a
                                                href="{{ asset('storage/' . $media->path) }}"
                                                target="_blank"
                                                class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                            >
                                                View
                                            </a>
                                        @endif

                                        <form
                                            method="POST"
                                            action="{{ route('admin.media.destroy', $media->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this media file?');"
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
                                    No media files found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($mediaItems->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>