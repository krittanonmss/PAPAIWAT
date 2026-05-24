<x-layouts.admin title="จัดการคลังสื่อ" header="จัดการคลังสื่อ">
    <div class="space-y-6 text-white">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="border-b border-white/10 bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950 px-6 py-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-300">คลังสื่อ</p>
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
            <div data-ajax-list-results class="transition-opacity">
                <section
                    class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur"
                    x-data="{
                        selectedMedia: [],
                        pageMediaIds: @js($mediaItems->pluck('id')->map(fn ($id) => (string) $id)->values()),
                        get selectedCount() {
                            return this.selectedMedia.length;
                        },
                        get isPageSelected() {
                            return this.pageMediaIds.length > 0 && this.pageMediaIds.every((id) => this.selectedMedia.includes(id));
                        },
                        togglePage(checked) {
                            this.selectedMedia = checked ? [...this.pageMediaIds] : [];
                        },
                        clearSelection() {
                            this.selectedMedia = [];
                        },
                    }"
                >
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

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="text-sm text-slate-400">
                                แสดง {{ $mediaItems->count() }} จาก {{ number_format($mediaItems->total()) }} ไฟล์
                            </div>

                            @php
                                $gridViewUrl = route('admin.media.index', array_merge(request()->except(['view_mode', 'page']), ['view_mode' => 'grid']));
                                $listViewUrl = route('admin.media.index', array_merge(request()->except(['view_mode', 'page']), ['view_mode' => 'list']));
                            @endphp

                            <div class="inline-flex overflow-hidden rounded-xl border border-white/10 bg-slate-950/40 p-1">
                                <a
                                    href="{{ $gridViewUrl }}"
                                    class="{{ $filters['view_mode'] === 'grid' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-white/[0.06] hover:text-white' }} rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                >
                                    Grid
                                </a>

                                <a
                                    href="{{ $listViewUrl }}"
                                    class="{{ $filters['view_mode'] === 'list' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-white/[0.06] hover:text-white' }} rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                >
                                    List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.media.index') }}" class="border-b border-white/10 p-5" data-ajax-list-form>
                    @php
                        $filterSelectClass = 'w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
                    @endphp

                    @if ($selectedFolderId !== '')
                        <input type="hidden" name="media_folder_id" value="{{ $selectedFolderId }}">
                    @endif
                    <input type="hidden" name="view_mode" value="{{ $filters['view_mode'] }}">

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_160px_160px_140px_auto] xl:items-end">
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
                            @include('admin.content.partials._searchable_select', [
                                'id' => 'media_type',
                                'name' => 'media_type',
                                'selected' => $filters['media_type'],
                                'emptyLabel' => 'ทุกประเภท',
                                'placeholder' => 'เลือกประเภทไฟล์',
                                'searchPlaceholder' => 'ค้นหาประเภทไฟล์...',
                                'inputClass' => $filterSelectClass,
                                'options' => collect($mediaTypes)->map(fn ($type) => [
                                    'value' => $type,
                                    'label' => ucfirst($type),
                                    'search' => $type . ' ' . ucfirst($type),
                                ]),
                            ])
                        </div>

                        <div>
                            <label for="visibility" class="mb-1.5 block text-sm font-medium text-slate-300">
                                การมองเห็น
                            </label>
                            @include('admin.content.partials._searchable_select', [
                                'id' => 'visibility',
                                'name' => 'visibility',
                                'selected' => $filters['visibility'],
                                'emptyLabel' => 'ทั้งหมด',
                                'placeholder' => 'เลือกการมองเห็น',
                                'searchPlaceholder' => 'ค้นหาการมองเห็น...',
                                'inputClass' => $filterSelectClass,
                                'options' => collect([
                                    ['value' => 'public', 'label' => 'สาธารณะ', 'search' => 'public สาธารณะ'],
                                    ['value' => 'private', 'label' => 'ส่วนตัว', 'search' => 'private ส่วนตัว'],
                                ]),
                            ])
                        </div>

                        <div>
                            <label for="per_page" class="mb-1.5 block text-sm font-medium text-slate-300">
                                จำนวนรายการ
                            </label>
                            @include('admin.content.partials._searchable_select', [
                                'id' => 'per_page',
                                'name' => 'per_page',
                                'selected' => (string) $filters['per_page'],
                                'allowEmpty' => false,
                                'placeholder' => 'เลือกจำนวนรายการ',
                                'searchPlaceholder' => 'ค้นหาจำนวน...',
                                'inputClass' => $filterSelectClass,
                                'options' => collect($perPageOptions)->map(fn ($option) => [
                                    'value' => (string) $option,
                                    'label' => $option . ' รายการ',
                                    'search' => $option . ' รายการ',
                                ]),
                            ])
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                ค้นหา
                            </button>

                            <a
                                href="{{ route('admin.media.index', array_filter([
                                    'media_folder_id' => $selectedFolderId !== '' ? $selectedFolderId : null,
                                    'view_mode' => $filters['view_mode'],
                                ], fn ($value) => $value !== null)) }}"
                                data-ajax-list-reset
                                class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/[0.06]"
                            >
                                ล้าง
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Media Items --}}
                <div class="p-5">
                    @if ($mediaItems->count())
                        <div class="mb-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                                <div class="flex flex-wrap items-center gap-3">
                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/[0.08]">
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-white/20 bg-slate-950/60 text-blue-600 focus:ring-2 focus:ring-blue-500/30"
                                            :checked="isPageSelected"
                                            @change="togglePage($event.target.checked)"
                                        >
                                        <span>เลือกทั้งหมดในหน้านี้</span>
                                    </label>

                                    <div class="text-sm text-slate-400">
                                        เลือกแล้ว <span class="font-semibold text-white" x-text="selectedCount"></span> ไฟล์
                                    </div>

                                    <button
                                        type="button"
                                        x-show="selectedCount > 0"
                                        x-cloak
                                        @click="clearSelection()"
                                        class="rounded-xl px-3 py-2 text-sm font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"
                                    >
                                        ล้างที่เลือก
                                    </button>
                                </div>

                                <form
                                    id="media-bulk-folder-form"
                                    method="POST"
                                    action="{{ route('admin.media.bulk-folder') }}"
                                    class="grid gap-3 sm:grid-cols-[minmax(220px,1fr)_auto] xl:min-w-[520px]"
                                    @submit="if (selectedCount === 0) { $event.preventDefault(); alert('กรุณาเลือกไฟล์ก่อน'); }"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label for="bulk_media_folder_id" class="mb-1.5 block text-xs font-medium text-slate-400">
                                            ย้ายไปโฟลเดอร์
                                        </label>
                                        @include('admin.content.partials._async_select', [
                                            'id' => 'bulk_media_folder_id',
                                            'name' => 'media_folder_id',
                                            'selected' => '',
                                            'searchUrl' => route('admin.lookups.media-folders'),
                                            'placeholder' => 'ค้นหาโฟลเดอร์',
                                            'searchPlaceholder' => 'ค้นหาชื่อ / slug / ID',
                                            'emptyLabel' => 'ไม่มีโฟลเดอร์',
                                        ])
                                    </div>

                                    <button
                                        type="submit"
                                        class="mt-auto inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="selectedCount === 0"
                                    >
                                        ย้ายไฟล์ที่เลือก
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($filters['view_mode'] === 'grid')
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4" data-media-view="grid">
                            @foreach ($mediaItems as $media)
                                @php
                                    $thumbnail = $media->variants->firstWhere('variant_name', 'thumbnail');
                                    $previewPath = $thumbnail?->path ?? $media->path;
                                @endphp

                                <article class="group overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:bg-white/[0.06]"
                                    :class="selectedMedia.includes('{{ $media->id }}') ? 'border-blue-400/60 ring-2 ring-blue-500/30' : ''"
                                >
                                    <div class="relative aspect-[4/3] bg-slate-950">
                                        @if ($media->media_type === 'image')
                                            <img
                                                src="{{ $media->visibility === 'private' ? route('admin.media.file', $media) : asset('storage/' . $previewPath) }}"
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

                                        <label class="absolute left-3 top-3 inline-flex cursor-pointer items-center gap-2 rounded-full border border-white/10 bg-slate-950/80 px-3 py-1 text-xs font-medium text-white shadow">
                                            <input
                                                type="checkbox"
                                                name="media_ids[]"
                                                value="{{ $media->id }}"
                                                form="media-bulk-folder-form"
                                                x-model="selectedMedia"
                                                class="h-4 w-4 rounded border-white/20 bg-slate-950/60 text-blue-600 focus:ring-2 focus:ring-blue-500/30"
                                            >
                                            <span>{{ ucfirst($media->media_type) }}</span>
                                        </label>

                                        <div class="absolute left-3 top-12">
                                            <span class="rounded-full border border-white/10 bg-slate-950/70 px-3 py-1 text-xs font-medium text-white shadow">
                                                #{{ $media->id }}
                                            </span>
                                        </div>

                                        <div class="absolute right-3 top-3 flex gap-2">
                                            <a
                                                href="{{ $media->visibility === 'private' ? route('admin.media.file', $media) : asset('storage/' . $media->path) }}"
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
                        @else
                        <div class="overflow-hidden rounded-2xl border border-white/10" data-media-view="list">
                            <div class="hidden grid-cols-[minmax(0,1.7fr)_120px_120px_140px_160px] gap-4 border-b border-white/10 bg-slate-950/50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 lg:grid">
                                <div>ไฟล์</div>
                                <div>ประเภท</div>
                                <div>ขนาด</div>
                                <div>การมองเห็น</div>
                                <div class="text-right">จัดการ</div>
                            </div>

                            <div class="divide-y divide-white/10">
                                @foreach ($mediaItems as $media)
                                    @php
                                        $thumbnail = $media->variants->firstWhere('variant_name', 'thumbnail');
                                        $previewPath = $thumbnail?->path ?? $media->path;
                                    @endphp

                                    <article
                                        class="grid gap-4 bg-slate-950/30 px-4 py-4 transition hover:bg-white/[0.06] lg:grid-cols-[minmax(0,1.7fr)_120px_120px_140px_160px] lg:items-center"
                                        :class="selectedMedia.includes('{{ $media->id }}') ? 'bg-blue-500/10' : ''"
                                    >
                                        <div class="flex min-w-0 items-center gap-3">
                                            <input
                                                type="checkbox"
                                                name="media_ids[]"
                                                value="{{ $media->id }}"
                                                form="media-bulk-folder-form"
                                                x-model="selectedMedia"
                                                class="h-4 w-4 shrink-0 rounded border-white/20 bg-slate-950/60 text-blue-600 focus:ring-2 focus:ring-blue-500/30"
                                                aria-label="เลือก {{ $media->original_filename }}"
                                            >

                                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-white/10 bg-slate-950">
                                                @if ($media->media_type === 'image')
                                                    <img
                                                        src="{{ $media->visibility === 'private' ? route('admin.media.file', $media) : asset('storage/' . $previewPath) }}"
                                                        alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}"
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-400">
                                                        {{ strtoupper($media->extension ?: 'FILE') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <h3 class="truncate text-sm font-semibold text-white">
                                                    {{ $media->title ?: $media->original_filename }}
                                                </h3>

                                                <p class="mt-1 truncate text-xs text-slate-400">
                                                    {{ $media->original_filename }}
                                                </p>

                                                <p class="mt-1 truncate text-xs text-slate-500">
                                                    โฟลเดอร์: {{ $media->folder?->name ?? 'ไม่มีโฟลเดอร์' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-sm text-slate-300">
                                            <span class="lg:hidden text-xs text-slate-500">ประเภท: </span>{{ ucfirst($media->media_type) }}
                                        </div>

                                        <div class="text-sm text-slate-300">
                                            <span class="lg:hidden text-xs text-slate-500">ขนาด: </span>{{ number_format($media->file_size / 1024, 1) }} KB
                                        </div>

                                        <div>
                                            <span class="{{ $media->visibility === 'public' ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-amber-400/20 bg-amber-500/10 text-amber-300' }} inline-flex rounded-full border px-2.5 py-1 text-xs">
                                                {{ $media->visibility }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 lg:justify-end">
                                            <a
                                                href="{{ $media->visibility === 'private' ? route('admin.media.file', $media) : asset('storage/' . $media->path) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium text-slate-300 transition hover:bg-white/[0.06] hover:text-white"
                                            >
                                                ดูไฟล์
                                            </a>

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
                                    </article>
                                @endforeach
                            </div>
                        </div>
                        @endif

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

        @include('admin.content.partials._ajax_index_loader')
    </div>
</x-layouts.admin>
