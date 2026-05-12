@php
    $decodeOldJson = function (string $key, array $fallback = []) {
        $value = old($key);

        if (! is_string($value) || $value === '') {
            return $fallback;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $fallback;
    };

    $initialContent = $decodeOldJson('content', $section->content ?? []);
    $initialSettings = $decodeOldJson('settings', $section->settings ?? []);
    $initialComponent = old('component_key', $section->component_key ?? 'hero');
    $initialStatus = old('status', $section->status ?? 'active');
    $initialVisible = (bool) old('is_visible', $section->is_visible ?? true);
    $sectionName = old('name', $section->name ?? '');
    $sectionKey = old('section_key', $section->section_key ?? '');
    $linkPages = $linkPages ?? collect();
    $bentoContents = $bentoContents ?? collect();
    $bentoLayoutPresets = [
        'feature_3' => ['label' => '3 กล่อง: Hero + 2 กล่องรอง', 'sizes' => ['large', 'wide', 'wide']],
        'balanced_4' => ['label' => '4 กล่อง: Hero + 3 กล่อง', 'sizes' => ['large', 'small', 'small', 'wide']],
        'mosaic_5' => ['label' => '5 กล่อง: Mosaic แนะนำ', 'sizes' => ['large', 'small', 'small', 'wide', 'small']],
        'editorial_6' => ['label' => '6 กล่อง: Editorial grid', 'sizes' => ['large', 'small', 'small', 'wide', 'small', 'small']],
        'compact_7' => ['label' => '7 กล่อง: Compact discovery', 'sizes' => ['wide', 'small', 'small', 'tall', 'small', 'small', 'wide']],
        'full_9' => ['label' => '9 กล่อง: Full discovery', 'sizes' => ['large', 'small', 'small', 'wide', 'small', 'tall', 'small', 'small', 'wide']],
    ];
    $initialBentoSlots = collect($initialContent['bento_slots'] ?? [])
        ->map(fn ($slot) => [
            'content_id' => (string) ($slot['content_id'] ?? ''),
            'size' => in_array(($slot['size'] ?? 'small'), ['large', 'wide', 'tall', 'small'], true) ? $slot['size'] : 'small',
        ])
        ->filter(fn ($slot) => $slot['content_id'] !== '')
        ->take(9)
        ->values()
        ->all();

    if ($initialBentoSlots === []) {
        $layoutSizes = $bentoLayoutPresets[$initialSettings['bento_layout'] ?? 'mosaic_5']['sizes'] ?? $bentoLayoutPresets['mosaic_5']['sizes'];
        $initialBentoSlots = collect($initialContent['bento_content_ids'] ?? [])
            ->take(9)
            ->values()
            ->map(fn ($contentId, $index) => [
                'content_id' => (string) $contentId,
                'size' => $layoutSizes[$index] ?? 'small',
            ])
            ->filter(fn ($slot) => $slot['content_id'] !== '')
            ->values()
            ->all();
    }

    $blocks = [
        'hero' => ['label' => 'Hero', 'description' => 'หัวหน้าใหญ่พร้อมปุ่ม'],
        'rich_text' => ['label' => 'ข้อความ', 'description' => 'หัวข้อและเนื้อหายาว'],
        'image_text' => ['label' => 'รูป + ข้อความ', 'description' => 'วางภาพคู่กับรายละเอียด'],
        'cta' => ['label' => 'ปุ่มเชิญชวน', 'description' => 'ข้อความสั้นพร้อมปุ่ม'],
        'article_grid' => ['label' => 'รายการบทความ', 'description' => 'ดึงบทความมาแสดงอัตโนมัติ'],
        'temple_grid' => ['label' => 'รายการวัด', 'description' => 'ดึงวัดมาแสดงอัตโนมัติ'],
        'travel_discovery_bento' => ['label' => 'Travel Discovery Bento', 'description' => 'บล็อกแนะนำหมวดท่องเที่ยวแบบ bento grid'],
        'article_list_full' => ['label' => 'หน้ารวมบทความ', 'description' => 'list เต็มพร้อมค้นหาและแบ่งหน้า'],
        'temple_list_full' => ['label' => 'หน้ารวมวัด', 'description' => 'list เต็มพร้อมตัวกรองและแบ่งหน้า'],
        'gallery' => ['label' => 'แกลเลอรี', 'description' => 'รวมรูปหลายรูปจาก URL'],
        'faq' => ['label' => 'FAQ', 'description' => 'คำถามคำตอบที่พบบ่อย'],
        'stats' => ['label' => 'ตัวเลขสำคัญ', 'description' => 'สถิติหรือจุดเด่นแบบสั้น'],
        'contact' => ['label' => 'ข้อมูลติดต่อ', 'description' => 'ที่อยู่ เบอร์โทร และอีเมล'],
    ];
@endphp

<div
    class="space-y-6"
    x-data="{
        component: @js($initialComponent),
        content: {
            eyebrow: @js($initialContent['eyebrow'] ?? ''),
            title: @js($initialContent['title'] ?? ''),
            subtitle: @js($initialContent['subtitle'] ?? ''),
            body: @js($initialContent['body'] ?? ''),
            primary_label: @js($initialContent['primary_label'] ?? ''),
            primary_url: @js($initialContent['primary_url'] ?? ''),
            secondary_label: @js($initialContent['secondary_label'] ?? ''),
            secondary_url: @js($initialContent['secondary_url'] ?? ''),
            all_button_enabled: @js((bool) ($initialContent['all_button_enabled'] ?? true)),
            all_button_label: @js($initialContent['all_button_label'] ?? ''),
            all_button_url: '',
            all_button_page_id: @js((string) ($initialContent['all_button_page_id'] ?? '')),
            image_media_id: @js((string) ($initialContent['image_media_id'] ?? '')),
            image_url: @js($initialContent['image_url'] ?? ''),
            gallery_text: @js($initialContent['gallery_text'] ?? ''),
            faq_text: @js($initialContent['faq_text'] ?? ''),
            stats_text: @js($initialContent['stats_text'] ?? ''),
            bento_slots: @js($initialBentoSlots),
            bento_note_label: @js($initialContent['bento_note_label'] ?? ''),
            bento_note_text: @js($initialContent['bento_note_text'] ?? ''),
            phone: @js($initialContent['phone'] ?? ''),
            email: @js($initialContent['email'] ?? ''),
            address: @js($initialContent['address'] ?? ''),
            map_url: @js($initialContent['map_url'] ?? '')
        },
        settings: {
            background: @js($initialSettings['background'] ?? 'dark'),
            align: @js($initialSettings['align'] ?? 'center'),
            layout: @js($initialSettings['layout'] ?? 'image_right'),
            source: @js($initialSettings['source'] ?? 'featured'),
            limit: @js((int) ($initialSettings['limit'] ?? 4)),
            bento_variant: @js($initialSettings['bento_variant'] ?? 'travel'),
            bento_layout: @js($initialSettings['bento_layout'] ?? 'mosaic_5'),
            image_opacity: @js((int) ($initialSettings['image_opacity'] ?? 100)),
            image_fit: @js($initialSettings['image_fit'] ?? 'contain'),
            image_position: @js($initialSettings['image_position'] ?? 'center'),
            show_search_box: @js((bool) ($initialSettings['show_search_box'] ?? false)),
            show_summary_stats: @js((bool) ($initialSettings['show_summary_stats'] ?? false))
        },
        bentoLayouts: @js($bentoLayoutPresets),
        bentoContentOptions: @js($bentoContents->mapWithKeys(fn ($content) => [
            (string) $content->id => [
                'title' => $content->title,
                'type' => $content->content_type === 'temple' ? 'วัด' : 'บทความ',
                'excerpt' => $content->excerpt,
            ],
        ])),
        bentoSearch: '',
        bentoTypeFilter: 'all',
        mediaSearch: '',
        selectedImage: @js((string) ($initialContent['image_media_id'] ?? '')),
        mediaHtml: @js(view('admin.content.layout.page-sections.partials._media_grid', [
            'mediaItems' => $sectionMediaItems,
        ])->render()),
        isUploading: false,
        uploadError: '',
        pick(type) {
            this.component = type;
            if (! this.content.title) {
                const labels = {
                    hero: 'หัวข้อหลักของหน้า',
                    rich_text: 'หัวข้อเนื้อหา',
                    image_text: 'หัวข้อพร้อมรูปภาพ',
                    cta: 'พร้อมเริ่มต้นใช้งาน',
                    article_grid: 'บทความแนะนำ',
                    temple_grid: 'วัดแนะนำ',
                    travel_discovery_bento: 'วางแผนเที่ยววัดในแบบของคุณ',
                    article_list_full: 'บทความทั้งหมด',
                    temple_list_full: 'รวมวัดทั่วไทย',
                    gallery: 'แกลเลอรี',
                    faq: 'คำถามที่พบบ่อย',
                    stats: 'ตัวเลขสำคัญ',
                    contact: 'ติดต่อเรา'
                };
                this.content.title = labels[type] || '';
            }
        },
        selectImage(mediaId) {
            this.selectedImage = mediaId;
            this.content.image_media_id = mediaId;
            if (mediaId) {
                this.content.image_url = '';
            }
        },
        bentoSlots() {
            return this.content.bento_slots;
        },
        bentoSizeLabel(size) {
            return {
                large: 'กล่องใหญ่',
                wide: 'กล่องกว้าง',
                tall: 'กล่องสูง',
                small: 'กล่องเล็ก'
            }[size] || size;
        },
        bentoPreviewClass(size) {
            return {
                large: 'md:col-span-2 md:row-span-2 min-h-56',
                wide: 'md:col-span-2 min-h-32',
                tall: 'md:row-span-2 min-h-56',
                small: 'min-h-32'
            }[size] || 'min-h-32';
        },
        bentoContentTitle(contentId) {
            return this.bentoContentOptions[contentId]?.title || 'ยังไม่ได้เลือก content';
        },
        bentoContentMeta(contentId) {
            const item = this.bentoContentOptions[contentId];
            return item ? item.type : 'เลือก content';
        },
        bentoContentExcerpt(contentId) {
            return this.bentoContentOptions[contentId]?.excerpt || 'ระบบจะแสดงคำโปรยหรือรายละเอียดเบื้องต้นของ content ตรงนี้';
        },
        filteredBentoOptions(currentContentId = '') {
            const search = this.bentoSearch.trim().toLowerCase();

            return Object.entries(this.bentoContentOptions)
                .filter(([id, item]) => {
                    if (currentContentId && id === String(currentContentId)) {
                        return true;
                    }

                    const matchesType = this.bentoTypeFilter === 'all' || item.type === this.bentoTypeFilter;
                    const matchesSearch = !search
                        || item.title.toLowerCase().includes(search)
                        || (item.excerpt || '').toLowerCase().includes(search);

                    return matchesType && matchesSearch;
                })
                .slice(0, 80)
                .map(([id, item]) => ({ id, ...item }));
        },
        addBentoBox(size = 'small') {
            if (this.content.bento_slots.length >= 9) {
                return;
            }
            this.content.bento_slots.push({ content_id: '', size });
        },
        removeBentoBox(index) {
            this.content.bento_slots.splice(index, 1);
        },
        applyBentoLayout(layoutKey) {
            const layout = this.bentoLayouts[layoutKey] || this.bentoLayouts.mosaic_5;
            const existing = this.content.bento_slots;
            this.settings.bento_layout = layoutKey;
            this.content.bento_slots = layout.sizes.slice(0, 9).map((size, index) => ({
                content_id: existing[index]?.content_id || '',
                size
            }));
        },
        async loadMediaPage(event) {
            const link = event.target.closest('a');

            if (!link) {
                return;
            }

            event.preventDefault();

            const response = await fetch(link.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                this.mediaHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.sectionMediaPicker);
                });
            }
        },
        async uploadImage() {
            this.uploadError = '';

            const file = this.$refs.sectionFileInput.files[0];

            if (!file) {
                this.uploadError = 'กรุณาเลือกรูปก่อนอัปโหลด';
                return;
            }

            const formData = new FormData();
            formData.append('_token', @js(csrf_token()));
            formData.append('file', file);
            formData.append('visibility', 'public');

            this.isUploading = true;

            try {
                const response = await fetch(@js(route('admin.media.store')), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    this.uploadError = 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
                    return;
                }

                window.location.reload();
            } catch (error) {
                this.uploadError = 'เกิดข้อผิดพลาดระหว่างอัปโหลด';
            } finally {
                this.isUploading = false;
            }
        },
    }"
>
    <input type="hidden" name="component_key" x-model="component">
    <input type="hidden" name="content" :value="JSON.stringify(content)">
    <input type="hidden" name="settings" :value="JSON.stringify(settings)">
    <input type="hidden" name="section_key" value="{{ $sectionKey }}">

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <p class="text-sm font-medium text-blue-300">Page Builder</p>
            <h2 class="mt-1 text-lg font-semibold text-white">เลือก block ที่ต้องการแสดง</h2>
            <p class="mt-1 text-sm text-slate-400">เลือกชนิด block แล้วกรอกเฉพาะข้อความ รูป และปุ่มที่ต้องใช้</p>
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach($blocks as $key => $block)
                <button
                    type="button"
                    class="rounded-2xl border p-4 text-left transition"
                    :class="component === @js($key) ? 'border-blue-400/60 bg-blue-500/15 text-white' : 'border-white/10 bg-slate-950/40 text-slate-300 hover:border-blue-400/30 hover:bg-white/[0.06]'"
                    @click="pick(@js($key))"
                >
                    <span class="block text-sm font-semibold">{{ $block['label'] }}</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-400">{{ $block['description'] }}</span>
                </button>
            @endforeach
        </div>

        @error('component_key')
            <p class="mt-3 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">เนื้อหา block</h2>
            <p class="mt-1 text-sm text-slate-400">ช่องที่ไม่เกี่ยวกับ block ที่เลือกจะถูกซ่อนไว้</p>
        </div>

        <div class="space-y-5">
            <div x-show="['hero', 'image_text', 'rich_text', 'cta', 'article_grid', 'temple_grid', 'travel_discovery_bento', 'article_list_full', 'temple_list_full', 'gallery', 'faq', 'stats', 'contact'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเล็กด้านบน</label>
                <input
                    type="text"
                    x-model="content.eyebrow"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น PAPAIWAT"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">หัวข้อหลัก</label>
                <input
                    type="text"
                    x-model="content.title"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="หัวข้อที่จะแสดงบนหน้าเว็บ"
                >
            </div>

            <div x-show="!['cta', 'contact'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายสั้น</label>
                <textarea
                    x-model="content.subtitle"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm leading-6 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="คำอธิบายสั้น ๆ ใต้หัวข้อ"
                ></textarea>
            </div>

            <div x-show="['rich_text', 'image_text', 'cta', 'travel_discovery_bento'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">เนื้อหา</label>
                <textarea
                    x-model="content.body"
                    rows="8"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="พิมพ์เนื้อหาได้เลย ระบบจะแสดงเป็นย่อหน้าให้อัตโนมัติ"
                ></textarea>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">รูปภาพของ Block</h3>
                    <p class="mt-1 text-xs text-slate-500">อัปโหลดรูปใหม่หรือเลือกรูปจาก Media Library เหมือนหน้า temple/article</p>
                </div>

                <div class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">อัปโหลดรูปใหม่</label>
                            <input
                                type="file"
                                accept="image/*"
                                x-ref="sectionFileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-blue-600"
                            >
                            <p x-show="uploadError" x-text="uploadError" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">รูปจะถูกบันทึกเข้า Media Library แล้ว refresh หน้าเพื่อให้เลือกรูปได้ทันที</p>
                        </div>

                        <button
                            type="button"
                            @click="uploadImage()"
                            :disabled="isUploading"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!isUploading">อัปโหลด</span>
                            <span x-show="isUploading">กำลังอัปโหลด...</span>
                        </button>
                    </div>
                </div>

                <div class="max-w-md">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ค้นหารูปจากชื่อ</label>
                    <input
                        type="text"
                        x-model="mediaSearch"
                        placeholder="พิมพ์ชื่อรูป, title หรือชื่อไฟล์..."
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                    <p class="mt-1 text-xs text-slate-500">ใช้ค้นหาเฉพาะรูปที่แสดงอยู่ในหน้าปัจจุบัน</p>
                </div>

                <div
                    x-ref="sectionMediaPicker"
                    x-html="mediaHtml"
                    @click="loadMediaPage($event)"
                ></div>
            </div>

            <div x-show="component === 'gallery'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">รายการรูปภาพ</label>
                <textarea
                    x-model="content.gallery_text"
                    rows="7"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 รูปต่อ 1 บรรทัด เช่น&#10;https://example.com/image.jpg | คำอธิบายรูป"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: URL รูป | คำอธิบายรูป</p>
            </div>

            <div x-show="component === 'faq'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">คำถามคำตอบ</label>
                <textarea
                    x-model="content.faq_text"
                    rows="8"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 คำถามต่อ 1 บรรทัด เช่น&#10;เปิดกี่โมง? | เปิดทุกวัน 08:00-17:00"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: คำถาม | คำตอบ</p>
            </div>

            <div x-show="component === 'stats'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ตัวเลขสำคัญ</label>
                <textarea
                    x-model="content.stats_text"
                    rows="6"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 รายการต่อ 1 บรรทัด เช่น&#10;120+ | วัดในระบบ"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: ตัวเลข | คำอธิบาย</p>
            </div>

            <div x-show="component === 'travel_discovery_bento'" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white">รายการใน Bento Grid</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">กดเพิ่มกล่องได้เอง สูงสุด 9 กล่อง แต่ละกล่องเลือก content และสัดส่วนได้</p>
                    </div>
                    <button
                        type="button"
                        @click="addBentoBox()"
                        :disabled="content.bento_slots.length >= 9"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        + เพิ่มกล่อง
                    </button>
                </div>

                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                        <p class="text-sm font-medium text-blue-100">เลือก content ที่จะใส่ในแต่ละกล่อง</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">ระบบจะใช้ชื่อ คำโปรย รูป cover และลิงก์ detail ของ content นั้นอัตโนมัติ</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ใช้ preset เริ่มต้น</label>
                        <select @change="applyBentoLayout($event.target.value)" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <template x-for="(layout, key) in bentoLayouts" :key="key">
                                <option :value="key" class="bg-slate-900" x-text="layout.label" :selected="settings.bento_layout === key"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4 lg:grid-cols-[minmax(0,1fr)_180px]">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหา content</label>
                        <input
                            type="search"
                            x-model.debounce.150ms="bentoSearch"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                            placeholder="ค้นหาชื่อหรือคำโปรย..."
                        >
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ประเภท</label>
                        <select x-model="bentoTypeFilter" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="all" class="bg-slate-900">ทั้งหมด</option>
                            <option value="วัด" class="bg-slate-900">วัด</option>
                            <option value="บทความ" class="bg-slate-900">บทความ</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <template x-for="(slot, index) in bentoSlots()" :key="index">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <label class="text-sm font-medium text-slate-300">
                                    <span x-text="`กล่องที่ ${index + 1}`"></span>
                                </label>
                                <button type="button" @click="removeBentoBox(index)" class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-2.5 py-1 text-xs font-medium text-rose-300 transition hover:bg-rose-500/20">
                                    ลบ
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-slate-500">Content</label>
                                    <select x-model="slot.content_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                        <option value="" class="bg-slate-900">เลือก content</option>
                                        <template x-for="option in filteredBentoOptions(slot.content_id)" :key="option.id">
                                            <option :value="option.id" class="bg-slate-900" x-text="`[${option.type}] ${option.title}`"></option>
                                        </template>
                                    </select>
                                    <p class="mt-1 text-[11px] leading-4 text-slate-500" x-show="slot.content_id" x-text="bentoContentExcerpt(slot.content_id)"></p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-slate-500">สัดส่วนกล่อง</label>
                                    <select x-model="slot.size" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                        <option value="large" class="bg-slate-900">กล่องใหญ่</option>
                                        <option value="wide" class="bg-slate-900">กล่องกว้าง</option>
                                        <option value="tall" class="bg-slate-900">กล่องสูง</option>
                                        <option value="small" class="bg-slate-900">กล่องเล็ก</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="content.bento_slots.length === 0" x-cloak class="rounded-2xl border border-dashed border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                    ยังไม่มีกล่อง กด “เพิ่มกล่อง” เพื่อเลือก content
                </div>

                <div x-show="content.bento_slots.length > 0" x-cloak class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Preview Bento</h4>
                            <p class="mt-1 text-xs text-slate-500">ตัวอย่างสัดส่วนและตำแหน่งโดยประมาณบน desktop</p>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-400">
                            <span x-text="content.bento_slots.length"></span>/9 กล่อง
                        </span>
                    </div>

                    <div class="grid auto-rows-[minmax(8rem,auto)] gap-3 md:grid-cols-4">
                        <template x-for="(slot, index) in bentoSlots()" :key="`preview-${index}`">
                            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800 via-slate-900 to-blue-950 p-4 shadow-lg shadow-slate-950/20" :class="bentoPreviewClass(slot.size)">
                                <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/30"></div>
                                <div class="relative flex h-full flex-col justify-between gap-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-medium text-blue-100" x-text="bentoContentMeta(slot.content_id)"></span>
                                        <span class="rounded-full bg-black/20 px-2.5 py-1 text-[11px] text-white/70" x-text="bentoSizeLabel(slot.size)"></span>
                                    </div>
                                    <div>
                                        <p class="line-clamp-2 text-sm font-semibold text-white" x-text="bentoContentTitle(slot.content_id)"></p>
                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-300" x-text="bentoContentExcerpt(slot.content_id)"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้าย note ด้านล่าง</label>
                        <input type="text" x-model="content.bento_note_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น Curated routes">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความ note ด้านล่าง</label>
                        <input type="text" x-model="content.bento_note_text" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น เลือกเส้นทางจากเวลาเดินทาง บรรยากาศ และความสนใจ">
                    </div>
                </div>
            </div>

            <div x-show="component === 'contact'" x-cloak class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">เบอร์โทร</label>
                    <input type="text" x-model="content.phone" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">อีเมล</label>
                    <input type="text" x-model="content.email" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ที่อยู่</label>
                    <textarea x-model="content.address" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"></textarea>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์แผนที่</label>
                    <input type="text" x-model="content.map_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="https://maps.google.com/...">
                </div>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">URL รูปภาพสำรอง</label>
                <input
                    type="text"
                    x-model="content.image_url"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="https://... หรือ /storage/..."
                >
            </div>

            <div x-show="['hero', 'cta', 'travel_discovery_bento'].includes(component)" x-cloak class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มหลัก</label>
                    <input type="text" x-model="content.primary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น ดูเพิ่มเติม">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์ปุ่มหลัก</label>
                    <input type="text" x-model="content.primary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น /temple-list">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มรอง</label>
                    <input type="text" x-model="content.secondary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น อ่านบทความ">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์ปุ่มรอง</label>
                    <input type="text" x-model="content.secondary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น /article-list">
                </div>
            </div>

            <div x-show="['article_grid', 'temple_grid'].includes(component)" x-cloak class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="flex items-start gap-3">
                    <input id="all_button_enabled" type="checkbox" x-model="content.all_button_enabled" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="all_button_enabled" class="text-sm font-medium text-white">แสดงปุ่มดูทั้งหมด</label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">ใช้กำหนดปุ่มด้านขวาของหัวข้อ section รายการวัดหรือรายการบทความ</p>
                    </div>
                </div>

                <div x-show="content.all_button_enabled" x-cloak class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มดูทั้งหมด</label>
                        <input type="text" x-model="content.all_button_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น ดูทั้งหมด">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">หน้าปลายทาง</label>
                        <select x-model="content.all_button_page_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="" class="bg-slate-900">ใช้หน้ารวมค่าเริ่มต้น</option>
                            @foreach($linkPages as $linkPage)
                                <option value="{{ $linkPage->id }}" class="bg-slate-900">
                                    {{ $linkPage->title }}
                                    @if($linkPage->is_homepage)
                                        (หน้าแรก)
                                    @elseif($linkPage->slug)
                                        (/{{ $linkPage->slug }})
                                    @endif
                                    @if($linkPage->status !== 'published')
                                        - {{ $linkPage->status }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">การแสดงผล</h2>
            <p class="mt-1 text-sm text-slate-400">ปรับหน้าตาและแหล่งข้อมูลแบบเลือกจากรายการ</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">พื้นหลัง</label>
                <select x-model="settings.background" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="dark" class="bg-slate-900">เข้ม</option>
                    <option value="soft" class="bg-slate-900">นุ่ม</option>
                    <option value="plain" class="bg-slate-900">เรียบ</option>
                </select>
            </div>

            <div x-show="component === 'image_text'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ตำแหน่งรูป</label>
                <select x-model="settings.layout" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="image_right" class="bg-slate-900">รูปอยู่ขวา</option>
                    <option value="image_left" class="bg-slate-900">รูปอยู่ซ้าย</option>
                </select>
            </div>

            <div x-show="component === 'travel_discovery_bento'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">สไตล์ Bento</label>
                <select x-model="settings.bento_variant" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="travel" class="bg-slate-900">Travel discovery</option>
                    <option value="calm" class="bg-slate-900">Calm minimal</option>
                    <option value="editorial" class="bg-slate-900">Editorial contrast</option>
                </select>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak>
                <div class="flex items-center justify-between gap-3">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ความทึบของรูป</label>
                    <span class="text-xs text-slate-500" x-text="`${settings.image_opacity}%`"></span>
                </div>
                <input type="range" min="10" max="100" step="5" x-model.number="settings.image_opacity" class="w-full accent-blue-500">
                <p class="mt-1 text-xs text-slate-500">100% คือแสดงรูปตามต้นฉบับ ยิ่งต่ำรูปยิ่งจาง</p>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">สัดส่วนการวางรูป</label>
                    <select x-model="settings.image_fit" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                        <option value="contain" class="bg-slate-900">พอดีรูป ไม่ครอป</option>
                        <option value="cover" class="bg-slate-900">เต็มพื้นที่ อาจครอป</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">ใช้ “พอดีรูป” เพื่อลดโอกาสรูปเบลอ แตก หรือถูกครอปผิดสัดส่วน</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ตำแหน่งรูป</label>
                    <select x-model="settings.image_position" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                        <option value="center" class="bg-slate-900">กลาง</option>
                        <option value="top" class="bg-slate-900">บน</option>
                        <option value="bottom" class="bg-slate-900">ล่าง</option>
                        <option value="left" class="bg-slate-900">ซ้าย</option>
                        <option value="right" class="bg-slate-900">ขวา</option>
                    </select>
                </div>
            </div>

            <div x-show="['hero', 'travel_discovery_bento'].includes(component)" x-cloak class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex items-start gap-3">
                    <input id="show_search_box" type="checkbox" x-model="settings.show_search_box" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="show_search_box" class="text-sm font-medium text-white">แสดงกล่องค้นหา</label>
                        <p x-show="component === 'hero'" class="mt-1 text-xs leading-5 text-slate-400">แสดง search box ค้นหารวมทั้งวัดและบทความบนหน้าเว็บไซต์</p>
                        <p x-show="component === 'travel_discovery_bento'" class="mt-1 text-xs leading-5 text-slate-400">แสดง search box เพื่อค้นหาวัด โดยส่งไปหน้ารวมวัด</p>
                    </div>
                </div>
                <div x-show="component === 'travel_discovery_bento'" x-cloak class="flex items-start gap-3">
                    <input id="show_summary_stats" type="checkbox" x-model="settings.show_summary_stats" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="show_summary_stats" class="text-sm font-medium text-white">แสดงกล่องข้อมูลรวม</label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">แสดงจำนวนวัดทั้งหมด จำนวนบทความทั้งหมด และยอดผู้เข้าชมทั้งหมด</p>
                    </div>
                </div>
            </div>

            <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">แหล่งข้อมูล</label>
                <select x-model="settings.source" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="featured" class="bg-slate-900">รายการแนะนำ</option>
                    <option value="popular" class="bg-slate-900">ยอดนิยม</option>
                    <option value="all" class="bg-slate-900">ล่าสุดทั้งหมด</option>
                </select>
            </div>

            <div x-show="['article_grid', 'temple_grid'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนที่แสดง</label>
                <input type="number" min="1" max="12" x-model.number="settings.limit" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">ลำดับบนหน้า</label>
                <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $section->sort_order ?? 0) }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                @error('sort_order')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">สถานะ</label>
                <select id="status" name="status" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="active" class="bg-slate-900" @selected($initialStatus === 'active')>เปิดใช้งาน</option>
                    <option value="inactive" class="bg-slate-900" @selected($initialStatus === 'inactive')>ปิดใช้งาน</option>
                </select>
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4 lg:col-span-2">
                <div class="flex items-start gap-3">
                    <input id="is_visible" type="checkbox" name="is_visible" value="1" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20" @checked($initialVisible)>
                    <div>
                        <label for="is_visible" class="text-sm font-medium text-white">แสดง block นี้บนหน้าเว็บ</label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">ถ้าปิดไว้ block นี้จะยังอยู่ในระบบ แต่ไม่แสดงบนหน้า frontend</p>
                    </div>
                </div>
            </div>
        </div>

        <details class="mt-5 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
            <summary class="cursor-pointer text-sm font-medium text-slate-300">ตั้งค่าขั้นสูง</summary>
            <div class="mt-4 grid gap-5 lg:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อ block ในระบบ</label>
                    <input id="name" type="text" name="name" value="{{ $sectionName }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เว้นว่างได้">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">รหัส block</label>
                    <input type="text" value="{{ $sectionKey ?: 'ระบบจะสร้างให้อัตโนมัติ' }}" disabled class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-slate-500">
                </div>
            </div>
        </details>
    </div>
</div>
