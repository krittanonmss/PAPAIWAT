<x-layouts.admin :title="$menu->name">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        รายละเอียดเมนู
                    </div>

                    <h1 class="text-2xl font-bold text-white">{{ $menu->name }}</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        รายละเอียดกลุ่มเมนูและรายการเมนูภายใน
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.content.menu-items.create', $menu) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                    >
                        เพิ่มเมนูย่อย
                    </a>

                    <a
                        href="{{ route('admin.content.menus.edit', $menu) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-950/40 transition hover:opacity-90"
                    >
                        แก้ไข
                    </a>

                    <a
                        href="{{ route('admin.content.menus.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปรายการเมนู
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
            </div>
        @endif

        @php
            $statusClass = $menu->status === 'active'
                ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                : 'border-slate-400/20 bg-slate-500/10 text-slate-300';
        @endphp

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 xl:col-span-2">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="flex flex-col gap-4 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-white">รายการเมนู</h2>
                            <p class="mt-1 text-sm text-slate-400">
                                รายการเมนูที่อยู่ภายใต้กลุ่มนี้
                            </p>
                        </div>

                        <a
                            href="{{ route('admin.content.menu-items.create', $menu) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            เพิ่ม
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-950/50 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">ชื่อเมนู</th>
                                    <th class="px-6 py-4 font-semibold">ประเภท</th>
                                    <th class="px-6 py-4 font-semibold">ปลายทาง</th>
                                    <th class="px-6 py-4 font-semibold">ลำดับ</th>
                                    <th class="px-6 py-4 font-semibold">สถานะ</th>
                                    <th class="px-6 py-4 text-right font-semibold">การจัดการ</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/10">
                                @forelse($menu->items as $item)
                                    <tr class="transition hover:bg-white/[0.05]">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white">
                                                {{ $item->label }}
                                            </div>

                                            @if($item->description)
                                                <p class="mt-1 max-w-md text-xs leading-5 text-slate-400">
                                                    {{ $item->description }}
                                                </p>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                                {{ $item->menu_item_type }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="break-all text-sm text-slate-300">
                                                {{ $item->target }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-2.5 text-xs font-semibold text-slate-200">
                                                {{ $item->sort_order }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            @if($item->is_enabled)
                                                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                                    Enabled
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400">
                                                    Disabled
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <a
                                                href="{{ route('admin.content.menu-items.edit', [$menu, $item]) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                                            >
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-14 text-center">
                                            <div class="mx-auto max-w-sm rounded-3xl border border-white/10 bg-slate-950/30 p-6">
                                                <div class="text-sm font-semibold text-white">
                                                    ยังไม่มีรายการเมนู
                                                </div>
                                                <p class="mt-2 text-sm text-slate-400">
                                                    เพิ่มรายการเมนูแรกสำหรับกลุ่มนี้
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">รายละเอียดเมนู</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">ชื่อ</p>
                            <p class="mt-0.5 break-words text-sm font-medium text-slate-200">{{ $menu->name }}</p>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">Slug</p>
                            <div class="mt-1">
                                <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                    {{ $menu->slug }}
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">Location Key</p>
                            <p class="mt-0.5 break-words text-sm text-slate-300">{{ $menu->location_key ?? '-' }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">ลำดับ</span>
                            <span class="text-sm font-medium text-slate-200">{{ $menu->sort_order }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">เริ่มต้น</span>
                            @if($menu->is_default)
                                <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                    เริ่มต้น
                                </span>
                            @else
                                <span class="text-sm text-slate-500">-</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">สถานะ</span>
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($menu->status) }}
                            </span>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">คำอธิบาย</p>
                            <p class="mt-0.5 text-sm leading-6 text-slate-300">
                                {{ $menu->description ?: '-' }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-rose-400/20 bg-rose-500/10 shadow-xl shadow-rose-950/20 backdrop-blur">
                    <div class="border-b border-rose-400/20 px-6 py-4">
                        <h2 class="text-base font-semibold text-rose-200">โซนอันตราย</h2>
                    </div>

                    <div class="p-6">
                        <p class="mb-4 text-sm text-rose-200/80">
                            ลบกลุ่มเมนูนี้ออกจากระบบ
                        </p>

                        <form
                            method="POST"
                            action="{{ route('admin.content.menus.destroy', $menu) }}"
                            onsubmit="return confirm('ยืนยันการลบเมนูนี้?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20"
                            >
                                Delete Menu
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.admin>
