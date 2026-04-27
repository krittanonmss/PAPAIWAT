<x-layouts.admin title="Upload Media" header="Upload Media">
    <div class="space-y-6 text-white">
        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">Media Library</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">อัปโหลดไฟล์สื่อ</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        เพิ่มไฟล์ใหม่เข้าสู่ media library พร้อมข้อมูลสำหรับการแสดงผลและ accessibility
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/10"
                >
                    กลับไปหน้ารายการ
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง
            </div>
        @endif

        <form
            action="{{ route('admin.media.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            @include('admin.content.media.items._form')

            <div class="sticky bottom-0 z-10 -mx-2 border-t border-white/10 bg-slate-950/90 px-2 py-4 backdrop-blur">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('admin.media.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ยกเลิก
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 hover:opacity-90"
                    >
                        อัปโหลดไฟล์
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>