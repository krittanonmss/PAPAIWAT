<x-layouts.admin title="จัดการหมวดหมู่" header="Category Management">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Category Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการหมวดหมู่</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการหมวดหมู่เนื้อหา และโครงสร้างลำดับชั้นของระบบ
                    </p>
                </div>

                <a
                    href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างหมวดหมู่
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-5 py-3 text-sm text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="relative z-40 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            @php
                $filterKeys = ['search', 'type_key', 'parent_id', 'status', 'deleted', 'per_page'];
                $activeFilterCount = collect(request()->only($filterKeys))->filter(fn ($value) => filled($value))->count();
                $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
            @endphp

            <form method="GET" action="{{ route('admin.categories.index') }}" class="space-y-4" data-ajax-list-form>
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองหมวดหมู่</h2>
                        <p class="mt-0.5 text-xs text-slate-400">จัดกลุ่มตามคำค้น, โครงสร้างหมวดหมู่ และสถานะข้อมูล</p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาหมวดหมู่</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อ / slug"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ประเภท</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'type_key',
                        'name' => 'type_key',
                        'selected' => request('type_key'),
                        'emptyLabel' => 'ทั้งหมด',
                        'placeholder' => 'เลือกประเภท',
                        'searchPlaceholder' => 'ค้นหาประเภท...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($types)->map(fn ($type) => [
                            'value' => $type,
                            'label' => ucfirst($type),
                            'search' => $type . ' ' . ucfirst($type),
                        ]),
                    ])
                </div>

                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">หมวดหมู่แม่</label>
                    @if (request('parent_id') === 'root')
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'filter_parent_id',
                            'name' => 'parent_id',
                            'selected' => 'root',
                            'emptyLabel' => 'ทั้งหมด',
                            'placeholder' => 'เลือกหมวดหมู่แม่',
                            'searchPlaceholder' => 'ค้นหาหมวดหมู่แม่...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'root', 'label' => 'Root เท่านั้น', 'search' => 'root Root เท่านั้น'],
                            ]),
                        ])
                    @else
                        @include('admin.content.partials._async_select', [
                            'id' => 'filter_parent_id',
                            'name' => 'parent_id',
                            'selected' => request('parent_id'),
                            'selectedOption' => $selectedParent ? [
                                'id' => $selectedParent->id,
                                'label' => $selectedParent->name,
                                'meta' => $selectedParent->type_key.' | Level '.$selectedParent->level.' | #'.$selectedParent->id,
                            ] : null,
                            'searchUrl' => route('admin.lookups.categories'),
                            'placeholder' => 'ค้นหาหมวดหมู่แม่',
                            'searchPlaceholder' => 'ค้นหาชื่อ / slug / ID',
                            'emptyLabel' => 'ทั้งหมด',
                        ])
                    @endif
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'status',
                        'name' => 'status',
                        'selected' => request('status'),
                        'emptyLabel' => 'ทั้งหมด',
                        'placeholder' => 'เลือกสถานะ',
                        'searchPlaceholder' => 'ค้นหาสถานะ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'active', 'label' => 'เปิดใช้งาน', 'search' => 'active เปิดใช้งาน'],
                            ['value' => 'inactive', 'label' => 'ปิดใช้งาน', 'search' => 'inactive ปิดใช้งาน'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">รายการที่ลบ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'deleted',
                        'name' => 'deleted',
                        'selected' => request('deleted'),
                        'emptyLabel' => 'ไม่รวมรายการที่ลบ',
                        'placeholder' => 'เลือกรายการที่ลบ',
                        'searchPlaceholder' => 'ค้นหารายการที่ลบ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'with', 'label' => 'รวมรายการที่ลบ', 'search' => 'with รวมรายการที่ลบ'],
                            ['value' => 'only', 'label' => 'เฉพาะรายการที่ลบ', 'search' => 'only เฉพาะรายการที่ลบ'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">แสดงต่อหน้า</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'per_page',
                        'name' => 'per_page',
                        'selected' => (string) request('per_page', 15),
                        'allowEmpty' => false,
                        'placeholder' => 'เลือกจำนวนต่อหน้า',
                        'searchPlaceholder' => 'ค้นหาจำนวน...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([5, 10, 15, 25, 50])->map(fn ($pageSize) => [
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
                        href="{{ route('admin.categories.index') }}"
                        data-ajax-list-reset
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
                </div>
            </form>
        </div>

        {{-- Bulk Actions --}}
        <div class="relative z-50 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">ย้ายหลายหมวดหมู่พร้อมกัน</h2>
                    <p class="text-sm text-slate-400">เลือกหมวดหมู่จากตาราง แล้วย้ายเข้าไปใต้หมวดหมู่ปลายทางที่สร้างไว้</p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('admin.categories.bulk-move') }}"
                class="grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-slate-950/30 p-3 md:grid-cols-[1fr_auto]"
                data-category-bulk-move-form
            >
                @csrf
                @method('PATCH')

                @include('admin.content.partials._searchable_select', [
                    'id' => 'bulk_move_parent_id',
                    'name' => 'parent_id',
                    'selected' => '',
                    'allowEmpty' => false,
                    'placeholder' => 'เลือกหมวดหมู่ปลายทาง',
                    'searchPlaceholder' => 'พิมพ์ชื่อ / ประเภท / ID เพื่อค้นหา...',
                    'inputClass' => 'w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20',
                    'options' => $parentOptions->map(fn ($parentOption) => [
                        'value' => $parentOption->id,
                        'label' => '[' . ucfirst($parentOption->type_key) . '] ' . str_repeat('— ', $parentOption->level) . $parentOption->name,
                        'meta' => 'Level ' . $parentOption->level . ' | #' . $parentOption->id,
                        'search' => $parentOption->name . ' ' . $parentOption->type_key . ' level ' . $parentOption->level . ' #' . $parentOption->id,
                    ]),
                ])

                <button
                    type="submit"
                    class="whitespace-nowrap rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    ย้ายเข้าหมวดหมู่
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="relative z-0 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur transition-opacity" data-ajax-list-results>
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการหมวดหมู่</h2>
                    <p class="text-sm text-slate-400">
                        จัดการโครงสร้างหมวดหมู่ และการแสดงผล
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="w-20 px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    data-category-select-all
                                    class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                    aria-label="เลือกหมวดหมู่ทั้งหมดในหน้านี้"
                                >
                            </th>
                            <th class="px-4 py-3 text-left">ชื่อ</th>
                            <th class="px-4 py-3 text-left">ประเภท</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่แม่</th>
                            <th class="px-4 py-3 text-left">ชั้น</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">ลำดับ</th>
                            <th class="px-4 py-3 text-left">แนะนำ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($categories as $category)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3 text-center">
                                    @if (! $category->trashed())
                                        <input
                                            type="checkbox"
                                            value="{{ $category->id }}"
                                            data-category-checkbox
                                            class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                            aria-label="เลือกหมวดหมู่ {{ $category->name }}"
                                        >
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-white">
                                            {{ $category->name }}
                                            @if ($category->trashed())
                                                <span class="ml-2 rounded-full border border-red-400/20 bg-red-500/10 px-2 py-0.5 text-[10px] font-medium text-red-300">ลบแล้ว</span>
                                            @endif
                                        </p>
                                        <p class="truncate text-xs text-slate-400">{{ $category->slug }}</p>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ ucfirst($category->type_key) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-300">
                                    {{ $category->parent?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        Level {{ $category->level }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($category->status === 'active')
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
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $category->sort_order }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($category->is_featured)
                                        <span class="inline-flex rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            แนะนำ
                                        </span>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if ($category->trashed())
                                            <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}" onsubmit="return confirm('ยืนยันการกู้คืน?');">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="rounded-xl border border-emerald-400/20 px-3 py-1.5 text-xs font-medium text-emerald-300 transition hover:bg-emerald-500/10"
                                                >
                                                    กู้คืน
                                                </button>
                                            </form>
                                        @else
                                            <a
                                                href="{{ route('admin.categories.edit', $category->id) }}"
                                                class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                            >
                                                แก้ไข
                                            </a>

                                            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('ยืนยันการลบ?');">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                                >
                                                    ลบ
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบข้อมูลหมวดหมู่</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ลองเปลี่ยนเงื่อนไขการค้นหา หรือสร้างหมวดหมู่ใหม่
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        @include('admin.content.partials._ajax_index_loader')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bulkForm = document.querySelector('[data-category-bulk-move-form]');

            const categoryCheckboxes = () => Array.from(document.querySelectorAll('[data-category-checkbox]'));
            const categorySelectAll = () => document.querySelector('[data-category-select-all]');

            document.addEventListener('change', (event) => {
                if (event.target.matches('[data-category-select-all]')) {
                    categoryCheckboxes().forEach((checkbox) => {
                        checkbox.checked = event.target.checked;
                    });
                    return;
                }

                if (event.target.matches('[data-category-checkbox]')) {
                    const checkboxes = categoryCheckboxes();
                    const selectAll = categorySelectAll();

                    if (selectAll) {
                        selectAll.checked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
                    }
                }
            });

            bulkForm?.addEventListener('submit', (event) => {
                bulkForm.querySelectorAll('[data-injected-category-id]').forEach((input) => input.remove());

                const selectedIds = categoryCheckboxes()
                    .filter((checkbox) => checkbox.checked)
                    .map((checkbox) => checkbox.value);

                if (selectedIds.length === 0) {
                    event.preventDefault();
                    alert('กรุณาเลือกหมวดหมู่อย่างน้อย 1 รายการ');
                    return;
                }

                if (! confirm('ยืนยันการย้ายหมวดหมู่ที่เลือกไปยังหมวดหมู่ปลายทางนี้?')) {
                    event.preventDefault();
                    return;
                }

                selectedIds.forEach((id) => {
                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.name = 'category_ids[]';
                    input.value = id;
                    input.setAttribute('data-injected-category-id', 'true');

                    bulkForm.appendChild(input);
                });
            });
        });
    </script>
</x-layouts.admin>
