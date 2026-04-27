<x-layouts.admin title="Media Folder Management" header="Media Folder Management">
    <div class="space-y-6 text-white">
        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">Media Library</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">จัดการโฟลเดอร์สื่อ</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        จัดการโฟลเดอร์สำหรับ media library แบบลำดับชั้น
                    </p>
                </div>

                <a
                    href="{{ route('admin.media-folders.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 hover:opacity-90"
                >
                    + สร้างโฟลเดอร์
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">โฟลเดอร์ทั้งหมด</p>
                <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($stats['total']) }}</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">เปิดใช้งาน</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-300">{{ number_format($stats['active']) }}</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">ปิดใช้งาน</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">{{ number_format($stats['inactive']) }}</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">Root Folders</p>
                <p class="mt-2 text-2xl font-semibold text-blue-300">{{ number_format($stats['root']) }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 shadow-xl shadow-slate-950/20">
            <form method="GET" action="{{ route('admin.media-folders.index') }}" class="border-b border-white/10 p-5">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-5">
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ค้นหา
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="ค้นหาชื่อโฟลเดอร์หรือ slug"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label for="parent_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                            โฟลเดอร์แม่
                        </label>

                        <select
                            id="parent_id"
                            name="parent_id"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทั้งหมด</option>
                            <option value="root" @selected($filters['parent_id'] === 'root')>Root Folder</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) $filters['parent_id'] === (string) $parent->id)>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                            สถานะ
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทุกสถานะ</option>
                            <option value="active" @selected($filters['status'] === 'active')>เปิดใช้งาน</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>ปิดใช้งาน</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 lg:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-500"
                        >
                            ค้นหา
                        </button>

                        <a
                            href="{{ route('admin.media-folders.index') }}"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5"
                        >
                            รีเซ็ต
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-b border-white/10 px-5 py-4">
                <div class="flex flex-col gap-2 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                    <p>มุมมองโครงสร้างโฟลเดอร์</p>

                    @if ($filters['search'] || $filters['status'] || $filters['parent_id'])
                        <p class="text-xs text-blue-300">กำลังใช้ตัวกรองอยู่</p>
                    @endif
                </div>
            </div>

            <div class="p-5">
                @if ($folders->isEmpty())
                    <div class="rounded-2xl border border-dashed border-white/15 bg-slate-900/40 px-4 py-10 text-center">
                        <p class="text-sm font-medium text-slate-200">ไม่พบโฟลเดอร์สื่อ</p>
                        <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนคำค้นหา หรือสร้างโฟลเดอร์ใหม่</p>
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