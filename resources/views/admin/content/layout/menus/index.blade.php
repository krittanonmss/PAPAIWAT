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

        {{-- Filter --}}
        <div class="relative z-40 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            @php
                $filterKeys = ['search', 'status', 'location_key', 'is_default', 'per_page'];
                $activeFilterCount = collect(request()->only($filterKeys))
                    ->reject(fn ($value, $key) => $key === 'per_page' && (string) $value === '15')
                    ->filter(fn ($value) => filled($value))
                    ->count();
                $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
            @endphp

            <form method="GET" action="{{ route('admin.content.menus.index') }}" class="space-y-4">
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองเมนู</h2>
                        <p class="mt-0.5 text-xs text-slate-400">ค้นหากลุ่มเมนูตามชื่อ slug location และสถานะ</p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-4">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาเมนู</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] ?? request('search') }}"
                            placeholder="ชื่อ / slug / location / รายละเอียด"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'status',
                            'name' => 'status',
                            'selected' => $filters['status'] ?? request('status'),
                            'emptyLabel' => 'ทุกสถานะ',
                            'placeholder' => 'เลือกสถานะ',
                            'searchPlaceholder' => 'ค้นหาสถานะ...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'active', 'label' => 'เปิดใช้งาน', 'search' => 'active เปิดใช้งาน'],
                                ['value' => 'inactive', 'label' => 'ปิดใช้งาน', 'search' => 'inactive ปิดใช้งาน'],
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="location_key" class="mb-1.5 block text-xs font-medium text-slate-400">Location</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'location_key',
                            'name' => 'location_key',
                            'selected' => $filters['location_key'] ?? request('location_key'),
                            'emptyLabel' => 'ทุก location',
                            'placeholder' => 'เลือก location',
                            'searchPlaceholder' => 'ค้นหา location...',
                            'inputClass' => $filterSelectClass,
                            'options' => $locations->map(fn ($location) => [
                                'value' => $location,
                                'label' => $location,
                                'search' => $location,
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="is_default" class="mb-1.5 block text-xs font-medium text-slate-400">เริ่มต้น</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'is_default',
                            'name' => 'is_default',
                            'selected' => $filters['is_default'] ?? request('is_default'),
                            'emptyLabel' => 'ทั้งหมด',
                            'placeholder' => 'เลือกสถานะเริ่มต้น',
                            'searchPlaceholder' => 'ค้นหาสถานะเริ่มต้น...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'yes', 'label' => 'เริ่มต้นเท่านั้น', 'search' => 'yes เริ่มต้น default'],
                                ['value' => 'no', 'label' => 'ไม่ใช่เริ่มต้น', 'search' => 'no ไม่ใช่เริ่มต้น'],
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">แสดงต่อหน้า</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'per_page',
                            'name' => 'per_page',
                            'selected' => (string) ($filters['per_page'] ?? request('per_page', 15)),
                            'allowEmpty' => false,
                            'placeholder' => 'เลือกจำนวนต่อหน้า',
                            'searchPlaceholder' => 'ค้นหาจำนวน...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect(\App\Services\Admin\AdminPreferenceService::PER_PAGE_OPTIONS)->map(fn ($pageSize) => [
                                'value' => (string) $pageSize,
                                'label' => $pageSize . ' รายการ',
                                'search' => $pageSize . ' รายการ',
                            ]),
                        ])
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            ค้นหา
                        </button>

                        <a
                            href="{{ route('admin.content.menus.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                        >
                            ล้าง
                        </a>
                    </div>
                </div>
            </form>
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
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-slate-800 text-slate-500 shadow-lg shadow-slate-950/30">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M5 17h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
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
                                            data-admin-detail-link
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
