<x-layouts.admin title="แก้ไขโฟลเดอร์สื่อ" header="แก้ไขโฟลเดอร์สื่อ">
    <div class="space-y-6 text-white">
        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        คลังสื่อ
                    </div>

                    <h1 class="text-2xl font-bold text-white">แก้ไขโฟลเดอร์สื่อ</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        อัปเดตข้อมูลโฟลเดอร์ ลำดับการแสดงผล และสถานะการใช้งาน
                    </p>
                </div>

                <a
                    href="{{ route('admin.media-folders.index') }}"
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
            action="{{ route('admin.media-folders.update', $folder->id) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="space-y-6">
                    @include('admin.content.media.folders._form', [
                        'folder' => $folder,
                    ])
                </div>

                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">ข้อมูลปัจจุบัน</h3>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3.5">
                                <p class="text-xs text-slate-500">ชื่อโฟลเดอร์</p>
                                <p class="mt-1 break-words text-sm font-medium text-slate-200">{{ $folder->name }}</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3.5">
                                <p class="text-xs text-slate-500">Slug</p>
                                <p class="mt-1 break-words text-sm text-slate-300">{{ $folder->slug }}</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3.5">
                                <p class="text-xs text-slate-500">สถานะ</p>
                                <p class="mt-1">
                                    <span class="{{ $folder->status === 'active' ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-amber-400/20 bg-amber-500/10 text-amber-300' }} rounded-full border px-2.5 py-1 text-xs">
                                        {{ $folder->status === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3.5">
                                <p class="text-xs text-slate-500">ลำดับ</p>
                                <p class="mt-1 text-sm text-slate-300">{{ number_format($folder->sort_order) }}</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        ตรวจสอบข้อมูลโฟลเดอร์ก่อนบันทึกการแก้ไข
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.media-folders.index') }}"
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
