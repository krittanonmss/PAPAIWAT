<x-layouts.admin :title="$title" header="จัดการข้อมูลวัด">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-300">Temple Management</p>
                <h1 class="mt-1 text-2xl font-bold text-white">จัดการข้อมูลวัด</h1>
                <p class="mt-1 text-sm text-slate-400">
                    จัดการข้อมูลวัด หมวดหมู่ สถานะการเผยแพร่ และข้อมูลสำหรับแสดงผลหน้าเว็บไซต์
                </p>
            </div>

            <a
                href="{{ route('admin.temples.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
            >
                + เพิ่มข้อมูลวัด
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter Panel --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-5 py-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-base font-semibold text-white">ค้นหาและกรองข้อมูล</h2>
                    <p class="text-sm text-slate-400">
                        ค้นหาจากชื่อวัด slug รายละเอียด สถานะ หรือหมวดหมู่
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.temples.index') }}" class="space-y-5 p-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-medium text-slate-300">
                            ค้นหา
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            placeholder="ชื่อวัด, slug, รายละเอียด"
                        >
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-slate-300">
                            สถานะ
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทุกสถานะ</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-medium text-slate-300">
                            หมวดหมู่
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ทุกหมวดหมู่</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="mb-2 block text-sm font-medium text-slate-300">
                            เรียงลำดับ
                        </label>
                        <select
                            id="sort"
                            name="sort"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">ล่าสุด</option>
                            <option value="popular" @selected(request('sort') === 'popular')>ยอดนิยม</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>เก่าสุด</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500"
                    >
                        กรองข้อมูล
                    </button>

                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        รีเซ็ต
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-white">รายการข้อมูลวัด</h2>
                <p class="text-sm text-slate-400">
                    จำนวน {{ $temples->total() }} รายการ
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-slate-950/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-300">ข้อมูลวัด</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-300">หมวดหมู่หลัก</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-300">สถานะ</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-300">แนะนำ</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-300">เผยแพร่เมื่อ</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-300">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @forelse ($temples as $temple)
                            @php
                                $content = $temple->content;
                                $primaryCategory = $content?->categories?->firstWhere('pivot.is_primary', true);
                            @endphp

                            <tr class="align-top transition hover:bg-white/[0.03]">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-white">
                                        {{ $content?->title ?? '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        slug: {{ $content?->slug ?? '-' }}
                                    </div>

                                    @if ($temple->address?->province)
                                        <div class="mt-2 inline-flex rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-300">
                                            {{ $temple->address->province }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-300">
                                    {{ $primaryCategory?->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $status = $content?->status;
                                    @endphp

                                    @if ($status === 'published')
                                        <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            Published
                                        </span>
                                    @elseif ($status === 'draft')
                                        <span class="inline-flex rounded-full border border-amber-400/20 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-300">
                                            Draft
                                        </span>
                                    @elseif ($status === 'archived')
                                        <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-3 py-1 text-xs font-medium text-slate-300">
                                            Archived
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs font-medium text-slate-300">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($content?->is_featured)
                                        <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                            ใช่
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-500">ไม่ใช่</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-300">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.temples.show', $temple) }}"
                                            class="rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.temples.edit', $temple) }}"
                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-blue-500"
                                        >
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.temples.destroy', $temple) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลวัดนี้?')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-2 text-xs font-medium text-rose-300 transition hover:bg-rose-500/20"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-xl text-slate-400">
                                            ฯ
                                        </div>
                                        <h3 class="mt-4 text-base font-semibold text-white">ยังไม่มีข้อมูลวัด</h3>
                                        <p class="mt-1 text-sm text-slate-400">
                                            เริ่มเพิ่มข้อมูลวัดแรกเพื่อใช้แสดงผลบนหน้าเว็บไซต์
                                        </p>
                                        <a
                                            href="{{ route('admin.temples.create') }}"
                                            class="mt-4 inline-flex rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-500"
                                        >
                                            + เพิ่มข้อมูลวัด
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($temples->hasPages())
                <div class="border-t border-white/10 px-5 py-4 text-slate-300">
                    {{ $temples->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>