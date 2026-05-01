<x-layouts.admin title="Media Management" header="Media Management">
    <div class="space-y-6 text-white">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="border-b border-white/10 bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950 px-6 py-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-300">Media Library</p>
                        <h1 class="mt-1 text-2xl font-bold text-white">จัดการคลังสื่อ</h1>
                        <p class="mt-2 max-w-3xl text-sm text-slate-400">
                            จัดการรูปภาพ เอกสาร และไฟล์ประกอบทั้งหมด โดยเลือกโฟลเดอร์ด้านซ้ายเพื่อกรองไฟล์เฉพาะกลุ่ม
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('admin.media-folders.create') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            + สร้างโฟลเดอร์
                        </a>

                        <a
                            href="{{ route('admin.media.create', ['media_folder_id' => $selectedFolderId ?: null]) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            + อัปโหลดไฟล์
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3 p-4 md:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">ไฟล์ทั้งหมด</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">รูปภาพ</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['images']) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">เอกสาร</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['documents']) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">ไม่มีโฟลเดอร์</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['unfoldered']) }}</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
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

        {{-- File Manager --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[300px_minmax(0,1fr)]">
            {{-- Sidebar --}}
            <aside class="h-fit overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                <div class="border-b border-white/10 px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-white">โฟลเดอร์</h2>
                            <p class="mt-1 text-xs text-slate-500">เลือกเพื่อกรองไฟล์</p>
                        </div>

                        <a
                            href="{{ route('admin.media-folders.index') }}"
                            class="rounded-lg border border-blue-400/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                        >
                            จัดการ
                        </a>
                    </div>
                </div>

                <nav class="max-h-[640px] space-y-1 overflow-y-auto p-3">
                    <a
                        href="{{ route('admin.media.index') }}"
                        class="{{ $selectedFolderId === '' ? 'border-blue-400/30 bg-blue-600/20 text-blue-200' : 'border-transparent text-slate-300 hover:bg-white/[0.06] hover:text-white' }} block rounded-xl border px-3 py-2.5 text-sm transition"
                    >
                        คลังสื่อทั้งหมด
                    </a>

                    <a
                        href="{{ route('admin.media.index', ['media_folder_id' => 'none']) }}"
                        class="{{ $selectedFolderId === 'none' ? 'border-blue-400/30 bg-blue-600/20 text-blue-200' : 'border-transparent text-slate-300 hover:bg-white/[0.06] hover:text-white' }} block rounded-xl border px-3 py-2.5 text-sm transition"
                    >
                        ไม่มีโฟลเดอร์
                    </a>

                    <div class="my-3 border-t border-white/10"></div>

                    @forelse ($folders as $folder)
                        @include('admin.content.media.items.partials.folder-node', [
                            'folder' => $folder,
                            'level' => 0,
                            'selectedFolderId' => $selectedFolderId,
                        ])
                    @empty
                        <div class="rounded-xl border border-dashed border-white/10 px-3 py-8 text-center text-sm text-slate-500">
                            ยังไม่มีโฟลเดอร์
                        </div>
                    @endforelse
                </nav>
            </aside>

            {{-- Content --}}
            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                {{-- Current Folder Bar --}}
                <div class="border-b border-white/10 bg-slate-950/30 px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">กำลังดู</p>

                            <h2 class="mt-1 text-lg font-semibold text-white">
                                @if ($selectedFolderId === 'none')
                                    ไม่มีโฟลเดอร์
                                @elseif ($selectedFolder)
                                    {{ $selectedFolder->name }}
                                @else
                                    คลังสื่อทั้งหมด
                                @endif
                            </h2>
                        </div>

                        <div class="text-sm text-slate-400">
                            แสดง {{ $mediaItems->count() }} จาก {{ number_format($mediaItems->total()) }} ไฟล์
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.media.index') }}" class="border-b border-white/10 p-5">
                    @if ($selectedFolderId !== '')
                        <input type="hidden" name="media_folder_id" value="{{ $selectedFolderId }}">
                    @endif

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto] lg:items-end">
                        <div>
                            <label for="search" class="mb-1.5 block text-sm font-medium text-slate-300">
                                ค้นหาไฟล์
                            </label>

                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ $filters['search'] }}"
                                placeholder="ชื่อไฟล์ ชื่อแสดงผล หรือ mime type"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                        </div>

                        <div>
                            <label for="media_type" class="mb-1.5 block text-sm font-medium text-slate-300">
                                ประเภทไฟล์
                            </label>

                            <select
                                id="media_type"
                                name="media_type"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="" class="bg-slate-900">ทุกประเภท</option>
                                @foreach ($mediaTypes as $type)
                                    <option value="{{ $type }}" class="bg-slate-900" @selected($filters['media_type'] === $type)>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="visibility" class="mb-1.5 block text-sm font-medium text-slate-300">
                                การมองเห็น
                            </label>

                            <select
                                id="visibility"
                                name="visibility"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="" class="bg-slate-900">ทั้งหมด</option>
                                <option value="public" class="bg-slate-900" @selected($filters['visibility'] === 'public')>Public</option>
                                <option value="private" class="bg-slate-900" @selected($filters['visibility'] === 'private')>Private</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                ค้นหา
                            </button>

                            <a
                                href="{{ route('admin.media.index', $selectedFolderId !== '' ? ['media_folder_id' => $selectedFolderId] : []) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/[0.06]"
                            >
                                ล้าง
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Media Grid --}}
                <div class="p-5">
                    @if ($mediaItems->count())
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                            @foreach ($mediaItems as $media)
                                @php
                                    $thumbnail = $media->variants->firstWhere('variant_name', 'thumbnail');
                                    $previewPath = $thumbnail?->path ?? $media->path;
                                @endphp

                                <article class="group overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:bg-white/[0.06]">
                                    <div class="relative aspect-[4/3] bg-slate-950">
                                        @if ($media->media_type === 'image')
                                            <img
                                                src="{{ asset('storage/' . $previewPath) }}"
                                                alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}"
                                                class="h-full w-full object-cover"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm font-semibold text-slate-300">
                                                    {{ strtoupper($media->extension ?: 'FILE') }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="absolute left-3 top-3">
                                            <span class="rounded-full border border-white/10 bg-slate-950/70 px-3 py-1 text-xs font-medium text-white shadow">
                                                {{ ucfirst($media->media_type) }}
                                            </span>
                                        </div>

                                        <div class="absolute right-3 top-3 flex gap-2">
                                            @if ($media->visibility === 'public')
                                                <a
                                                    href="{{ asset('storage/' . $media->path) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-950/75 text-slate-200 shadow transition hover:bg-slate-700 hover:text-white"
                                                    title="ดูไฟล์"
                                                    aria-label="ดูไฟล์ {{ $media->original_filename }}"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.5-4.5m0 0H16.5m3 0V8.5M9 14l-4.5 4.5m0 0H7.5m-3 0v-3" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-3 p-4">
                                        <div>
                                            <h3 class="truncate text-sm font-semibold text-white">
                                                {{ $media->title ?: $media->original_filename }}
                                            </h3>

                                            <p class="mt-1 truncate text-xs text-slate-400">
                                                {{ $media->original_filename }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-slate-300">
                                                {{ number_format($media->file_size / 1024, 1) }} KB
                                            </span>

                                            @if ($media->width || $media->height)
                                                <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-slate-300">
                                                    {{ $media->width ?? '-' }} × {{ $media->height ?? '-' }}
                                                </span>
                                            @endif

                                            <span class="{{ $media->visibility === 'public' ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-amber-400/20 bg-amber-500/10 text-amber-300' }} rounded-full border px-2.5 py-1">
                                                {{ $media->visibility }}
                                            </span>
                                        </div>

                                        <div class="truncate text-xs text-slate-400">
                                            โฟลเดอร์:
                                            <span class="text-slate-300">
                                                {{ $media->folder?->name ?? 'ไม่มีโฟลเดอร์' }}
                                            </span>
                                        </div>

                                        @if ($media->variants->isNotEmpty())
                                            <div class="truncate text-xs text-slate-400">
                                                Variants:
                                                <span class="text-slate-300">
                                                    {{ $media->variants->pluck('variant_name')->join(', ') }}
                                                </span>
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-end gap-2 border-t border-white/10 pt-3">
                                            <a
                                                href="{{ route('admin.media.edit', $media) }}"
                                                class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium text-blue-300 transition hover:bg-white/[0.06] hover:text-blue-200"
                                            >
                                                แก้ไข
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.media.destroy', $media) }}"
                                                onsubmit="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่? หากไฟล์ถูกใช้งานอยู่ ระบบจะไม่อนุญาตให้ลบ');"
                                                class="inline-flex"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium text-red-300 transition hover:bg-white/[0.06] hover:text-red-200"
                                                >
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if ($mediaItems->hasPages())
                            <div class="mt-5 border-t border-white/10 pt-4">
                                {{ $mediaItems->links() }}
                            </div>
                        @endif
                    @else
                        <div class="rounded-2xl border border-dashed border-white/10 bg-white/[0.04] px-6 py-16 text-center">
                            <h3 class="text-base font-semibold text-white">ไม่พบไฟล์สื่อ</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                ลองเปลี่ยนโฟลเดอร์ ตัวกรอง หรืออัปโหลดไฟล์ใหม่
                            </p>

                            <a
                                href="{{ route('admin.media.create', ['media_folder_id' => $selectedFolderId ?: null]) }}"
                                class="mt-5 inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                + อัปโหลดไฟล์
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>