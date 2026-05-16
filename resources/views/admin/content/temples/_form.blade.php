{{-- _form.blade.php --}}
{{-- Variables expected: $temple, $statusOptions, $categories, $mediaItems, $facilities, $nearbyTemples --}}

@php
    $content = $temple?->content;
    $address = $temple?->address;
    $existingCategoryIds = $content?->categories?->pluck('id')->toArray() ?? [];
    $primaryCategoryId = $content?->categories?->firstWhere('pivot.is_primary', true)?->id;
    $coverMedia = $content?->mediaUsages?->firstWhere('role_key', 'cover');
    $galleryMediaIds = $content?->mediaUsages?->where('role_key', 'gallery')->pluck('media_id')->toArray() ?? [];
    $openingHours = $temple?->openingHours ?? collect();
    $fees = $temple?->fees ?? collect();
    $facilityItems = $temple?->facilityItems ?? collect();
    $highlights = $temple?->highlights ?? collect();
    $visitRules = $temple?->visitRules ?? collect();
    $travelInfos = $temple?->travelInfos ?? collect();
    $nearbyPlaces = $temple?->nearbyPlaces ?? collect();
    $detailTemplates = $detailTemplates ?? collect();
    $selectedTemplateId = old('template_id', $content?->template_id);
    $templatePreviewUrl = $templatePreviewUrl ?? null;
    $templatePreviewSrc = $templatePreviewUrl
        ? $templatePreviewUrl . '?' . http_build_query(array_filter([
            'template_id' => $selectedTemplateId,
            '_preview_ts' => time(),
        ]))
        : null;
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $oldOpeningHours = old('opening_hours');
    $jsOpeningHours = $oldOpeningHours !== null ? collect($oldOpeningHours)->map(function ($h) {
        return [
            'day_of_week' => isset($h['day_of_week']) ? (int) $h['day_of_week'] : null,
            'open_time'   => $h['open_time'] ?? '',
            'close_time'  => $h['close_time'] ?? '',
            'is_closed'   => (bool) ($h['is_closed'] ?? false),
            'note'        => $h['note'] ?? '',
        ];
    })->filter(fn ($h) => $h['day_of_week'] !== null)->values() : $openingHours->map(function ($h) {
        return [
            'day_of_week' => $h->day_of_week,
            'open_time'   => $h->open_time ? substr((string) $h->open_time, 0, 5) : '',
            'close_time'  => $h->close_time ? substr((string) $h->close_time, 0, 5) : '',
            'is_closed'   => (bool) $h->is_closed,
            'note'        => $h->note ?? '',
        ];
    })->values();

    $oldFees = old('fees');
    $jsFees = $oldFees !== null ? collect($oldFees)->map(function ($f) {
        return [
            'fee_type'   => $f['fee_type'] ?? '',
            'label'      => $f['label'] ?? '',
            'amount'     => $f['amount'] ?? '',
            'currency'   => $f['currency'] ?? 'THB',
            'note'       => $f['note'] ?? '',
            'is_active'  => (bool) ($f['is_active'] ?? false),
            'sort_order' => $f['sort_order'] ?? 0,
        ];
    })->values() : $fees->map(function ($f) {
        return [
            'fee_type'   => $f->fee_type,
            'label'      => $f->label,
            'amount'     => $f->amount,
            'currency'   => $f->currency ?? 'THB',
            'note'       => $f->note ?? '',
            'is_active'  => (bool) $f->is_active,
            'sort_order' => $f->sort_order ?? 0,
        ];
    })->values();

    $oldFacilityItems = old('facility_items');
    $jsFacilityItems = $oldFacilityItems !== null ? collect($oldFacilityItems)->map(function ($item) {
        return [
            'facility_id' => $item['facility_id'] ?? '',
            'facility_name' => $item['facility_name'] ?? '',
            'value' => $item['value'] ?? '',
            'note' => $item['note'] ?? '',
            'sort_order' => $item['sort_order'] ?? 0,
        ];
    })->values() : $facilityItems->map(function ($item) {
        return [
            'facility_id' => $item->facility_id,
            'facility_name' => '',
            'value' => $item->value ?? '',
            'note' => $item->note ?? '',
            'sort_order' => $item->sort_order ?? 0,
        ];
    })->values();

    $jsFacilities = $facilities->map(fn ($facility) => [
        'id' => $facility->id,
        'name' => $facility->name,
    ])->values();

    $jsNearbyTemples = $nearbyTemples->map(fn ($temple) => [
        'id' => (string) $temple->id,
        'title' => $temple->content?->title ?? 'Temple #' . $temple->id,
        'search' => mb_strtolower(($temple->content?->title ?? 'Temple #' . $temple->id) . ' ' . $temple->id),
    ])->values();

    $oldHighlights = old('highlights');
    $jsHighlights = $oldHighlights !== null ? collect($oldHighlights)->map(function ($h) {
        return [
            'title'       => $h['title'] ?? '',
            'description' => $h['description'] ?? '',
            'sort_order'  => $h['sort_order'] ?? 0,
        ];
    })->values() : $highlights->map(function ($h) {
        return [
            'title'       => $h->title,
            'description' => $h->description ?? '',
            'sort_order'  => $h->sort_order ?? 0,
        ];
    })->values();

    $oldVisitRules = old('visit_rules');
    $jsVisitRules = $oldVisitRules !== null ? collect($oldVisitRules)->map(function ($r) {
        return [
            'rule_text'  => $r['rule_text'] ?? '',
            'sort_order' => $r['sort_order'] ?? 0,
        ];
    })->values() : $visitRules->map(function ($r) {
        return [
            'rule_text'  => $r->rule_text,
            'sort_order' => $r->sort_order ?? 0,
        ];
    })->values();

    $oldTravelInfos = old('travel_infos');
    $jsTravelInfos = $oldTravelInfos !== null ? collect($oldTravelInfos)->map(function ($t) {
        return [
            'travel_type'      => $t['travel_type'] ?? '',
            'start_place'      => $t['start_place'] ?? '',
            'distance_km'      => $t['distance_km'] ?? '',
            'duration_minutes' => $t['duration_minutes'] ?? '',
            'cost_estimate'    => $t['cost_estimate'] ?? '',
            'note'             => $t['note'] ?? '',
            'is_active'        => (bool) ($t['is_active'] ?? false),
            'sort_order'       => $t['sort_order'] ?? 0,
        ];
    })->values() : $travelInfos->map(function ($t) {
        return [
            'travel_type'      => $t->travel_type,
            'start_place'      => $t->start_place ?? '',
            'distance_km'      => $t->distance_km,
            'duration_minutes' => $t->duration_minutes,
            'cost_estimate'    => $t->cost_estimate ?? '',
            'note'             => $t->note ?? '',
            'is_active'        => (bool) $t->is_active,
            'sort_order'       => $t->sort_order ?? 0,
        ];
    })->values();

    $oldNearbyPlaces = old('nearby_places');
    $jsNearbyPlaces = $oldNearbyPlaces !== null ? collect($oldNearbyPlaces)->map(function ($n) {
        return [
            'nearby_temple_id' => $n['nearby_temple_id'] ?? '',
            'relation_type'    => $n['relation_type'] ?? '',
            'distance_km'      => $n['distance_km'] ?? '',
            'duration_minutes' => $n['duration_minutes'] ?? '',
            'score'            => $n['score'] ?? '',
            'sort_order'       => $n['sort_order'] ?? 0,
        ];
    })->values() : $nearbyPlaces->map(function ($n) {
        return [
            'nearby_temple_id' => $n->nearby_temple_id,
            'relation_type'    => $n->relation_type ?? '',
            'distance_km'      => $n->distance_km,
            'duration_minutes' => $n->duration_minutes,
            'score'            => $n->score,
            'sort_order'       => $n->sort_order ?? 0,
        ];
    })->values();
@endphp

<div class="space-y-6 text-white">

    @once
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

        <style>
            .temple-editor-toolbar.ql-toolbar {
                border: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 0.375rem;
                align-items: center;
                font-family: inherit;
            }

            .temple-editor-toolbar.ql-toolbar .ql-formats {
                align-items: center;
                border-right: 1px solid rgb(255 255 255 / 0.1);
                display: inline-flex;
                gap: 0.125rem;
                margin: 0;
                padding-right: 0.375rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-formats:last-child {
                border-right: 0;
                padding-right: 0;
            }

            .temple-editor-toolbar.ql-toolbar button {
                border-radius: 0.5rem;
                color: rgb(203 213 225);
                height: 2rem;
                padding: 0.375rem;
                width: 2rem;
            }

            .temple-editor-toolbar.ql-toolbar button:hover,
            .temple-editor-toolbar.ql-toolbar button:focus,
            .temple-editor-toolbar.ql-toolbar button.ql-active {
                background: rgb(59 130 246 / 0.14);
                color: rgb(147 197 253);
            }

            .temple-editor-toolbar.ql-toolbar button svg,
            .temple-editor-toolbar.ql-toolbar .ql-picker-label svg {
                filter: drop-shadow(0 0 0 transparent);
            }

            .temple-editor-toolbar.ql-toolbar .ql-stroke {
                stroke: currentColor;
            }

            .temple-editor-toolbar.ql-toolbar .ql-fill {
                fill: currentColor;
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker {
                color: rgb(203 213 225);
                height: 2rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker-label {
                align-items: center;
                border: 1px solid rgb(255 255 255 / 0.1);
                border-radius: 0.5rem;
                display: flex;
                min-width: 6.25rem;
                padding-left: 0.625rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker-label:hover,
            .temple-editor-toolbar.ql-toolbar .ql-picker-label.ql-active {
                border-color: rgb(96 165 250 / 0.6);
                color: rgb(147 197 253);
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker-options {
                border: 1px solid rgb(255 255 255 / 0.12);
                border-radius: 0.75rem;
                background: rgb(15 23 42);
                box-shadow: 0 20px 40px rgb(2 6 23 / 0.45);
                color: rgb(226 232 240);
                margin-top: 0.375rem;
                padding: 0.375rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker-item {
                border-radius: 0.5rem;
                padding: 0.375rem 0.625rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-picker-item:hover,
            .temple-editor-toolbar.ql-toolbar .ql-picker-item.ql-selected {
                background: rgb(59 130 246 / 0.16);
                color: rgb(147 197 253);
            }

            .temple-editor-toolbar-compact.ql-toolbar {
                gap: 0.25rem;
            }

            .temple-editor-toolbar-compact.ql-toolbar .ql-formats {
                padding-right: 0.25rem;
            }

            .temple-editor-toolbar-compact.ql-toolbar button {
                height: 1.75rem;
                width: 1.75rem;
                padding: 0.25rem;
            }

            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label::before,
            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item::before {
                content: 'Paragraph';
            }

            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="1"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="1"]::before {
                content: 'Heading 1';
            }

            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="2"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="2"]::before {
                content: 'Heading 2';
            }

            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="3"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="3"]::before {
                content: 'Heading 3';
            }

            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label::before,
            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item::before {
                content: 'Normal';
            }

            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="tight"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="tight"]::before {
                content: 'Tight';
            }

            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="relaxed"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="relaxed"]::before {
                content: 'Relaxed';
            }

            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="loose"]::before,
            .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="loose"]::before {
                content: 'Loose';
            }

            .temple-rich-editor.ql-container {
                border: 0;
                font-family: inherit;
            }

            .temple-rich-editor .ql-editor {
                min-height: inherit;
                padding: 0;
                color: rgb(241 245 249);
                font-size: 0.875rem;
                line-height: 1.5;
            }

            .temple-rich-editor .ql-editor.ql-blank::before {
                color: rgb(100 116 139);
                font-style: normal;
                left: 0;
                right: 0;
            }

            .temple-rich-editor .ql-editor h1,
            .temple-rich-editor .ql-editor h2,
            .temple-rich-editor .ql-editor h3 {
                margin: 0.875rem 0 0.5rem;
                color: white;
                font-weight: 700;
            }

            .temple-rich-editor .ql-editor h1 {
                font-size: 1.5rem;
                line-height: 2rem;
            }

            .temple-rich-editor .ql-editor h2 {
                font-size: 1.25rem;
                line-height: 1.875rem;
            }

            .temple-rich-editor .ql-editor h3 {
                font-size: 1rem;
                line-height: 1.75rem;
            }

            .temple-rich-editor .ql-editor p,
            .temple-rich-editor .ql-editor ul,
            .temple-rich-editor .ql-editor ol,
            .temple-rich-editor .ql-editor blockquote {
                margin-bottom: 0.25rem;
            }

            .temple-rich-editor .ql-editor a {
                color: rgb(147 197 253);
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .temple-rich-editor .ql-editor blockquote {
                border-left: 3px solid rgb(96 165 250 / 0.6);
                color: rgb(203 213 225);
                padding-left: 0.875rem;
            }

            .temple-rich-editor .ql-editor .ql-code-block {
                border-radius: 0.75rem;
                background: rgb(2 6 23 / 0.85);
                color: rgb(203 213 225);
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                padding: 0.125rem 0.75rem;
            }

            .temple-rich-editor .ql-editor .ql-lineheight-tight {
                line-height: 1.25;
            }

            .temple-rich-editor .ql-editor .ql-lineheight-normal {
                line-height: 1.5;
            }

            .temple-rich-editor .ql-editor .ql-lineheight-relaxed {
                line-height: 1.75;
            }

            .temple-rich-editor .ql-editor .ql-lineheight-loose {
                line-height: 2;
            }

            @for ($i = 1; $i <= 8; $i++)
                .temple-rich-editor .ql-editor .ql-indent-{{ $i }} {
                    padding-left: {{ $i * 1.5 }}rem;
                }
            @endfor

            .temple-studio-tab-content .temple-panel:not(.temple-panel-content),
            .temple-studio-tab-details .temple-panel:not(.temple-panel-details),
            .temple-studio-tab-media .temple-panel:not(.temple-panel-media),
            .temple-studio-tab-visit .temple-panel:not(.temple-panel-visit),
            .temple-studio-tab-publish .temple-panel:not(.temple-panel-publish) {
                display: none !important;
            }
        </style>
    @endonce

    <input type="hidden" name="content_id" value="{{ $content?->id }}">
    <input type="hidden" name="temple_id" value="{{ $temple?->id }}">

    {{-- Section: Basic Info --}}
    <section
        x-data="{
            title: window.templeDraft('title', @js(old('title', $content?->title))),
            slug: window.templeDraft('slug', @js(old('slug', $content?->slug))),
            slugEdited: Boolean(window.templeDraft('slug', @js(old('slug', $content?->slug)))),
            makeSlug(value) {
                return value
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            },
            syncSlug() {
                if (! this.slugEdited) {
                    this.slug = this.makeSlug(this.title);
                }
            },
            resetSlug() {
                this.slugEdited = false;
                this.syncSlug();
            }
        }"
        class="temple-panel temple-panel-content overflow-visible rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div id="basic-info" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลหลักของวัด</h2>
            <p class="mt-1 text-xs text-slate-400">ตั้งชื่อ จัดการ slug เนื้อหาหลัก และ template หน้ารายละเอียด</p>
        </div>

        <div class="space-y-5 p-6">
            <div class="space-y-5">
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อ <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        x-model="title"
                        @input="syncSlug()"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3 text-base font-medium text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('title') border-rose-400 @enderror"
                        placeholder="เช่น พระแก้ว"
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <label for="slug" class="block text-sm font-medium text-slate-300">
                            Slug <span class="text-rose-400">*</span>
                        </label>

                        <button
                            type="button"
                            @click="resetSlug()"
                            class="text-xs font-medium text-blue-300 hover:text-blue-200"
                        >
                            สร้างจากชื่ออีกครั้ง
                        </button>
                    </div>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        x-model="slug"
                        @input="slugEdited = true"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('slug') border-rose-400 @enderror"
                        placeholder="wat-phra-kaew"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ระบบจะสร้าง slug ให้อัตโนมัติจากชื่อ แต่สามารถแก้เองได้ แนะนำให้ใช้ภาษาอังกฤษ ตัวเลข และขีดกลาง
                    </p>

                    @error('slug')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายสั้น</label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('excerpt') border-rose-400 @enderror"
                    placeholder="คำอธิบายสั้นๆ เกี่ยวกับวัด ใช้ในรายการและตัวอย่างเวลาแชร์"
                >{{ old('excerpt', $content?->excerpt) }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            @include('admin.content.temples.partials._rich_text_editor', [
                'name' => 'description',
                'id' => 'description',
                'label' => 'รายละเอียด',
                'value' => $content?->description,
                'placeholder' => 'เล่าเนื้อหาหลักของวัด จุดสำคัญ บรรยากาศ และข้อมูลที่ผู้เข้าชมควรรู้',
                'hint' => 'รองรับหัวข้อ ลิสต์ ลิงก์ และข้อความเน้น',
                'minHeight' => '300px',
            ])

            <div class="grid grid-cols-1 gap-5">
                <div class="sm:col-span-2">
                    <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                        เทมเพลต หน้า Detail
                    </label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'template_id',
                        'name' => 'template_id',
                        'selected' => old('template_id', $content?->template_id),
                        'emptyLabel' => 'ใช้ค่าเริ่มต้นของวัด',
                        'placeholder' => 'เลือกเทมเพลต',
                        'searchPlaceholder' => 'ค้นหาเทมเพลต...',
                        'errorKey' => 'template_id',
                        'visibleLimit' => null,
                        'dataAttributes' => [
                            'data-template-preview-select' => '',
                            'data-preview-target' => 'temple-template-preview',
                            'data-preview-base' => $templatePreviewUrl,
                        ],
                        'options' => $detailTemplates->map(fn ($template) => [
                            'value' => $template->id,
                            'label' => $template->name,
                            'meta' => $template->key,
                            'search' => $template->name . ' ' . $template->key . ' ' . $template->view_path,
                        ]),
                    ])
                    @error('template_id')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">
                        ถ้าไม่เลือก ระบบจะใช้เทมเพลต temple-detail ที่เปิดใช้งานอยู่
                    </p>

                </div>

            </div>
        </div>
    </section>

    {{-- Section: Temple Details --}}
    <section class="temple-panel temple-panel-details overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div id="temple-details" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลเฉพาะของวัด</h2>
            <p class="mt-1 text-xs text-slate-400">ประเภท นิกาย สถาปัตยกรรม ประวัติ และข้อแนะนำการเข้าชม</p>
        </div>

        <div class="space-y-5 p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="temple_type" class="mb-1.5 block text-sm font-medium text-slate-300">ประเภท</label>
                    <select
                        id="temple_type"
                        name="temple_type"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกประเภท —</option>
                        @foreach (['ราษฎร์', 'พระอารามหลวง', 'หลวง', 'สำนักสงฆ์', 'ร้าง', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('temple_type', $temple?->temple_type) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sect" class="mb-1.5 block text-sm font-medium text-slate-300">นิกาย</label>
                    <select
                        id="sect"
                        name="sect"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกนิกาย —</option>
                        @foreach (['มหานิกาย', 'ธรรมยุติกนิกาย', 'จีนนิกาย', 'อนัมนิกาย', 'ไม่ระบุ', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('sect', $temple?->sect) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="architecture_style" class="mb-1.5 block text-sm font-medium text-slate-300">รูปแบบสถาปัตยกรรม</label>
                    <select
                        id="architecture_style"
                        name="architecture_style"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกรูปแบบสถาปัตยกรรม —</option>
                        @foreach (['รัตนโกสินทร์', 'อยุธยา', 'สุโขทัย', 'ล้านนา', 'ล้านช้าง', 'ขอม', 'จีน', 'ไทยร่วมสมัย', 'ผสมผสาน', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('architecture_style', $temple?->architecture_style) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="founded_year" class="mb-1.5 block text-sm font-medium text-slate-300">ปีที่ก่อตั้ง</label>
                    <input
                        type="number"
                        id="founded_year"
                        name="founded_year"
                        value="{{ old('founded_year', $temple?->founded_year) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เช่น 1782"
                        min="0"
                    >
                </div>

                <div>
                    <label for="dress_code" class="mb-1.5 block text-sm font-medium text-slate-300">การแต่งกาย</label>
                    <select
                        id="dress_code"
                        name="dress_code"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกคำแนะนำการแต่งกาย —</option>
                        @foreach ([
                            'แต่งกายสุภาพ',
                            'แต่งกายสุภาพ ห้ามเสื้อแขนกุดและกางเกงขาสั้น',
                            'แต่งกายสุภาพ ถอดรองเท้าก่อนเข้าอาคาร',
                            'แต่งกายสุภาพและงดใช้เสียงดัง',
                            'ไม่มีข้อกำหนดเฉพาะ',
                            'อื่น ๆ',
                        ] as $option)
                            <option value="{{ $option }}" @selected(old('dress_code', $temple?->dress_code) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="recommended_visit_start_time" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เวลาแนะนำให้ไป (เริ่ม)
                        </label>
                        <input
                            type="time"
                            id="recommended_visit_start_time"
                            name="recommended_visit_start_time"
                            value="{{ old('recommended_visit_start_time', $temple?->recommended_visit_start_time ? substr((string) $temple->recommended_visit_start_time, 0, 5) : '') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div>
                        <label for="recommended_visit_end_time" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เวลาแนะนำให้ไป (สิ้นสุด)
                        </label>
                        <input
                            type="time"
                            id="recommended_visit_end_time"
                            name="recommended_visit_end_time"
                            value="{{ old('recommended_visit_end_time', $temple?->recommended_visit_end_time ? substr((string) $temple->recommended_visit_end_time, 0, 5) : '') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>
                </div>
            </div>

            @include('admin.content.temples.partials._rich_text_editor', [
                'name' => 'history',
                'id' => 'history',
                'label' => 'ประวัติ',
                'value' => $temple?->history,
                'placeholder' => 'ประวัติความเป็นมา เหตุการณ์สำคัญ บุคคลสำคัญ หรือข้อมูลเชิงวัฒนธรรมของวัด',
                'hint' => 'เหมาะกับเนื้อหายาว แยกย่อหน้าและหัวข้อได้',
                'minHeight' => '300px',
            ])
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6">
        {{-- Section: SEO --}}
        <section class="temple-panel temple-panel-publish overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">SEO</h2>
                <p class="mt-1 text-xs text-slate-400">ข้อมูลสำหรับ title และ description ของหน้าเว็บ</p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">Meta Title</label>
                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $content?->meta_title) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เช่น พระแก้ว | PAPAIWAT"
                    >
                    <p class="mt-1 text-xs text-slate-500">ควรสั้น กระชับ และสื่อถึงชื่อหรือจังหวัด</p>
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">Meta คำอธิบาย</label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คำอธิบายสั้นสำหรับผลการค้นหา เช่น ประวัติ จุดเด่น และข้อมูลการเข้าชม"
                    >{{ old('meta_description', $content?->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">ใช้สำหรับแสดงในผลการค้นหาและ ตัวอย่างเวลาแชร์</p>
                </div>
            </div>
        </section>

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

            <div class="p-6">
                @if ($categories->isEmpty())
                    <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/40 px-4 py-8 text-center">
                        <p class="text-sm font-medium text-slate-300">ยังไม่มีหมวดหมู่</p>
                        <p class="mt-1 text-xs text-slate-500">สร้างหมวดหมู่ก่อนเพื่อจัดกลุ่มวัดบนหน้าเว็บไซต์</p>
                    </div>
                @else
                    <div
                        class="grid gap-5 2xl:grid-cols-[minmax(0,1fr)_360px]"
                        x-data="{
                            search: '',
                            selectedCategoryIds: @js(array_map('strval', old('category_ids', $existingCategoryIds))),
                            categories: @js($categories->map(fn ($cat) => [
                                'id' => (string) $cat->id,
                                'name' => $cat->name,
                                'search' => mb_strtolower($cat->name . ' ' . $cat->id),
                            ])->values()),
                            isSelected(id) {
                                return this.selectedCategoryIds.includes(String(id));
                            },
                            get filteredCategories() {
                                const keyword = this.search.toLowerCase().trim();
                                if (!keyword) {
                                    return this.categories;
                                }
                                return this.categories.filter((category) => category.search.includes(keyword));
                            },
                            get selectedCategories() {
                                return this.categories.filter((category) => this.isSelected(category.id));
                            }
                        }"
                    >
                        <div class="min-w-0 space-y-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <label for="temple_category_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                                        ค้นหาหมวดหมู่
                                    </label>
                                    <input
                                        id="temple_category_search"
                                        type="search"
                                        x-model.debounce.100ms="search"
                                        placeholder="ค้นหาจากชื่อหมวดหมู่หรือ ID"
                                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    >
                                </div>

                                <div class="rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-300">
                                    เลือกแล้ว <span class="font-semibold text-blue-300" x-text="selectedCategoryIds.length"></span>
                                </div>
                            </div>

                            <div class="grid max-h-[320px] grid-cols-1 gap-2 overflow-y-auto pr-1 md:grid-cols-2 2xl:grid-cols-3">
                                @foreach ($categories as $cat)
                                    @php
                                        $checked = in_array($cat->id, old('category_ids', $existingCategoryIds));
                                    @endphp

                                    <label
                                        x-show="filteredCategories.some((category) => category.id === '{{ $cat->id }}')"
                                        class="group flex cursor-pointer items-center gap-3 rounded-xl border px-3 py-2.5 transition hover:bg-white/[0.06]"
                                        :class="isSelected('{{ $cat->id }}') ? 'border-blue-400/40 bg-blue-500/10' : 'border-white/10 bg-slate-950/40'"
                                    >
                                        <input
                                            type="checkbox"
                                            name="category_ids[]"
                                            value="{{ $cat->id }}"
                                            x-model="selectedCategoryIds"
                                            class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                                            @checked($checked)
                                            onchange="updatePrimaryCategoryOptions()"
                                        >
                                        <span class="min-w-0 flex-1 truncate text-sm text-slate-300">{{ $cat->name }}</span>
                                        <span x-show="isSelected('{{ $cat->id }}')" class="rounded-full bg-blue-500/20 px-2 py-0.5 text-[11px] font-medium text-blue-200">เลือก</span>
                                    </label>
                                @endforeach
                            </div>

                            <div
                                x-show="filteredCategories.length === 0"
                                class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500"
                            >
                                ไม่พบหมวดหมู่ที่ตรงกับคำค้นหา
                            </div>
                        </div>

                        <aside class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div>
                                <label for="primary_category_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                                    หมวดหมู่หลัก
                                </label>

                                @include('admin.content.partials._searchable_select', [
                                    'id' => 'primary_category_id',
                                    'name' => 'primary_category_id',
                                    'selected' => old('primary_category_id', $primaryCategoryId),
                                    'emptyLabel' => '— ไม่ระบุ —',
                                    'placeholder' => 'เลือกหมวดหมู่หลัก',
                                    'searchPlaceholder' => 'ค้นหาหมวดหมู่หลัก...',
                                    'options' => $categories->map(fn ($cat) => [
                                        'value' => $cat->id,
                                        'label' => $cat->name,
                                        'meta' => 'Category #' . $cat->id,
                                        'search' => $cat->name . ' ' . $cat->id,
                                    ]),
                                ])

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    ใช้เป็นหมวดหลักในรายการและ metadata ของหน้าเว็บไซต์
                                </p>
                            </div>

                            <div class="border-t border-white/10 pt-4">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">ที่เลือก</p>
                                    <button
                                        type="button"
                                        x-show="selectedCategoryIds.length > 0"
                                        @click="selectedCategoryIds = []; $nextTick(() => updatePrimaryCategoryOptions())"
                                        class="text-xs font-medium text-rose-300 hover:text-rose-200"
                                    >
                                        ล้างทั้งหมด
                                    </button>
                                </div>

                                <div x-show="selectedCategories.length > 0" class="flex max-h-32 flex-wrap gap-2 overflow-y-auto pr-1">
                                    <template x-for="category in selectedCategories" :key="category.id">
                                        <span class="rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs text-blue-100" x-text="category.name"></span>
                                    </template>
                                </div>

                                <p x-show="selectedCategories.length === 0" class="rounded-xl border border-dashed border-white/10 px-3 py-3 text-center text-xs text-slate-500">
                                    ยังไม่ได้เลือกหมวดหมู่
                                </p>
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Section: Media --}}
    <section
        id="media-section"
        x-data="{
            mediaSearch: '',
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

            toggleGallery(id) {
                id = String(id);

                if (this.selectedGallery.includes(id)) {
                    this.selectedGallery = this.selectedGallery.filter((item) => item !== id);
                    return;
                }

                this.selectedGallery.push(id);
            },

            async loadCoverPage(event) {
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
                this.coverHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.coverPicker);
                });
            }
        },

        async loadGalleryPage(event) {
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
                this.galleryHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.galleryPicker);
                });
            }
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
                            ค้นหารูปในหน้านี้
                        </label>

                        <input
                            id="media_search"
                            type="text"
                            x-model="mediaSearch"
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

    {{-- Section: Address --}}
    <section class="temple-panel temple-panel-media overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ที่ตั้งและแผนที่</h2>
            <p class="mt-1 text-xs text-slate-400">ที่อยู่สำหรับแสดงผลและข้อมูลพิกัดสำหรับลิงก์แผนที่</p>
        </div>
        <div class="grid gap-5 p-6 2xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">ที่อยู่</h3>
                    <p class="mt-1 text-xs text-slate-500">ข้อมูลนี้ใช้แสดงในหน้า Detail และตัวกรองจังหวัด</p>
                </div>

                <div>
                    <label for="address_line" class="mb-1.5 block text-sm font-medium text-slate-300">ที่อยู่</label>
                    <input type="text" id="address_line" name="address[address_line]" value="{{ old('address.address_line', $address?->address_line) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="เลขที่ ถนน หรือชื่อชุมชน">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="subdistrict" class="mb-1.5 block text-sm font-medium text-slate-300">แขวง / ตำบล</label>
                        <input type="text" id="subdistrict" name="address[subdistrict]" value="{{ old('address.subdistrict', $address?->subdistrict) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="district" class="mb-1.5 block text-sm font-medium text-slate-300">เขต / อำเภอ</label>
                        <input type="text" id="district" name="address[district]" value="{{ old('address.district', $address?->district) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="province" class="mb-1.5 block text-sm font-medium text-slate-300">จังหวัด</label>
                        <input type="text" id="province" name="address[province]" value="{{ old('address.province', $address?->province) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="postal_code" class="mb-1.5 block text-sm font-medium text-slate-300">รหัสไปรษณีย์</label>
                        <input type="text" id="postal_code" name="address[postal_code]" value="{{ old('address.postal_code', $address?->postal_code) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>
            </div>

            <aside class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">พิกัดและแผนที่</h3>
                    <p class="mt-1 text-xs text-slate-500">กรอกพิกัดเมื่อต้องการแสดงตำแหน่งแบบแม่นยำ</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-1">
                    <div>
                        <label for="latitude" class="mb-1.5 block text-sm font-medium text-slate-300">ละติจูด</label>
                        <input type="text" id="latitude" name="address[latitude]" value="{{ old('address.latitude', $address?->latitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="13.7563">
                    </div>
                    <div>
                        <label for="longitude" class="mb-1.5 block text-sm font-medium text-slate-300">ลองจิจูด</label>
                        <input type="text" id="longitude" name="address[longitude]" value="{{ old('address.longitude', $address?->longitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="100.5018">
                    </div>
                </div>

                <div>
                    <label for="google_place_id" class="mb-1.5 block text-sm font-medium text-slate-300">Google Place ID</label>
                    <input type="text" id="google_place_id" name="address[google_place_id]" value="{{ old('address.google_place_id', $address?->google_place_id) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="google_maps_url" class="mb-1.5 block text-sm font-medium text-slate-300">Google Maps URL</label>
                    <input type="url" id="google_maps_url" name="address[google_maps_url]" value="{{ old('address.google_maps_url', $address?->google_maps_url) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="https://maps.google.com/...">
                </div>
            </aside>
        </div>
    </section>

    {{-- Section: Opening Hours --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="openingHoursManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">เวลาเปิด-ปิด</h2>
                <p class="mt-1 text-xs text-slate-400">กำหนดเวลาทำการแบบช่วงวัน เช่น ทุกวัน หรือ จันทร์ - ศุกร์</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มช่วงเวลา
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, rowIndex) in rows" :key="rowIndex">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">

                        {{-- Preset --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">รูปแบบวัน</label>
                            <select
                                x-model="row.preset"
                                @change="applyPreset(row)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="everyday">ทุกวัน</option>
                                <option value="weekdays">จันทร์ - ศุกร์</option>
                                <option value="weekend">เสาร์ - อาทิตย์</option>
                                <option value="oneday">วันเดียว</option>
                                <option value="custom">กำหนดเอง</option>
                            </select>
                        </div>

                        {{-- From Day --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">จากวัน</label>
                            <select
                                x-model.number="row.day_from"
                                :disabled="!['custom','oneday'].includes(row.preset)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                                @foreach ($days as $di => $dayName)
                                    <option value="{{ $di }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- To Day --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ถึงวัน</label>
                            <select
                                x-model.number="row.day_to"
                                :disabled="!['custom','oneday'].includes(row.preset)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                                @foreach ($days as $di => $dayName)
                                    <option value="{{ $di }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Open --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เปิด</label>
                            <input
                                type="time"
                                x-model="row.open_time"
                                :disabled="row.is_closed"
                                class="w-full min-w-[120px] rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                        </div>

                        {{-- Close --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ปิด</label>
                            <input
                                type="time"
                                x-model="row.close_time"
                                :disabled="row.is_closed"
                                class="w-full min-w-[120px] rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                        </div>

                        {{-- การจัดการs --}}
                        <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                            <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-400">
                                <input
                                    type="checkbox"
                                    x-model="row.is_closed"
                                    class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                >
                                ปิด
                            </label>

                            <button
                                type="button"
                                @click="removeRow(rowIndex)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-2 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕
                            </button>
                        </div>

                        {{-- Note --}}
                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น วันหยุดนักขัตฤกษ์"
                            >
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-xs text-slate-400">
                        จะบันทึกเป็น:
                        <span class="text-slate-200" x-text="previewDays(row)"></span>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มช่วงเวลาเพื่อเพิ่ม
            </p>

            <template x-for="(item, index) in expandedRows()" :key="index">
                <div>
                    <input type="hidden" :name="`opening_hours[${index}][day_of_week]`" :value="item.day_of_week">
                    <input type="hidden" :name="`opening_hours[${index}][open_time]`" :value="item.is_closed ? '' : item.open_time">
                    <input type="hidden" :name="`opening_hours[${index}][close_time]`" :value="item.is_closed ? '' : item.close_time">
                    <input type="hidden" :name="`opening_hours[${index}][note]`" :value="item.note">
                    <input type="hidden" :name="`opening_hours[${index}][is_closed]`" :value="item.is_closed ? 1 : 0">
                </div>
            </template>
        </div>
    </section>

    {{-- Section: Fees --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="feesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ธรรมเนียม</h2>
                <p class="mt-1 text-xs text-slate-400">เข้าชม จอดรถ หรือใช้จ่ายอื่น ๆ</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มธรรมเนียม
            </button>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="grid grid-cols-12 items-start gap-3">
                            {{-- Fee Type --}}
                            <div class="col-span-12 md:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-slate-400">ประเภทธรรมเนียม</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][fee_type]`"
                                    x-model="row.fee_type"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น admission, parking"
                                >
                            </div>

                            {{-- Label --}}
                            <div class="col-span-12 md:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-400">ชื่อที่แสดง</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][label]`"
                                    x-model="row.label"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น เข้าชมผู้ใหญ่"
                                >
                            </div>

                            {{-- Amount --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-xs font-medium text-slate-400">จำนวนเงิน</label>
                                <input
                                    type="number"
                                    :name="`fees[${index}][amount]`"
                                    x-model="row.amount"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="0"
                                    min="0"
                                    step="0.01"
                                >
                            </div>

                            {{-- Currency --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-xs font-medium text-slate-400">สกุลเงิน</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][currency]`"
                                    x-model="row.currency"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="THB"
                                >
                            </div>

                            {{-- เปิดใช้งาน --}}
                            <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                                <label class="mt-6 flex cursor-pointer items-center gap-1.5 text-xs text-slate-400 md:mt-7">
                                    <input
                                        type="checkbox"
                                        :name="`fees[${index}][is_active]`"
                                        value="1"
                                        x-model="row.is_active"
                                        class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                    >
                                    ใช้งาน
                                </label>
                            </div>

                            {{-- Note --}}
                            <div class="col-span-12">
                                <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][note]`"
                                    x-model="row.note"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น เด็กอายุต่ำกว่า 12 ปีเข้าฟรี"
                                >
                            </div>

                            {{-- Remove --}}
                            <div class="col-span-12 flex justify-end">
                                <button
                                    type="button"
                                    @click="removeRow(index)"
                                    class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                                >
                                    ✕ ลบรายการนี้
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <p x-show="rows.length === 0" class="text-sm text-slate-400">
                    ยังไม่มีข้อมูล — กดเพิ่มธรรมเนียมเพื่อเพิ่ม
                </p>
            </div>
        </div>
    </section>

    {{-- Section: Facilities --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="facilitiesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">สิ่งอำนวยความสะดวก</h2>
                <p class="mt-1 text-xs text-slate-400">เพิ่มสิ่งอำนวยความสะดวกของได้จากหน้านี้โดยตรง</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มสิ่งอำนวยความสะดวก
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="row._key || index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เลือกจากรายการเดิม</label>
                            <input
                                type="search"
                                x-model.debounce.100ms="row.facility_search"
                                class="mb-2 w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="ค้นหาสิ่งอำนวยความสะดวก..."
                            >
                            <select
                                :name="`facility_items[${index}][facility_id]`"
                                x-model="row.facility_id"
                                @change="if (row.facility_id) row.facility_name = ''"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="">สร้างรายการใหม่</option>
                                <template x-for="facility in filteredFacilities(row)" :key="facility.id">
                                    <option :value="facility.id" x-text="facility.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ชื่อรายการใหม่</label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][facility_name]`"
                                x-model="row.facility_name"
                                :disabled="Boolean(row.facility_id)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400 disabled:opacity-50"
                                placeholder="เช่น ห้องน้ำ, ที่จอดรถ"
                            >
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400"></label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][value]`"
                                x-model="row.value"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น มี, ฟรี"
                            >
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>
                            <input
                                type="number"
                                :name="`facility_items[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                                min="0"
                            >
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][note]`"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="รายละเอียดเพิ่มเติม"
                            >
                        </div>

                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มสิ่งอำนวยความสะดวกเพื่อเพิ่ม
            </p>
        </div>
    </section>

    {{-- Section: Highlights --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="repeaterManager('highlights', @json($jsHighlights))"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">จุดเด่นของ</h2>
                <p class="mt-1 text-xs text-slate-400">ไฮไลต์สำคัญที่ใช้แสดงในหน้ารายละเอียด</p>
            </div>

            <button
                type="button"
                @click="addRow({ title: '', description: '', sort_order: rows.length })"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มจุดเด่น
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="row._key || index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- หัวข้อ --}}
                        <div class="col-span-12 md:col-span-5">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                ชื่อจุดเด่น <span class="text-rose-400">*</span>
                            </label>

                            <input
                                type="text"
                                :name="`highlights[${index}][title]`"
                                x-model="row.title"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น พระพุทธรูปสำคัญ"
                            >
                        </div>

                        {{-- ลำดับ --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>

                            <input
                                type="number"
                                :name="`highlights[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                                min="0"
                            >
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">รายละเอียด</label>

                            <input
                                type="hidden"
                                :name="`highlights[${index}][description]`"
                                x-model="row.description"
                            >

                            <div
                                class="overflow-hidden rounded-xl border border-white/10 bg-slate-950/70 transition focus-within:border-blue-400"
                                data-inline-rich-editor
                                data-placeholder="รายละเอียดเพิ่มเติมของจุดเด่นนี้"
                                x-init="$nextTick(() => initTempleInlineRichEditor($el, row, 'description'))"
                            >
                                <div data-editor-toolbar class="temple-editor-toolbar temple-editor-toolbar-compact border-b border-white/10 bg-slate-900/90 px-2 py-1.5">
                                    <span class="ql-formats">
                                        <select class="ql-lineheight" title="Line spacing">
                                            <option selected></option>
                                            <option value="tight"></option>
                                            <option value="relaxed"></option>
                                            <option value="loose"></option>
                                        </select>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-bold" title="Bold"></button>
                                        <button type="button" class="ql-italic" title="Italic"></button>
                                        <button type="button" class="ql-underline" title="Underline"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-list" value="bullet" title="Bullet list"></button>
                                        <button type="button" class="ql-link" title="Link"></button>
                                        <button type="button" class="ql-clean" title="Clear formatting"></button>
                                    </span>
                                </div>
                                <div data-editor-body class="temple-rich-editor px-4 py-3 text-sm leading-6 text-slate-100" style="min-height: 130px"></div>
                            </div>
                        </div>

                        {{-- Remove --}}
                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มจุดเด่นเพื่อเพิ่ม
            </p>
        </div>
    </section>

    {{-- Section: Visit Rules --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="repeaterManager('visit_rules', @json($jsVisitRules))"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">กฎการเข้าชม</h2>
                <p class="mt-1 text-xs text-slate-400">ข้อควรปฏิบัติสำหรับผู้เข้าชม เช่น การแต่งกาย การถ่ายภาพ หรือพื้นที่ห้ามเข้า</p>
            </div>

            <button
                type="button"
                @click="addRow({ rule_text: '', sort_order: rows.length })"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มกฎ
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="row._key || index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Rule Text --}}
                        <div class="col-span-12 md:col-span-10">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                รายละเอียดกฎ <span class="text-rose-400">*</span>
                            </label>

                            <input
                                type="hidden"
                                :name="`visit_rules[${index}][rule_text]`"
                                x-model="row.rule_text"
                            >

                            <div
                                class="overflow-hidden rounded-xl border border-white/10 bg-slate-950/70 transition focus-within:border-blue-400"
                                data-inline-rich-editor
                                data-placeholder="เช่น แต่งกายสุภาพ ไม่สวมกางเกงขาสั้น หรือเสื้อแขนกุด"
                                x-init="$nextTick(() => initTempleInlineRichEditor($el, row, 'rule_text'))"
                            >
                                <div data-editor-toolbar class="temple-editor-toolbar temple-editor-toolbar-compact border-b border-white/10 bg-slate-900/90 px-2 py-1.5">
                                    <span class="ql-formats">
                                        <select class="ql-lineheight" title="Line spacing">
                                            <option selected></option>
                                            <option value="tight"></option>
                                            <option value="relaxed"></option>
                                            <option value="loose"></option>
                                        </select>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-bold" title="Bold"></button>
                                        <button type="button" class="ql-italic" title="Italic"></button>
                                        <button type="button" class="ql-underline" title="Underline"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-list" value="bullet" title="Bullet list"></button>
                                        <button type="button" class="ql-link" title="Link"></button>
                                        <button type="button" class="ql-clean" title="Clear formatting"></button>
                                    </span>
                                </div>
                                <div data-editor-body class="temple-rich-editor px-4 py-3 text-sm leading-6 text-slate-100" style="min-height: 130px"></div>
                            </div>

                            <p class="mt-1 text-xs text-slate-500">
                                เขียนเป็นข้อความสั้น กระชับ และอ่านเข้าใจง่ายสำหรับผู้เข้าชม
                            </p>
                        </div>

                        {{-- ลำดับ --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>

                            <input
                                type="number"
                                :name="`visit_rules[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- Remove --}}
                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/30 px-4 py-5 text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มกฎเพื่อเพิ่มข้อควรปฏิบัติ
            </p>
        </div>
    </section>

    {{-- Section: Travel Infos --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="travelInfosManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ข้อมูลการเดินทาง</h2>
                <p class="mt-1 text-xs text-slate-400">วิธีเดินทาง ระยะทาง ระยะเวลา และใช้จ่ายโดยประมาณ</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มข้อมูลเดินทาง
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Travel Type --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                วิธีเดินทาง <span class="text-rose-400">*</span>
                            </label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][travel_type]`"
                                x-model="row.travel_type"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น BTS, รถยนต์, รถเมล์"
                            >
                        </div>

                        {{-- Start Place --}}
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">จุดเริ่มต้น</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][start_place]`"
                                x-model="row.start_place"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น สถานีสนามไชย, อนุสาวรีย์ชัยฯ"
                            >
                        </div>

                        {{-- Distance --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ระยะทาง (กม.)</label>
                            <input
                                type="number"
                                :name="`travel_infos[${index}][distance_km]`"
                                x-model="row.distance_km"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                                min="0"
                            >
                        </div>

                        {{-- Duration --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เวลา (นาที)</label>
                            <input
                                type="number"
                                :name="`travel_infos[${index}][duration_minutes]`"
                                x-model="row.duration_minutes"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- เปิดใช้งาน --}}
                        <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                            <label class="mt-6 flex cursor-pointer items-center gap-1.5 text-xs text-slate-400 md:mt-7">
                                <input
                                    type="checkbox"
                                    :name="`travel_infos[${index}][is_active]`"
                                    value="1"
                                    x-model="row.is_active"
                                    class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                >
                                ใช้งาน
                            </label>
                        </div>

                        {{-- Cost Estimate --}}
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ใช้จ่ายโดยประมาณ</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][cost_estimate]`"
                                x-model="row.cost_estimate"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น 30-50 บาท"
                            >
                        </div>

                        {{-- Note --}}
                        <div class="col-span-12 md:col-span-8">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][note]`"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น เดินต่อประมาณ 5 นาทีจากสถานี"
                            >
                        </div>

                        {{-- Remove --}}
                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/30 px-4 py-5 text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มข้อมูลเดินทางเพื่อเพิ่มวิธีเดินทาง
            </p>
        </div>
    </section>

    {{-- Section: Nearby Places --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="nearbyPlacesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ใกล้เคียง</h2>
                <p class="mt-1 text-xs text-slate-400">เชื่อมโยงที่อยู่ใกล้กันหรือเกี่ยวข้องกัน</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มใกล้เคียง
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Temple --}}
                        <div class="col-span-12 md:col-span-5">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                ที่เกี่ยวข้อง <span class="text-rose-400">*</span>
                            </label>

                            <input
                                type="search"
                                x-model.debounce.100ms="row.temple_search"
                                class="mb-2 w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="ค้นหาชื่อหรือ ID..."
                            >
                            <select
                                :name="`nearby_places[${index}][nearby_temple_id]`"
                                x-model="row.nearby_temple_id"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="">— เลือก —</option>
                                <template x-for="temple in filteredNearbyTemples(row)" :key="temple.id">
                                    <option :value="temple.id" x-text="temple.title"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Relation Type --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ประเภทความเกี่ยวข้อง</label>

                            <input
                                type="text"
                                :name="`nearby_places[${index}][relation_type]`"
                                x-model="row.relation_type"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น nearby, same_complex"
                            >
                        </div>

                        {{-- Distance --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ระยะทาง (กม.)</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][distance_km]`"
                                x-model="row.distance_km"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                                min="0"
                            >
                        </div>

                        {{-- Duration --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เวลาเดินทาง (นาที)</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][duration_minutes]`"
                                x-model="row.duration_minutes"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- Score --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">คะแนนความเกี่ยวข้อง</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][score]`"
                                x-model="row.score"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                            >
                        </div>

                        {{-- ลำดับ --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- Remove --}}
                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/30 px-4 py-5 text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มใกล้เคียงเพื่อเชื่อมโยงที่เกี่ยวข้อง
            </p>
        </div>
    </section>

    {{-- Section: Publishing --}}
    <section class="temple-panel temple-panel-publish overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div id="temple-publishing" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">การเผยแพร่</h2>
            <p class="mt-1 text-xs text-slate-400">กำหนดสถานะ เวลาเผยแพร่ และการแสดงผลของวัด</p>
        </div>

        <div class="space-y-6 p-6">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                        สถานะ <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('status') border-rose-400 @enderror"
                    >
                        @foreach ($statusOptions as $opt)
                            <option value="{{ $opt }}" @selected(old('status', $content?->status) === $opt)>
                                {{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-300">เผยแพร่เมื่อ</label>
                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        value="{{ old('published_at', $content?->published_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('published_at') border-rose-400 @enderror"
                    >
                    @error('published_at')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4 text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_featured', $content?->is_featured))
                    >
                    <div>
                        <div class="text-sm font-medium text-slate-200">แนะนำบนหน้าเว็บ</div>
                        <div class="text-xs text-slate-500">ใช้เน้นในส่วนสำคัญของเว็บไซต์</div>
                    </div>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4 text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_popular"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_popular', $content?->is_popular))
                    >
                    <div>
                        <div class="text-sm font-medium text-slate-200">ยอดนิยม</div>
                        <div class="text-xs text-slate-500">กำหนดให้วัดนี้อยู่ในกลุ่มยอดนิยม</div>
                    </div>
                </label>
            </div>
        </div>
    </section>
</div>

<script>
    window.templeFormHasServerErrors = @json($errors->any());

    const templeDraftStore = {
        key: 'papaiwat:temple-form-draft:' + window.location.pathname,
        storage: window.sessionStorage,

        read() {
            try {
                return JSON.parse(this.storage.getItem(this.key)) || {};
            } catch (error) {
                return {};
            }
        },

        write(payload) {
            this.storage.setItem(this.key, JSON.stringify({
                ...this.read(),
                ...payload,
            }));
        },

        get(key, fallback = null) {
            const draft = this.read();

            return Object.prototype.hasOwnProperty.call(draft, key)
                ? draft[key]
                : fallback;
        },

        clear() {
            this.storage.removeItem(this.key);
        },
    };

    window.templeDraft = Object.assign(function (name, fallback = '') {
        if (window.templeFormHasServerErrors) {
            return fallback;
        }

        const fields = window.templeDraft.get('fields', {});

        return Object.prototype.hasOwnProperty.call(fields, name)
            ? fields[name]
            : fallback;
    }, templeDraftStore);

    window.normalizeTempleMediaIds = function (value) {
        const values = Array.isArray(value) ? value : [value];

        return values
            .map((item) => String(item ?? '').trim())
            .filter((item) => /^\d+$/.test(item));
    };

    window.templeDraftMediaId = function (name, fallback = '') {
        const fallbackIds = window.normalizeTempleMediaIds(fallback);
        const draftIds = window.normalizeTempleMediaIds(window.templeDraft(name, fallback));

        return draftIds[0] ?? fallbackIds[0] ?? '';
    };

    window.templeDraftMediaIdArray = function (name, fallback = []) {
        const fallbackIds = window.normalizeTempleMediaIds(fallback);
        const draftIds = window.normalizeTempleMediaIds(window.templeDraft(name, fallback));

        return draftIds.length > 0 ? draftIds : fallbackIds;
    };

    function getTempleForm() {
        return document.getElementById('temple-form');
    }

    function quickMediaUploader() {
        return {
            isUploading: false,
            errorMessage: '',

            async upload() {
                this.errorMessage = '';

                const files = Array.from(this.$refs.fileInput.files || []);

                if (files.length === 0) {
                    this.errorMessage = 'กรุณาเลือกรูปก่อนอัปโหลด';
                    return;
                }

                const maxFileSize = 5 * 1024 * 1024;
                const invalidFile = files.find((file) => !file.type.startsWith('image/'));
                const oversizedFile = files.find((file) => file.size > maxFileSize);

                if (invalidFile) {
                    this.errorMessage = 'อัปโหลดได้เฉพาะไฟล์รูปภาพเท่านั้น';
                    return;
                }

                if (oversizedFile) {
                    this.errorMessage = `ไฟล์ ${oversizedFile.name} มีขนาดเกิน 5 MB`;
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('visibility', 'public');
                files.forEach((file) => formData.append('files[]', file));

                this.isUploading = true;

                try {
                    const response = await fetch('{{ route('admin.media.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        const payload = await response.json().catch(() => null);
                        this.errorMessage = payload?.message || 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
                        return;
                    }

                    window.location.reload();
                } catch (error) {
                    this.errorMessage = 'เกิดข้อผิดพลาดระหว่างอัปโหลด';
                } finally {
                    this.isUploading = false;
                }
            },
        };
    }

    function updatePrimaryCategoryOptions() {
        const form = getTempleForm();
        const primarySelect = document.getElementById('primary_category_id');

        if (!form || !primarySelect) {
            return;
        }

        const selecteds = Array.from(form.querySelectorAll('input[name="category_ids[]"]'))
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value);

        if (primarySelect.options) {
            Array.from(primarySelect.options).forEach((option) => {
                option.hidden = option.value ? !selecteds.includes(option.value) : false;
            });
        }

        if (primarySelect.value && !selecteds.includes(primarySelect.value)) {
            primarySelect.value = '';
            primarySelect.dispatchEvent(new Event('input', { bubbles: true }));
            primarySelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function collectTempleFields() {
        const form = getTempleForm();
        const data = {};

        if (!form) {
            return data;
        }

        form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
            if (field.type === 'hidden'
                && ! field.matches('[data-rich-editor-input]')
                && ! ['cover_media_id', 'gallery_media_ids[]'].includes(field.name)
            ) {
                return;
            }

            if (field.name.endsWith('[]')) {
                data[field.name] = data[field.name] || [];

                if (field.type !== 'checkbox' || field.checked) {
                    data[field.name].push(field.value);
                }

                return;
            }

            if (field.type === 'checkbox') {
                data[field.name] = field.checked;
                return;
            }

            if (field.type === 'radio') {
                if (field.checked) {
                    data[field.name] = field.value;
                }

                return;
            }

            data[field.name] = field.value;
        });

        return data;
    }

    function saveTempleDraft() {
        window.templeDraft.write({
            fields: collectTempleFields(),
        });
    }

    function restoreTempleFields() {
        const form = getTempleForm();
        const fields = window.templeDraft.get('fields', {});

        if (!form || !fields || window.templeFormHasServerErrors) {
            return;
        }

        Object.entries(fields).forEach(([name, value]) => {
            const elements = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);

            elements.forEach((field) => {
                if (field.type === 'checkbox') {
                    field.checked = Array.isArray(value)
                        ? value.includes(field.value)
                        : Boolean(value);

                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                if (field.type === 'radio') {
                    field.checked = String(field.value) === String(value);
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                field.value = value ?? '';
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));

                if (field.matches('[data-rich-editor-input]') && field._quill) {
                    field._quill.root.innerHTML = value ?? '';
                    const sourceEditor = field
                        .closest('[data-rich-editor]')
                        ?.querySelector('[data-editor-source]');

                    if (sourceEditor) {
                        sourceEditor.value = value ?? '';
                    }
                }
            });
        });

        updatePrimaryCategoryOptions();
    }

    function bindTempleDraftEvents() {
        const form = getTempleForm();

        if (!form || form.dataset.draftBound === 'true') {
            return;
        }

        form.dataset.draftBound = 'true';

        form.addEventListener('input', saveTempleDraft, true);
        form.addEventListener('change', () => {
            updatePrimaryCategoryOptions();
            saveTempleDraft();
        }, true);

        form.addEventListener('submit', () => {
            window.templeDraft.clear();
        });

        window.addEventListener('beforeunload', saveTempleDraft);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initTempleRichEditors();
        restoreTempleFields();
        bindTempleDraftEvents();
    });

    document.addEventListener('alpine:load', () => {
        setTimeout(() => {
            window.Alpine.nextTick(() => {
                initTempleRichEditors();
                restoreTempleFields();
                bindTempleDraftEvents();
                saveTempleDraft();
            });
        }, 100);
    });

    function initTempleRichEditors() {
        if (! window.Quill) {
            return;
        }

        registerTempleRichTextFormats();

        document.querySelectorAll('[data-rich-editor]').forEach((wrapper) => {
            if (wrapper.dataset.richEditorBound === 'true') {
                return;
            }

            const input = wrapper.querySelector('[data-rich-editor-input]');
            const editorBody = wrapper.querySelector('[data-editor-body]');
            const sourceEditor = wrapper.querySelector('[data-editor-source]');
            const sourceToggle = wrapper.querySelector('[data-editor-source-toggle]');
            const toolbar = wrapper.querySelector('[data-editor-toolbar]');
            const counter = wrapper.querySelector('[data-editor-count]');
            const modeLabel = wrapper.querySelector('[data-editor-mode-label]');

            if (! input || ! editorBody || ! toolbar) {
                return;
            }

            wrapper.dataset.richEditorBound = 'true';

            const quill = new Quill(editorBody, {
                theme: 'snow',
                placeholder: wrapper.dataset.placeholder || '',
                modules: {
                    history: {
                        delay: 1000,
                        maxStack: 100,
                        userOnly: true,
                    },
                    toolbar,
                },
                formats: [
                    'blockquote',
                    'bold',
                    'code-block',
                    'header',
                    'indent',
                    'italic',
                    'lineheight',
                    'link',
                    'list',
                    'script',
                    'strike',
                    'underline',
                ],
            });

            quill.root.innerHTML = input.value || '';
            input._quill = quill;
            if (sourceEditor) {
                sourceEditor.value = input.value || '';
            }

            const dispatchChange = () => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            };

            const updateCounter = () => {
                if (counter) {
                    counter.textContent = `${Math.max(quill.getLength() - 1, 0).toLocaleString()} ตัวอักษร`;
                }
            };

            const sync = () => {
                const html = quill.root.innerHTML.trim();
                input.value = html === '<p><br></p>' ? '' : html;
                if (sourceEditor && sourceEditor.classList.contains('hidden')) {
                    sourceEditor.value = input.value;
                }
                updateCounter();
                dispatchChange();
            };

            quill.on('text-change', sync);
            updateCounter();

            if (sourceEditor) {
                sourceEditor.addEventListener('input', () => {
                    input.value = sourceEditor.value.trim();
                    dispatchChange();
                });
            }

            if (sourceToggle && sourceEditor) {
                sourceToggle.addEventListener('click', () => {
                    const sourceIsHidden = sourceEditor.classList.contains('hidden');

                    if (sourceIsHidden) {
                        sourceEditor.value = input.value;
                        editorBody.classList.add('hidden');
                        sourceEditor.classList.remove('hidden');
                        sourceToggle.classList.add('text-blue-300');
                        if (modeLabel) {
                            modeLabel.textContent = 'HTML source';
                        }
                        return;
                    }

                    input.value = sourceEditor.value.trim();
                    quill.root.innerHTML = input.value || '';
                    sourceEditor.classList.add('hidden');
                    editorBody.classList.remove('hidden');
                    sourceToggle.classList.remove('text-blue-300');
                    if (modeLabel) {
                        modeLabel.textContent = 'Rich text';
                    }
                    updateCounter();
                    dispatchChange();
                });
            }
        });
    }

    function initTempleInlineRichEditor(wrapper, row, field) {
        if (! window.Quill || ! wrapper || wrapper.dataset.richEditorBound === 'true') {
            return;
        }

        registerTempleRichTextFormats();

        const editorBody = wrapper.querySelector('[data-editor-body]');
        const toolbar = wrapper.querySelector('[data-editor-toolbar]');

        if (! editorBody || ! toolbar) {
            return;
        }

        wrapper.dataset.richEditorBound = 'true';

        const quill = new Quill(editorBody, {
            theme: 'snow',
            placeholder: wrapper.dataset.placeholder || '',
            modules: {
                toolbar,
            },
            formats: ['bold', 'italic', 'lineheight', 'list', 'link', 'underline'],
        });

        quill.root.innerHTML = row[field] || '';

        quill.on('text-change', () => {
            const html = quill.root.innerHTML.trim();
            row[field] = html === '<p><br></p>' ? '' : html;
            saveTempleDraft();
        });
    }

    function registerTempleRichTextFormats() {
        if (window.templeRichTextFormatsRegistered || ! window.Quill) {
            return;
        }

        const Parchment = window.Quill.import('parchment');
        const LineHeight = new Parchment.ClassAttributor('lineheight', 'ql-lineheight', {
            scope: Parchment.Scope.BLOCK,
            whitelist: ['tight', 'normal', 'relaxed', 'loose'],
        });

        window.Quill.register(LineHeight, true);
        window.templeRichTextFormatsRegistered = true;
    }

    function repeaterManager(prefix, initialRows = []) {
        return {
            rows: [],

            init() {
                const fallbackRows = initialRows.length ? initialRows : [];
                const rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get(prefix, fallbackRows);

                this.rows = rows.map((row) => ({
                    _key: row._key || `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    ...row,
                }));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        [prefix]: value,
                    });
                });
            },

            addRow(defaults = {}) {
                this.rows.push({
                    _key: `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    ...defaults,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function openingHoursManager() {
        const existing = @json($jsOpeningHours);

        return {
            dayNames: @json(array_values($days)),
            rows: [],

            init() {
                const defaultRows = existing.length
                    ? this.compactRows(existing)
                    : [
                        {
                            preset: 'everyday',
                            day_from: 0,
                            day_to: 6,
                            open_time: '08:00',
                            close_time: '16:00',
                            note: '',
                            is_closed: false,
                        },
                    ];

                this.rows = window.templeFormHasServerErrors
                    ? defaultRows
                    : window.templeDraft.get('opening_hours_rows', defaultRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        opening_hours_rows: value,
                    });
                });
            },

            compactRows(rows) {
                const normalizedRows = rows
                    .map((row) => ({
                        day: Number(row.day_of_week ?? 0),
                        open_time: row.open_time || '',
                        close_time: row.close_time || '',
                        is_closed: Boolean(row.is_closed),
                        note: row.note || '',
                    }))
                    .filter((row) => row.day >= 0 && row.day <= 6)
                    .sort((a, b) => a.day - b.day);

                const groupedRows = [];

                normalizedRows.forEach((row) => {
                    const last = groupedRows[groupedRows.length - 1];
                    const hasSames = last
                        && last.day_to + 1 === row.day
                        && last.open_time === row.open_time
                        && last.close_time === row.close_time
                        && last.is_closed === row.is_closed
                        && last.note === row.note;

                    if (hasSames) {
                        last.day_to = row.day;
                        last.preset = this.detectPreset(last.day_from, last.day_to);
                        return;
                    }

                    groupedRows.push({
                        preset: 'oneday',
                        day_from: row.day,
                        day_to: row.day,
                        open_time: row.open_time,
                        close_time: row.close_time,
                        is_closed: row.is_closed,
                        note: row.note,
                    });
                });

                const first = groupedRows[0];
                const last = groupedRows[groupedRows.length - 1];

                if (
                    groupedRows.length > 1
                    && first.day_from === 0
                    && first.day_to === 0
                    && last.day_from === 6
                    && last.day_to === 6
                    && first.open_time === last.open_time
                    && first.close_time === last.close_time
                    && first.is_closed === last.is_closed
                    && first.note === last.note
                ) {
                    last.day_to = 0;
                    groupedRows.shift();
                }

                return groupedRows.map((row) => ({
                    ...row,
                    preset: this.detectPreset(row.day_from, row.day_to),
                }));
            },

            detectPreset(from, to) {
                if (from === 0 && to === 6) {
                    return 'everyday';
                }

                if (from === 1 && to === 5) {
                    return 'weekdays';
                }

                if (from === 6 && to === 0) {
                    return 'weekend';
                }

                return from === to ? 'oneday' : 'custom';
            },

            addRow() {
                this.rows.push({
                    preset: 'weekdays',
                    day_from: 1,
                    day_to: 5,
                    open_time: '08:00',
                    close_time: '16:00',
                    note: '',
                    is_closed: false,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            applyPreset(row) {
                if (row.preset === 'everyday') {
                    row.day_from = 0;
                    row.day_to = 6;
                }

                if (row.preset === 'weekdays') {
                    row.day_from = 1;
                    row.day_to = 5;
                }

                if (row.preset === 'weekend') {
                    row.day_from = 6;
                    row.day_to = 0;
                }

                if (row.preset === 'oneday') {
                    row.day_to = row.day_from;
                }
            },

            getDaysInRange(from, to) {
                const days = [];

                if (from <= to) {
                    for (let day = from; day <= to; day++) {
                        days.push(day);
                    }

                    return days;
                }

                for (let day = from; day <= 6; day++) {
                    days.push(day);
                }

                for (let day = 0; day <= to; day++) {
                    days.push(day);
                }

                return days;
            },

            previewDays(row) {
                const days = this.getDaysInRange(Number(row.day_from), Number(row.day_to));

                if (days.length === 1) {
                    return this.dayNames[days[0]];
                }

                return days.map((day) => this.dayNames[day]).join(', ');
            },

            expandedRows() {
                const itemsByDay = {};

                this.rows.forEach((row) => {
                    this.getDaysInRange(Number(row.day_from), Number(row.day_to)).forEach((day) => {
                        itemsByDay[day] = {
                            day_of_week: day,
                            open_time: row.open_time,
                            close_time: row.close_time,
                            note: row.note,
                            is_closed: row.is_closed,
                        };
                    });
                });

                return Object.values(itemsByDay).sort((a, b) => a.day_of_week - b.day_of_week);
            },
        };
    }

    function feesManager() {
        const existing = @json($jsFees);

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('fees_rows', fallbackRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        fees_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    fee_type: '',
                    label: '',
                    amount: '',
                    currency: 'THB',
                    note: '',
                    is_active: true,
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function facilitiesManager() {
        const existing = @json($jsFacilityItems);
        const facilities = @json($jsFacilities);

        return {
            facilities,
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = (window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('facility_items_rows', fallbackRows)
                ).map((row) => ({
                    _key: row._key || `facility-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    facility_id: row.facility_id ? String(row.facility_id) : '',
                    facility_search: row.facility_search || '',
                    facility_name: row.facility_name || '',
                    value: row.value || '',
                    note: row.note || '',
                    sort_order: row.sort_order ?? 0,
                }));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        facility_items_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    _key: `facility-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    facility_id: '',
                    facility_search: '',
                    facility_name: '',
                    value: '',
                    note: '',
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            filteredFacilities(row) {
                const keyword = (row.facility_search || '').toLowerCase().trim();

                return this.facilities
                    .filter((facility) => {
                        if (row.facility_id && String(facility.id) === String(row.facility_id)) {
                            return true;
                        }

                        return !keyword || facility.name.toLowerCase().includes(keyword) || String(facility.id).includes(keyword);
                    })
                    .slice(0, 80);
            },
        };
    }

    function travelInfosManager() {
        const existing = @json($jsTravelInfos);

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('travel_infos_rows', fallbackRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        travel_infos_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    travel_type: '',
                    start_place: '',
                    distance_km: '',
                    duration_minutes: '',
                    cost_estimate: '',
                    note: '',
                    is_active: true,
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function nearbyPlacesManager() {
        const existing = @json($jsNearbyPlaces);
        const nearbyTemples = @json($jsNearbyTemples);

        return {
            rows: [],
            nearbyTemples,

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('nearby_places_rows', fallbackRows);
                this.rows = this.rows.map((row) => ({
                    ...row,
                    nearby_temple_id: row.nearby_temple_id ? String(row.nearby_temple_id) : '',
                    temple_search: row.temple_search || '',
                }));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        nearby_places_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    nearby_temple_id: '',
                    temple_search: '',
                    relation_type: '',
                    distance_km: '',
                    duration_minutes: '',
                    score: '',
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            filteredNearbyTemples(row) {
                const keyword = (row.temple_search || '').toLowerCase().trim();

                return this.nearbyTemples
                    .filter((temple) => {
                        if (row.nearby_temple_id && String(temple.id) === String(row.nearby_temple_id)) {
                            return true;
                        }

                        return !keyword || temple.search.includes(keyword);
                    })
                    .slice(0, 80);
            },
        };
    }
</script>
