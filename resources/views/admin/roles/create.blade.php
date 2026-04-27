<x-layouts.admin title="สร้างบทบาท">
    <div class="mx-auto max-w-3xl space-y-6 text-white">

        <div>
            <h1 class="text-2xl font-bold text-white">สร้างบทบาท</h1>
            <p class="text-sm text-slate-400">สร้างบทบาทผู้ดูแลระบบใหม่</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-400">ชื่อบทบาท</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-slate-400">รายละเอียด</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90"
                    >
                        บันทึก
                    </button>

                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>