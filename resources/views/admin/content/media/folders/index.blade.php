<x-layouts.admin title="Media Folder Management" header="Media Folder Management">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-[#0f1424] px-6 py-6 shadow-lg shadow-slate-950/20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.28em] text-blue-300">
                        MEDIA LIBRARY
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการโฟลเดอร์สื่อ</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        จัดการโฟลเดอร์สำหรับ media library แบบลำดับชั้น
                    </p>
                </div>

                <a
                    href="{{ route('admin.media-folders.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างโฟลเดอร์
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <p class="text-xs text-slate-400">โฟลเดอร์ทั้งหมด</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['total']) }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                <p class="text-xs text-emerald-300">เปิดใช้งาน</p>
                <p class="mt-1 text-2xl font-bold text-emerald-300">{{ number_format($stats['active']) }}</p>
            </div>

            <div class="rounded-2xl border border-yellow-400/20 bg-yellow-500/10 p-4">
                <p class="text-xs text-yellow-300">ปิดใช้งาน</p>
                <p class="mt-1 text-2xl font-bold text-yellow-300">{{ number_format($stats['inactive']) }}</p>
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                <p class="text-xs text-blue-300">Root Folders</p>
                <p class="mt-1 text-2xl font-bold text-blue-300">{{ number_format($stats['root']) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.media-folders.index') }}" class="border-b border-white/10 p-5">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-5">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">
                            ค้นหาโฟลเดอร์
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="ชื่อโฟลเดอร์ / slug"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label for="parent_id" class="mb-1.5 block text-xs font-medium text-slate-400">
                            โฟลเดอร์แม่
                        </label>

                        <select
                            id="parent_id"
                            name="parent_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทั้งหมด</option>
                            <option value="root" class="bg-slate-900" @selected($filters['parent_id'] === 'root')>Root Folder</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" class="bg-slate-900" @selected((string) $filters['parent_id'] === (string) $parent->id)>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">
                            สถานะ
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทุกสถานะ</option>
                            <option value="active" class="bg-slate-900" @selected($filters['status'] === 'active')>เปิดใช้งาน</option>
                            <option value="inactive" class="bg-slate-900" @selected($filters['status'] === 'inactive')>ปิดใช้งาน</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            ค้นหา
                        </button>

                        <a
                            href="{{ route('admin.media-folders.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                        >
                            ล้าง
                        </a>
                    </div>
                </div>
            </form>

            <div class="border-b border-white/10 px-5 py-3">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-white">มุมมองโครงสร้างโฟลเดอร์</h2>
                        <p class="text-sm text-slate-400">แสดงโฟลเดอร์แบบลำดับชั้น</p>
                    </div>

                    @if ($filters['search'] || $filters['status'] || $filters['parent_id'])
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                            กำลังใช้ตัวกรองอยู่
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-5">
                @if ($folders->isEmpty())
                    <div class="rounded-2xl border border-dashed border-white/10 bg-white/[0.04] px-5 py-10 text-center">
                        <p class="text-base font-medium text-slate-300">ไม่พบโฟลเดอร์สื่อ</p>
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