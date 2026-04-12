<x-layouts.admin title="Edit Category" header="Edit Category">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">แก้ไขหมวดหมู่</h1>
                <p class="mt-1 text-sm text-slate-600">อัปเดตข้อมูลหมวดหมู่ในระบบ</p>
            </div>

            <a
                href="{{ route('admin.categories.index') }}"
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
            action="{{ route('admin.categories.update', $category->id) }}"
            method="POST"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            @csrf
            @method('PUT')

            @include('admin.content.categories._form', [
                'category' => $category,
            ])

            <div class="mt-6 flex items-center justify-between gap-3">
                <a
                    href="{{ route('admin.categories.index') }}"
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