<x-layouts.admin title="จัดการหมวดหมู่" header="Category Management">
    <div class="space-y-6 text-white">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Category Management</h1>
                <p class="text-sm text-slate-400">จัดการหมวดหมู่เนื้อหา</p>
            </div>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90"
            >
                + สร้างหมวดหมู่
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl backdrop-blur">

            <form method="GET" action="{{ route('admin.categories.index') }}" class="border-b border-white/10 p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm text-slate-400">ค้นหา</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ค้นหาชื่อหรือ slug"
                            class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block text-sm text-slate-400">ประเภท</label>
                        <select
                            name="type_key"
                            class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                        >
                            <option value="">ทั้งหมด</option>
                            @foreach ($types as $type)
                                <option class="bg-slate-900" value="{{ $type }}" @selected(request('type_key') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm text-slate-400">สถานะ</label>
                        <select
                            name="status"
                            class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                        >
                            <option value="">ทั้งหมด</option>
                            <option class="bg-slate-900" value="active" @selected(request('status') === 'active')>เปิดใช้งาน</option>
                            <option class="bg-slate-900" value="inactive" @selected(request('status') === 'inactive')>ปิดใช้งาน</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button class="rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm text-white">
                        ค้นหา
                    </button>

                    <a href="{{ route('admin.categories.index') }}"
                       class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300 hover:bg-white/5">
                        ล้าง
                    </a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-white/[0.03] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ชื่อ</th>
                            <th class="px-4 py-3 text-left">ประเภท</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่แม่</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">ลำดับ</th>
                            <th class="px-4 py-3 text-left">แนะนำ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-4">
                                    <div class="text-white font-medium">{{ $category->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $category->slug }}</div>
                                </td>

                                <td class="px-4 py-4">{{ ucfirst($category->type_key) }}</td>

                                <td class="px-4 py-4">{{ $category->parent?->name ?? '-' }}</td>

                                <td class="px-4 py-4">
                                    @if ($category->status === 'active')
                                        <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs text-emerald-300">
                                            เปิดใช้งาน
                                        </span>
                                    @else
                                        <span class="rounded-full bg-white/10 px-2.5 py-1 text-xs text-slate-400">
                                            ปิดใช้งาน
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">{{ $category->sort_order }}</td>

                                <td class="px-4 py-4">
                                    @if ($category->is_featured)
                                        <span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-xs text-amber-300">
                                            แนะนำ
                                        </span>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-slate-300 hover:bg-white/5">
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('ยืนยันการลบ?');">
                                            @csrf
                                            @method('DELETE')

                                            <button class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs text-red-300 hover:bg-red-500/10">
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                    ไม่พบข้อมูล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-white/10 px-4 py-4">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layouts.admin>