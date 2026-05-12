<x-layouts.admin title="จัดการหมวดหมู่" header="Category Management">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Category Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการหมวดหมู่</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการหมวดหมู่เนื้อหา และโครงสร้างลำดับชั้นของระบบ
                    </p>
                </div>

                <a
                    href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างหมวดหมู่
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-5 py-3 text-sm text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาหมวดหมู่</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อ / slug"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ประเภท</label>
                    <select
                        name="type_key"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทั้งหมด</option>
                        @foreach ($types as $type)
                            <option class="bg-slate-900" value="{{ $type }}" @selected(request('type_key') === $type)>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">หมวดหมู่แม่</label>
                    <select
                        name="parent_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทั้งหมด</option>
                        <option value="root" class="bg-slate-900" @selected(request('parent_id') === 'root')>Root เท่านั้น</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" class="bg-slate-900" @selected((string) request('parent_id') === (string) $parent->id)>
                                {{ $parent->name }} ({{ $parent->type_key }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    <select
                        name="status"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทั้งหมด</option>
                        <option class="bg-slate-900" value="active" @selected(request('status') === 'active')>เปิดใช้งาน</option>
                        <option class="bg-slate-900" value="inactive" @selected(request('status') === 'inactive')>ปิดใช้งาน</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-3 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการหมวดหมู่</h2>
                    <p class="text-sm text-slate-400">
                        จัดการโครงสร้างหมวดหมู่ และการแสดงผล
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ชื่อ</th>
                            <th class="px-4 py-3 text-left">ประเภท</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่แม่</th>
                            <th class="px-4 py-3 text-left">ชั้น</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">ลำดับ</th>
                            <th class="px-4 py-3 text-left">แนะนำ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($categories as $category)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">{{ $category->name }}</p>
                                            <p class="truncate text-xs text-slate-400">{{ $category->slug }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ ucfirst($category->type_key) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-300">
                                    {{ $category->parent?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        Level {{ $category->level }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($category->status === 'active')
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
                                        {{ $category->sort_order }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($category->is_featured)
                                        <span class="inline-flex rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            แนะนำ
                                        </span>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('ยืนยันการลบ?');">
                                            @csrf
                                            @method('DELETE')

                                            <button
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
                                <td colspan="8" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบข้อมูลหมวดหมู่</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ลองเปลี่ยนเงื่อนไขการค้นหา หรือสร้างหมวดหมู่ใหม่
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
