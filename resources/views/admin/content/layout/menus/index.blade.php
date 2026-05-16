<x-layouts.admin :title="'Menus'">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        จัดการเมนู
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการเมนู</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการกลุ่มเมนู เช่น Header / Footer / Sidebar
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.menus.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างเมนู
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการเมนู</h2>
                    <p class="text-sm text-slate-400">
                        รายการกลุ่มเมนูทั้งหมดในระบบ
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ชื่อ</th>
                            <th class="px-4 py-3 text-left">Slug</th>
                            <th class="px-4 py-3 text-left">Location</th>
                            <th class="px-4 py-3 text-left">Items</th>
                            <th class="px-4 py-3 text-left">เริ่มต้น</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-right">การจัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse($menus as $menu)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($menu->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">
                                                {{ $menu->name }}
                                            </p>

                                            @if($menu->description)
                                                <p class="truncate text-xs text-slate-400">
                                                    {{ $menu->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $menu->slug }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $menu->location_key ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                        {{ $menu->items_count }} รายการ
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if($menu->is_default)
                                        <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">
                                            เริ่มต้น
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @if($menu->status === 'active')
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
                                    <div class="flex justify-end">
                                        <a
                                            href="{{ route('admin.content.menus.show', $menu) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ยังไม่มีเมนู</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        เริ่มสร้างเมนูสำหรับ Header หรือ Footer ได้เลย
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($menus->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
