        {{-- Categories --}}
        <section class="article-panel article-panel-taxonomy overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-categories" class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">Categories / Tags</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        เลือกหมวดหมู่ที่เกี่ยวข้องกับ
                    </p>
                </div>

                <a
                    href="{{ route('admin.categories.index') }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการหมวดหมู่
                </a>
            </div>

            <div class="space-y-4 p-6">
                @include('admin.content.partials._async_multi_select', [
                    'id' => 'article_categories',
                    'name' => 'category_ids',
                    'label' => 'ค้นหาหมวดหมู่',
                    'placeholder' => 'ค้นหาจากชื่อหมวดหมู่ slug หรือ ID',
                    'emptyText' => 'ยังไม่ได้เลือกหมวดหมู่',
                    'noResultsText' => 'ไม่พบหมวดหมู่ที่ตรงกับคำค้นหา',
                    'searchUrl' => route('admin.lookups.categories', ['type' => 'article', 'status' => 'active']),
                    'selectedIds' => $selectedCategoryIds,
                    'selectedOptions' => $categories->map(fn ($category) => [
                        'id' => $category->id,
                        'label' => $category->name,
                        'meta' => $category->type_key . ' #' . $category->id,
                    ]),
                ])

                @error('category_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('category_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>
