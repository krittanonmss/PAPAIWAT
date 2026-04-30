<x-layouts.admin title="Edit Media" header="Edit Media">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Media Library
                    </div>

                    <h1 class="text-2xl font-bold text-white">แก้ไขไฟล์สื่อ</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        กำลังแก้ไข:
                        <span class="font-medium text-white">
                            {{ $media->title ?: $media->original_filename }}
                        </span>
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.index', ['media_folder_id' => $media->media_folder_id]) }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    กลับไปหน้ารายการ
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="space-y-3">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                    <p class="font-semibold text-rose-100">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form
            action="{{ route('admin.media.update', $media) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

                {{-- Main Form --}}
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">ข้อมูลไฟล์</h2>
                            <p class="mt-1 text-xs text-slate-400">ข้อมูลนี้ใช้แสดงผลและช่วยเรื่อง accessibility</p>
                        </div>

                        <div class="space-y-5 p-6">
                            <div>
                                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    ชื่อแสดงผล
                                </label>
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    value="{{ old('title', $media->title) }}"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                @error('title')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alt_text" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    Alt Text
                                </label>
                                <input
                                    id="alt_text"
                                    type="text"
                                    name="alt_text"
                                    value="{{ old('alt_text', $media->alt_text) }}"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                @error('alt_text')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="caption" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    Caption
                                </label>
                                <input
                                    id="caption"
                                    type="text"
                                    name="caption"
                                    value="{{ old('caption', $media->caption) }}"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                @error('caption')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >{{ old('description', $media->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="media_folder_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    โฟลเดอร์
                                </label>
                                <select
                                    id="media_folder_id"
                                    name="media_folder_id"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="" class="bg-slate-900">ไม่มีโฟลเดอร์</option>
                                    @foreach ($folders as $folder)
                                        <option value="{{ $folder->id }}" class="bg-slate-900" @selected((string) old('media_folder_id', $media->media_folder_id) === (string) $folder->id)>
                                            {{ $folder->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('media_folder_id')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Side Panel --}}
                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">ตัวอย่างไฟล์</h3>

                        <div class="mt-4 space-y-4">
                            <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950">
                                @if ($media->media_type === 'image')
                                    <img
                                        src="{{ asset('storage/' . $media->path) }}"
                                        alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}"
                                        class="h-auto w-full object-cover"
                                    >
                                @else
                                    <div class="flex aspect-[4/3] items-center justify-center">
                                        <span class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm font-semibold text-slate-300">
                                            {{ strtoupper($media->extension ?: 'FILE') }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-500">ชื่อไฟล์เดิม</p>
                                    <p class="mt-1 break-words text-sm text-slate-300">{{ $media->original_filename }}</p>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-500">MIME Type</p>
                                    <p class="mt-1 text-sm text-slate-300">{{ $media->mime_type }}</p>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-500">ขนาดไฟล์</p>
                                    <p class="mt-1 text-sm text-slate-300">{{ number_format($media->file_size / 1024, 1) }} KB</p>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-500">Visibility</p>
                                    <p class="mt-1 text-sm text-slate-300">{{ $media->visibility }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        ตรวจสอบข้อมูลไฟล์ก่อนบันทึกการแก้ไข
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.media.index', ['media_folder_id' => $media->media_folder_id]) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                        >
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>