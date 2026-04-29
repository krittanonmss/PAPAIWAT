<x-layouts.admin title="สร้างบทบาท">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold text-white">สร้างบทบาท</h1>
            <p class="text-sm text-slate-400">สร้างบทบาทผู้ดูแลระบบใหม่</p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 shadow-xl shadow-slate-950/30 backdrop-blur">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-400">
                        ชื่อบทบาท
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เช่น Admin, Editor"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Empty (balance layout) --}}
                <div class="hidden lg:block"></div>

                {{-- Description --}}
                <div class="lg:col-span-2">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-400">
                        รายละเอียด
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        placeholder="อธิบายหน้าที่ของบทบาทนี้"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 lg:col-span-2 pt-2">
                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center rounded-xl border border-white/10 px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ยกเลิก
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-blue-700"
                    >
                        บันทึก
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>