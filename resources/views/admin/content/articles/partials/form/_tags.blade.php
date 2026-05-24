        {{-- Tags --}}
        <section class="article-panel article-panel-taxonomy overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-tags" class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">แท็ก</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        เลือกแท็กที่เกี่ยวข้องกับบทความ
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.article-tags.index') }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการแท็ก
                </a>
            </div>

            <div class="space-y-4 p-6">
                @include('admin.content.partials._async_multi_select', [
                    'id' => 'article_tags',
                    'name' => 'tag_ids',
                    'label' => 'ค้นหาแท็ก',
                    'placeholder' => 'ค้นหาจากชื่อแท็ก slug หรือ ID',
                    'emptyText' => 'ยังไม่ได้เลือกแท็ก',
                    'noResultsText' => 'ไม่พบแท็กที่ตรงกับคำค้นหา',
                    'searchUrl' => route('admin.lookups.article-tags'),
                    'selectedIds' => $selectedTagIds,
                    'selectedOptions' => $tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'label' => $tag->name,
                        'meta' => $tag->slug,
                    ]),
                ])

                @error('tag_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('tag_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>
