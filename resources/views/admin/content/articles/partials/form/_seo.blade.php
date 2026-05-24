        {{-- SEO --}}
        <section class="article-panel article-panel-seo overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-seo" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">SEO</h2>
                <p class="mt-1 text-xs text-slate-400">ข้อมูลสำหรับ เครื่องมือค้นหา และการแชร์</p>
            </div>

            <div class="grid gap-6 p-6">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Meta Title
                    </label>
                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $content->meta_title ?? '') }}"
                        class="@error('meta_title') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="หัวข้อ SEO"
                    >
                    @error('meta_title')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Meta คำอธิบาย
                    </label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="@error('meta_description') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คำอธิบาย SEO"
                    >{{ old('meta_description', $content->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seo_keywords" class="mb-1.5 block text-sm font-medium text-slate-300">
                        SEO Keywords
                    </label>
                    <textarea
                        id="seo_keywords"
                        name="seo_keywords"
                        rows="3"
                        class="@error('seo_keywords') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คีย์เวิร์ด คั่นด้วย comma"
                    >{{ old('seo_keywords', $article->seo_keywords ?? '') }}</textarea>
                    @error('seo_keywords')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
