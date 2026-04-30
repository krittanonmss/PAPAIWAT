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
                                            required
                                        >
                                            <option value="route" class="bg-slate-900" @selected(old('menu_item_type', 'route') === 'route')>Route</option>
                                            <option value="page" class="bg-slate-900" @selected(old('menu_item_type') === 'page')>Page</option>
                                            <option value="content" class="bg-slate-900" @selected(old('menu_item_type') === 'content')>Content</option>
                                            <option value="external_url" class="bg-slate-900" @selected(old('menu_item_type') === 'external_url')>External URL</option>
                                            <option value="anchor" class="bg-slate-900" @selected(old('menu_item_type') === 'anchor')>Anchor</option>
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
                                <h3 class="text-sm font-semibold text-white">ปลายทางของเมนู</h3>

                                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                                    <div>
                                        <label for="route_name" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            Route Name
                                        </label>
                                        <input
                                            id="route_name"
                                            type="text"
                                            name="route_name"
                                            value="{{ old('route_name') }}"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="เช่น home, temples.index"
                                        >
                                        @error('route_name')
                                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="page_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            Page
                                        </label>
                                        <select
                                            id="page_id"
                                            name="page_id"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="" class="bg-slate-900">ไม่เลือก Page</option>
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

                                    <div>
                                        <label for="content_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            Content
                                        </label>
                                        <select
                                            id="content_id"
                                            name="content_id"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            <option value="" class="bg-slate-900">ไม่เลือก Content</option>
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

                                    <div>
                                        <label for="external_url" class="mb-1.5 block text-sm font-medium text-slate-300">
                                            External URL / Anchor
                                        </label>
                                        <input
                                            id="external_url"
                                            type="text"
                                            name="external_url"
                                            value="{{ old('external_url') }}"
                                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="https://example.com หรือ #section"
                                        >
                                        @error('external_url')
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