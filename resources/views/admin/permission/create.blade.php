<x-layouts.admin :title="'Create Permission'">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">เพิ่ม Permission</h1>
            <p class="mt-1 text-sm text-gray-600">สร้างสิทธิ์ใหม่สำหรับใช้งานในระบบ</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-6">
                @csrf

                @include('admin.permission._form')

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                    >
                        บันทึก
                    </button>

                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>