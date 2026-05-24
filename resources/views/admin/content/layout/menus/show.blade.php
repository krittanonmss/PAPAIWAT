@php
    $items = $menu->items;
    $rootItems = $items->whereNull('parent_id')->values();
    $statusClass = $menu->status === 'active'
        ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
        : 'border-slate-400/20 bg-slate-500/10 text-slate-300';
@endphp

<x-layouts.admin :title="$menu->name">
    <div
        class="space-y-6 text-white"
        x-data="{
            structureOpen: {{ $items->isEmpty() ? 'false' : 'true' }},
            parentLabel(value) {
                return value ? 'อยู่ใต้ #' + value : 'Root menu';
            }
        }"
    >
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Menu Builder</p>
                    <h1 class="mt-1 truncate text-2xl font-bold text-white">{{ $menu->name }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-white/10 bg-white/[0.05] px-3 py-1 text-xs font-medium text-slate-300">{{ $menu->location_key ?: 'no location' }}</span>
                        <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $statusClass }}">{{ $menu->status }}</span>
                        @if($menu->is_default)
                            <span class="rounded-full border border-indigo-400/20 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">Default</span>
                        @endif
                        <span class="text-xs text-slate-500">{{ $items->count() }} items</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.content.menu-items.create', $menu) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:bg-blue-500">
                        เพิ่มรายการ
                    </a>
                    <a href="{{ route('admin.content.menus.edit', $menu) }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                        ตั้งค่าเมนู
                    </a>
                    <a href="{{ route('admin.content.menus.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                        กลับ
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200">
                <p class="font-semibold text-rose-100">ไม่สามารถทำรายการได้</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($menuWarnings))
            <section class="rounded-3xl border border-amber-400/20 bg-amber-500/10 p-5 text-amber-100 shadow-xl shadow-amber-950/20">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold">รายการที่ควรตรวจสอบก่อนเผยแพร่</h2>
                        <p class="mt-1 text-sm text-amber-100/75">ระบบยังบันทึกได้ แต่รายการเหล่านี้อาจทำให้ผู้ชมกดเมนูแล้วไม่ไปปลายทางที่ต้องการ</p>
                    </div>
                    <span class="w-fit rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-medium">{{ count($menuWarnings) }} warnings</span>
                </div>
                <ul class="mt-4 grid gap-2 text-sm md:grid-cols-2">
                    @foreach ($menuWarnings as $warning)
                        <li class="rounded-2xl border border-amber-300/20 bg-slate-950/30 px-4 py-3">{{ $warning }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($items->isNotEmpty())
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                <button
                    type="button"
                    @click="structureOpen = !structureOpen"
                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                >
                    <span>
                        <span class="block text-base font-semibold text-white">จัดโครงสร้างเร็ว</span>
                        <span class="mt-1 block text-sm text-slate-400">เปลี่ยนเมนูแม่และลำดับหลายรายการในครั้งเดียว เหมาะสำหรับจัด header/footer หลังเพิ่มรายการครบแล้ว</span>
                    </span>
                    <span class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-300" x-text="structureOpen ? 'ซ่อน' : 'เปิด'"></span>
                </button>

                <form x-show="structureOpen" x-cloak method="POST" action="{{ route('admin.content.menus.structure.update', $menu) }}" class="border-t border-white/10">
                    @csrf
                    @method('PATCH')

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-950/40 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">รายการ</th>
                                    <th class="px-5 py-3 font-semibold">อยู่ใต้เมนู</th>
                                    <th class="px-5 py-3 font-semibold">ลำดับ</th>
                                    <th class="px-5 py-3 font-semibold">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($items as $index => $item)
                                    <tr>
                                        <td class="px-5 py-3">
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <div class="font-medium text-white">#{{ $item->id }} {{ $item->label }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $item->menu_item_type }}</div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <select name="items[{{ $index }}][parent_id]" class="w-full min-w-56 rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                                                <option value="">Root menu</option>
                                                @foreach ($items as $parentOption)
                                                    @continue((int) $parentOption->id === (int) $item->id)
                                                    <option value="{{ $parentOption->id }}" @selected((int) ($item->parent_id ?? 0) === (int) $parentOption->id)>
                                                        {{ str_repeat('— ', max(0, $parentOption->depth ?? 0)) }}{{ $parentOption->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-5 py-3">
                                            <input type="number" min="0" name="items[{{ $index }}][sort_order]" value="{{ $item->sort_order }}" class="w-28 rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                                        </td>
                                        <td class="px-5 py-3">
                                            @if ($item->is_enabled)
                                                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">Enabled</span>
                                            @else
                                                <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400">Hidden</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-white/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-400">รองรับไม่เกิน 3 ชั้น เพื่อให้เมนูอ่านง่ายและแสดงผลบนมือถือได้ดี</p>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:bg-blue-500">
                            บันทึกโครงสร้าง
                        </button>
                    </div>
                </form>
            </section>
        @endif

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                <div class="flex flex-col gap-3 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-white">โครงสร้างเมนู</h2>
                        <p class="mt-1 text-sm text-slate-400">แสดงตาม parent/child และลำดับ sort order</p>
                    </div>
                    <a href="{{ route('admin.content.menu-items.create', $menu) }}" class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-sm font-medium text-blue-200 transition hover:bg-blue-500/20">
                        เพิ่ม root item
                    </a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($rootItems as $item)
                        @include('admin.content.layout.menus._tree_item', [
                            'menu' => $menu,
                            'item' => $item,
                            'childrenByParent' => $childrenByParent,
                            'depth' => 0,
                        ])
                    @empty
                        <div class="rounded-3xl border border-dashed border-white/15 bg-slate-950/30 p-10 text-center">
                            <p class="text-base font-semibold text-white">ยังไม่มีรายการเมนู</p>
                            <p class="mt-2 text-sm text-slate-400">เริ่มจากเพิ่ม root item เช่น หน้าแรก หรือหัวข้อ Footer</p>
                            <a href="{{ route('admin.content.menu-items.create', $menu) }}" class="mt-5 inline-flex rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">เพิ่มรายการแรก</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-5 xl:sticky xl:top-6 xl:self-start">
                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-white">Preview</h2>
                        <span class="rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-400">enabled only</span>
                    </div>

                    <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        @if ($menuPreviewItems->isNotEmpty())
                            <ul class="space-y-2 text-sm">
                                @foreach ($menuPreviewItems as $previewItem)
                                    @include('admin.content.layout.menus._preview_item', ['item' => $previewItem, 'depth' => 0])
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-400">ยังไม่มีรายการที่เปิดใช้งานให้แสดงผล</p>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-sm font-semibold text-white">สรุปเมนู</h2>
                    <div class="mt-4 divide-y divide-white/10 text-sm">
                        <div class="flex justify-between gap-4 py-3">
                            <span class="text-slate-400">Slug</span>
                            <span class="truncate text-slate-200">{{ $menu->slug }}</span>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <span class="text-slate-400">Location</span>
                            <span class="text-slate-200">{{ $menu->location_key ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <span class="text-slate-400">Root items</span>
                            <span class="text-slate-200">{{ $rootItems->count() }}</span>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <span class="text-slate-400">Child items</span>
                            <span class="text-slate-200">{{ $items->count() - $rootItems->count() }}</span>
                        </div>
                    </div>
                    @if($menu->description)
                        <p class="mt-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4 text-sm leading-6 text-slate-300">{{ $menu->description }}</p>
                    @endif
                </section>

	                <section class="rounded-3xl border border-rose-400/20 bg-rose-500/10 p-5 shadow-xl shadow-rose-950/20">
	                    <h2 class="text-sm font-semibold text-rose-100">ลบเมนู</h2>
	                    <p class="mt-2 text-sm leading-6 text-rose-200/80">ลบได้เมื่อไม่ใช่ default และไม่มีรายการ หรือใช้ force อย่างชัดเจน</p>
	                    <form method="POST" action="{{ route('admin.content.menus.destroy', $menu) }}" class="mt-4" onsubmit="return confirm('ยืนยันการลบเมนูนี้?')">
	                        @csrf
	                        @method('DELETE')
	                        <label class="mb-3 flex items-start gap-3 rounded-2xl border border-rose-300/20 bg-slate-950/30 p-3 text-sm text-rose-100">
	                            <input type="checkbox" name="force" value="1" class="mt-1 h-4 w-4 rounded border-rose-300/30 bg-slate-950 text-rose-600 focus:ring-rose-500">
	                            <span>Force delete พร้อมลบรายการทั้งหมดในเมนูนี้</span>
	                        </label>
	                        <button type="submit" class="w-full rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20">Delete Menu</button>
	                    </form>
	                </section>
            </aside>
        </div>
    </div>
</x-layouts.admin>
