<x-layouts.admin title="Edit Media Folder" header="Edit Media Folder">
    <div class="space-y-6 text-white">
        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">Media Library</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">แก้ไขโฟลเดอร์สื่อ</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        อัปเดตข้อมูลโฟลเดอร์ ลำดับการแสดงผล และสถานะการใช้งาน
                    </p>
                </div>

                <a
                    href="{{ route('admin.media-folders.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/10"
                >
                    กลับไปหน้ารายการ
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง
            </div>
        @endif

        <form
            action="{{ route('admin.media-folders.update', $folder->id) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            @include('admin.content.media.folders._form', [
                'folder' => $folder,
            ])

            <div class="sticky bottom-0 z-10 -mx-2 border-t border-white/10 bg-slate-950/90 px-2 py-4 backdrop-blur">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a
                        href="{{ route('admin.media-folders.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ย้อนกลับ
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 hover:opacity-90"
                    >
                        บันทึกการแก้ไข
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>