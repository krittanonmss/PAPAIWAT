<x-layouts.admin :title="'Edit Menu Item'">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-4 p-6">
                <div>
                    <p class="text-sm font-medium text-blue-300">Menu Management</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">แก้ไขเมนูย่อย</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ $menuItem->label }} (Menu: {{ $menu->name }})
                    </p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST"
              action="{{ route('admin.content.menu-items.update', [$menu, $menuItem]) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur space-y-5">

                {{-- Parent --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Parent</label>
                    <select name="parent_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Root --</option>
                        @foreach($parentItems as $item)
                            <option value="{{ $item->id }}"
                                @selected($menuItem->parent_id == $item->id)>
                                {{ $item->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Label --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Label</label>
                    <input name="label"
                        value="{{ old('label', $menuItem->label) }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>

                {{-- Type --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Type</label>
                    <select name="menu_item_type"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        @foreach(['route','page','content','external_url','anchor'] as $type)
                            <option value="{{ $type }}"
                                @selected($menuItem->menu_item_type === $type)>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Target --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Target</label>
                    <select name="target"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="_self" @selected($menuItem->target === '_self')>Same Tab</option>
                        <option value="_blank" @selected($menuItem->target === '_blank')>New Tab</option>
                    </select>
                </div>

                {{-- Enabled --}}
                <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                    <input type="checkbox"
                        name="is_enabled"
                        value="1"
                        class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500"
                        {{ $menuItem->is_enabled ? 'checked' : '' }}>
                    <label class="text-sm text-slate-300">เปิดใช้งาน</label>
                </div>

            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    {{-- Delete --}}
                    <button
                        type="button"
                        onclick="if(confirm('ยืนยันการลบ menu item นี้?')) document.getElementById('delete-menu-item-form').submit()"
                        class="inline-flex items-center justify-center rounded-xl border border-rose-400/30 bg-rose-500/10 px-5 py-2.5 text-sm font-medium text-rose-300 hover:bg-rose-500/20"
                    >
                        ลบ
                    </button>

                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('admin.content.menus.show', $menu) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 hover:opacity-90"
                        >
                            บันทึกการแก้ไข
                        </button>
                    </div>

                </div>
            </div>
        </form>

        {{-- Separate delete form (fix nested form bug) --}}
        <form id="delete-menu-item-form"
              method="POST"
              action="{{ route('admin.content.menu-items.destroy', [$menu, $menuItem]) }}"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>
</x-layouts.admin>