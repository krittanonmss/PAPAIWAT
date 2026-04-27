<x-layouts.admin title="Media Management" header="Media Management">
    <div class="space-y-6 text-white">
        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">Media Library</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">จัดการไฟล์สื่อ</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        จัดการไฟล์ รูปภาพ เอกสาร และข้อมูลประกอบใน media library
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 hover:opacity-90"
                >
                    + อัปโหลดไฟล์
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

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 shadow-xl shadow-slate-950/20">
            <form method="GET" action="{{ route('admin.media.index') }}" class="border-b border-white/10 p-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ค้นหา
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ค้นหาชื่อไฟล์ ชื่อแสดงผล หรือ mime type"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div>
                        <label for="media_type" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ประเภทไฟล์
                        </label>

                        <select
                            id="media_type"
                            name="media_type"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทุกประเภท</option>
                            @foreach ($mediaTypes as $type)
                                <option value="{{ $type }}" @selected(request('media_type') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="visibility" class="mb-1.5 block text-sm font-medium text-slate-300">
                            Visibility
                        </label>

                        <select
                            id="visibility"
                            name="visibility"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทั้งหมด</option>
                            <option value="public" @selected(request('visibility') === 'public')>Public</option>
                            <option value="private" @selected(request('visibility') === 'private')>Private</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="media_folder_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                            โฟลเดอร์
                        </label>

                        <select
                            id="media_folder_id"
                            name="media_folder_id"
                            class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทุกโฟลเดอร์</option>
                            <option value="none" @selected(request('media_folder_id') === 'none')>ไม่มีโฟลเดอร์</option>
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
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-500"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.media.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        รีเซ็ต
                    </a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-slate-900/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Preview
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                File
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Folder
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Size
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Uploaded By
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @forelse ($mediaItems as $media)
                            <tr class="hover:bg-white/[0.03]">
                                <td class="px-4 py-4 align-top">
                                    @if ($media->media_type === 'image')
                                        <img
                                            src="{{ asset('storage/' . $media->path) }}"
                                            alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}"
                                            class="h-16 w-16 rounded-xl border border-white/10 object-cover"
                                        >
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-white/10 bg-slate-900 text-xs font-medium text-slate-400">
                                            {{ strtoupper($media->extension ?: 'FILE') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="font-medium text-white">
                                        {{ $media->title ?: $media->original_filename }}
                                    </div>
                                    <div class="text-sm text-slate-400">
                                        {{ $media->original_filename }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $media->mime_type }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-300">
                                    {{ ucfirst($media->media_type) }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-300">
                                    {{ $media->folder?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-300">
                                    {{ number_format($media->file_size / 1024, 2) }} KB
                                </td>

                                <td class="px-4 py-4 align-top text-sm text-slate-300">
                                    {{ $media->uploader?->username ?? '-' }}
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($media->visibility === 'public')
                                            <a
                                                href="{{ asset('storage/' . $media->path) }}"
                                                target="_blank"
                                                class="inline-flex items-center rounded-xl border border-white/10 px-3 py-1.5 text-sm font-medium text-slate-300 hover:bg-white/5"
                                            >
                                                ดูไฟล์
                                            </a>
                                        @endif

                                        <form
                                            method="POST"
                                            action="{{ route('admin.media.destroy', $media->id) }}"
                                            onsubmit="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-xl border border-red-400/20 px-3 py-1.5 text-sm font-medium text-red-300 hover:bg-red-500/10"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                    ไม่พบไฟล์สื่อ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($mediaItems->hasPages())
                <div class="border-t border-white/10 px-4 py-4">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>