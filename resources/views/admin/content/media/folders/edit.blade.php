<x-layouts.admin title="Edit Media Folder" header="Edit Media Folder">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">แก้ไขโฟลเดอร์สื่อ</h1>
                <p class="mt-1 text-sm text-slate-600">อัปเดตข้อมูลโฟลเดอร์ใน media library</p>
            </div>

            <a
                href="{{ route('admin.media-folders.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                กลับไปหน้ารายการ
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง
            </div>
        @endif

        <form
            action="{{ route('admin.media-folders.update', $folder->id) }}"
            method="POST"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            @csrf
            @method('PUT')

            @include('admin.content.media.folders._form', [
                'folder' => $folder,
            ])

            <div class="mt-6 flex items-center justify-between gap-3">
                <a
                    href="{{ route('admin.media-folders.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    ย้อนกลับ
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                >
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>