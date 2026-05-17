@php
    $isEdit = isset($menuItem);
    $currentType = old('menu_item_type', $menuItem->menu_item_type ?? ($menu->location_key === 'footer' ? 'heading' : 'route'));
    $currentPage = $pages->first();
    $currentContent = $contents->first();
@endphp

<div
    class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]"
    x-data="{
        type: @js($currentType),
        target: @js(old('target', $menuItem->target ?? '_self')),
        routeName: @js(old('route_name', $menuItem->route_name ?? 'home')),
        externalUrl: @js(old('external_url', $menuItem->external_url ?? '')),
        anchor: @js(old('anchor', $menuItem->anchor ?? '')),
        apply(type, data = {}) {
            this.type = type;
            this.routeName = data.routeName || '';
            this.externalUrl = data.externalUrl || '';
            this.anchor = data.anchor || '';
        }
    }"
>
    <div class="space-y-5">
        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-4 border-b border-white/10 pb-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Menu Item</p>
                    <h2 class="mt-1 text-lg font-semibold text-white">{{ $isEdit ? 'แก้ไขรายการเมนู' : 'เพิ่มรายการเมนู' }}</h2>
                    <p class="mt-1 text-sm text-slate-400">เลือกประเภท แล้วกรอกเฉพาะปลายทางที่ต้องใช้</p>
                </div>
                <div class="inline-flex rounded-2xl border border-white/10 bg-slate-950/50 p-1">
                    <button type="button" @click="apply('route', { routeName: 'home' })" class="rounded-xl px-3 py-2 text-xs font-medium transition" :class="type === 'route' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'">Route</button>
                    <button type="button" @click="apply('page')" class="rounded-xl px-3 py-2 text-xs font-medium transition" :class="type === 'page' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'">Page</button>
                    <button type="button" @click="apply('content')" class="rounded-xl px-3 py-2 text-xs font-medium transition" :class="type === 'content' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'">Content</button>
                </div>
            </div>

            <div class="mt-6 grid gap-5 lg:grid-cols-2">
                <div>
                    <label for="label" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อที่แสดง <span class="text-rose-300">*</span></label>
                    <input id="label" name="label" value="{{ old('label', $menuItem->label ?? '') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="เช่น หน้าแรก, บทความ, ติดต่อเรา">
                    @error('label')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="parent_id" class="mb-1.5 block text-sm font-medium text-slate-300">อยู่ใต้เมนู</label>
                    <select id="parent_id" name="parent_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Root menu</option>
                        @foreach($parentItems as $item)
                            <option value="{{ $item->id }}" @selected((string) old('parent_id', $menuItem->parent_id ?? '') === (string) $item->id)>{{ $item->label }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="mb-2 block text-sm font-medium text-slate-300">ประเภทปลายทาง</label>
                <input type="hidden" name="menu_item_type" x-model="type">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach([
                        'heading' => ['หัวข้อ', 'ไม่เป็นลิงก์ ใช้จัดกลุ่ม'],
                        'route' => ['Route', 'หน้าในระบบ เช่น home'],
                        'page' => ['Page', 'เพจที่สร้างใน Page Builder'],
                        'content' => ['Content', 'บทความหรือวัด'],
                        'external_url' => ['URL', 'ลิงก์ภายนอกหรือ path'],
                        'anchor' => ['Anchor', 'จุดในหน้า เช่น #contact'],
                    ] as $type => [$title, $desc])
                        <button type="button" @click="apply(@js($type), {{ $type === 'route' ? "{ routeName: 'home' }" : '{}' }})" class="rounded-2xl border p-4 text-left transition" :class="type === @js($type) ? 'border-blue-400/50 bg-blue-500/15 text-white' : 'border-white/10 bg-slate-950/40 text-slate-300 hover:border-white/20 hover:bg-white/[0.06]'">
                            <span class="block text-sm font-semibold">{{ $title }}</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>
                @error('menu_item_type')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
            </div>
        </section>

        <section x-show="type !== 'heading'" x-cloak class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-3 border-b border-white/10 pb-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">ปลายทาง</h2>
                    <p class="mt-1 text-sm text-slate-400">ระบบจะ validate ตามประเภทที่เลือก</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="apply('route', { routeName: 'home' })" class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">หน้าแรก</button>
                    <button type="button" @click="apply('external_url', { externalUrl: '/favorites' })" class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">Favorites</button>
                    <button type="button" @click="apply('anchor', { anchor: '#contact' })" class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">#contact</button>
                </div>
            </div>

            <div class="mt-6 space-y-5">
                <div x-show="type === 'route'" x-cloak>
                    <label for="route_name" class="mb-1.5 block text-sm font-medium text-slate-300">Route name</label>
                    <input id="route_name" name="route_name" x-model="routeName" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="home">
                    @error('route_name')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div x-show="type === 'page'" x-cloak>
                    @include('admin.content.partials._async_select', [
                        'id' => 'page_id',
                        'name' => 'page_id',
                        'label' => 'Page',
                        'selected' => old('page_id', $menuItem->page_id ?? ''),
                        'selectedOption' => (old('page_id', $menuItem->page_id ?? null) && $currentPage ? [
                            'id' => $currentPage->id,
                            'label' => $currentPage->title,
                            'meta' => '/' . ltrim($currentPage->slug, '/') . ' | ' . $currentPage->status,
                        ] : null),
                        'searchUrl' => route('admin.content.menu-items.lookups.pages', $menu),
                        'placeholder' => 'ค้นหา page',
                        'emptyLabel' => 'ยังไม่เลือก page',
                    ])
                    @error('page_id')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div x-show="type === 'content'" x-cloak>
                    @include('admin.content.partials._async_select', [
                        'id' => 'content_id',
                        'name' => 'content_id',
                        'label' => 'Content',
                        'selected' => old('content_id', $menuItem->content_id ?? ''),
                        'selectedOption' => (old('content_id', $menuItem->content_id ?? null) && $currentContent ? [
                            'id' => $currentContent->id,
                            'label' => $currentContent->title,
                            'meta' => $currentContent->content_type . ' | ' . $currentContent->slug . ' | ' . $currentContent->status,
                        ] : null),
                        'searchUrl' => route('admin.content.menu-items.lookups.contents', $menu),
                        'placeholder' => 'ค้นหา content',
                        'emptyLabel' => 'ยังไม่เลือก content',
                    ])
                    @error('content_id')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div x-show="type === 'external_url'" x-cloak>
                    <label for="external_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL หรือ path</label>
                    <input id="external_url" name="external_url" x-model="externalUrl" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="https://example.com หรือ /contact">
                    @error('external_url')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div x-show="type === 'anchor'" x-cloak>
                    <label for="anchor" class="mb-1.5 block text-sm font-medium text-slate-300">Anchor</label>
                    <input id="anchor" name="anchor" x-model="anchor" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="#contact">
                    @error('anchor')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                </div>

                <details class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <summary class="cursor-pointer text-sm font-medium text-slate-300">ตัวเลือกขั้นสูง</summary>
                    <div class="mt-4 grid gap-5 lg:grid-cols-2">
                        <div>
                            <label for="route_params" class="mb-1.5 block text-sm font-medium text-slate-300">Route params JSON</label>
                            <input id="route_params" name="route_params" value="{{ old('route_params', isset($menuItem) && $menuItem->route_params ? json_encode($menuItem->route_params, JSON_UNESCAPED_UNICODE) : '') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 font-mono text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder='{"slug":"about"}'>
                            @error('route_params')<p class="mt-1 text-sm text-rose-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="rel" class="mb-1.5 block text-sm font-medium text-slate-300">Rel</label>
                            <input id="rel" name="rel" value="{{ old('rel', $menuItem->rel ?? '') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="nofollow">
                            <p class="mt-1 text-xs text-slate-500">ถ้าเปิดแท็บใหม่ ระบบเติม noopener noreferrer ให้เอง</p>
                        </div>
                    </div>
                </details>
            </div>
        </section>
    </div>

    <aside class="space-y-5 xl:sticky xl:top-6 xl:self-start">
        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
            <h3 class="text-sm font-semibold text-white">การแสดงผล</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="target" class="mb-1.5 block text-sm font-medium text-slate-300">เปิดลิงก์</label>
                    <select id="target" name="target" x-model="target" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="_self">แท็บเดิม</option>
                        <option value="_blank">แท็บใหม่</option>
                    </select>
                </div>
                <div>
                    <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">ลำดับ</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <input type="checkbox" name="is_enabled" value="1" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500" @checked(old('is_enabled', $menuItem->is_enabled ?? true))>
                    <span>
                        <span class="block text-sm font-medium text-white">เปิดใช้งาน</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">ปิดไว้ได้ถ้ายังไม่พร้อมแสดงบนหน้าเว็บ</span>
                    </span>
                </label>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
            <h3 class="text-sm font-semibold text-white">รายละเอียดเสริม</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="icon" class="mb-1.5 block text-sm font-medium text-slate-300">Icon class</label>
                    <input id="icon" name="icon" value="{{ old('icon', $menuItem->icon ?? '') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="css_class" class="mb-1.5 block text-sm font-medium text-slate-300">CSS class</label>
                    <input id="css_class" name="css_class" value="{{ old('css_class', $menuItem->css_class ?? '') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">หมายเหตุ</label>
                    <textarea id="description" name="description" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">{{ old('description', $menuItem->description ?? '') }}</textarea>
                </div>
            </div>
        </section>
    </aside>
</div>
