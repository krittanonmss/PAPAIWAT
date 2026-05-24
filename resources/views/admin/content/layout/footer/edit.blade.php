@php
    $footerMenus = collect($footerMenus ?? []);
    $activeFooterMenus = $footerMenus->where('status', 'active');
    $activeFooterMenu = $activeFooterMenu ?? $footerMenus->firstWhere('is_default', true) ?? $activeFooterMenus->first();
    $rootItems = collect($rootItems ?? []);
    $childrenByParent = collect($childrenByParent ?? []);
    $standaloneItems = $rootItems
        ->filter(fn ($item) => $childrenByParent->get($item->id, collect())->isEmpty())
        ->values();
    $columnItems = $rootItems
        ->reject(fn ($item) => $childrenByParent->get($item->id, collect())->isEmpty())
        ->values();
    $previewBackground = old('background_style', $settings['background_style']);
    $settingField = 'w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
@endphp

<x-layouts.admin :title="'Footer Manager'" header="Footer Manager">
    <div class="space-y-5 text-white">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Footer Manager</p>
                    <h1 class="text-2xl font-bold text-white">จัดการ Footer</h1>
                    <p class="mt-1 text-sm text-slate-400">จัดคอลัมน์ ลิงก์ ข้อความแบรนด์ และรูปแบบ footer ในหน้าเดียว</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.content.menus.index', ['location_key' => 'footer']) }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                        เมนูทั้งหมด
                    </a>
                    <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-xl border border-blue-400/30 bg-blue-500/10 px-4 py-2.5 text-sm font-medium text-blue-200 transition hover:bg-blue-500/20">
                        ดูหน้าเว็บ
                    </a>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="text-xs text-slate-500">Footer menus</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($footerMenus->count()) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="text-xs text-slate-500">Active</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-200">{{ number_format($activeFooterMenus->count()) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="text-xs text-slate-500">Columns</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($columnItems->count()) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <p class="text-xs text-slate-500">Links</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($activeFooterMenu?->items_count ?? $activeFooterMenu?->items?->count() ?? 0) }}</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
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

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_390px]">
            <div class="space-y-5">
                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="flex flex-col gap-4 border-b border-white/10 pb-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-white">Footer menu</h2>
                            <p class="mt-1 text-sm text-slate-400">เมนูตำแหน่ง footer ที่ใช้สร้างคอลัมน์ลิงก์ด้านล่างเว็บไซต์</p>
                        </div>

                        <form method="POST" action="{{ route('admin.content.footer.menu.store') }}" class="flex flex-col gap-2 sm:flex-row">
                            @csrf
                            <input name="name" class="min-w-56 rounded-xl border border-white/10 bg-slate-950/50 px-3 py-2 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="ชื่อเมนูใหม่">
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">
                                สร้าง Footer Menu
                            </button>
                        </form>
                    </div>

                    @if ($footerMenus->isNotEmpty())
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach ($footerMenus as $menu)
                                <div class="rounded-2xl border {{ $activeFooterMenu?->id === $menu->id ? 'border-blue-400/40 bg-blue-500/10' : 'border-white/10 bg-slate-950/40' }} p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-white">{{ $menu->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $menu->items_count }} items · {{ $menu->root_items_count }} root</p>
                                        </div>
                                        @if ($menu->is_default)
                                            <span class="shrink-0 rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2.5 py-1 text-xs text-indigo-200">Default</span>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @unless ($menu->is_default)
                                            <form method="POST" action="{{ route('admin.content.footer.menu.default', $menu) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                                                    ใช้เมนูนี้
                                                </button>
                                            </form>
                                        @endunless
                                        <a href="{{ route('admin.content.menus.show', $menu) }}" class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                                            แก้ละเอียด
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                            ยังไม่มี Footer Menu ให้สร้างเมนูก่อน แล้วค่อยเพิ่มคอลัมน์และลิงก์
                        </div>
                    @endif
                </section>

                @if ($activeFooterMenu)
                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="flex flex-col gap-3 border-b border-white/10 pb-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-white">โครงสร้าง Footer</h2>
                                <p class="mt-1 text-sm text-slate-400">คอลัมน์คือ root item ประเภทหัวข้อ ส่วนลิงก์คือลูกของคอลัมน์นั้น</p>
                            </div>
                            <a href="{{ route('admin.content.menu-items.create', $activeFooterMenu) }}" class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-sm font-medium text-blue-200 transition hover:bg-blue-500/20">
                                เพิ่มแบบละเอียด
                            </a>
                        </div>

                        <div class="mt-5 grid gap-4 lg:grid-cols-2">
                            <form method="POST" action="{{ route('admin.content.footer.columns.store', $activeFooterMenu) }}" class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                @csrf
                                <h3 class="text-sm font-semibold text-white">เพิ่มคอลัมน์</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-[minmax(0,1fr)_96px]">
                                    <input name="label" class="{{ $settingField }}" placeholder="เช่น เมนูหลัก, บทความ, ติดต่อ">
                                    <input name="sort_order" type="number" min="0" class="{{ $settingField }}" placeholder="ลำดับ">
                                </div>
                                <button type="submit" class="mt-3 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">เพิ่มคอลัมน์</button>
                            </form>

                            <form method="POST" action="{{ route('admin.content.footer.links.store', $activeFooterMenu) }}" class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                @csrf
                                <h3 class="text-sm font-semibold text-white">เพิ่มลิงก์</h3>
                                <div class="mt-3 grid gap-3">
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <input name="label" class="{{ $settingField }}" placeholder="ชื่อที่แสดง">
                                        <select name="parent_id" class="{{ $settingField }}">
                                            <option value="">ลิงก์เดี่ยว</option>
                                            @foreach ($rootItems as $rootItem)
                                                <option value="{{ $rootItem->id }}">{{ $rootItem->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_130px]">
                                        <input name="external_url" class="{{ $settingField }}" placeholder="/about หรือ https://example.com">
                                        <select name="target" class="{{ $settingField }}">
                                            <option value="_self">แท็บเดิม</option>
                                            <option value="_blank">แท็บใหม่</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="mt-3 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">เพิ่มลิงก์</button>
                            </form>
                        </div>

                        <div class="mt-5 grid gap-4 lg:grid-cols-2">
                            @forelse ($columnItems as $column)
                                @php
                                    $children = $childrenByParent->get($column->id, collect())->values();
                                @endphp
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-white">{{ $column->label }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $children->count() }} links · #{{ $column->sort_order }}</p>
                                        </div>
                                        <a href="{{ route('admin.content.menu-items.edit', [$activeFooterMenu, $column]) }}" class="shrink-0 rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">แก้</a>
                                    </div>

                                    <ul class="mt-4 divide-y divide-white/10">
                                        @foreach ($children as $child)
                                            @php
                                                $url = \App\Support\MenuUrl::resolve($child);
                                            @endphp
                                            <li class="flex items-center justify-between gap-3 py-2.5">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm text-slate-200">{{ $child->label }}</p>
                                                    <p class="truncate text-xs text-slate-500">{{ $url }}</p>
                                                </div>
                                                <a href="{{ route('admin.content.menu-items.edit', [$activeFooterMenu, $child]) }}" class="shrink-0 rounded-lg border border-white/10 px-2.5 py-1 text-xs text-slate-400 transition hover:bg-white/5 hover:text-white">แก้</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-white/15 bg-slate-950/30 p-6 text-sm text-slate-400 lg:col-span-2">
                                    ยังไม่มีคอลัมน์ เพิ่มคอลัมน์แรกก่อน แล้วเพิ่มลิงก์เข้าไปในคอลัมน์นั้น
                                </div>
                            @endforelse
                        </div>

                        @if ($standaloneItems->isNotEmpty())
                            <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <h3 class="text-sm font-semibold text-white">ลิงก์เดี่ยว</h3>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($standaloneItems as $item)
                                        <a href="{{ route('admin.content.menu-items.edit', [$activeFooterMenu, $item]) }}" class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs text-slate-300 transition hover:bg-white/10 hover:text-white">
                                            {{ $item->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>
                @endif
            </div>

            <aside class="space-y-5 xl:sticky xl:top-6 xl:self-start">
                <form method="POST" action="{{ route('admin.content.footer.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-sm font-semibold text-white">แบรนด์</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="brand_title" class="mb-1.5 block text-sm text-slate-300">ชื่อแบรนด์</label>
                                <input id="brand_title" name="brand_title" value="{{ old('brand_title', $settings['brand_title']) }}" class="{{ $settingField }}" placeholder="PAPAIWAT">
                            </div>
                            <div>
                                <label for="brand_description" class="mb-1.5 block text-sm text-slate-300">คำอธิบาย</label>
                                <textarea id="brand_description" name="brand_description" rows="3" class="{{ $settingField }}">{{ old('brand_description', $settings['brand_description']) }}</textarea>
                            </div>
                            <div>
                                <label for="footer_note" class="mb-1.5 block text-sm text-slate-300">ข้อความเสริม</label>
                                <textarea id="footer_note" name="footer_note" rows="2" class="{{ $settingField }}">{{ old('footer_note', $settings['footer_note']) }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-sm font-semibold text-white">การแสดงผล</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                            <div>
                                <label for="background_style" class="mb-1.5 block text-sm text-slate-300">พื้นหลัง</label>
                                <select id="background_style" name="background_style" class="{{ $settingField }}">
                                    <option value="glass" @selected(old('background_style', $settings['background_style']) === 'glass')>โปร่งแสง</option>
                                    <option value="solid" @selected(old('background_style', $settings['background_style']) === 'solid')>เข้มทึบ</option>
                                    <option value="minimal" @selected(old('background_style', $settings['background_style']) === 'minimal')>เรียบ</option>
                                </select>
                            </div>
                            <div>
                                <label for="column_count" class="mb-1.5 block text-sm text-slate-300">จำนวนคอลัมน์</label>
                                <select id="column_count" name="column_count" class="{{ $settingField }}">
                                    <option value="3" @selected(old('column_count', $settings['column_count']) === '3')>3 คอลัมน์</option>
                                    <option value="4" @selected(old('column_count', $settings['column_count']) === '4')>4 คอลัมน์</option>
                                    <option value="5" @selected(old('column_count', $settings['column_count']) === '5')>5 คอลัมน์</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-2">
                            @foreach ([
                                'show_brand' => 'แสดงแบรนด์',
                                'show_menu' => 'แสดงเมนู',
                                'show_bottom_bar' => 'แสดงแถบล่าง',
                                'show_border' => 'แสดงเส้นคั่น',
                            ] as $key => $label)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                                    <input type="checkbox" name="{{ $key }}" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500" @checked(old($key, $settings[$key]))>
                                    <span class="text-sm text-slate-200">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-sm font-semibold text-white">Copyright</h2>
                        <input name="copyright_text" value="{{ old('copyright_text', $settings['copyright_text']) }}" class="mt-4 {{ $settingField }}" placeholder="© {year} {brand}. All rights reserved.">
                        <p class="mt-2 text-xs text-slate-500">ใช้ตัวแปรได้: {year}, {brand}</p>
                    </section>

                    <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-950/50 shadow-xl shadow-slate-950/30">
                        <div class="border-b border-white/10 px-5 py-4">
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-blue-300">Preview</p>
                            <h3 class="mt-1 text-sm font-semibold text-white">ภาพรวม footer</h3>
                        </div>
                        <div class="p-5">
                            <div class="@if($previewBackground === 'solid') bg-slate-950 @elseif($previewBackground === 'minimal') bg-transparent @else bg-white/[0.04] @endif rounded-2xl border border-white/10 p-4">
                                <p class="text-sm font-semibold text-white">{{ old('brand_title', $settings['brand_title']) ?: 'PAPAIWAT' }}</p>
                                <p class="mt-2 text-xs leading-5 text-slate-400">{{ old('brand_description', $settings['brand_description']) ?: 'Brand description' }}</p>
                                <div class="mt-4 grid grid-cols-3 gap-3">
                                    @for ($i = 0; $i < 3; $i++)
                                        <div class="space-y-2">
                                            <span class="block h-2 w-14 rounded-full bg-slate-600"></span>
                                            <span class="block h-2 w-20 rounded-full bg-slate-800"></span>
                                            <span class="block h-2 w-16 rounded-full bg-slate-800"></span>
                                        </div>
                                    @endfor
                                </div>
                                <div class="mt-4 border-t border-white/10 pt-3 text-[11px] text-slate-500">
                                    {{ \App\Support\FooterSettings::copyright($settings) }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90">
                        บันทึก Footer Settings
                    </button>
                </form>
            </aside>
        </div>
    </div>
</x-layouts.admin>
