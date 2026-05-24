        {{-- Cover Media --}}
        <section
            x-data="{
                mediaSearch: '',
                coverPickerUrl: @js(route('admin.content.articles.media-picker.cover')),
                selectedCover: window.articleDraftMediaId('cover_media_id', @js((string) $selectedCoverMediaId)),
                coverHtml: @js(view('admin.content.articles.partials._cover_media_grid', [
                    'mediaItems' => $coverMediaItems ?? $mediaItems,
                ])->render()),
                mediaSearchTimer: null,

                init() {
                    this.$watch('selectedCover', () => this.$nextTick(() => window.saveArticleDraft?.()));
                },

                coverUrl(url = this.coverPickerUrl) {
                    const nextUrl = new URL(url, window.location.origin);

                    if (this.mediaSearch.trim()) {
                        nextUrl.searchParams.set('q', this.mediaSearch.trim());
                    } else {
                        nextUrl.searchParams.delete('q');
                    }

                    return nextUrl.toString();
                },

                scheduleCoverSearch() {
                    window.clearTimeout(this.mediaSearchTimer);
                    this.mediaSearchTimer = window.setTimeout(() => this.loadCoverUrl(), 300);
                },

                async loadCoverUrl(url = this.coverPickerUrl) {
                    const response = await fetch(this.coverUrl(url), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (response.ok) {
                        this.coverHtml = await response.text();

                        this.$nextTick(() => {
                            window.Alpine.initTree(this.$refs.coverPicker);
                        });
                    }
                },

                async loadCoverPage(event) {
                    const link = event.target.closest('a');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();

                    await this.loadCoverUrl(link.href);
                },
            }"
            class="article-panel article-panel-media overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
            <div id="article-media" class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Media</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        อัปโหลดรูปใหม่หรือเลือกรูปจาก คลังสื่อ เพื่อใช้เป็นภาพหน้าปกของ
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.index') }}"
                    target="_blank"
                    class="shrink-0 rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการมีเดีย
                </a>
            </div>

            <div class="space-y-6 p-6">
                {{-- Quick Upload --}}
                <div
                    x-data="quickArticleMediaUploader()"
                    class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label for="article_quick_media_file" class="mb-1.5 block text-sm font-medium text-slate-300">
                                อัปโหลดรูปหน้าปกใหม่
                            </label>

                            <input
                                id="article_quick_media_file"
                                type="file"
                                accept="image/*"
                                multiple
                                x-ref="fileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white
                                    file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500
                                    file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white
                                    hover:file:bg-blue-600"
                            >

                            <p x-show="errorMessage" x-text="errorMessage" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">
                                เลือกได้หลายรูป ขนาดไม่เกิน 5 MB ต่อรูป ระบบจะบันทึกเข้า คลังสื่อ แล้ว refresh หน้า
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="upload()"
                            :disabled="isUploading"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!isUploading">อัปโหลด</span>
                            <span x-show="isUploading">กำลังอัปโหลด...</span>
                        </button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="max-w-md">
                    <label for="cover_media_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ค้นหารูปจากชื่อ
                    </label>

                    <input
                        type="text"
                        id="cover_media_search"
                        x-model="mediaSearch"
                        @input="scheduleCoverSearch()"
                        placeholder="พิมพ์ชื่อรูป, title หรือชื่อไฟล์..."
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ค้นหาจากคลังสื่อทั้งหมด
                    </p>
                </div>

                <input type="hidden" name="cover_media_id" :value="selectedCover">

                <div class="space-y-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200">รูปหน้าปก</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            เลือกรูปเดียวสำหรับ card, หน้า list และหน้ารายละเอียด ของ
                        </p>
                    </div>

                    <div
                        x-ref="coverPicker"
                        x-html="coverHtml"
                        @click="loadCoverPage($event)"
                    ></div>
                </div>

                @error('cover_media_id')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>
