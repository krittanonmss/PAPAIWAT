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
    $initialสถานะ = old('status', $section->status ?? 'active');
    $initialVisible = (bool) old('is_visible', $section->is_visible ?? true);
    $sectionชื่อ = old('name', $section->name ?? '');
    $sectionKey = old('section_key', $section->section_key ?? '');
    $linkหน้า = $linkหน้า ?? collect();
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

    $legacyBackgroundColor = match ($initialSettings['background'] ?? 'dark') {
        'plain' => '#ffffff',
        'soft' => '#f1f5f9',
        default => '#020617',
    };
    $initialBackgroundColor = $initialSettings['background_color'] ?? $legacyBackgroundColor;
    $initialBackgroundColorEnd = $initialSettings['background_color_end'] ?? $initialBackgroundColor;
    $initialBentoContentAlign = in_array(($initialSettings['bento_content_align'] ?? 'left'), ['left', 'center', 'right'], true)
        ? ($initialSettings['bento_content_align'] ?? 'left')
        : 'left';
    $initialBentoVariant = ($initialSettings['bento_variant'] ?? 'travel') === 'article_filter'
        ? 'article_filter'
        : 'travel';
    $initialGalleryMediaIds = collect($initialContent['gallery_media_ids'] ?? [])
        ->map(fn ($id) => (string) $id)
        ->filter()
        ->unique()
        ->values()
        ->all();

    $blocks = [
        'hero' => ['label' => 'ฮีโร่', 'description' => 'หัวหน้าใหญ่พร้อมปุ่ม'],
        'banner' => ['label' => 'แบนเนอร์', 'description' => 'แบนเนอร์กว้าง สัดส่วน 1920 x 540 ใช้การตั้งเหมือนฮีโร่'],
        'rich_text' => ['label' => 'ข้อความ', 'description' => 'หัวข้อและเนื้อหายาว'],
        'image_text' => ['label' => 'รูป + ข้อความ', 'description' => 'วางภาพคู่กับรายละเอียด'],
        'cta' => ['label' => 'ปุ่มเชิญชวน', 'description' => 'ข้อความสั้นพร้อมปุ่ม'],
        'article_grid' => ['label' => 'รายการ', 'description' => 'ดึงมาแสดงอัตโนมัติ'],
        'temple_grid' => ['label' => 'รายการ', 'description' => 'ดึงมาแสดงอัตโนมัติ'],
        'travel_discovery_bento' => ['label' => 'แนะนำทริปแบบ Bento', 'description' => 'เซกชันแนะนำหมวดท่องเที่ยวแบบ bento grid'],
        'favorites_list' => ['label' => 'รายการโปรด', 'description' => 'แสดงรายการโปรด แยกและ'],
        'article_list_full' => ['label' => 'หน้ารวม', 'description' => 'list เต็มพร้อมค้นหาและแบ่งหน้า'],
        'temple_list_full' => ['label' => 'หน้ารวม', 'description' => 'list เต็มพร้อมตัวกรองและแบ่งหน้า'],
        'gallery' => ['label' => 'แกลเลอรี', 'description' => 'รวมรูปหลายรูปจากคลังสื่อหรือ URL'],
        'faq' => ['label' => 'คำถามที่พบบ่อย', 'description' => 'คำถามคำตอบที่พบบ่อย'],
        'stats' => ['label' => 'ตัวเลขสำคัญ', 'description' => 'สถิติหรือจุดเด่นแบบสั้น'],
        'contact' => ['label' => 'ข้อมูลติดต่อ', 'description' => 'ที่อยู่ เบอร์โทร และอีเมล'],
    ];
@endphp

<div
    class="space-y-6"
    x-data="{
        activeTab: 'content',
        component: @js($initialComponent),
        content: {
            eyebrow: @js($initialContent['eyebrow'] ?? ''),
            title: @js($initialContent['title'] ?? ''),
            subtitle: @js($initialContent['subtitle'] ?? ''),
            body: @js($initialContent['body'] ?? ''),
            primary_enabled: @js((bool) ($initialContent['primary_enabled'] ?? true)),
            primary_label: @js($initialContent['primary_label'] ?? ''),
            primary_url: @js($initialContent['primary_url'] ?? ''),
            primary_page_id: @js((string) ($initialContent['primary_page_id'] ?? '')),
            secondary_enabled: @js((bool) ($initialContent['secondary_enabled'] ?? true)),
            secondary_label: @js($initialContent['secondary_label'] ?? ''),
            secondary_url: @js($initialContent['secondary_url'] ?? ''),
            secondary_page_id: @js((string) ($initialContent['secondary_page_id'] ?? '')),
            all_button_enabled: @js((bool) ($initialContent['all_button_enabled'] ?? true)),
            all_button_label: @js($initialContent['all_button_label'] ?? ''),
            all_button_url: @js($initialContent['all_button_url'] ?? ''),
            all_button_page_id: @js((string) ($initialContent['all_button_page_id'] ?? '')),
            image_media_id: @js((string) ($initialContent['image_media_id'] ?? '')),
            image_url: @js($initialContent['image_url'] ?? ''),
            gallery_text: @js($initialContent['gallery_text'] ?? ''),
            gallery_media_ids: @js($initialGalleryMediaIds),
            faq_text: @js($initialContent['faq_text'] ?? ''),
            stats_text: @js($initialContent['stats_text'] ?? ''),
            bento_slots: @js($initialBentoSlots),
            bento_note_label: @js($initialContent['bento_note_label'] ?? ''),
            bento_note_text: @js($initialContent['bento_note_text'] ?? ''),
            phone: @js($initialContent['phone'] ?? ''),
            email: @js($initialContent['email'] ?? ''),
            address: @js($initialContent['address'] ?? ''),
            map_url: @js($initialContent['map_url'] ?? ''),
            empty_title: @js($initialContent['empty_title'] ?? 'ยังไม่มีรายการโปรด'),
            empty_subtitle: @js($initialContent['empty_subtitle'] ?? 'กดปุ่มหัวใจในหน้าหรือเพื่อเพิ่มรายการโปรด'),
            temple_eyebrow: @js($initialContent['temple_eyebrow'] ?? 'Temples'),
            temple_title: @js($initialContent['temple_title'] ?? 'ที่บันทึกไว้'),
            temple_card_label: @js($initialContent['temple_card_label'] ?? ''),
            article_eyebrow: @js($initialContent['article_eyebrow'] ?? 'Articles'),
            article_title: @js($initialContent['article_title'] ?? 'ที่บันทึกไว้'),
            article_card_label: @js($initialContent['article_card_label'] ?? ''),
            section_count_suffix: @js($initialContent['section_count_suffix'] ?? 'รายการ'),
            favorite_count_suffix: @js($initialContent['favorite_count_suffix'] ?? 'รายการโปรด'),
            open_label: @js($initialContent['open_label'] ?? 'เปิดดู'),
            remove_label: @js($initialContent['remove_label'] ?? 'ลบ'),
            search_label: @js($initialContent['search_label'] ?? 'ค้นหา'),
            search_placeholder: @js($initialContent['search_placeholder'] ?? ''),
            search_button_label: @js($initialContent['search_button_label'] ?? 'ค้นหา'),
            submit_label: @js($initialContent['submit_label'] ?? 'ค้นหา'),
            clear_label: @js($initialContent['clear_label'] ?? 'ล้างตัวกรอง'),
            empty_text: @js($initialContent['empty_text'] ?? ''),
            empty_excerpt: @js($initialContent['empty_excerpt'] ?? 'ยังไม่มีคำโปรย'),
            empty_image_text: @js($initialContent['empty_image_text'] ?? ''),
            article_meta_fallback: @js($initialContent['article_meta_fallback'] ?? 'เผยแพร่แล้ว'),
            province_fallback: @js($initialContent['province_fallback'] ?? 'ไม่ระบุจังห'),
            total_label: @js($initialContent['total_label'] ?? 'ทั้งหมด'),
            total_suffix: @js($initialContent['total_suffix'] ?? ''),
            all_option_label: @js($initialContent['all_option_label'] ?? 'ทั้งหมด'),
            category_filter_label: @js($initialContent['category_filter_label'] ?? 'หมวดหมู่'),
            tag_filter_label: @js($initialContent['tag_filter_label'] ?? 'แท็ก'),
            author_filter_label: @js($initialContent['author_filter_label'] ?? 'ผู้เขียน'),
            sort_filter_label: @js($initialContent['sort_filter_label'] ?? 'เรียงตาม'),
            latest_option_label: @js($initialContent['latest_option_label'] ?? 'ล่าสุด'),
            popular_option_label: @js($initialContent['popular_option_label'] ?? ''),
            likes_option_label: @js($initialContent['likes_option_label'] ?? 'ถูกใจมากสุด'),
            oldest_option_label: @js($initialContent['oldest_option_label'] ?? 'เก่าสุด'),
            rating_option_label: @js($initialContent['rating_option_label'] ?? 'รีวิวดีที่สุด'),
            province_all_label: @js($initialContent['province_all_label'] ?? 'ทุกจังห'),
            category_all_label: @js($initialContent['category_all_label'] ?? 'ทุกหมวดหมู่'),
            sort_default_label: @js($initialContent['sort_default_label'] ?? 'เรียงตามระบบ'),
            phone_label: @js($initialContent['phone_label'] ?? 'โทร:'),
            email_label: @js($initialContent['email_label'] ?? 'อีเมล:'),
            map_button_label: @js($initialContent['map_button_label'] ?? 'เปิดแผนที่'),
            show_phone: @js((bool) ($initialContent['show_phone'] ?? true)),
            show_email: @js((bool) ($initialContent['show_email'] ?? true)),
            show_map_button: @js((bool) ($initialContent['show_map_button'] ?? true)),
            temple_stat_label: @js($initialContent['temple_stat_label'] ?? 'ทั้งหมด'),
            article_stat_label: @js($initialContent['article_stat_label'] ?? 'ทั้งหมด'),
            view_stat_label: @js($initialContent['view_stat_label'] ?? 'ยอดผู้เข้าชม')
        },
        settings: {
            background_color: @js($initialBackgroundColor),
            background_gradient: @js((bool) ($initialSettings['background_gradient'] ?? false)),
            background_color_end: @js($initialBackgroundColorEnd),
            background_gradient_direction: @js($initialSettings['background_gradient_direction'] ?? 'to bottom'),
            align: @js($initialSettings['align'] ?? 'center'),
            layout: @js($initialSettings['layout'] ?? 'image_right'),
            source: @js($initialSettings['source'] ?? 'featured'),
            limit: @js((int) ($initialSettings['limit'] ?? 4)),
            slider_threshold: @js((int) ($initialSettings['slider_threshold'] ?? 4)),
            list_rows: @js((int) ($initialSettings['list_rows'] ?? 4)),
            list_columns: @js((int) ($initialSettings['list_columns'] ?? 4)),
            banner_height: @js((int) ($initialSettings['banner_height'] ?? 540)),
            bento_variant: @js($initialBentoVariant),
            bento_content_type: @js(($initialSettings['bento_content_type'] ?? '') === '' ? '' : ''),
            bento_layout: @js($initialSettings['bento_layout'] ?? 'mosaic_5'),
            bento_content_align: @js($initialBentoContentAlign),
            image_opacity: @js((int) ($initialSettings['image_opacity'] ?? 100)),
            image_fit: @js($initialSettings['image_fit'] ?? 'contain'),
            image_position: @js($initialSettings['image_position'] ?? 'center'),
            show_search_box: @js((bool) ($initialSettings['show_search_box'] ?? false)),
            show_summary_stats: @js((bool) ($initialSettings['show_summary_stats'] ?? false)),
            text_color: @js($initialSettings['text_color'] ?? '#ffffff'),
            font_size: @js($initialSettings['font_size'] ?? 'base'),
            font_weight: @js($initialSettings['font_weight'] ?? 'normal'),
            text_align: @js($initialSettings['text_align'] ?? 'inherit'),
            spacing_padding: @js($initialSettings['spacing_padding'] ?? 'default'),
            spacing_margin: @js($initialSettings['spacing_margin'] ?? 'none'),
            button_style: @js($initialSettings['button_style'] ?? 'solid'),
            button_radius: @js($initialSettings['button_radius'] ?? '2xl'),
            border_radius: @js($initialSettings['border_radius'] ?? 'none'),
            layout_width: @js($initialSettings['layout_width'] ?? '7xl'),
            visibility: @js($initialSettings['visibility'] ?? 'all'),
            animation_type: @js($initialSettings['animation_type'] ?? 'none'),
            animation_duration: @js((int) ($initialSettings['animation_duration'] ?? 500)),
            animation_delay: @js((int) ($initialSettings['animation_delay'] ?? 0)),
            animation_class: @js($initialSettings['animation_class'] ?? ''),
            custom_animation_css: @js($initialSettings['custom_animation_css'] ?? '')
        },
        bentoLayouts: @js($bentoLayoutPresets),
        bentoContentOptions: @js($bentoContents->mapWithKeys(fn ($content) => [
            (string) $content->id => [
                'title' => $content->title,
                'type' => $content->content_type === '' ? '' : '',
                'excerpt' => $content->excerpt,
            ],
        ])),
        linkPageOptions: @js($linkหน้า->map(fn ($page) => [
            'id' => (string) $page->id,
            'title' => $page->title,
            'label' => $page->title . ($page->is_homepage ? ' (หน้าแรก)' : ($page->slug ? ' (/' . $page->slug . ')' : '')) . ($page->status !== 'published' ? ' - ' . $page->status : ''),
            'search' => mb_strtolower($page->title . ' ' . $page->slug . ' ' . $page->status . ' ' . $page->id),
        ])->values()),
        pageSearch: {
            primary: '',
            secondary: '',
            all: '',
        },
        bentoSearch: '',
        bentoTypeFilter: 'all',
        mediaSearch: '',
        selectedImage: @js((string) ($initialContent['image_media_id'] ?? '')),
        mediaHtml: @js(view('admin.content.layout.page-sections.partials._media_grid', [
            'mediaItems' => $sectionMediaItems,
        ])->render()),
        galleryMediaHtml: @js(view('admin.content.layout.page-sections.partials._gallery_media_grid', [
            'mediaItems' => $sectionMediaItems,
        ])->render()),
        galleryMediaSearch: '',
        isUploading: false,
        uploadError: '',
        pick(type) {
            this.component = type;
            if (! this.content.title) {
                const labels = {
                    hero: 'หัวข้อหลักของหน้า',
                    banner: 'แบนเนอร์หลัก',
                    rich_text: 'หัวข้อเนื้อหา',
                    image_text: 'หัวข้อพร้อมรูปภาพ',
                    cta: 'พร้อมเริ่มต้นใช้งาน',
                    article_grid: 'แนะนำ',
                    temple_grid: 'แนะนำ',
                    travel_discovery_bento: 'วางแผนเที่ยวในแบบของคุณ',
                    favorites_list: 'รายการโปรดของฉัน',
                    article_list_full: 'ทั้งหมด',
                    temple_list_full: 'รวมทั่วไทย',
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
        galleryImageIds() {
            return Array.isArray(this.content.gallery_media_ids) ? this.content.gallery_media_ids : [];
        },
        isGalleryImageSelected(mediaId) {
            return this.galleryImageIds().includes(String(mediaId));
        },
        toggleGalleryImage(mediaId) {
            const id = String(mediaId);
            const ids = this.galleryImageIds();

            this.content.gallery_media_ids = ids.includes(id)
                ? ids.filter((selectedId) => selectedId !== id)
                : [...ids, id];
        },
        clearGalleryImages() {
            this.content.gallery_media_ids = [];
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
        bentoPreviewAreas() {
            const layouts = {
                1: ['a a a a'],
                2: ['a a b b'],
                3: ['a a b b', 'a a c c'],
                4: ['a a b b', 'c c d d'],
                5: ['a a b c', 'a a d e'],
                6: ['a a b b', 'c c d d', 'e e f f'],
                7: ['a a b c', 'a a d e', 'f f g g'],
                8: ['a a b b', 'c c d d', 'e e f f', 'g g h h'],
                9: ['a a b c', 'a a d e', 'f g h i'],
            };
            const count = Math.max(1, Math.min(this.content.bento_slots.length, 9));

            return layouts[count] || layouts[9];
        },
        bentoPreviewGridStyle() {
            const areas = this.bentoPreviewAreas()
                .map((row) => `'${row}'`)
                .join(' ');

            return `grid-template-columns: repeat(4, minmax(0, 1fr)); grid-template-areas: ${areas}; grid-auto-rows: minmax(8rem, auto);`;
        },
        bentoPreviewItemStyle(index) {
            const areaNames = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

            return `grid-area: ${areaNames[index] || 'i'};`;
        },
        bentoContentหัวข้อ(contentId) {
            return this.bentoContentOptions[contentId]?.title || 'ยังไม่ได้เลือก content';
        },
        bentoContentMeta(contentId) {
            const item = this.bentoContentOptions[contentId];
            return item ? item.type : 'เลือก content';
        },
        bentoContentExcerpt(contentId) {
            return this.bentoContentOptions[contentId]?.excerpt || 'ระบบจะแสดงคำโปรยหรือรายละเอียดเบื้องต้นของ content ตรงนี้';
        },
        filteredLinkหน้า(key, currentPageId = '') {
            const search = (this.pageSearch[key] || '').toLowerCase().trim();

            return this.linkPageOptions
                .filter((page) => {
                    if (currentPageId && page.id === String(currentPageId)) {
                        return true;
                    }

                    return !search || page.search.includes(search);
                })
                .slice(0, 80);
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
        async loadGalleryMediaPage(event) {
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
                this.galleryMediaHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.galleryMediaPicker);
                });
            }
        },
        async uploadImage(inputRef = 'sectionFileInput') {
            this.uploadError = '';

            const files = Array.from(this.$refs[inputRef]?.files || []);

            if (files.length === 0) {
                this.uploadError = 'กรุณาเลือกรูปก่อนอัปโหลด';
                return;
            }

            const maxFileSize = 5 * 1024 * 1024;
            const invalidFile = files.find((file) => !file.type.startsWith('image/'));
            const oversizedFile = files.find((file) => file.size > maxFileSize);

            if (invalidFile) {
                this.uploadError = 'อัปโหลดได้เฉพาะไฟล์รูปภาพเท่านั้น';
                return;
            }

            if (oversizedFile) {
                this.uploadError = `ไฟล์ ${oversizedFile.name} มีขนาดเกิน 5 MB`;
                return;
            }

            const formData = new FormData();
            formData.append('_token', @js(csrf_token()));
            formData.append('visibility', 'public');
            files.forEach((file) => formData.append('files[]', file));

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
                    const payload = await response.json().catch(() => null);
                    this.uploadError = payload?.message || 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
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
    <input type="hidden" name="section_id" value="{{ $section->id ?? '' }}">

    <div class="space-y-6">
        <section
            class="overflow-hidden rounded-3xl border border-white/10 bg-slate-950/60 shadow-xl shadow-slate-950/30 backdrop-blur"
            data-cms-section-preview
            data-preview-url="{{ route('admin.content.pages.sections.preview', $page) }}"
        >
            <div class="border-b border-white/10 px-5 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Section Studio</p>
                        <h2 class="mt-1 text-lg font-semibold text-white">ออกแบบเซกชัน</h2>
                    </div>
                    <div
                        class="hidden w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200"
                        data-cms-section-preview-loading
                    >
                        กำลังอัปเดตตัวอย่าง...
                    </div>
                </div>
            </div>

            <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="min-w-0 border-b border-white/10 xl:border-b-0 xl:border-r">
                    <div class="flex items-center justify-between gap-3 border-b border-white/10 bg-black/20 px-5 py-3">
                        <div>
                            <p class="text-sm font-semibold text-white">ตัวอย่างเซกชัน</p>
                            <p class="mt-0.5 text-xs text-slate-500">อัปเดตอัตโนมัติเมื่อแก้ข้อมูล</p>
                        </div>
                    </div>

                    <div class="relative h-[320px] bg-slate-950 md:h-[420px]">
                        <iframe
                            title="ตัวอย่างเซกชันแบบเรียลไทม์"
                            class="h-full w-full bg-slate-950"
                            sandbox="allow-scripts allow-same-origin"
                            data-cms-section-preview-frame
                        ></iframe>
                    </div>

                    <div
                        class="hidden border-t border-rose-400/20 bg-rose-500/10 px-5 py-3 text-sm text-rose-200"
                        data-cms-section-preview-error
                    ></div>
                </div>

                <aside class="space-y-5 bg-white/[0.03] p-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">ชนิดเซกชัน</label>
                        <select
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm font-semibold text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                            :value="component"
                            @change="pick($event.target.value)"
                        >
                            @foreach($blocks as $key => $block)
                                <option value="{{ $key }}" class="bg-slate-900">{{ $block['label'] }}</option>
                            @endforeach
                        </select>

                        <div class="mt-3 rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                            @foreach($blocks as $key => $block)
                                <div x-show="component === @js($key)" x-cloak>
                                    <p class="text-sm font-semibold text-white">{{ $block['label'] }}</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-400">{{ $block['description'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        @error('component_key')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div>
                            <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">สถานะ</label>
                            <select id="status" name="status" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                <option value="active" class="bg-slate-900" @selected($initialสถานะ === 'active')>เปิดใช้งาน</option>
                                <option value="inactive" class="bg-slate-900" @selected($initialสถานะ === 'inactive')>ปิดใช้งาน</option>
                            </select>
                        </div>

                        <div>
                            <label for="sort_order" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">ลำดับ</label>
                            <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $section->sort_order ?? 0) }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            @error('sort_order')
                                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        <input id="is_visible" type="checkbox" name="is_visible" value="1" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20" @checked($initialVisible)>
                        <span>
                            <span class="block text-sm font-medium text-white">แสดงบนหน้าเว็บ</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">ปิดไว้ได้ถ้าต้องการเตรียมเซกชันโดยยังไม่เผยแพร่</span>
                        </span>
                    </label>

                    <details class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        <summary class="cursor-pointer list-none text-sm font-semibold text-white">ตั้งค่าขั้นสูง</summary>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อเซกชันในระบบ</label>
                                <input id="name" type="text" name="name" value="{{ $sectionชื่อ }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เว้นว่างได้">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-300">รหัสเซกชัน</label>
                                <input type="text" value="{{ $sectionKey ?: 'ระบบจะสร้างให้อัตโนมัติ' }}" disabled class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-500">
                            </div>
                        </div>
                    </details>
                </aside>
            </div>
        </section>

        <div class="min-w-0 space-y-6">
            <div class="sticky top-4 z-20 rounded-3xl border border-white/10 bg-slate-950/90 p-2 shadow-xl shadow-slate-950/30 backdrop-blur">
                <div class="grid gap-2 md:grid-cols-3">
                    <button
                        type="button"
                        @click="activeTab = 'content'"
                        :class="activeTab === 'content' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'"
                        class="rounded-2xl border px-4 py-3 text-sm font-semibold transition"
                    >
                        เนื้อหา
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'media'"
                        :class="activeTab === 'media' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'"
                        class="rounded-2xl border px-4 py-3 text-sm font-semibold transition"
                    >
                        สื่อ
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'design'"
                        :class="activeTab === 'design' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'"
                        class="rounded-2xl border px-4 py-3 text-sm font-semibold transition"
                    >
                        หน้าตา แอนิเมชัน และการแสดงผล
                    </button>
                </div>
            </div>

    <div x-show="['content', 'media'].includes(activeTab)" x-cloak class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <p class="text-sm font-medium text-blue-300" x-text="activeTab === 'media' ? 'ส่วนที่ 2' : 'ส่วนที่ 1'"></p>
            <h2 class="mt-1 text-lg font-semibold text-white" x-text="activeTab === 'media' ? 'สื่อและรูปภาพ' : 'เนื้อหาหลัก'"></h2>
            <p class="mt-1 text-sm text-slate-400" x-text="activeTab === 'media' ? 'อัปโหลด เลือกรูปภาพ จัดการแกลเลอรี และตั้งค่า URL รูปภาพสำรอง' : 'จัดการหัวข้อ คำอธิบาย ข้อความสำรอง และรายละเอียดเฉพาะเซกชัน'"></p>
        </div>

        <div class="space-y-5 cms-section-tab-body" :class="activeTab === 'media' ? 'cms-section-media-mode' : 'cms-section-content-mode'">
            <div x-show="['hero', 'banner', 'image_text', 'rich_text', 'cta', 'article_grid', 'temple_grid', 'travel_discovery_bento', 'favorites_list', 'article_list_full', 'temple_list_full', 'gallery', 'faq', 'stats', 'contact'].includes(component)" x-cloak>
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

            <div x-show="component === 'favorites_list'" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">ข้อความทั้งหมดในเซกชันรายการโปรด</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">แก้ label ที่แสดงใน empty state, หัวข้อกลุ่ม, การ์ด และปุ่มได้จากตรงนี้</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">หัวข้อเมื่อยังไม่มีรายการ</label>
                        <input type="text" x-model="content.empty_title" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายเมื่อยังไม่มีรายการ</label>
                        <input type="text" x-model="content.empty_subtitle" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเล็กกลุ่ม</label>
                        <input type="text" x-model="content.temple_eyebrow" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">หัวข้อกลุ่ม</label>
                        <input type="text" x-model="content.temple_title" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายประเภทบนการ์ด</label>
                        <input type="text" x-model="content.temple_card_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเล็กกลุ่ม</label>
                        <input type="text" x-model="content.article_eyebrow" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">หัวข้อกลุ่ม</label>
                        <input type="text" x-model="content.article_title" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายประเภทบนการ์ด</label>
                        <input type="text" x-model="content.article_card_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">คำต่อท้ายจำนวนในหัวข้อกลุ่ม</label>
                        <input type="text" x-model="content.section_count_suffix" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น รายการ">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">คำต่อท้ายยอดบนการ์ด</label>
                        <input type="text" x-model="content.favorite_count_suffix" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น รายการโปรด">
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มเปิดดู</label>
                        <input type="text" x-model="content.open_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มลบ</label>
                        <input type="text" x-model="content.remove_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>
            </div>

            <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full', 'gallery', 'faq', 'stats', 'travel_discovery_bento', 'image_text'].includes(component)" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">ข้อความสถานะว่างและข้อความสำรอง</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">ใช้เมื่อลิสต์ไม่มีข้อมูล ไม่มีรูป หรือรายการนั้นไม่มีคำโปรย</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full', 'gallery', 'faq', 'stats', 'travel_discovery_bento'].includes(component)" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเมื่อไม่มีรายการ</label>
                        <input type="text" x-model="content.empty_text" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full'].includes(component)" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">คำโปรยสำรอง</label>
                        <input type="text" x-model="content.empty_excerpt" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full', 'image_text'].includes(component)" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเมื่อไม่มีรูป</label>
                        <input type="text" x-model="content.empty_image_text" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div x-show="['article_grid', 'article_list_full'].includes(component)" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความวันที่สำรองบนการ์ด</label>
                    <input type="text" x-model="content.article_meta_fallback" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>

                <div x-show="['temple_grid', 'temple_list_full'].includes(component)" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความจังหสำรองบนการ์ด</label>
                    <input type="text" x-model="content.province_fallback" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>
            </div>

            <div x-show="['article_list_full', 'temple_list_full', 'hero', 'banner'].includes(component)" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">ข้อความค้นหาและตัวกรอง</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">ทุกข้อความในฟอร์มค้นหาและปุ่ม action สามารถแก้ได้จากตรงนี้</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div x-show="component === 'article_list_full'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายกำกับช่องค้นหา</label>
                        <input type="text" x-model="content.search_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">Placeholder ค้นหา</label>
                        <input type="text" x-model="content.search_placeholder" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มค้นหา</label>
                        <input type="text" x-model="content.search_button_label" x-show="['hero', 'banner'].includes(component)" x-cloak class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                        <input type="text" x-model="content.submit_label" x-show="['article_list_full', 'temple_list_full'].includes(component)" x-cloak class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div x-show="['article_list_full', 'temple_list_full'].includes(component)" x-cloak class="grid gap-5 lg:grid-cols-4">
                    <input type="text" x-model="content.clear_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ล้างตัวกรอง">
                    <input type="text" x-model="content.total_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ทั้งหมด">
                    <input type="text" x-model="content.latest_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ล่าสุด">
                    <input type="text" x-model="content.popular_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ยอดนิยม">
                </div>

                <div x-show="component === 'article_list_full'" x-cloak class="grid gap-5 lg:grid-cols-4">
                    <input type="text" x-model="content.category_filter_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="หมวดหมู่">
                    <input type="text" x-model="content.tag_filter_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="แท็ก">
                    <input type="text" x-model="content.author_filter_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ผู้เขียน">
                    <input type="text" x-model="content.sort_filter_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เรียงตาม">
                    <input type="text" x-model="content.all_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ทั้งหมด">
                    <input type="text" x-model="content.likes_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ถูกใจมากสุด">
                    <input type="text" x-model="content.oldest_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เก่าสุด">
                </div>

                <div x-show="component === 'temple_list_full'" x-cloak class="grid gap-5 lg:grid-cols-4">
                    <input type="text" x-model="content.province_all_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ทุกจังห">
                    <input type="text" x-model="content.category_all_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ทุกหมวดหมู่">
                    <input type="text" x-model="content.sort_default_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เรียงตามระบบ">
                    <input type="text" x-model="content.rating_option_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="รีวิวดีที่สุด">
                    <input type="text" x-model="content.total_suffix" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="">
                </div>
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

            <div x-show="['hero', 'banner', 'image_text'].includes(component)" x-cloak class="cms-media-panel space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">รูปภาพของเซกชัน</h3>
                    <p class="mt-1 text-xs text-slate-500">อัปโหลดรูปใหม่หรือเลือกรูปจากคลังสื่อเหมือนหน้าและ</p>
                </div>

                <div class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">อัปโหลดรูปใหม่</label>
                            <input
                                type="file"
                                accept="image/*"
                                multiple
                                x-ref="sectionFileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-blue-600"
                            >
                            <p x-show="uploadError" x-text="uploadError" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">เลือกได้หลายรูป ขนาดไม่เกิน 5 MB ต่อรูป ระบบจะบันทึกเข้าคลังสื่อแล้วโหลดหน้าใหม่</p>
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

                <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                    <p class="text-xs font-medium text-blue-200">รูปที่เลือกอยู่</p>
                    <p class="mt-1 text-sm text-white" x-text="selectedImage ? `Media ID #${selectedImage}` : 'ไม่ใช้รูปจากคลังสื่อ'"></p>
                    <p class="mt-1 text-xs text-slate-400" x-show="content.image_url && !selectedImage">ใช้ URL สำรอง: <span x-text="content.image_url"></span></p>
                </div>

                <div
                    x-ref="sectionMediaPicker"
                    x-html="mediaHtml"
                    @click="loadMediaPage($event)"
                ></div>
            </div>

            <div x-show="component === 'gallery'" x-cloak class="cms-media-panel space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white">รูปในแกลเลอรี</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">ติ๊กเลือกรูปจากคลังสื่อได้เลย ระบบจะใช้ชื่อรูปเป็นคำอธิบายใต้ภาพ</p>
                    </div>
                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-sm text-blue-100">
                        เลือกอยู่ <span class="font-semibold" x-text="galleryImageIds().length"></span> รูป
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">อัปโหลดรูปใหม่</label>
                            <input
                                type="file"
                                accept="image/*"
                                multiple
                                x-ref="galleryFileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-blue-600"
                            >
                            <p x-show="uploadError" x-text="uploadError" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">เลือกได้หลายรูป ขนาดไม่เกิน 5 MB ต่อรูป หลังอัปโหลดเสร็จระบบจะ refresh เพื่อให้เลือกรูปใหม่จากรายการได้ทันที</p>
                        </div>

                        <button
                            type="button"
                            @click="uploadImage('galleryFileInput')"
                            :disabled="isUploading"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!isUploading">อัปโหลด</span>
                            <span x-show="isUploading">กำลังอัปโหลด...</span>
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ค้นหารูปจากชื่อ</label>
                        <input
                            type="text"
                            x-model="galleryMediaSearch"
                            placeholder="พิมพ์ชื่อรูป, title, ชื่อไฟล์ หรือ Media ID..."
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        <p class="mt-1 text-xs text-slate-500">ค้นหาเฉพาะรูปที่แสดงอยู่ในหน้าปัจจุบัน ถ้าต้องการรูปเก่ากว่านี้ให้เปลี่ยนหน้าในรายการรูป</p>
                    </div>

                    <button
                        type="button"
                        x-show="galleryImageIds().length"
                        @click="clearGalleryImages()"
                        class="rounded-xl border border-white/10 bg-white/[0.06] px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >
                        ล้างรูปที่เลือก
                    </button>
                </div>

                <div
                    x-show="galleryImageIds().length"
                    x-cloak
                    class="rounded-2xl border border-white/10 bg-white/[0.04] p-4"
                >
                    <p class="text-xs font-medium text-slate-400">ลำดับรูปที่เลือก</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <template x-for="(mediaId, index) in galleryImageIds()" :key="mediaId">
                            <span class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-100">
                                <span x-text="`${index + 1}. #${mediaId}`"></span>
                                <button type="button" class="text-blue-100/70 hover:text-white" @click="toggleGalleryImage(mediaId)">ลบ</button>
                            </span>
                        </template>
                    </div>
                </div>

                <div
                    x-ref="galleryMediaPicker"
                    x-html="galleryMediaHtml"
                    @click="loadGalleryMediaPage($event)"
                ></div>
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
                    placeholder="ใส่ 1 รายการต่อ 1 บรรทัด เช่น&#10;120+ | ในระบบ"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: ตัวเลข | คำอธิบาย</p>
            </div>

            <div x-show="component === 'travel_discovery_bento'" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">รูปแบบ Bento</label>
                        <select x-model="settings.bento_variant" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="travel" class="bg-slate-900">เลือกเนื้อหาเอง</option>
                            <option value="article_filter" class="bg-slate-900">สุ่มจากประเภทและหมวดหมู่</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ตำแหน่งเนื้อหาเซกชัน</label>
                        <select x-model="settings.bento_content_align" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="left" class="bg-slate-900">ชิดซ้าย</option>
                            <option value="center" class="bg-slate-900">กึ่งกลาง</option>
                            <option value="right" class="bg-slate-900">ชิดขวา</option>
                        </select>
                    </div>
                    <div x-show="settings.bento_variant === 'article_filter'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนที่สุ่มแสดง</label>
                        <input type="number" min="1" max="12" x-model.number="settings.limit" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <div x-show="settings.bento_variant === 'article_filter'" x-cloak class="space-y-4">
                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                        <p class="text-sm font-medium text-blue-100">Bento แบบตัวกรอง</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">เลือกแหล่งข้อมูลเป็นหรือ แล้วหน้าเว็บจะแสดงตัวกรองจากหมวดหมู่ของเนื้อหาประเภทนั้นให้ผู้ใช้เลือกสายที่สนใจ</p>
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-slate-400">แหล่งข้อมูล</label>
                            <select x-model="settings.bento_content_type" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                <option value="" class="bg-slate-900"></option>
                                <option value="" class="bg-slate-900"></option>
                            </select>
                            <p class="mt-1 text-xs leading-5 text-slate-500">ถ้าเลือก filter จะเป็นหมวดหมู่ ถ้าเลือก filter จะเป็นหมวดหมู่</p>
                        </div>
                        <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ชุดขนาดกล่อง</label>
                            <select @change="applyBentoLayout($event.target.value)" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                <template x-for="(layout, key) in bentoLayouts" :key="key">
                                    <option :value="key" class="bg-slate-900" x-text="layout.label" :selected="settings.bento_layout === key"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <div x-show="settings.bento_variant !== 'article_filter'" x-cloak class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white">รายการใน Bento Grid</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">กดเพิ่มกล่องได้เอง สูงสุด 9 กล่อง แต่ละกล่องเลือกเนื้อหาและสัดส่วนได้</p>
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

                <div x-show="settings.bento_variant !== 'article_filter'" x-cloak class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                        <p class="text-sm font-medium text-blue-100">เลือกเนื้อหาที่จะใส่ในแต่ละกล่อง</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">ระบบจะใช้ชื่อ คำโปรย รูปปก และลิงก์รายละเอียดของเนื้อหานั้นอัตโนมัติ</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ใช้ชุดเริ่มต้น</label>
                        <select @change="applyBentoLayout($event.target.value)" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <template x-for="(layout, key) in bentoLayouts" :key="key">
                                <option :value="key" class="bg-slate-900" x-text="layout.label" :selected="settings.bento_layout === key"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div x-show="settings.bento_variant !== 'article_filter'" x-cloak class="grid gap-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4 lg:grid-cols-[minmax(0,1fr)_180px]">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาเนื้อหา</label>
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
                            <option value="" class="bg-slate-900"></option>
                            <option value="" class="bg-slate-900"></option>
                        </select>
                    </div>
                </div>

                <div x-show="settings.bento_variant !== 'article_filter'" x-cloak class="grid gap-4 lg:grid-cols-2">
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
                                    <label class="mb-1.5 block text-xs font-medium text-slate-500">เนื้อหา</label>
                                    <select x-model="slot.content_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                        <option value="" class="bg-slate-900">เลือกเนื้อหา</option>
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

                <div x-show="settings.bento_variant !== 'article_filter' && content.bento_slots.length === 0" x-cloak class="rounded-2xl border border-dashed border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                    ยังไม่มีกล่อง กด “เพิ่มกล่อง” เพื่อเลือกเนื้อหา
                </div>

                <div x-show="settings.bento_variant !== 'article_filter' && content.bento_slots.length > 0" x-cloak class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">ตัวอย่าง Bento</h4>
                            <p class="mt-1 text-xs text-slate-500">ตัวอย่างสัดส่วนและตำแหน่งโดยประมาณบนเดสก์ท็อป</p>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-400">
                            <span x-text="content.bento_slots.length"></span>/9 กล่อง
                        </span>
                    </div>

                    <div class="grid gap-3" :style="bentoPreviewGridStyle()">
                        <template x-for="(slot, index) in bentoSlots()" :key="`preview-${index}`">
                            <div class="relative min-h-32 overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800 via-slate-900 to-blue-950 p-4 shadow-lg shadow-slate-950/20" :style="bentoPreviewItemStyle(index)">
                                <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/30"></div>
                                <div class="relative flex h-full flex-col justify-between gap-4">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-medium text-blue-100" x-text="bentoContentMeta(slot.content_id)"></span>
                                        <span class="rounded-full bg-black/20 px-2.5 py-1 text-[11px] text-white/70" x-text="bentoSizeLabel(slot.size)"></span>
                                    </div>
                                    <div>
                                        <p class="line-clamp-2 text-sm font-semibold text-white" x-text="bentoContentหัวข้อ(slot.content_id)"></p>
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
                <div class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-4 lg:col-span-2">
                    <h3 class="text-sm font-semibold text-white">ป้ายกำกับและการแสดงผล</h3>
                    <div class="grid gap-5 lg:grid-cols-3">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" x-model="content.show_phone" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">แสดงเบอร์โทร</span>
                        </label>
                        <label class="flex items-start gap-3">
                            <input type="checkbox" x-model="content.show_email" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">แสดงอีเมล</span>
                        </label>
                        <label class="flex items-start gap-3">
                            <input type="checkbox" x-model="content.show_map_button" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">แสดงปุ่มแผนที่</span>
                        </label>
                    </div>
                    <div class="grid gap-5 lg:grid-cols-3">
                        <input type="text" x-model="content.phone_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="โทร:">
                        <input type="text" x-model="content.email_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="อีเมล:">
                        <input type="text" x-model="content.map_button_label" class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เปิดแผนที่">
                    </div>
                </div>
            </div>

            <div x-show="['hero', 'banner', 'image_text'].includes(component)" x-cloak class="cms-media-panel">
                <label class="mb-1.5 block text-sm font-medium text-slate-300">URL รูปภาพสำรอง</label>
                <input
                    type="text"
                    x-model="content.image_url"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="https://... หรือ /storage/..."
                >
            </div>

            <div
                x-show="!['hero', 'banner', 'image_text', 'gallery'].includes(component)"
                x-cloak
                class="cms-media-panel rounded-2xl border border-dashed border-white/10 bg-slate-950/30 p-8 text-center"
            >
                <p class="text-sm font-semibold text-white">เซกชันนี้ไม่มีสื่อให้ตั้งค่า</p>
                <p class="mt-2 text-sm leading-6 text-slate-400">ถ้าเลือกชนิดฮีโร่ แบนเนอร์ รูป + ข้อความ หรือแกลเลอรี ระบบจะแสดงตัวเลือกรูปภาพในแท็บนี้</p>
            </div>

            <div x-show="['hero', 'banner', 'image_text', 'cta', 'travel_discovery_bento'].includes(component)" x-cloak class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">ปุ่มและลิงก์</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">เลือกหน้าจากระบบเพื่อกัน path ผิด หรือกรอก URL เองเมื่อเป็นลิงก์ภายนอก</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" x-model="content.primary_enabled" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span>
                                <span class="block text-sm font-medium text-white">แสดงปุ่มหลัก</span>
                                <span class="block text-xs text-slate-500">ใช้เป็นปุ่มสำคัญของเซกชัน</span>
                            </span>
                        </label>
	                        <div x-show="content.primary_enabled" x-cloak class="space-y-3">
	                            <input type="text" x-model="content.primary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ข้อความปุ่มหลัก">
	                            <input type="search" x-model.debounce.100ms="pageSearch.primary" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ค้นหาหน้าปลายทาง...">
	                            <select x-model="content.primary_page_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
	                                <option value="" class="bg-slate-900">ไม่เลือกหน้า ใช้ URL ด้านล่าง</option>
	                                <template x-for="page in filteredLinkหน้า('primary', content.primary_page_id)" :key="page.id">
	                                    <option :value="page.id" class="bg-slate-900" x-text="page.label"></option>
	                                </template>
	                            </select>
                            <input type="text" x-model="content.primary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="URL สำรอง เช่น /-list หรือ https://...">
                        </div>
                    </div>

                    <div class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" x-model="content.secondary_enabled" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span>
                                <span class="block text-sm font-medium text-white">แสดงปุ่มรอง</span>
                                <span class="block text-xs text-slate-500">เหมาะกับลิงก์เสริม</span>
                            </span>
                        </label>
	                        <div x-show="content.secondary_enabled" x-cloak class="space-y-3">
	                            <input type="text" x-model="content.secondary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ข้อความปุ่มรอง">
	                            <input type="search" x-model.debounce.100ms="pageSearch.secondary" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ค้นหาหน้าปลายทาง...">
	                            <select x-model="content.secondary_page_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
	                                <option value="" class="bg-slate-900">ไม่เลือกหน้า ใช้ URL ด้านล่าง</option>
	                                <template x-for="page in filteredLinkหน้า('secondary', content.secondary_page_id)" :key="page.id">
	                                    <option :value="page.id" class="bg-slate-900" x-text="page.label"></option>
	                                </template>
	                            </select>
                            <input type="text" x-model="content.secondary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="URL สำรอง เช่น /-list หรือ https://...">
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="['article_grid', 'temple_grid'].includes(component)" x-cloak class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="flex items-start gap-3">
                    <input id="all_button_enabled" type="checkbox" x-model="content.all_button_enabled" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="all_button_enabled" class="text-sm font-medium text-white">แสดงปุ่มดูทั้งหมด</label>
                    <p class="mt-1 text-xs leading-5 text-slate-400">ใช้กำหนดปุ่มด้านขวาของหัวข้อเซกชันรายการหรือรายการ</p>
                    </div>
                </div>

                <div x-show="content.all_button_enabled" x-cloak class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มดูทั้งหมด</label>
                        <input type="text" x-model="content.all_button_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น ดูทั้งหมด">
                    </div>
	                    <div>
	                        <label class="mb-1.5 block text-sm font-medium text-slate-300">หน้าปลายทาง</label>
	                        <input type="search" x-model.debounce.100ms="pageSearch.all" class="mb-2 w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="ค้นหาหน้าปลายทาง...">
	                        <select x-model="content.all_button_page_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
	                            <option value="" class="bg-slate-900">ใช้หน้ารวมเริ่มต้น</option>
	                            <template x-for="page in filteredLinkหน้า('all', content.all_button_page_id)" :key="page.id">
	                                <option :value="page.id" class="bg-slate-900" x-text="page.label"></option>
	                            </template>
	                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">URL สำรอง</label>
                        <input type="text" x-model="content.all_button_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="/-list หรือ https://...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'design'" x-cloak class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <p class="text-sm font-medium text-blue-300">ส่วนที่ 2</p>
            <h2 class="mt-1 text-lg font-semibold text-white">การแสดงผลและสถานะ</h2>
            <p class="mt-1 text-sm text-slate-400">ตั้งพื้นหลัง รูปภาพ แหล่งข้อมูล จำนวนรายการ และสถานะการเผยแพร่</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">สีพื้นหลังเซกชัน</label>
                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-medium text-slate-400">สีหลัก</span>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="settings.background_color" class="h-11 w-14 cursor-pointer rounded-xl border border-white/10 bg-slate-950/40 p-1">
                                <input type="text" x-model="settings.background_color" class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="#020617">
                            </div>
                        </label>

                        <label class="flex items-center gap-3 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-3">
                            <input type="checkbox" x-model="settings.background_gradient" class="h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">ไล่สีพื้นหลัง</span>
                        </label>
                    </div>

                    <div x-show="settings.background_gradient" x-cloak class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-medium text-slate-400">สีปลายทาง</span>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="settings.background_color_end" class="h-11 w-14 cursor-pointer rounded-xl border border-white/10 bg-slate-950/40 p-1">
                                <input type="text" x-model="settings.background_color_end" class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="#0f172a">
                            </div>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-medium text-slate-400">ทิศทางการไล่สี</span>
                            <select x-model="settings.background_gradient_direction" class="w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                <option value="to bottom" class="bg-slate-900">บนลงล่าง</option>
                                <option value="to top" class="bg-slate-900">ล่างขึ้นบน</option>
                                <option value="to right" class="bg-slate-900">ซ้ายไปขวา</option>
                                <option value="to left" class="bg-slate-900">ขวาไปซ้าย</option>
                                <option value="135deg" class="bg-slate-900">เฉียง</option>
                            </select>
                        </label>
                    </div>

                    <div class="mt-4 h-12 rounded-xl border border-white/10" :style="settings.background_gradient ? `background: linear-gradient(${settings.background_gradient_direction}, ${settings.background_color}, ${settings.background_color_end});` : `background-color: ${settings.background_color};`"></div>
                </div>
            </div>

            <div class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-5 lg:col-span-2">
                <div>
                    <h3 class="text-sm font-semibold text-white">ตั้งหน้าตา</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">ปรับสี ตัวอักษร ระยะห่าง ปุ่ม ขนาด layout และ animation แบบปลอดภัยโดยไม่ใช้ JavaScript ดิบ</p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">สีข้อความ</span>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="settings.text_color" class="h-11 w-14 cursor-pointer rounded-xl border border-white/10 bg-slate-950/40 p-1">
                            <input type="text" x-model="settings.text_color" class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="#ffffff">
                        </div>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ขนาดตัวอักษร</span>
                        <select x-model="settings.font_size" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="sm" class="bg-slate-900">เล็ก</option>
                            <option value="base" class="bg-slate-900">มาตรฐาน</option>
                            <option value="lg" class="bg-slate-900">ใหญ่</option>
                            <option value="xl" class="bg-slate-900">ใหญ่มาก</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">น้ำหนักตัวอักษร</span>
                        <select x-model="settings.font_weight" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="normal" class="bg-slate-900">ปกติ</option>
                            <option value="medium" class="bg-slate-900">กลาง</option>
                            <option value="semibold" class="bg-slate-900">กึ่งหนา</option>
                            <option value="bold" class="bg-slate-900">หนา</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">จัดแนวข้อความ</span>
                        <select x-model="settings.text_align" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="inherit" class="bg-slate-900">ตาม template</option>
                            <option value="left" class="bg-slate-900">ซ้าย</option>
                            <option value="center" class="bg-slate-900">กลาง</option>
                            <option value="right" class="bg-slate-900">ขวา</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ระยะขอบด้านใน</span>
                        <select x-model="settings.spacing_padding" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="default" class="bg-slate-900">มาตรฐาน</option>
                            <option value="compact" class="bg-slate-900">กระชับ</option>
                            <option value="spacious" class="bg-slate-900">โปร่ง</option>
                            <option value="none" class="bg-slate-900">ไม่มี</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ระยะห่างด้านนอก</span>
                        <select x-model="settings.spacing_margin" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="none" class="bg-slate-900">ไม่มี</option>
                            <option value="sm" class="bg-slate-900">เล็ก</option>
                            <option value="md" class="bg-slate-900">กลาง</option>
                            <option value="lg" class="bg-slate-900">ใหญ่</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">สไตล์ปุ่ม</span>
                        <select x-model="settings.button_style" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="solid" class="bg-slate-900">ทึบ</option>
                            <option value="outline" class="bg-slate-900">เส้นขอบ</option>
                            <option value="ghost" class="bg-slate-900">โปร่ง</option>
                            <option value="glass" class="bg-slate-900">กระจก</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ความโค้งปุ่ม</span>
                        <select x-model="settings.button_radius" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="lg" class="bg-slate-900">โค้งเล็ก</option>
                            <option value="2xl" class="bg-slate-900">2XL</option>
                            <option value="full" class="bg-slate-900">เม็ดยา</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ความโค้งกรอบ</span>
                        <select x-model="settings.border_radius" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="none" class="bg-slate-900">ไม่มี</option>
                            <option value="xl" class="bg-slate-900">XL</option>
                            <option value="2xl" class="bg-slate-900">2XL</option>
                            <option value="3xl" class="bg-slate-900">3XL</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ความกว้างเนื้อหา</span>
                        <select x-model="settings.layout_width" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="4xl" class="bg-slate-900">แคบ</option>
                            <option value="5xl" class="bg-slate-900">กลาง</option>
                            <option value="7xl" class="bg-slate-900">กว้าง</option>
                            <option value="full" class="bg-slate-900">เต็มพื้นที่</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">การแสดงผลตามอุปกรณ์</span>
                        <select x-model="settings.visibility" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="all" class="bg-slate-900">ทุกอุปกรณ์</option>
                            <option value="desktop" class="bg-slate-900">เฉพาะเดสก์ท็อป</option>
                            <option value="mobile" class="bg-slate-900">เฉพาะมือถือ</option>
                            <option value="hidden" class="bg-slate-900">ซ่อน</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">แอนิเมชัน</span>
                        <select x-model="settings.animation_type" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                            <option value="none" class="bg-slate-900">ไม่มี</option>
                            <option value="fade" class="bg-slate-900">ค่อย ๆ แสดง</option>
                            <option value="fade-up" class="bg-slate-900">เลื่อนขึ้นพร้อมเฟด</option>
                            <option value="fade-down" class="bg-slate-900">เลื่อนลงพร้อมเฟด</option>
                            <option value="slide-left" class="bg-slate-900">เลื่อนจากซ้าย</option>
                            <option value="slide-right" class="bg-slate-900">เลื่อนจากขวา</option>
                            <option value="zoom-in" class="bg-slate-900">ซูมเข้า</option>
                            <option value="zoom-out" class="bg-slate-900">ซูมออก</option>
                        </select>
                    </label>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">ระยะเวลาแอนิเมชัน (ms)</span>
                        <input type="number" min="100" max="3000" step="50" x-model.number="settings.animation_duration" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-400">หน่วงเวลาแอนิเมชัน (ms)</span>
                        <input type="number" min="0" max="3000" step="50" x-model.number="settings.animation_delay" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </label>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-white">CSS แอนิเมชันเพิ่มเติม</label>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            ต้องมี <span class="font-mono text-blue-200">{section}</span> อย่างน้อย 1 จุดเพื่อ scope CSS เฉพาะเซกชันนี้ ระบบไม่รับ JavaScript, @import และ url()
                        </p>
                    </div>
                    <textarea
                        x-model="settings.custom_animation_css"
                        rows="9"
                        class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 font-mono text-xs leading-6 text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                        placeholder="@keyframes mySectionIn {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

{section} {
  animation: mySectionIn 800ms ease both;
}"
                    ></textarea>
                </div>
            </div>

            <div x-show="component === 'image_text'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ตำแหน่งรูป</label>
                <select x-model="settings.layout" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="image_right" class="bg-slate-900">รูปอยู่ขวา</option>
                    <option value="image_left" class="bg-slate-900">รูปอยู่ซ้าย</option>
                </select>
            </div>

            <div x-show="component === 'banner'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ขนาดแบนเนอร์</label>
                <select x-model.number="settings.banner_height" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option :value="540" class="bg-slate-900">1920 x 540</option>
                    <option :value="720" class="bg-slate-900">1920 x 720</option>
                </select>
            </div>

            <div x-show="['hero', 'banner', 'image_text'].includes(component)" x-cloak>
                <div class="flex items-center justify-between gap-3">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ความทึบของรูป</label>
                    <span class="text-xs text-slate-500" x-text="`${settings.image_opacity}%`"></span>
                </div>
                <input type="range" min="10" max="100" step="5" x-model.number="settings.image_opacity" class="w-full accent-blue-500">
                <p class="mt-1 text-xs text-slate-500">100% คือแสดงรูปตามต้นฉบับ ยิ่งต่ำรูปยิ่งจาง</p>
            </div>

            <div x-show="['hero', 'banner', 'image_text'].includes(component)" x-cloak class="grid gap-5 lg:grid-cols-2">
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

            <div x-show="['hero', 'banner'].includes(component) || (component === 'travel_discovery_bento' && settings.bento_variant !== 'article_filter')" x-cloak class="space-y-3 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex items-start gap-3">
                    <input id="show_search_box" type="checkbox" x-model="settings.show_search_box" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="show_search_box" class="text-sm font-medium text-white">แสดงกล่องค้นหา</label>
                        <p x-show="['hero', 'banner'].includes(component)" class="mt-1 text-xs leading-5 text-slate-400">แสดงกล่องค้นหารวมทั้งและบนหน้าเว็บไซต์</p>
                        <p x-show="component === 'travel_discovery_bento'" class="mt-1 text-xs leading-5 text-slate-400">แสดงกล่องค้นหา โดยส่งไปหน้ารวม</p>
                    </div>
                </div>
                <div x-show="['hero', 'banner'].includes(component)" x-cloak class="flex items-start gap-3">
                    <input id="show_summary_stats" type="checkbox" x-model="settings.show_summary_stats" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20">
                    <div>
                        <label for="show_summary_stats" class="text-sm font-medium text-white">แสดงตัวเลขสรุป</label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">แสดงจำนวนทั้งหมด จำนวนทั้งหมด และยอดผู้เข้าชมทั้งหมดใต้หัวข้อ</p>
                    </div>
                </div>
                <div x-show="['hero', 'banner'].includes(component) && settings.show_summary_stats" x-cloak class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายกำกับสถิติ</label>
                        <input type="text" x-model="content.temple_stat_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายกำกับสถิติ</label>
                        <input type="text" x-model="content.article_stat_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">ป้ายกำกับสถิติผู้เข้าชม</label>
                        <input type="text" x-model="content.view_stat_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
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

            <div x-show="['article_grid', 'temple_grid'].includes(component)" x-cloak class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-300">เริ่มใช้สไลด์เมื่อเกิน</label>
                <input type="number" min="1" max="12" x-model.number="settings.slider_threshold" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                <p class="mt-2 text-xs leading-5 text-slate-500">
                    ถ้าจำนวนรายการจริงมากกว่านี้ หน้าเว็บจะเปลี่ยนจากกริดเป็นสไลด์อัตโนมัติ
                </p>
            </div>

            <div x-show="['article_list_full', 'temple_list_full'].includes(component)" x-cloak class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/30 p-5 lg:col-span-2">
                <div>
                    <h3 class="text-sm font-semibold text-white">จำนวนการ์ดต่อหน้า</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">ระบบจะคำนวณจำนวนที่แสดงใน 1 หน้าเป็น จำนวนแถว x จำนวนคอลัมน์ แล้วแบ่งหน้าต่อไปอัตโนมัติ</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนแถวต่อหน้า</label>
                        <input type="number" min="1" max="12" x-model.number="settings.list_rows" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนคอลัมน์บนเดสก์ท็อป</label>
                        <input type="number" min="1" max="6" x-model.number="settings.list_columns" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    </div>
                </div>

                <p class="text-xs text-blue-200">
                    แสดง <span x-text="Math.max(1, Math.min(Number(settings.list_rows) || 4, 12)) * Math.max(1, Math.min(Number(settings.list_columns) || 4, 6))"></span> การ์ดต่อหน้า
                </p>
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4 lg:col-span-2">
                <p class="text-sm font-medium text-blue-100">สถานะ ลำดับ และชื่อในระบบอยู่ในแผงควบคุมด้านขวาของ preview</p>
                <p class="mt-1 text-xs leading-5 text-slate-400">พื้นที่นี้โฟกัสเฉพาะเนื้อหาของเซกชัน เพื่อให้แก้ข้อความและสื่อได้ต่อเนื่องโดยไม่ต้องเลื่อนหาช่องเผยแพร่</p>
            </div>
        </div>
    </div>

        </div>
    </div>
</div>

@once
    <style>
        .cms-section-tab-body.cms-section-content-mode > .cms-media-panel,
        .cms-section-tab-body.cms-section-media-mode > :not(.cms-media-panel) {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-cms-section-preview]').forEach((preview) => {
                const form = preview.closest('form');
                const frame = preview.querySelector('[data-cms-section-preview-frame]');
                const loading = preview.querySelector('[data-cms-section-preview-loading]');
                const error = preview.querySelector('[data-cms-section-preview-error]');
                const previewUrl = preview.dataset.previewUrl;
                let timer = null;
                let controller = null;

                if (!form || !frame || !previewUrl) {
                    return;
                }

                const setLoading = (value) => {
                    loading?.classList.toggle('hidden', !value);
                };

                const setError = (message = '') => {
                    if (!error) {
                        return;
                    }

                    error.textContent = message;
                    error.classList.toggle('hidden', !message);
                };

                const แสดงผลPreview = async () => {
                    controller?.abort();
                    controller = new AbortController();

                    const formData = new FormData(form);
                    formData.delete('_method');

                    setLoading(true);
                    setError('');

                    try {
                        const response = await fetch(previewUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                Accept: 'application/json',
                            },
                            signal: controller.signal,
                        });

                        if (!response.ok) {
                            throw new Error(`โหลดตัวอย่างไม่สำเร็จ (${response.status})`);
                        }

                        const payload = await response.json();
                        frame.srcdoc = payload.html || '';
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            setError(error.message || 'ไม่สามารถโหลด preview ได้');
                        }
                    } finally {
                        setLoading(false);
                    }
                };

                const schedulePreview = () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(แสดงผลPreview, 350);
                };

                form.addEventListener('input', schedulePreview);
                form.addEventListener('change', schedulePreview);
                แสดงผลPreview();
            });
        });
    </script>
@endonce
