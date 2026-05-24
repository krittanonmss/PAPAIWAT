<div
    x-data="{
        status: @js(old('status', $page->status ?? 'draft')),
        publishedAt: @js(old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '')),
        formatNow() {
            const now = new Date();
            now.setSeconds(0, 0);
            const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);

            return local.toISOString().slice(0, 16);
        },
        useCurrentTime() {
            this.publishedAt = this.formatNow();
        },
        syncPublishTime() {
            if (this.status === 'published' && !this.publishedAt) {
                this.useCurrentTime();
            }
        },
    }"
    x-init="syncPublishTime()"
    class="space-y-6"
>
    {{-- Page Information --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">ตัวสร้างหน้า</p>
            <h2 class="mt-1 text-lg font-semibold text-white">ข้อมูลหน้า</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลหลักของหน้าเว็บไซต์</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ชื่อหน้า <span class="text-rose-400">*</span>
                </label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $page->title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น หน้าแรก"
                    required
                >
                @error('title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                    URL ของหน้า
                </label>
                <input
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ old('slug', $page->slug ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น about"
                >
                <p class="mt-1.5 text-xs text-slate-500">กรอกเองได้ หรือเว้นว่างไว้เพื่อให้ระบบสร้างจากชื่อหน้า ถ้าชื่อเป็นภาษาไทยระบบจะแปลงเป็นตัวอักษรอังกฤษให้</p>
                @error('slug')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                    รูปแบบหน้า
                </label>
                <select
                    id="template_id"
                    name="template_id"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                    <option value="">Page Builder / เริ่มต้น</option>
                    @foreach($templates as $template)
                        <option
                            value="{{ $template->id }}"
                            {{ old('template_id', $page->template_id ?? '') == $template->id ? 'selected' : '' }}
                        >
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
                @error('template_id')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden">
                <label for="page_type" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ประเภทหน้า <span class="text-rose-400">*</span>
                </label>
                <input
                    id="page_type"
                    type="text"
                    name="page_type"
                    value="{{ old('page_type', $page->page_type ?? 'custom') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="custom"
                    required
                >
                @error('page_type')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                    สถานะ <span class="text-rose-400">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    x-model="status"
                    @change="syncPublishTime()"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    required
                >
                    @foreach(['draft' => 'ฉบับร่าง', 'published' => 'เผยแพร่', 'archived' => 'เก็บถาวร'] as $status => $label)
                        <option
                            value="{{ $status }}"
                            {{ old('status', $page->status ?? 'draft') === $status ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden">
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ลำดับ
                </label>
                <input
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('sort_order')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4 lg:col-span-2">
                <div class="flex items-start gap-3">
                    <input
                        id="is_homepage"
                        type="checkbox"
                        name="is_homepage"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20"
                        {{ old('is_homepage', $page->is_homepage ?? false) ? 'checked' : '' }}
                    >
                    <div>
                        <label for="is_homepage" class="text-sm font-medium text-white">
                            ตั้งเป็นหน้าแรก
                        </label>
                        <p class="mt-1 text-xs text-slate-400">ถ้าเลือกหน้านี้ หน้า homepage เดิมจะถูกยกเลิก</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Summary --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">เนื้อหาหน้า</p>
            <h2 class="mt-1 text-lg font-semibold text-white">สรุปเนื้อหา</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อความสรุปและรายละเอียดของหน้า</p>
        </div>

        <div class="space-y-5">
            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">คำโปรย</label>
                <input
                    id="excerpt"
                    type="text"
                    name="excerpt"
                    value="{{ old('excerpt', $page->excerpt ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="คำอธิบายสั้น ๆ ของหน้า"
                >
                @error('excerpt')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบาย</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="รายละเอียดเพิ่มเติมของหน้า"
                >{{ old('description', $page->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- SEO Settings --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">การค้นหาและการแชร์</p>
            <h2 class="mt-1 text-lg font-semibold text-white">ตั้งค่า SEO</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลสำหรับเครื่องมือค้นหาและการแชร์บนโซเชียล</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อ SEO</label>
                <input
                    id="meta_title"
                    type="text"
                    name="meta_title"
                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('meta_title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_keywords" class="mb-1.5 block text-sm font-medium text-slate-300">คีย์เวิร์ด SEO</label>
                <input
                    id="meta_keywords"
                    type="text"
                    name="meta_keywords"
                    value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ไทย, ธรรมะ, ท่องเที่ยว"
                >
                @error('meta_keywords')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">Meta คำอธิบาย</label>
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                @error('meta_description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="canonical_url" class="mb-1.5 block text-sm font-medium text-slate-300">Canonical URL</label>
                <input
                    id="canonical_url"
                    type="text"
                    name="canonical_url"
                    value="{{ old('canonical_url', $page->canonical_url ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('canonical_url')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                @php
                    $selectedOgImageId = (string) old('og_image_media_id', $page->og_image_media_id ?? '');
                @endphp

                <label class="mb-2 block text-sm font-medium text-slate-300">Cover / OG Image</label>
                <p class="mb-3 text-xs text-slate-500">ใช้เป็นรูปหน้าปกในรายการหน้าเว็บไซต์ และเป็นรูปตอนแชร์หน้านี้บนโซเชียล</p>

                <div
                    x-data="{
                        ogImageSearch: '',
                        selectedOgImage: @js($selectedOgImageId),
                        pickerUrl: @js(route('admin.content.pages.media-picker.og-image')),
                        pickerHtml: @js(view('admin.content.layout.pages.partials._og_image_media_grid', [
                            'mediaItems' => $ogImageMediaItems,
                        ])->render()),
                        searchTimer: null,

                        requestUrl(url = this.pickerUrl) {
                            const nextUrl = new URL(url, window.location.origin);

                            if (this.ogImageSearch.trim()) {
                                nextUrl.searchParams.set('q', this.ogImageSearch.trim());
                            } else {
                                nextUrl.searchParams.delete('q');
                            }

                            if (this.selectedOgImage) {
                                nextUrl.searchParams.set('selected', this.selectedOgImage);
                            }

                            return nextUrl.toString();
                        },

                        scheduleSearch() {
                            window.clearTimeout(this.searchTimer);
                            this.searchTimer = window.setTimeout(() => this.loadPicker(), 300);
                        },

                        async loadPicker(url = this.pickerUrl) {
                            const response = await fetch(this.requestUrl(url), {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (response.ok) {
                                this.pickerHtml = await response.text();
                                this.$nextTick(() => window.Alpine.initTree(this.$refs.ogImagePicker));
                            }
                        },

                        async loadPage(event) {
                            const link = event.target.closest('a');

                            if (!link) {
                                return;
                            }

                            event.preventDefault();
                            await this.loadPicker(link.href);
                        },
                    }"
                    class="space-y-4"
                >
                    <div class="max-w-md">
                        <label for="page_og_image_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ค้นหารูป
                        </label>
                        <input
                            id="page_og_image_search"
                            type="text"
                            x-model="ogImageSearch"
                            @input="scheduleSearch()"
                            placeholder="พิมพ์ชื่อรูป, title, ชื่อไฟล์ หรือ ID..."
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        <p class="mt-1 text-xs text-slate-500">ค้นหาจากคลังสื่อทั้งหมด และแสดงผลครั้งละไม่เกิน 7 รูป</p>
                    </div>

                    <input type="hidden" name="og_image_media_id" :value="selectedOgImage">

                    <div
                        x-ref="ogImagePicker"
                        x-html="pickerHtml"
                        @click="loadPage($event)"
                    ></div>
                </div>

                @error('og_image_media_id')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_title" class="mb-1.5 block text-sm font-medium text-slate-300">OG Title</label>
                <input
                    id="og_title"
                    type="text"
                    name="og_title"
                    value="{{ old('og_title', $page->og_title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('og_title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_description" class="mb-1.5 block text-sm font-medium text-slate-300">OG คำอธิบาย</label>
                <textarea
                    id="og_description"
                    name="og_description"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >{{ old('og_description', $page->og_description ?? '') }}</textarea>
                @error('og_description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Publishing Schedule --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">การเผยแพร่</p>
            <h2 class="mt-1 text-lg font-semibold text-white">กำหนดเวลาเผยแพร่</h2>
            <p class="mt-1 text-sm text-slate-400">กำหนดช่วงเวลาที่หน้าเผยแพร่</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <label for="published_at" class="block text-sm font-medium text-slate-300">เผยแพร่เมื่อ</label>
                    <button
                        type="button"
                        @click="useCurrentTime()"
                        class="text-xs font-medium text-blue-300 hover:text-blue-200"
                    >
                        ใช้เวลาปัจจุบัน
                    </button>
                </div>
                <input
                    id="published_at"
                    type="datetime-local"
                    name="published_at"
                    x-model="publishedAt"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('published_at')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unpublished_at" class="mb-1.5 block text-sm font-medium text-slate-300">สิ้นสุดการเผยแพร่</label>
                <input
                    id="unpublished_at"
                    type="datetime-local"
                    name="unpublished_at"
                    value="{{ old('unpublished_at', isset($page) && $page->unpublished_at ? $page->unpublished_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('unpublished_at')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
