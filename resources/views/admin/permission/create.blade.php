<x-layouts.admin :title="'สร้างสิทธิ์ (สิทธิ์)'">
    <div class="mx-auto max-w-3xl space-y-6 text-white">

        <div>
            <h1 class="text-2xl font-bold text-white">เพิ่ม สิทธิ์</h1>
            <p class="mt-1 text-sm text-slate-400">สร้างสิทธิ์ใหม่สำหรับใช้งานในระบบ</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-6">
                @csrf

                @include('admin.permission._form')

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90"
                    >
                        บันทึก
                    </button>

                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>