        {{-- Section: Categories --}}
        <section class="temple-panel temple-panel-media overflow-visible rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                        <p class="mt-1 text-xs text-slate-400">กำหนดกลุ่มเนื้อหาและหมวดหมู่หลักสำหรับหน้าแสดงผลวัด</p>
                    </div>

                    <a
                        href="{{ route('admin.categories.index') }}"
                        target="_blank"
                        class="inline-flex w-fit items-center justify-center rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                    >
                        + จัดการหมวดหมู่
                    </a>
                </div>
            </div>

            <div class="grid gap-5 p-6 2xl:grid-cols-[minmax(0,1fr)_360px]">
                <div>
                    @include('admin.content.partials._async_multi_select', [
                        'id' => 'temple_categories',
                        'name' => 'category_ids',
                        'label' => 'ค้นหาหมวดหมู่',
                        'placeholder' => 'ค้นหาจากชื่อหมวดหมู่ slug หรือ ID',
                        'emptyText' => 'ยังไม่ได้เลือกหมวดหมู่',
                        'noResultsText' => 'ไม่พบหมวดหมู่ที่ตรงกับคำค้นหา',
                        'searchUrl' => route('admin.lookups.categories', ['type' => 'temple']),
                        'selectedIds' => old('category_ids', $existingCategoryIds),
                        'selectedOptions' => $categories->map(fn ($cat) => [
                            'id' => $cat->id,
                            'label' => $cat->name,
                            'meta' => 'Category #' . $cat->id,
                        ]),
                    ])
                </div>

                <aside class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div>
                        @include('admin.content.partials._async_select', [
                            'id' => 'primary_category_id',
                            'name' => 'primary_category_id',
                            'label' => 'หมวดหมู่หลัก',
                            'selected' => old('primary_category_id', $primaryCategoryId),
                            'selectedOption' => ($primaryCategory = $categories->firstWhere('id', old('primary_category_id', $primaryCategoryId))) ? [
                                'id' => $primaryCategory->id,
                                'label' => $primaryCategory->name,
                                'meta' => 'Category #' . $primaryCategory->id,
                            ] : null,
                            'emptyLabel' => '— ไม่ระบุ —',
                            'placeholder' => 'เลือกหมวดหมู่หลัก',
                            'searchPlaceholder' => 'ค้นหาหมวดหมู่หลัก...',
                            'searchUrl' => route('admin.lookups.categories', ['type' => 'temple']),
                        ])

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            ใช้เป็นหมวดหลักในรายการและ metadata ของหน้าเว็บไซต์
                        </p>
                    </div>
                </aside>
            </div>
        </section>
