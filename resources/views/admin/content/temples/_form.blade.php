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
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $jsOpeningHours = $openingHours->map(function ($h) {
        return [
            'day_of_week' => $h->day_of_week,
            'open_time'   => $h->open_time ? substr($h->open_time, 0, 5) : '',
            'close_time'  => $h->close_time ? substr($h->close_time, 0, 5) : '',
            'is_closed'   => (bool) $h->is_closed,
            'note'        => $h->note ?? '',
        ];
    })->values();

    $jsFees = $fees->map(function ($f) {
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

    $jsHighlights = $highlights->map(function ($h) {
        return [
            'title'       => $h->title,
            'description' => $h->description ?? '',
            'sort_order'  => $h->sort_order ?? 0,
        ];
    })->values();

    $jsVisitRules = $visitRules->map(function ($r) {
        return [
            'rule_text'  => $r->rule_text,
            'sort_order' => $r->sort_order ?? 0,
        ];
    })->values();

    $jsTravelInfos = $travelInfos->map(function ($t) {
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

    $jsNearbyPlaces = $nearbyPlaces->map(function ($n) {
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

    {{-- Section: Basic Info --}}
    <section
        x-data="{
            title: window.templeDraftValue('title', @js(old('title', $content?->title))),
            slug: window.templeDraftValue('slug', @js(old('slug', $content?->slug))),
            slugEdited: Boolean(window.templeDraftValue('slug', @js(old('slug', $content?->slug)))),
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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลพื้นฐาน</h2>
            <p class="mt-1 text-xs text-slate-400">ชื่อวัด รายละเอียด สถานะ และข้อมูลเผยแพร่</p>
        </div>

        <div class="space-y-5 p-6">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ชื่อวัด <span class="text-rose-400">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    x-model="title"
                    @input="syncSlug()"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('title') border-rose-400 @enderror"
                    placeholder="เช่น วัดพระแก้ว"
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
                        สร้างจากชื่อวัดอีกครั้ง
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
                    ระบบจะสร้าง slug ให้อัตโนมัติจากชื่อวัด แต่สามารถแก้เองได้ แนะนำให้ใช้ภาษาอังกฤษ ตัวเลข และขีดกลาง
                </p>

                @error('slug')
                    <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายสั้น</label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('excerpt') border-rose-400 @enderror"
                    placeholder="คำอธิบายสั้นๆ เกี่ยวกับวัด"
                >{{ old('excerpt', $content?->excerpt) }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">รายละเอียด</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('description') border-rose-400 @enderror"
                    placeholder="รายละเอียดของวัด"
                >{{ old('description', $content?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_featured', $content?->is_featured))
                    >
                    แนะนำบนหน้าเว็บ
                </label>

                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_popular"
                        value="1"
                        class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_popular', $content?->is_popular))
                    >
                    ยอดนิยม
                </label>
            </div>
        </div>
    </section>

    {{-- Section: Temple Details --}}
    <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลเฉพาะของวัด</h2>
            <p class="mt-1 text-xs text-slate-400">ประเภทวัด นิกาย สถาปัตยกรรม ประวัติ และข้อแนะนำการเข้าชม</p>
        </div>

        <div class="space-y-5 p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="temple_type" class="mb-1.5 block text-sm font-medium text-slate-300">ประเภทวัด</label>
                    <select
                        id="temple_type"
                        name="temple_type"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกประเภทวัด —</option>
                        @foreach (['วัดราษฎร์', 'พระอารามหลวง', 'วัดหลวง', 'สำนักสงฆ์', 'วัดร้าง', 'อื่น ๆ'] as $option)
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
                            value="{{ old('recommended_visit_start_time', $temple?->recommended_visit_start_time ? \Carbon\Carbon::parse($temple->recommended_visit_start_time)->format('H:i') : '') }}"
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
                            value="{{ old('recommended_visit_end_time', $temple?->recommended_visit_end_time ? \Carbon\Carbon::parse($temple->recommended_visit_end_time)->format('H:i') : '') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>
                </div>
            </div>

            <div>
                <label for="history" class="mb-1.5 block text-sm font-medium text-slate-300">ประวัติ</label>
                <textarea
                    id="history"
                    name="history"
                    rows="5"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="ประวัติความเป็นมาของวัด"
                >{{ old('history', $temple?->history) }}</textarea>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Section: SEO --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
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
                        placeholder="เช่น วัดพระแก้ว | PAPAIWAT"
                    >
                    <p class="mt-1 text-xs text-slate-500">ควรสั้น กระชับ และสื่อถึงชื่อวัดหรือจังหวัด</p>
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">Meta Description</label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คำอธิบายสั้นสำหรับผลการค้นหา เช่น ประวัติ จุดเด่น และข้อมูลการเข้าชม"
                    >{{ old('meta_description', $content?->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">ใช้สำหรับแสดงในผลการค้นหาและ social preview</p>
                </div>
            </div>
        </section>

        {{-- Section: Categories --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            
            {{-- Header --}}
            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                    <p class="mt-1 text-xs text-slate-400">เลือกหมวดหมู่และหมวดหมู่หลักของวัด</p>
                </div>

                <a
                    href="{{ route('admin.categories.index') }}"
                    target="_blank"
                    class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการหมวดหมู่
                </a>
            </div>

            {{-- Content --}}
            <div class="p-6">
                @if ($categories->isEmpty())
                    <p class="text-sm text-slate-400">ไม่มีหมวดหมู่</p>
                @else
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($categories as $cat)
                            @php
                                $checked = in_array($cat->id, old('category_ids', $existingCategoryIds));
                            @endphp

                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 px-4 py-3 transition hover:bg-white/[0.06] @if($checked) border-blue-400/40 bg-blue-500/10 @else bg-slate-950/40 @endif">
                                <input
                                    type="checkbox"
                                    name="category_ids[]"
                                    value="{{ $cat->id }}"
                                    class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                                    @checked($checked)
                                    onchange="updatePrimaryCategoryOptions()"
                                >
                                <span class="text-sm text-slate-300">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <label for="primary_category_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                            หมวดหมู่หลัก
                        </label>

                        <select
                            id="primary_category_id"
                            name="primary_category_id"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 sm:w-64"
                        >
                            <option value="">— ไม่ระบุ —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('primary_category_id', $primaryCategoryId) == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-1 text-xs text-slate-500">
                            หมวดหมู่หลักควรเป็นหมวดที่ใช้จัดกลุ่มหลักบนหน้าเว็บไซต์
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Section: Media --}}
    <section
        x-data="{
            mediaSearch: '',
            selectedCover: @js((string) old('cover_media_id', $coverMedia?->media_id ?? '')),
            selectedGallery: @js(array_map('strval', old('gallery_media_ids', $galleryMediaIds))),

            coverHtml: @js(view('admin.content.temples.partials._cover_media_grid', [
                'mediaItems' => $coverMediaItems ?? $mediaItems,
            ])->render()),

            galleryHtml: @js(view('admin.content.temples.partials._gallery_media_grid', [
                'mediaItems' => $galleryMediaItems ?? $mediaItems,
            ])->render()),

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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">รูปภาพและมีเดีย</h2>
                <p class="mt-1 text-xs text-slate-400">เลือกรูป Cover และ Gallery สำหรับหน้าแสดงผล</p>
            </div>

            <a
                href="{{ route('admin.media.index') }}"
                target="_blank"
                class="shrink-0 rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs text-blue-300 transition hover:bg-blue-500/20"
            >
                + ไปจัดการมีเดีย
            </a>
        </div>

        <div class="space-y-8 p-6">
            {{-- Quick Upload --}}
            <div
                x-data="quickMediaUploader()"
                class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="quick_media_file" class="mb-1.5 block text-sm font-medium text-slate-300">
                            อัปโหลดรูปใหม่แบบด่วน
                        </label>

                        <input
                            id="quick_media_file"
                            type="file"
                            accept="image/*"
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
                        class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span x-show="!isUploading">อัปโหลด</span>
                        <span x-show="isUploading">กำลังอัปโหลด...</span>
                    </button>
                </div>

                <p class="mt-2 text-xs text-slate-500">
                    รูปจะถูกบันทึกเข้า Media Library แบบไม่มีโฟลเดอร์ แล้ว refresh หน้าเพื่อให้เลือกรูปได้
                </p>
            </div>

            {{-- Search --}}
            <div class="max-w-md">
                <label for="media_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ค้นหารูปจากชื่อ
                </label>

                <input
                    id="media_search"
                    type="text"
                    x-model="mediaSearch"
                    placeholder="พิมพ์ชื่อรูป, title หรือชื่อไฟล์..."
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >

                <p class="mt-1 text-xs text-slate-500">
                    ใช้ค้นหาเฉพาะรูปที่แสดงอยู่ในหน้าปัจจุบันของ Cover และ Gallery
                </p>
            </div>

            {{-- Hidden values --}}
            <input type="hidden" name="cover_media_id" :value="selectedCover">

            <template x-for="mediaId in selectedGallery" :key="mediaId">
                <input type="hidden" name="gallery_media_ids[]" :value="mediaId">
            </template>

            {{-- Cover --}}
            <div class="space-y-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">รูป Cover</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        เลือกรูปหลักที่ใช้แสดงเป็นภาพหน้าปกของเนื้อหานี้
                    </p>
                </div>

                <div
                    x-ref="coverPicker"
                    x-html="coverHtml"
                    @click="loadCoverPage($event)"
                ></div>
            </div>

            {{-- Gallery --}}
            <div class="space-y-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">Gallery Images</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        เลือกได้หลายรูปสำหรับแสดงในแกลเลอรีของหน้าแสดงผล
                    </p>
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
    <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ที่ตั้ง</h2>
            <p class="mt-1 text-xs text-slate-400">ที่อยู่ พิกัด และลิงก์ Google Maps</p>
        </div>
        <div class="space-y-5 p-6">
            <div>
                <label for="address_line" class="mb-1.5 block text-sm font-medium text-slate-300">Address Line</label>
                <input type="text" id="address_line" name="address[address_line]" value="{{ old('address.address_line', $address?->address_line) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="เลขที่ ถนน">
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
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

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="mb-1.5 block text-sm font-medium text-slate-300">Latitude</label>
                    <input type="text" id="latitude" name="address[latitude]" value="{{ old('address.latitude', $address?->latitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white font-mono outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="13.7563">
                </div>
                <div>
                    <label for="longitude" class="mb-1.5 block text-sm font-medium text-slate-300">Longitude</label>
                    <input type="text" id="longitude" name="address[longitude]" value="{{ old('address.longitude', $address?->longitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white font-mono outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="100.5018">
                </div>
                <div>
                    <label for="google_place_id" class="mb-1.5 block text-sm font-medium text-slate-300">Google Place ID</label>
                    <input type="text" id="google_place_id" name="address[google_place_id]" value="{{ old('address.google_place_id', $address?->google_place_id) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white font-mono outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="google_maps_url" class="mb-1.5 block text-sm font-medium text-slate-300">Google Maps URL</label>
                    <input type="url" id="google_maps_url" name="address[google_maps_url]" value="{{ old('address.google_maps_url', $address?->google_maps_url) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="https://maps.google.com/...">
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Opening Hours --}}
    <section
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
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

                        {{-- Actions --}}
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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="feesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ค่าธรรมเนียม</h2>
                <p class="mt-1 text-xs text-slate-400">ค่าเข้าชม ค่าจอดรถ หรือค่าใช้จ่ายอื่น ๆ</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มค่าธรรมเนียม
            </button>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="grid grid-cols-12 items-start gap-3">
                            {{-- Fee Type --}}
                            <div class="col-span-12 md:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-slate-400">ประเภทค่าธรรมเนียม</label>
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
                                    placeholder="เช่น ค่าเข้าชมผู้ใหญ่"
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

                            {{-- Active --}}
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
                    ยังไม่มีข้อมูล — กดเพิ่มค่าธรรมเนียมเพื่อเพิ่ม
                </p>
            </div>
        </div>
    </section>

    {{-- Section: Facilities --}}
    <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">สิ่งอำนวยความสะดวก</h2>
            <p class="mt-1 text-xs text-slate-400">สิ่งอำนวยความสะดวกภายในวัด</p>
        </div>
        <div class="p-6">
            @if ($facilities->isEmpty())
                <p class="text-sm text-slate-400">ไม่มีข้อมูล facility</p>
            @else
                <div class="space-y-3">
                    @foreach ($facilities as $fi => $facility)
                        @php
                            $existingItem = $facilityItems->firstWhere('facility_id', $facility->id);
                        @endphp
                        <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-3">
                            <input type="hidden" name="facility_items[{{ $fi }}][facility_id]" value="{{ $facility->id }}">
                            <div class="col-span-12 flex items-center gap-2 pt-1 sm:col-span-4">
                                <span class="text-sm font-medium text-slate-200">{{ $facility->name }}</span>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-400">Value</label>
                                <input type="text" name="facility_items[{{ $fi }}][value]" value="{{ old("facility_items.$fi.value", $existingItem?->value) }}" class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400" placeholder="เช่น มี, ฟรี, 10 บาท">
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-400">Note</label>
                                <input type="text" name="facility_items[{{ $fi }}][note]" value="{{ old("facility_items.$fi.note", $existingItem?->note) }}" class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400" placeholder="หมายเหตุ">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Section: Highlights --}}
    <section
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="repeaterManager('highlights', @json($jsHighlights))"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">จุดเด่นของวัด</h2>
                <p class="mt-1 text-xs text-slate-400">ไฮไลต์สำคัญที่ใช้แสดงในหน้า detail</p>
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
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Title --}}
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

                        {{-- Sort Order --}}
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

                        {{-- Description --}}
                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">รายละเอียด</label>

                            <textarea
                                :name="`highlights[${index}][description]`"
                                x-model="row.description"
                                rows="3"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="รายละเอียดเพิ่มเติมของจุดเด่นนี้"
                            ></textarea>
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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
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
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Rule Text --}}
                        <div class="col-span-12 md:col-span-10">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                รายละเอียดกฎ <span class="text-rose-400">*</span>
                            </label>

                            <textarea
                                :name="`visit_rules[${index}][rule_text]`"
                                x-model="row.rule_text"
                                rows="3"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น แต่งกายสุภาพ ไม่สวมกางเกงขาสั้น หรือเสื้อแขนกุด"
                            ></textarea>

                            <p class="mt-1 text-xs text-slate-500">
                                เขียนเป็นข้อความสั้น กระชับ และอ่านเข้าใจง่ายสำหรับผู้เข้าชม
                            </p>
                        </div>

                        {{-- Sort Order --}}
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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="travelInfosManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ข้อมูลการเดินทาง</h2>
                <p class="mt-1 text-xs text-slate-400">วิธีเดินทาง ระยะทาง ระยะเวลา และค่าใช้จ่ายโดยประมาณ</p>
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

                        {{-- Active --}}
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
                            <label class="mb-1 block text-xs font-medium text-slate-400">ค่าใช้จ่ายโดยประมาณ</label>
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
        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="nearbyPlacesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">วัดใกล้เคียง</h2>
                <p class="mt-1 text-xs text-slate-400">เชื่อมโยงวัดที่อยู่ใกล้กันหรือเกี่ยวข้องกัน</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มวัดใกล้เคียง
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Temple --}}
                        <div class="col-span-12 md:col-span-5">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                วัดที่เกี่ยวข้อง <span class="text-rose-400">*</span>
                            </label>

                            <select
                                :name="`nearby_places[${index}][nearby_temple_id]`"
                                x-model="row.nearby_temple_id"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="">— เลือกวัด —</option>
                                @foreach ($nearbyTemples as $nt)
                                    <option value="{{ $nt->id }}">{{ $nt->content?->title ?? "Temple #$nt->id" }}</option>
                                @endforeach
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

                        {{-- Sort Order --}}
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
                ยังไม่มีข้อมูล — กดเพิ่มวัดใกล้เคียงเพื่อเชื่อมโยงวัดที่เกี่ยวข้อง
            </p>
        </div>
    </section>
</div>

<script>
    window.templeDraft = {
        key: 'papaiwat:temple-form-draft:' + window.location.pathname,

        read() {
            try {
                return JSON.parse(localStorage.getItem(this.key)) || {};
            } catch (error) {
                return {};
            }
        },

        write(payload) {
            localStorage.setItem(this.key, JSON.stringify({
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
            localStorage.removeItem(this.key);
        },
    };

    window.templeDraftValue = function (name, fallback = '') {
        const fields = window.templeDraft.get('fields', {});

        return Object.prototype.hasOwnProperty.call(fields, name)
            ? fields[name]
            : fallback;
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

                const file = this.$refs.fileInput.files[0];

                if (!file) {
                    this.errorMessage = 'กรุณาเลือกรูปก่อนอัปโหลด';
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('file', file);
                formData.append('visibility', 'public');

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
                        this.errorMessage = 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
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

        const selectedValues = Array.from(form.querySelectorAll('input[name="category_ids[]"]'))
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value);

        Array.from(primarySelect.options).forEach((option) => {
            option.hidden = option.value ? !selectedValues.includes(option.value) : false;
        });

        if (primarySelect.value && !selectedValues.includes(primarySelect.value)) {
            primarySelect.value = '';
        }
    }

    function collectTempleFields() {
        const form = getTempleForm();
        const data = {};

        if (!form) {
            return data;
        }

        form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
            if (field.type === 'hidden') {
                return;
            }

            if (field.type === 'checkbox') {
                if (field.name.endsWith('[]')) {
                    data[field.name] = data[field.name] || [];

                    if (field.checked) {
                        data[field.name].push(field.value);
                    }

                    return;
                }

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

        if (!form || !fields) {
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
        bindTempleDraftEvents();
    });

    document.addEventListener('alpine:load', () => {
        setTimeout(() => {
            window.Alpine.nextTick(() => {
                restoreTempleFields();
                bindTempleDraftEvents();
                saveTempleDraft();
            });
        }, 100);
    });

    function repeaterManager(prefix, initialRows = []) {
        return {
            rows: [],

            init() {
                this.rows = window.templeDraft.get(prefix, initialRows.length ? initialRows : []);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        [prefix]: value,
                    });
                });
            },

            addRow(defaults = {}) {
                this.rows.push(defaults);
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
                    ? existing.map((row) => ({
                        preset: 'oneday',
                        day_from: Number(row.day_of_week ?? 0),
                        day_to: Number(row.day_of_week ?? 0),
                        open_time: row.open_time || '',
                        close_time: row.close_time || '',
                        is_closed: Boolean(row.is_closed),
                        note: row.note || '',
                    }))
                    : [
                        {
                            preset: 'everyday',
                            day_from: 0,
                            day_to: 6,
                            open_time: '08:00',
                            close_time: '17:00',
                            note: '',
                            is_closed: false,
                        },
                    ];

                this.rows = window.templeDraft.get('opening_hours_rows', defaultRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        opening_hours_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    preset: 'weekdays',
                    day_from: 1,
                    day_to: 5,
                    open_time: '08:00',
                    close_time: '17:00',
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
                const items = [];

                this.rows.forEach((row) => {
                    this.getDaysInRange(Number(row.day_from), Number(row.day_to)).forEach((day) => {
                        items.push({
                            day_of_week: day,
                            open_time: row.open_time,
                            close_time: row.close_time,
                            note: row.note,
                            is_closed: row.is_closed,
                        });
                    });
                });

                return items;
            },
        };
    }

    function feesManager() {
        const existing = @json($jsFees);

        return {
            rows: [],

            init() {
                this.rows = window.templeDraft.get('fees_rows', existing.length ? existing : []);

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

    function travelInfosManager() {
        const existing = @json($jsTravelInfos);

        return {
            rows: [],

            init() {
                this.rows = window.templeDraft.get('travel_infos_rows', existing.length ? existing : []);

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

        return {
            rows: [],

            init() {
                this.rows = window.templeDraft.get('nearby_places_rows', existing.length ? existing : []);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        nearby_places_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    nearby_temple_id: '',
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
        };
    }
</script>