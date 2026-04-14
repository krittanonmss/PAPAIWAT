<x-layouts.admin title="Media Folder Management" header="Media Folder Management">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Media Folder Management</h1>
                <p class="mt-1 text-sm text-slate-500">จัดการโฟลเดอร์สำหรับ media library แบบลำดับชั้น</p>
            </div>

            <a
                href="{{ route('admin.media-folders.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
            >
                + Create Media Folder
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Folders</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Active</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['active']) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Inactive</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['inactive']) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Root Folders</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['root']) }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.media-folders.index') }}" class="border-b border-slate-200 p-4 md:p-5">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-5">
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Search
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Search folder name or slug"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label for="parent_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Parent Folder
                        </label>
                        <select
                            id="parent_id"
                            name="parent_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All</option>
                            <option value="root" @selected($filters['parent_id'] === 'root')>Root Folder</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) $filters['parent_id'] === (string) $parent->id)>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option value="">All Status</option>
                            <option value="active" @selected($filters['status'] === 'active')>Active</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 lg:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('admin.media-folders.index') }}"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-b border-slate-200 px-4 py-3 md:px-5">
                <div class="flex flex-col gap-2 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                    <p>Structured folder tree view</p>

                    @if ($filters['search'] || $filters['status'] || $filters['parent_id'])
                        <p class="text-xs text-slate-400">Filter is currently applied</p>
                    @endif
                </div>
            </div>

            <div class="p-4 md:p-5">
                @if ($folders->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center">
                        <p class="text-sm font-medium text-slate-700">No media folders found.</p>
                        <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนคำค้นหา หรือกดสร้างโฟลเดอร์ใหม่</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($folders as $folder)
                            @include('admin.content.media.folders.partials.folder-node', [
                                'folder' => $folder,
                                'level' => 0,
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>