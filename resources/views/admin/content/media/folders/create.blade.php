<x-layouts.admin title="Create Media Folder" header="Create Media Folder">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">สร้างโฟลเดอร์สื่อ</h1>
                <p class="mt-1 text-sm text-slate-600">เพิ่มโฟลเดอร์ใหม่สำหรับจัดเก็บไฟล์ใน media library</p>
            </div>

            <a
                href="{{ route('admin.media-folders.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                กลับไปหน้ารายการ
            </a>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง
            </div>
        @endif

        <form
            action="{{ route('admin.media-folders.store') }}"
            method="POST"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            @csrf

            @include('admin.content.media.folders._form')

            <div class="mt-6 flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.media-folders.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    ยกเลิก
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                >
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>