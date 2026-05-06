<x-layouts.admin :title="'Create Menu Item'" header="สร้างเมนูย่อย">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Menu Management
                    </div>

                    <h1 class="text-2xl font-bold text-white">สร้างเมนูย่อย</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        เพิ่มรายการเมนูภายใน:
                        <span class="font-medium text-white">{{ $menu->name }}</span>
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.menus.show', $menu) }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    กลับไปหน้ารายละเอียดเมนู
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="space-y-3">
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

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                    <p class="font-semibold text-rose-100">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form
            method="POST"
            action="{{ route('admin.content.menu-items.store', $menu) }}"
            class="space-y-6"
            x-data="{
                type: @js(old('menu_item_type', 'route')),
                routeName: @js(old('route_name', 'home')),
                externalUrl: @js(old('external_url', '')),
                anchor: @js(old('anchor', '')),
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
            }"
        >
            @csrf

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

                {{-- Main Form --}}
                <div class="space-y-6">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="space-y-6">

                            {{-- Section: Basic --}}
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                                <h3 class="text-sm font-semibold text-white">ข้อมูลเมนูย่อย</h3>

                                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                                    <div>
                                        <label for="label" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            ชื่อที่แสดงในเมนู <span class="text-red-300">*</span>
                                        </label>
                                        <input
                                            id="label"
                                            type="text"
                                            name="label"
                                            value="{{ old('label') }}"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="เช่น หน้าแรก, บทความ, ติดต่อเรา"
                                            required
                                        >
                                        @error('label')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="parent_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            เมนูแม่
                                        </label>
                                        <select
                                            id="parent_id"
                                            name="parent_id"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="" class="bg-slate-900">Root Menu</option>
                                            @foreach($parentItems as $item)
                                                <option value="{{ $item->id }}" class="bg-slate-900" @selected((string) old('parent_id') === (string) $item->id)>
                                                    {{ $item->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-slate-500">
                                            เลือกเมนูแม่ หากต้องการสร้างเป็นเมนูย่อย
                                        </p>
                                        @error('parent_id')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Link Type --}}
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                                <h3 class="text-sm font-semibold text-white">ประเภทลิงก์</h3>

                                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                                    <div>
                                        <label for="menu_item_type" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            ประเภทเมนู <span class="text-red-300">*</span>
                                        </label>
                                        <select
                                            id="menu_item_type"
                                            name="menu_item_type"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            x-model="type"
                                            required
                                        >
                                            <option value="route" class="bg-slate-900" @selected(old('menu_item_type', 'route') === 'route')>หน้าในระบบ</option>
                                            <option value="page" class="bg-slate-900" @selected(old('menu_item_type') === 'page')>เพจที่สร้างไว้</option>
                                            <option value="content" class="bg-slate-900" @selected(old('menu_item_type') === 'content')>บทความหรือวัด</option>
                                            <option value="external_url" class="bg-slate-900" @selected(old('menu_item_type') === 'external_url')>ลิงก์หรือ path</option>
                                            <option value="anchor" class="bg-slate-900" @selected(old('menu_item_type') === 'anchor')>จุดในหน้าเดียวกัน</option>
                                        </select>
                                        @error('menu_item_type')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="target" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            วิธีเปิดลิงก์
                                        </label>
                                        <select
                                            id="target"
                                            name="target"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="_self" class="bg-slate-900" @selected(old('target', '_self') === '_self')>เปิดในแท็บเดิม</option>
                                            <option value="_blank" class="bg-slate-900" @selected(old('target') === '_blank')>เปิดแท็บใหม่</option>
                                        </select>
                                        @error('target')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Link Target --}}
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-white">ปลายทางของเมนู</h3>
                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            เลือกจากรายการยอดนิยม หรือเลือกประเภทปลายทางด้านบนแล้วกรอกเฉพาะช่องที่แสดง
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
                                        <label for="route_name" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            เลือกหน้าในระบบ
                                        </label>
                                        <select
                                            id="route_name"
                                            name="route_name"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            x-model="routeName"
                                        >
                                            <option value="home" class="bg-slate-900">หน้าแรก</option>
                                            @if(old('route_name') && old('route_name') !== 'home')
                                                <option value="{{ old('route_name') }}" class="bg-slate-900">{{ old('route_name') }}</option>
                                            @endif
                                        </select>
                                        @error('route_name')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div x-show="type === 'page'" x-cloak>
                                        <label for="page_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            เลือกเพจที่สร้างไว้
                                        </label>
                                        <select
                                            id="page_id"
                                            name="page_id"
                                            x-ref="page"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="" class="bg-slate-900">เลือกเพจ</option>
                                            @foreach($pages as $page)
                                                <option value="{{ $page->id }}" class="bg-slate-900" @selected((string) old('page_id') === (string) $page->id)>
                                                    {{ $page->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('page_id')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div x-show="type === 'content'" x-cloak>
                                        <label for="content_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            เลือกบทความหรือวัด
                                        </label>
                                        <select
                                            id="content_id"
                                            name="content_id"
                                            x-ref="content"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="" class="bg-slate-900">เลือกบทความหรือวัด</option>
                                            @foreach($contents as $content)
                                                <option value="{{ $content->id }}" class="bg-slate-900" @selected((string) old('content_id') === (string) $content->id)>
                                                    {{ $content->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('content_id')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div x-show="type === 'external_url'" x-cloak>
                                        <label for="external_url" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            ลิงก์หรือ path
                                        </label>
                                        <input
                                            id="external_url"
                                            type="text"
                                            name="external_url"
                                            x-model="externalUrl"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="เช่น /contact หรือ https://example.com"
                                        >
                                        <p class="mt-1 text-xs text-slate-500">ใช้ / นำหน้าสำหรับหน้าในเว็บนี้ หรือใส่ https:// สำหรับเว็บภายนอก</p>
                                        @error('external_url')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div x-show="type === 'anchor'" x-cloak>
                                        <label for="anchor" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            จุดในหน้าเดียวกัน
                                        </label>
                                        <input
                                            id="anchor"
                                            type="text"
                                            name="anchor"
                                            x-model="anchor"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="เช่น #contact"
                                        >
                                        @error('anchor')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <details class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                                        <summary class="cursor-pointer text-sm font-medium text-slate-300">ตั้งค่าขั้นสูง</summary>
                                        <div class="mt-4">
                                            <label for="route_params" class="mb-1.5 block text-sm font-medium text-slate-300">
                                                Route Params (JSON)
                                            </label>
                                            <input
                                                id="route_params"
                                                type="text"
                                                name="route_params"
                                                x-ref="routeParams"
                                                value="{{ old('route_params') }}"
                                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 font-mono text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                                placeholder='{"slug":"about"}'
                                            >
                                            @error('route_params')
                                                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </details>

                                    <div>
                                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            ลำดับการแสดงผล
                                        </label>
                                        <input
                                            id="sort_order"
                                            type="number"
                                            name="sort_order"
                                            min="0"
                                            value="{{ old('sort_order', 0) }}"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                        @error('sort_order')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Status --}}
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                                <h3 class="text-sm font-semibold text-white">สถานะ</h3>

                                <div class="mt-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                                    <input
                                        id="is_enabled"
                                        type="checkbox"
                                        name="is_enabled"
                                        value="1"
                                        class="h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                        @checked(old('is_enabled', true))
                                    >
                                    <div>
                                        <label for="is_enabled" class="text-sm font-medium text-slate-200">
                                            เปิดใช้งานเมนูนี้
                                        </label>
                                        <p class="text-xs text-slate-500">
                                            หากปิด เมนูนี้จะไม่ถูกแสดงบนหน้าเว็บไซต์
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Side Panel --}}
                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">เช็กลิสต์ก่อนบันทึก</h3>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">ชื่อเมนู</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    ใช้ชื่อที่สั้น ชัดเจน และตรงกับสิ่งที่ผู้ใช้จะเห็น
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">ประเภทลิงก์</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    เลือกประเภทให้ตรงกับปลายทาง เช่น Route, Page, Content หรือ External URL
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">Target</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    ลิงก์ภายในควรเปิดแท็บเดิม ส่วนลิงก์ภายนอกสามารถเปิดแท็บใหม่ได้
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        ตรวจสอบข้อมูลเมนูย่อยก่อนกดบันทึก
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.content.menus.show', $menu) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                        >
                            บันทึกเมนูย่อย
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
