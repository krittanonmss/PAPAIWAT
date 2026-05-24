    {{-- Section: Media --}}
    <section
        id="media-section"
        x-data="{
            mediaSearch: '',
            mediaSearchTimer: null,
            coverPickerUrl: @js(route('admin.temples.media-picker.cover')),
            galleryPickerUrl: @js(route('admin.temples.media-picker.gallery')),
            selectedCover: window.templeDraftMediaId('cover_media_id', @js((string) old('cover_media_id', $coverMedia?->media_id ?? ''))),
            selectedGallery: window.templeDraftMediaIdArray('gallery_media_ids[]', @js(array_map('strval', old('gallery_media_ids', $galleryMediaIds)))),

            coverHtml: @js(view('admin.content.temples.partials._cover_media_grid', [
                'mediaItems' => $coverMediaItems ?? $mediaItems,
            ])->render()),

            galleryHtml: @js(view('admin.content.temples.partials._gallery_media_grid', [
                'mediaItems' => $galleryMediaItems ?? $mediaItems,
            ])->render()),

            init() {
                this.$watch('selectedCover', () => this.$nextTick(() => saveTempleDraft()));
                this.$watch('selectedGallery', () => this.$nextTick(() => saveTempleDraft()));
            },

            mediaUrl(url) {
                const nextUrl = new URL(url, window.location.origin);

                if (this.mediaSearch.trim()) {
                    nextUrl.searchParams.set('q', this.mediaSearch.trim());
                } else {
                    nextUrl.searchParams.delete('q');
                }

                return nextUrl.toString();
            },

            scheduleMediaSearch() {
                window.clearTimeout(this.mediaSearchTimer);
                this.mediaSearchTimer = window.setTimeout(() => {
                    this.loadCoverUrl();
                    this.loadGalleryUrl();
                }, 300);
            },

            toggleGallery(id) {
                id = String(id);

                if (this.selectedGallery.includes(id)) {
                    this.selectedGallery = this.selectedGallery.filter((item) => item !== id);
                    return;
                }

                this.selectedGallery.push(id);
            },

            async loadCoverUrl(url = this.coverPickerUrl) {
            const response = await fetch(this.mediaUrl(url), {
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

        async loadGalleryUrl(url = this.galleryPickerUrl) {
            const response = await fetch(this.mediaUrl(url), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                this.galleryHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.galleryPicker);
                });
            }
        },

        async loadGalleryPage(event) {
            const link = event.target.closest('a');

            if (!link) {
                return;
            }

            event.preventDefault();

            await this.loadGalleryUrl(link.href);
        },
        }"
        class="temple-panel temple-panel-media overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div class="border-b border-white/10 px-6 py-4">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รูปภาพและมีเดีย</h2>
                    <p class="mt-1 text-xs text-slate-400">เลือกรูปปกและรูปแกลเลอรีที่ใช้แสดงหน้า Detail</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="w-full sm:w-72">
                        <label for="media_search" class="mb-1.5 block text-xs font-medium text-slate-400">
                            ค้นหารูปในคลังสื่อ
                        </label>

                        <input
                            id="media_search"
                            type="text"
                            x-model="mediaSearch"
                            @input="scheduleMediaSearch()"
                            placeholder="ชื่อรูปหรือชื่อไฟล์..."
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <a
                        href="{{ route('admin.media.index') }}"
                        target="_blank"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2.5 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                    >
                        + คลังสื่อ
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6 p-6">
            <div
                x-data="quickMediaUploader()"
                class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4"
            >
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="quick_media_file" class="block text-sm font-medium text-slate-300">
                                อัปโหลดรูปใหม่แบบด่วน
                            </label>
                            <span class="text-xs text-slate-500">สูงสุด 5 MB ต่อรูป</span>
                        </div>

                        <input
                            id="quick_media_file"
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
                    </div>

                    <button
                        type="button"
                        @click="upload()"
                        :disabled="isUploading"
                        class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span x-show="!isUploading">อัปโหลด</span>
                        <span x-show="isUploading">กำลังอัปโหลด...</span>
                    </button>
                </div>
            </div>

            {{-- Hidden values --}}
            <input type="hidden" name="cover_media_id" :value="selectedCover">

            <template x-for="mediaId in selectedGallery" :key="mediaId">
                <input type="hidden" name="gallery_media_ids[]" :value="mediaId">
            </template>

            {{-- รูปปก --}}
            <div class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200">รูปปก</h3>
                        <p class="mt-1 text-xs text-slate-500">รูปหลักที่ใช้เป็นภาพนำของหน้า Detail</p>
                    </div>
                    <button
                        type="button"
                        x-show="selectedCover"
                        @click="selectedCover = ''"
                        class="w-fit rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-300 transition hover:bg-white/10"
                    >
                        ล้างรูปปก
                    </button>
                </div>

                <div
                    x-ref="coverPicker"
                    x-html="coverHtml"
                    @click="loadCoverPage($event)"
                ></div>
            </div>

            {{-- Gallery --}}
            <div class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200">รูปแกลเลอรี</h3>
                        <p class="mt-1 text-xs text-slate-500">เลือกได้หลายรูปสำหรับส่วนแกลเลอรี</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400">เลือกแล้ว <span class="font-semibold text-blue-300" x-text="selectedGallery.length"></span></span>
                        <button
                            type="button"
                            x-show="selectedGallery.length > 0"
                            @click="selectedGallery = []"
                            class="rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-300 transition hover:bg-white/10"
                        >
                            ล้างแกลเลอรี
                        </button>
                    </div>
                </div>

                <div
                    x-ref="galleryPicker"
                    x-html="galleryHtml"
                    @click="loadGalleryPage($event)"
                ></div>
            </div>
        </div>
    </section>
