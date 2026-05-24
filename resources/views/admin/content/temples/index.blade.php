<x-layouts.admin :title="$title" header="จัดการข้อมูล">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Temple Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการข้อมูล</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการข้อมูล หมวดหมู่ สถานะการเผยแพร่ และข้อมูลสำหรับแสดงผลหน้าเว็บไซต์
                    </p>
                </div>

                <a
                    href="{{ route('admin.temples.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    เพิ่มข้อมูล
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="relative z-[90] rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            @php
                $filterKeys = [
                    'search', 'status', 'category_id', 'template_id', 'featured',
                    'temple_type', 'sect', 'province', 'district', 'has_location',
                    'has_media', 'published_from', 'published_to', 'created_from',
                    'created_to', 'sort', 'per_page',
                ];
                $activeFilterCount = collect(request()->only($filterKeys))->filter(fn ($value) => filled($value))->count();
                $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
            @endphp

            <form method="GET" action="{{ route('admin.temples.index') }}" class="space-y-4" data-ajax-list-form>
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองรายการวัด</h2>
                        <p class="mt-0.5 text-xs text-slate-400">
                            ค้นหาครอบคลุมชื่อ, slug, รายละเอียด, ประเภทวัด, นิกาย และที่อยู่
                        </p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-12">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">ค้นหาและจัดกลุ่ม</p>
                </div>

                <div class="lg:col-span-5">
                    <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาข้อมูลหลัก / ที่อยู่</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อ / slug / รายละเอียด / ประเภท / นิกาย / จังหวัด"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'status',
                        'name' => 'status',
                        'selected' => request('status'),
                        'emptyLabel' => 'ทุกสถานะ',
                        'placeholder' => 'เลือกสถานะ',
                        'searchPlaceholder' => 'ค้นหาสถานะ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($statuses)->map(fn ($status) => [
                            'value' => $status,
                            'label' => ucfirst($status),
                            'search' => $status . ' ' . ucfirst($status),
                        ]),
                    ])
                </div>

                <div class="lg:col-span-3">
                    <label for="category_id" class="mb-1.5 block text-xs font-medium text-slate-400">หมวดหมู่</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'category_id',
                        'name' => 'category_id',
                        'selected' => request('category_id'),
                        'emptyLabel' => 'ทุกหมวดหมู่',
                        'placeholder' => 'เลือกหมวดหมู่',
                        'searchPlaceholder' => 'ค้นหาหมวดหมู่...',
                        'inputClass' => $filterSelectClass,
                        'options' => $categories->map(fn ($category) => [
                            'value' => $category->id,
                            'label' => $category->name,
                            'meta' => $category->parent_id ? 'Parent #' . $category->parent_id : '',
                            'search' => $category->name . ' ' . $category->id,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="template_id" class="mb-1.5 block text-xs font-medium text-slate-400">Template</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'template_id',
                        'name' => 'template_id',
                        'selected' => request('template_id'),
                        'emptyLabel' => 'ทุก template',
                        'placeholder' => 'เลือก template',
                        'searchPlaceholder' => 'ค้นหา template...',
                        'inputClass' => $filterSelectClass,
                        'options' => $detailTemplates->map(fn ($template) => [
                            'value' => $template->id,
                            'label' => $template->name,
                            'search' => $template->name . ' ' . $template->id,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-12">
                    <p class="pt-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">รายละเอียดวัดและพื้นที่</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="featured" class="mb-1.5 block text-xs font-medium text-slate-400">แนะนำ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'featured',
                        'name' => 'featured',
                        'selected' => request('featured'),
                        'emptyLabel' => 'ทั้งหมด',
                        'placeholder' => 'เลือกสถานะแนะนำ',
                        'searchPlaceholder' => 'ค้นหาสถานะแนะนำ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'yes', 'label' => 'เฉพาะที่แนะนำ', 'search' => 'yes featured แนะนำ'],
                            ['value' => 'no', 'label' => 'ไม่แนะนำ', 'search' => 'no not featured ไม่แนะนำ'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="temple_type" class="mb-1.5 block text-xs font-medium text-slate-400">ประเภทวัด</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'temple_type',
                        'name' => 'temple_type',
                        'selected' => request('temple_type'),
                        'emptyLabel' => 'ทุกประเภท',
                        'placeholder' => 'เลือกประเภทวัด',
                        'searchPlaceholder' => 'ค้นหาประเภทวัด...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($templeTypes)->map(fn ($templeType) => [
                            'value' => $templeType,
                            'label' => $templeType,
                            'search' => $templeType,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="sect" class="mb-1.5 block text-xs font-medium text-slate-400">นิกาย</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'sect',
                        'name' => 'sect',
                        'selected' => request('sect'),
                        'emptyLabel' => 'ทุกนิกาย',
                        'placeholder' => 'เลือกนิกาย',
                        'searchPlaceholder' => 'ค้นหานิกาย...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($sects)->map(fn ($sect) => [
                            'value' => $sect,
                            'label' => $sect,
                            'search' => $sect,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="province" class="mb-1.5 block text-xs font-medium text-slate-400">จังหวัด</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'province',
                        'name' => 'province',
                        'selected' => request('province'),
                        'emptyLabel' => 'ทุกจังหวัด',
                        'placeholder' => 'เลือกจังหวัด',
                        'searchPlaceholder' => 'ค้นหาจังหวัด...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($provinces)->map(fn ($province) => [
                            'value' => $province,
                            'label' => $province,
                            'search' => $province,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="district" class="mb-1.5 block text-xs font-medium text-slate-400">อำเภอ/เขต</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'district',
                        'name' => 'district',
                        'selected' => request('district'),
                        'emptyLabel' => 'ทุกอำเภอ/เขต',
                        'placeholder' => 'เลือกอำเภอ/เขต',
                        'searchPlaceholder' => 'ค้นหาอำเภอ/เขต...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect($districts)->map(fn ($district) => [
                            'value' => $district,
                            'label' => $district,
                            'search' => $district,
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="has_location" class="mb-1.5 block text-xs font-medium text-slate-400">พิกัดแผนที่</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'has_location',
                        'name' => 'has_location',
                        'selected' => request('has_location'),
                        'emptyLabel' => 'ทั้งหมด',
                        'placeholder' => 'เลือกสถานะพิกัด',
                        'searchPlaceholder' => 'ค้นหาสถานะพิกัด...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'yes', 'label' => 'มีพิกัด', 'search' => 'yes มีพิกัด location'],
                            ['value' => 'no', 'label' => 'ยังไม่มีพิกัด', 'search' => 'no ยังไม่มีพิกัด missing location'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="has_media" class="mb-1.5 block text-xs font-medium text-slate-400">รูปภาพ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'has_media',
                        'name' => 'has_media',
                        'selected' => request('has_media'),
                        'emptyLabel' => 'ทั้งหมด',
                        'placeholder' => 'เลือกสถานะรูปภาพ',
                        'searchPlaceholder' => 'ค้นหาสถานะรูปภาพ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'cover', 'label' => 'มีภาพปก', 'search' => 'cover มีภาพปก'],
                            ['value' => 'missing_cover', 'label' => 'ไม่มีภาพปก', 'search' => 'missing_cover ไม่มีภาพปก'],
                            ['value' => 'gallery', 'label' => 'มีแกลเลอรี', 'search' => 'gallery มีแกลเลอรี'],
                            ['value' => 'missing_gallery', 'label' => 'ไม่มีแกลเลอรี', 'search' => 'missing_gallery ไม่มีแกลเลอรี'],
                            ['value' => 'any', 'label' => 'มีรูปใดก็ได้', 'search' => 'any มีรูปใดก็ได้'],
                            ['value' => 'none', 'label' => 'ไม่มีรูปเลย', 'search' => 'none ไม่มีรูปเลย'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-12">
                    <p class="pt-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">ความพร้อม วันที่ และการแสดงผล</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="published_from" class="mb-1.5 block text-xs font-medium text-slate-400">เผยแพร่ตั้งแต่</label>
                    <input
                        type="date"
                        id="published_from"
                        name="published_from"
                        value="{{ request('published_from') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="published_to" class="mb-1.5 block text-xs font-medium text-slate-400">เผยแพร่ถึง</label>
                    <input
                        type="date"
                        id="published_to"
                        name="published_to"
                        value="{{ request('published_to') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="created_from" class="mb-1.5 block text-xs font-medium text-slate-400">สร้างตั้งแต่</label>
                    <input
                        type="date"
                        id="created_from"
                        name="created_from"
                        value="{{ request('created_from') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="created_to" class="mb-1.5 block text-xs font-medium text-slate-400">สร้างถึง</label>
                    <input
                        type="date"
                        id="created_to"
                        name="created_to"
                        value="{{ request('created_to') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="sort" class="mb-1.5 block text-xs font-medium text-slate-400">เรียงลำดับ</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'sort',
                        'name' => 'sort',
                        'selected' => request('sort'),
                        'emptyLabel' => 'ล่าสุด',
                        'placeholder' => 'เลือกการเรียงลำดับ',
                        'searchPlaceholder' => 'ค้นหาการเรียงลำดับ...',
                        'inputClass' => $filterSelectClass,
                        'options' => collect([
                            ['value' => 'popular', 'label' => 'ยอดนิยม', 'search' => 'popular ยอดนิยม'],
                            ['value' => 'reviews', 'label' => 'รีวิวมากสุด', 'search' => 'reviews รีวิวมากสุด'],
                            ['value' => 'favorites', 'label' => 'รายการโปรดมากสุด', 'search' => 'favorites รายการโปรดมากสุด'],
                            ['value' => 'title_asc', 'label' => 'ชื่อ ก-ฮ', 'search' => 'title_asc ชื่อ ก-ฮ'],
                            ['value' => 'title_desc', 'label' => 'ชื่อ ฮ-ก', 'search' => 'title_desc ชื่อ ฮ-ก'],
                            ['value' => 'published_newest', 'label' => 'เผยแพร่ล่าสุด', 'search' => 'published_newest เผยแพร่ล่าสุด'],
                            ['value' => 'published_oldest', 'label' => 'เผยแพร่เก่าสุด', 'search' => 'published_oldest เผยแพร่เก่าสุด'],
                            ['value' => 'oldest', 'label' => 'เก่าสุด', 'search' => 'oldest เก่าสุด'],
                        ]),
                    ])
                </div>

                <div class="lg:col-span-2">
                    <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">แสดงต่อหน้า</label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'per_page',
                        'name' => 'per_page',
                        'selected' => (string) $temples->perPage(),
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
                        href="{{ route('admin.temples.index') }}"
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
        <div class="relative z-[70] rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">จัดหมวดหมู่หลายวัดพร้อมกัน</h2>
                    <p class="text-sm text-slate-400">เลือกข้อมูลวัดจากตาราง แล้วเพิ่มเข้าหมวดหมู่ที่ต้องการโดยไม่ล้างหมวดหมู่เดิม</p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('admin.temples.bulk-category') }}"
                class="grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-slate-950/30 p-3 md:grid-cols-[1fr_auto]"
                data-temple-bulk-category-form
            >
                @csrf
                @method('PATCH')

                @include('admin.content.partials._async_select', [
                    'id' => 'bulk_temple_category_id',
                    'name' => 'category_id',
                    'selected' => '',
                    'searchUrl' => route('admin.lookups.categories', ['type' => 'temple', 'status' => 'active']),
                    'placeholder' => 'ค้นหาหมวดหมู่วัด',
                    'searchPlaceholder' => 'ค้นหาชื่อ / slug / ID',
                    'emptyLabel' => 'ยังไม่เลือกหมวดหมู่',
                ])

                <button
                    type="submit"
                    class="whitespace-nowrap rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    เพิ่มเข้าหมวดหมู่
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="relative z-0 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur transition-opacity" data-ajax-list-results>
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการข้อมูล</h2>
                    <p class="text-sm text-slate-400">
                        จำนวน {{ $temples->total() }} รายการ
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
                                    data-temple-select-all
                                    class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                    aria-label="เลือกวัดทั้งหมดในหน้านี้"
                                >
                            </th>
                            <th class="px-4 py-3 text-left">ข้อมูล</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่หลัก</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">สถิติ</th>
                            <th class="px-4 py-3 text-left">แนะนำ</th>
                            <th class="px-4 py-3 text-left">เผยแพร่เมื่อ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($temples as $temple)
                            @php
                                $content = $temple->content;
                                $primaryCategory = $content?->categories?->firstWhere('pivot.is_primary', true);
                                $coverMedia = $content?->mediaUsages?->firstWhere('role_key', 'cover')?->media;
                                $stat = $temple->stat;
                                $viewCount = (int) data_get($stat, 'view_count', 0);
                                $reviewCount = (int) data_get($stat, 'review_count', 0);
                                $averageRating = (float) data_get($stat, 'average_rating', 0);
                                $favoriteCount = (int) data_get($stat, 'favorite_count', 0);
                                $shareCount = (int) data_get($stat, 'share_count', 0);
                                $score = (float) data_get($stat, 'score', 0);
                            @endphp

                            <tr class="align-top transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="checkbox"
                                        value="{{ $temple->id }}"
                                        data-temple-checkbox
                                        class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                        aria-label="เลือกวัด {{ $content?->title ?? $temple->id }}"
                                    >
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 shadow-lg shadow-slate-950/30">
                                            @if ($coverMedia)
                                                <img
                                                    src="{{ asset('storage/' . $coverMedia->path) }}"
                                                    alt="{{ $coverMedia->alt_text ?: $content?->title }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-slate-800 text-slate-500">
                                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M4.75 6.75a2 2 0 0 1 2-2h10.5a2 2 0 0 1 2 2v10.5a2 2 0 0 1-2 2H6.75a2 2 0 0 1-2-2V6.75Z" stroke="currentColor" stroke-width="1.7"/>
                                                        <path d="m7.5 16 3.1-3.1a1.2 1.2 0 0 1 1.7 0l1.1 1.1.85-.85a1.2 1.2 0 0 1 1.7 0L18 15.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8.75 8.75h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">
                                                {{ $content?->title ?? '-' }}
                                            </p>

                                            <p class="mt-1 truncate text-xs text-slate-400">
                                                slug: {{ $content?->slug ?? '-' }}
                                            </p>

                                            @if ($temple->address?->province)
                                                <span class="mt-2 inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                    {{ $temple->address->province }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $primaryCategory?->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $status = $content?->status;
                                    @endphp

                                    @if ($status === 'published')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            Published
                                        </span>
                                    @elseif ($status === 'review')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-400/20 bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-sky-300"></span>
                                            Review
                                        </span>
                                    @elseif ($status === 'draft')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-300"></span>
                                            Draft
                                        </span>
                                    @elseif ($status === 'archived')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            Archived
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    <div class="grid gap-1 text-xs">
                                        <div>เข้าชม: {{ number_format($viewCount) }}</div>
                                        <div>รีวิว: {{ number_format($reviewCount) }} ({{ $averageRating > 0 ? number_format($averageRating, 1) : '-' }})</div>
                                        <div>Fav: {{ number_format($favoriteCount) }}</div>
                                        <div>แชร์: {{ number_format($shareCount) }}</div>
                                        <div>Score: {{ number_format($score, 2) }}</div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($content?->is_featured)
                                        <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                            ใช่
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-400">
                                            ไม่ใช่
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.temples.show', $temple) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.temples.edit', $temple) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.temples.destroy', $temple) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลนี้?')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ยังไม่มีข้อมูล</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        เริ่มเพิ่มข้อมูลแรกเพื่อใช้แสดงผลบนหน้าเว็บไซต์
                                    </p>

                                    <a
                                        href="{{ route('admin.temples.create') }}"
                                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                                    >
                                        <span class="text-lg leading-none">+</span>
                                        เพิ่มข้อมูล
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($temples->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $temples->links() }}
                </div>
            @endif
        </div>

        @include('admin.content.partials._ajax_index_loader')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bulkForm = document.querySelector('[data-temple-bulk-category-form]');

            const templeCheckboxes = () => Array.from(document.querySelectorAll('[data-temple-checkbox]'));
            const templeSelectAll = () => document.querySelector('[data-temple-select-all]');

            document.addEventListener('change', (event) => {
                if (event.target.matches('[data-temple-select-all]')) {
                    templeCheckboxes().forEach((checkbox) => {
                        checkbox.checked = event.target.checked;
                    });
                    return;
                }

                if (event.target.matches('[data-temple-checkbox]')) {
                    const checkboxes = templeCheckboxes();
                    const selectAll = templeSelectAll();

                    if (selectAll) {
                        selectAll.checked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
                    }
                }
            });

            bulkForm?.addEventListener('submit', (event) => {
                bulkForm.querySelectorAll('[data-injected-temple-id]').forEach((input) => input.remove());

                const selectedIds = templeCheckboxes()
                    .filter((checkbox) => checkbox.checked)
                    .map((checkbox) => checkbox.value);

                if (selectedIds.length === 0) {
                    event.preventDefault();
                    alert('กรุณาเลือกข้อมูลวัดอย่างน้อย 1 รายการ');
                    return;
                }

                if (! confirm('ยืนยันการเพิ่มข้อมูลวัดที่เลือกเข้าหมวดหมู่นี้?')) {
                    event.preventDefault();
                    return;
                }

                selectedIds.forEach((id) => {
                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.name = 'temple_ids[]';
                    input.value = id;
                    input.setAttribute('data-injected-temple-id', 'true');

                    bulkForm.appendChild(input);
                });
            });
        });
    </script>
</x-layouts.admin>
