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
              class="space-y-6"
              x-data="{
                  type: @js(old('menu_item_type', $menuItem->menu_item_type)),
                  routeName: @js(old('route_name', $menuItem->route_name ?: 'home')),
                  externalUrl: @js(old('external_url', $menuItem->external_url)),
                  anchor: @js(old('anchor', $menuItem->anchor)),
                  applyPreset(preset) {
                      this.type = preset.type;
                      this.routeName = preset.routeName || '';
                      this.externalUrl = preset.externalUrl || '';
                      this.anchor = preset.anchor || '';

                      this.$nextTick(() => {
                          if (this.$refs.page) this.$refs.page.value = '';
                          if (this.$refs.content) this.$refs.content.value = '';
                          if (this.$refs.routeParams) this.$refs.routeParams.value = '';
                      });
                  }
              }">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur space-y-5">

                {{-- Parent --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">เมนูแม่</label>
                    <select name="parent_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Root Menu</option>
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
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อที่แสดงในเมนู</label>
                    <input name="label"
                        value="{{ old('label', $menuItem->label) }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>

                {{-- Type --}}
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ประเภทปลายทาง</label>
                        <select name="menu_item_type"
                            x-model="type"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            <option value="heading">หัวข้อกลุ่ม ไม่เป็นลิงก์</option>
                            <option value="route">หน้าในระบบ</option>
                            <option value="page">เพจที่สร้างไว้</option>
                            <option value="content">บทความหรือวัด</option>
                            <option value="external_url">ลิงก์หรือ path</option>
                            <option value="anchor">จุดในหน้าเดียวกัน</option>
                        </select>
                    </div>

                    <div x-show="type !== 'heading'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">วิธีเปิดลิงก์</label>
                        <select name="target"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            <option value="_self" @selected(old('target', $menuItem->target) === '_self')>เปิดในแท็บเดิม</option>
                            <option value="_blank" @selected(old('target', $menuItem->target) === '_blank')>เปิดแท็บใหม่</option>
                        </select>
                    </div>
                </div>

                <div x-show="type !== 'heading'" x-cloak class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-white">ปลายทางของเมนู</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                เลือกจากรายการยอดนิยม หรือแก้เฉพาะช่องที่ตรงกับประเภทปลายทาง
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <button
                            type="button"
                            class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-left transition hover:border-blue-400/40 hover:bg-blue-500/10"
                            @click="applyPreset({ type: 'route', routeName: 'home' })"
                        >
                            <span class="block text-sm font-medium text-white">หน้าแรก</span>
                            <span class="mt-1 block text-xs text-slate-500">ลิงก์ไปหน้าแรกของเว็บไซต์</span>
                        </button>
                        <button
                            type="button"
                            class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-left transition hover:border-blue-400/40 hover:bg-blue-500/10"
                            @click="applyPreset({ type: 'external_url', externalUrl: '/temple-list' })"
                        >
                            <span class="block text-sm font-medium text-white">รายการวัด</span>
                            <span class="mt-1 block text-xs text-slate-500">ลิงก์ไป /temple-list</span>
                        </button>
                        <button
                            type="button"
                            class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-left transition hover:border-blue-400/40 hover:bg-blue-500/10"
                            @click="applyPreset({ type: 'external_url', externalUrl: '/article-list' })"
                        >
                            <span class="block text-sm font-medium text-white">รายการบทความ</span>
                            <span class="mt-1 block text-xs text-slate-500">ลิงก์ไป /article-list</span>
                        </button>
                    </div>

                    <div class="mt-5 space-y-5">
                        <div x-show="type === 'route'" x-cloak>
                            <label for="route_name" class="mb-1.5 block text-sm font-medium text-slate-300">เลือกหน้าในระบบ</label>
                            <select
                                id="route_name"
                                name="route_name"
                                x-model="routeName"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="home">หน้าแรก</option>
                                @php($currentRouteName = old('route_name', $menuItem->route_name))
                                @if($currentRouteName && $currentRouteName !== 'home')
                                    <option value="{{ $currentRouteName }}">{{ $currentRouteName }}</option>
                                @endif
                            </select>
                        </div>

                        <div x-show="type === 'page'" x-cloak>
                            <label for="page_id" class="mb-1.5 block text-sm font-medium text-slate-300">เลือกเพจที่สร้างไว้</label>
                            <select
                                id="page_id"
                                name="page_id"
                                x-ref="page"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="">เลือกเพจ</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}" @selected((string) old('page_id', $menuItem->page_id) === (string) $page->id)>
                                        {{ $page->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="type === 'content'" x-cloak>
                            <label for="content_id" class="mb-1.5 block text-sm font-medium text-slate-300">เลือกบทความหรือวัด</label>
                            <select
                                id="content_id"
                                name="content_id"
                                x-ref="content"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="">เลือกบทความหรือวัด</option>
                                @foreach($contents as $content)
                                    <option value="{{ $content->id }}" @selected((string) old('content_id', $menuItem->content_id) === (string) $content->id)>
                                        {{ $content->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="type === 'external_url'" x-cloak>
                            <label for="external_url" class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์หรือ path</label>
                            <input
                                id="external_url"
                                name="external_url"
                                x-model="externalUrl"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="เช่น /contact หรือ https://example.com"
                            >
                            <p class="mt-1 text-xs text-slate-500">ใช้ / นำหน้าสำหรับหน้าในเว็บนี้ หรือใส่ https:// สำหรับเว็บภายนอก</p>
                        </div>

                        <div x-show="type === 'anchor'" x-cloak>
                            <label for="anchor" class="mb-1.5 block text-sm font-medium text-slate-300">จุดในหน้าเดียวกัน</label>
                            <input
                                id="anchor"
                                name="anchor"
                                x-model="anchor"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="เช่น #contact"
                            >
                        </div>

                        <details class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                            <summary class="cursor-pointer text-sm font-medium text-slate-300">ตั้งค่าขั้นสูง</summary>
                            <div class="mt-4">
                                <label for="route_params" class="mb-1.5 block text-sm font-medium text-slate-300">Route Params (JSON)</label>
                                <input
                                    id="route_params"
                                    name="route_params"
                                    x-ref="routeParams"
                                    value="{{ old('route_params', $menuItem->route_params ? json_encode($menuItem->route_params, JSON_UNESCAPED_UNICODE) : '') }}"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 font-mono text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    placeholder='{"slug":"about"}'
                                >
                            </div>
                        </details>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">ลำดับการแสดงผล</label>
                        <input
                            id="sort_order"
                            type="number"
                            min="0"
                            name="sort_order"
                            value="{{ old('sort_order', $menuItem->sort_order) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>
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
