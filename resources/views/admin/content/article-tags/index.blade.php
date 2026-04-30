<x-layouts.admin title="จัดการแท็กบทความ" header="จัดการแท็กบทความ">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Article Tag Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการแท็กบทความ</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการแท็กสำหรับจัดกลุ่ม ค้นหา และกรองบทความในระบบ
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.article-tags.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    เพิ่มแท็กใหม่
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.content.article-tags.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาแท็ก</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อแท็ก / slug"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-3">
                    <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทุกสถานะ</option>
                        <option value="active" class="bg-slate-900" @selected(request('status') === 'active')>เปิดใช้งาน</option>
                        <option value="inactive" class="bg-slate-900" @selected(request('status') === 'inactive')>ปิดใช้งาน</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.content.article-tags.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการแท็กบทความ</h2>
                    <p class="text-sm text-slate-400">
                        แสดงชื่อแท็ก slug สถานะ ลำดับ และวันที่สร้าง
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-white/10 bg-slate-950/30 px-3 py-1 text-xs font-medium text-slate-400">
                    ทั้งหมด {{ $articleTags->total() }} รายการ
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ชื่อแท็ก</th>
                            <th class="px-4 py-3 text-left">Slug</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">ลำดับ</th>
                            <th class="px-4 py-3 text-left">สร้างเมื่อ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($articleTags as $articleTag)
                            <tr class="align-top transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">{{ $articleTag->name }}</p>
                                            <p class="truncate text-xs text-slate-400">Article Tag</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $articleTag->slug }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($articleTag->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            เปิดใช้งาน
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            ปิดใช้งาน
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $articleTag->sort_order }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $articleTag->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.content.article-tags.edit', $articleTag) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.content.article-tags.destroy', $articleTag) }}"
                                            onsubmit="return confirm('ยืนยันการลบแท็กบทความนี้หรือไม่?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบแท็กบทความ</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ยังไม่มีแท็กในระบบ หรือไม่มีข้อมูลที่ตรงกับตัวกรอง
                                    </p>

                                    <a
                                        href="{{ route('admin.content.article-tags.create') }}"
                                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                                    >
                                        <span class="text-lg leading-none">+</span>
                                        เพิ่มแท็กใหม่
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articleTags->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $articleTags->links() }}
                </div>
            @endif
        </section>

    </div>
</x-layouts.admin>