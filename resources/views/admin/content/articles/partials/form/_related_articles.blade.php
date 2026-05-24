        {{-- Related Articles --}}
        <section class="article-panel article-panel-taxonomy overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-related" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">Related Content</h2>
            </div>

            <div class="space-y-4 p-6">
                @include('admin.content.partials._async_multi_select', [
                    'id' => 'article_related',
                    'name' => 'related_article_ids',
                    'label' => 'ค้นหาที่เกี่ยวข้อง',
                    'placeholder' => 'ค้นหาจากชื่อ slug หรือ ID',
                    'emptyText' => 'ยังไม่ได้เลือกบทความที่เกี่ยวข้อง',
                    'noResultsText' => 'ไม่พบบทความที่ตรงกับคำค้นหา',
                    'searchUrl' => route('admin.lookups.articles', array_filter([
                        'exclude_id' => $article?->id,
                    ])),
                    'selectedIds' => $selectedRelatedArticleIds,
                    'selectedOptions' => $relatedArticles->map(fn ($relatedArticle) => [
                        'id' => $relatedArticle->id,
                        'label' => $relatedArticle->content?->title ?? 'ไม่มีชื่อ',
                        'meta' => '#' . $relatedArticle->id . ' | ' . ($relatedArticle->content?->slug ?? '-'),
                    ]),
                ])

                @error('related_article_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('related_article_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>
