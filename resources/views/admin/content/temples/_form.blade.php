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

    // Prepare JS data — avoid @json inside arrow functions (Blade parse error)
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

<div class="space-y-6">

    {{-- ── Section: Basic Info ──────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Basic Information</h2>
            <p class="mt-0.5 text-xs text-slate-500">ข้อมูลพื้นฐานของวัด</p>
        </div>
        <div class="space-y-5 p-6">

            {{-- Title --}}
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">
                    ชื่อวัด <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $content?->title) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 @error('title') border-rose-400 @enderror"
                    placeholder="เช่น วัดพระแก้ว"
                >
                @error('title')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Slug <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $content?->slug) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-mono outline-none transition focus:border-slate-900 @error('slug') border-rose-400 @enderror"
                    placeholder="wat-phra-kaew"
                >
                @error('slug')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Excerpt --}}
            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-700">Excerpt</label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 @error('excerpt') border-rose-400 @enderror"
                    placeholder="คำอธิบายสั้นๆ เกี่ยวกับวัด"
                >{{ old('excerpt', $content?->excerpt) }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 @error('description') border-rose-400 @enderror"
                    placeholder="รายละเอียดของวัด"
                >{{ old('description', $content?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status / Published At --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 @error('status') border-rose-400 @enderror"
                    >
                        @foreach ($statusOptions as $opt)
                            <option value="{{ $opt }}" @selected(old('status', $content?->status) === $opt)>
                                {{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-700">Published At</label>
                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        value="{{ old('published_at', $content?->published_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    >
                </div>
            </div>

            {{-- Flags --}}
            <div class="flex flex-wrap gap-6">
                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-700">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="h-4 w-4 rounded border-slate-300 text-slate-900"
                        @checked(old('is_featured', $content?->is_featured))
                    >
                    Featured
                </label>
                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-700">
                    <input
                        type="checkbox"
                        name="is_popular"
                        value="1"
                        class="h-4 w-4 rounded border-slate-300 text-slate-900"
                        @checked(old('is_popular', $content?->is_popular))
                    >
                    Popular
                </label>
            </div>
        </div>
    </div>

    {{-- ── Section: Temple Details ──────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Temple Details</h2>
            <p class="mt-0.5 text-xs text-slate-500">ข้อมูลเฉพาะของวัด</p>
        </div>
        <div class="space-y-5 p-6">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="temple_type" class="mb-1.5 block text-sm font-medium text-slate-700">Temple Type</label>
                    <input
                        type="text"
                        id="temple_type"
                        name="temple_type"
                        value="{{ old('temple_type', $temple?->temple_type) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น วัดราษฎร์, พระอารามหลวง"
                    >
                </div>

                <div>
                    <label for="sect" class="mb-1.5 block text-sm font-medium text-slate-700">Sect / นิกาย</label>
                    <input
                        type="text"
                        id="sect"
                        name="sect"
                        value="{{ old('sect', $temple?->sect) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น มหานิกาย, ธรรมยุต"
                    >
                </div>

                <div>
                    <label for="architecture_style" class="mb-1.5 block text-sm font-medium text-slate-700">Architecture Style</label>
                    <input
                        type="text"
                        id="architecture_style"
                        name="architecture_style"
                        value="{{ old('architecture_style', $temple?->architecture_style) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น รัตนโกสินทร์, ล้านนา"
                    >
                </div>

                <div>
                    <label for="founded_year" class="mb-1.5 block text-sm font-medium text-slate-700">Founded Year</label>
                    <input
                        type="number"
                        id="founded_year"
                        name="founded_year"
                        value="{{ old('founded_year', $temple?->founded_year) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น 1782"
                        min="0"
                    >
                </div>

                <div>
                    <label for="dress_code" class="mb-1.5 block text-sm font-medium text-slate-700">Dress Code</label>
                    <input
                        type="text"
                        id="dress_code"
                        name="dress_code"
                        value="{{ old('dress_code', $temple?->dress_code) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น สุภาพ ไม่สวมเสื้อแขนกุด"
                    >
                </div>

                <div>
                    <label for="recommended_visit_duration_minutes" class="mb-1.5 block text-sm font-medium text-slate-700">Recommended Visit Duration (minutes)</label>
                    <input
                        type="number"
                        id="recommended_visit_duration_minutes"
                        name="recommended_visit_duration_minutes"
                        value="{{ old('recommended_visit_duration_minutes', $temple?->recommended_visit_duration_minutes) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="เช่น 60"
                        min="0"
                    >
                </div>
            </div>

            {{-- History --}}
            <div>
                <label for="history" class="mb-1.5 block text-sm font-medium text-slate-700">History / ประวัติ</label>
                <textarea
                    id="history"
                    name="history"
                    rows="5"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    placeholder="ประวัติความเป็นมาของวัด"
                >{{ old('history', $temple?->history) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── Section: SEO ─────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">SEO</h2>
        </div>
        <div class="space-y-5 p-6">
            <div>
                <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-700">Meta Title</label>
                <input
                    type="text"
                    id="meta_title"
                    name="meta_title"
                    value="{{ old('meta_title', $content?->meta_title) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                >
            </div>
            <div>
                <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-700">Meta Description</label>
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="2"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                >{{ old('meta_description', $content?->meta_description) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── Section: Categories ──────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Categories</h2>
        </div>
        <div class="p-6">
            @if ($categories->isEmpty())
                <p class="text-sm text-slate-500">ไม่มีหมวดหมู่</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($categories as $cat)
                        @php $checked = in_array($cat->id, old('category_ids', $existingCategoryIds)); @endphp
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 @if($checked) bg-slate-50 border-slate-400 @endif">
                            <input
                                type="checkbox"
                                name="category_ids[]"
                                value="{{ $cat->id }}"
                                class="h-4 w-4 rounded border-slate-300"
                                @checked($checked)
                                onchange="updatePrimaryCategoryOptions()"
                            >
                            <span class="text-sm text-slate-700">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label for="primary_category_id" class="mb-1.5 block text-sm font-medium text-slate-700">Primary Category</label>
                    <select
                        id="primary_category_id"
                        name="primary_category_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 sm:w-64"
                    >
                        <option value="">— ไม่ระบุ —</option>
                        @foreach ($categories as $cat)
                            <option
                                value="{{ $cat->id }}"
                                @selected(old('primary_category_id', $primaryCategoryId) == $cat->id)
                            >
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Section: Media ───────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Media</h2>
            <p class="mt-0.5 text-xs text-slate-500">Cover image และ Gallery</p>
        </div>
        <div class="space-y-5 p-6">

            {{-- Cover --}}
            <div>
                <label for="cover_media_id" class="mb-1.5 block text-sm font-medium text-slate-700">Cover Image</label>
                <select
                    id="cover_media_id"
                    name="cover_media_id"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                >
                    <option value="">— ไม่ระบุ —</option>
                    @foreach ($mediaItems as $media)
                        <option
                            value="{{ $media->id }}"
                            @selected(old('cover_media_id', $coverMedia?->media_id) == $media->id)
                        >
                            [{{ $media->id }}] {{ $media->title ?: $media->original_filename }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Gallery --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Gallery Images</label>
                @if ($mediaItems->isEmpty())
                    <p class="text-sm text-slate-500">ไม่มีไฟล์มีเดีย</p>
                @else
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($mediaItems as $media)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50">
                                <input
                                    type="checkbox"
                                    name="gallery_media_ids[]"
                                    value="{{ $media->id }}"
                                    class="h-4 w-4 rounded border-slate-300"
                                    @checked(in_array($media->id, old('gallery_media_ids', $galleryMediaIds)))
                                >
                                <span class="truncate text-sm text-slate-700">
                                    [{{ $media->id }}] {{ $media->title ?: $media->original_filename }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Section: Address ────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Address</h2>
        </div>
        <div class="space-y-5 p-6">

            <div>
                <label for="address_line" class="mb-1.5 block text-sm font-medium text-slate-700">Address Line</label>
                <input
                    type="text"
                    id="address_line"
                    name="address[address_line]"
                    value="{{ old('address.address_line', $address?->address_line) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    placeholder="เลขที่ ถนน"
                >
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="subdistrict" class="mb-1.5 block text-sm font-medium text-slate-700">Subdistrict / แขวง-ตำบล</label>
                    <input
                        type="text"
                        id="subdistrict"
                        name="address[subdistrict]"
                        value="{{ old('address.subdistrict', $address?->subdistrict) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    >
                </div>
                <div>
                    <label for="district" class="mb-1.5 block text-sm font-medium text-slate-700">District / เขต-อำเภอ</label>
                    <input
                        type="text"
                        id="district"
                        name="address[district]"
                        value="{{ old('address.district', $address?->district) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    >
                </div>
                <div>
                    <label for="province" class="mb-1.5 block text-sm font-medium text-slate-700">Province / จังหวัด</label>
                    <input
                        type="text"
                        id="province"
                        name="address[province]"
                        value="{{ old('address.province', $address?->province) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    >
                </div>
                <div>
                    <label for="postal_code" class="mb-1.5 block text-sm font-medium text-slate-700">Postal Code</label>
                    <input
                        type="text"
                        id="postal_code"
                        name="address[postal_code]"
                        value="{{ old('address.postal_code', $address?->postal_code) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="mb-1.5 block text-sm font-medium text-slate-700">Latitude</label>
                    <input
                        type="text"
                        id="latitude"
                        name="address[latitude]"
                        value="{{ old('address.latitude', $address?->latitude) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-mono outline-none transition focus:border-slate-900"
                        placeholder="13.7563"
                    >
                </div>
                <div>
                    <label for="longitude" class="mb-1.5 block text-sm font-medium text-slate-700">Longitude</label>
                    <input
                        type="text"
                        id="longitude"
                        name="address[longitude]"
                        value="{{ old('address.longitude', $address?->longitude) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-mono outline-none transition focus:border-slate-900"
                        placeholder="100.5018"
                    >
                </div>
                <div>
                    <label for="google_place_id" class="mb-1.5 block text-sm font-medium text-slate-700">Google Place ID</label>
                    <input
                        type="text"
                        id="google_place_id"
                        name="address[google_place_id]"
                        value="{{ old('address.google_place_id', $address?->google_place_id) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-mono outline-none transition focus:border-slate-900"
                    >
                </div>
                <div>
                    <label for="google_maps_url" class="mb-1.5 block text-sm font-medium text-slate-700">Google Maps URL</label>
                    <input
                        type="url"
                        id="google_maps_url"
                        name="address[google_maps_url]"
                        value="{{ old('address.google_maps_url', $address?->google_maps_url) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900"
                        placeholder="https://maps.google.com/..."
                    >
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section: Opening Hours ───────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="openingHoursManager()">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Opening Hours</h2>
                <p class="mt-0.5 text-xs text-slate-500">เวลาทำการของวัด</p>
            </div>
            <button type="button" @click="addRow()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Row
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <div class="col-span-12 sm:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Day</label>
                            <select
                                :name="`opening_hours[${index}][day_of_week]`"
                                x-model="row.day_of_week"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            >
                                @foreach ($days as $di => $dayName)
                                    <option value="{{ $di }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-5 sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Open</label>
                            <input type="time" :name="`opening_hours[${index}][open_time]`" x-model="row.open_time"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900" :disabled="row.is_closed">
                        </div>
                        <div class="col-span-5 sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Close</label>
                            <input type="time" :name="`opening_hours[${index}][close_time]`" x-model="row.close_time"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900" :disabled="row.is_closed">
                        </div>
                        <div class="col-span-12 sm:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Note</label>
                            <input type="text" :name="`opening_hours[${index}][note]`" x-model="row.note"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900" placeholder="หมายเหตุ">
                        </div>
                        <div class="col-span-6 flex items-end gap-3 sm:col-span-1 pb-1">
                            <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-600">
                                <input type="checkbox" :name="`opening_hours[${index}][is_closed]`" value="1" x-model="row.is_closed"
                                    class="h-3.5 w-3.5 rounded border-slate-300">
                                Closed
                            </label>
                        </div>
                        <div class="col-span-6 flex items-end justify-end sm:col-span-1 pb-1">
                            <button type="button" @click="removeRow(index)"
                                class="rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">
                                ✕
                            </button>
                        </div>
                    </div>
                </template>
                <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล — กด Add Row เพื่อเพิ่ม</p>
            </div>
        </div>
    </div>

    {{-- ── Section: Fees ────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="feesManager()">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Fees / ค่าธรรมเนียม</h2>
            </div>
            <button type="button" @click="addRow()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Fee
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <div class="col-span-12 sm:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Fee Type</label>
                            <input type="text" :name="`fees[${index}][fee_type]`" x-model="row.fee_type"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                placeholder="เช่น admission, parking">
                        </div>
                        <div class="col-span-12 sm:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Label</label>
                            <input type="text" :name="`fees[${index}][label]`" x-model="row.label"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                placeholder="เช่น ค่าเข้าชม ผู้ใหญ่">
                        </div>
                        <div class="col-span-6 sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Amount</label>
                            <input type="number" :name="`fees[${index}][amount]`" x-model="row.amount"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Currency</label>
                            <input type="text" :name="`fees[${index}][currency]`" x-model="row.currency"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                placeholder="THB">
                        </div>
                        <div class="col-span-12 sm:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Note</label>
                            <input type="text" :name="`fees[${index}][note]`" x-model="row.note"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900" placeholder="หมายเหตุ">
                        </div>
                        <div class="col-span-6 flex items-end gap-3 sm:col-span-1 pb-1">
                            <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-600">
                                <input type="checkbox" :name="`fees[${index}][is_active]`" value="1" x-model="row.is_active"
                                    class="h-3.5 w-3.5 rounded border-slate-300">
                                Active
                            </label>
                        </div>
                        <div class="col-span-6 flex items-end justify-end sm:col-span-12 xl:col-span-1 pb-1">
                            <button type="button" @click="removeRow(index)"
                                class="rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">
                                ✕ Remove
                            </button>
                        </div>
                    </div>
                </template>
                <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล — กด Add Fee เพื่อเพิ่ม</p>
            </div>
        </div>
    </div>

    {{-- ── Section: Facilities ──────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Facilities</h2>
            <p class="mt-0.5 text-xs text-slate-500">สิ่งอำนวยความสะดวกภายในวัด</p>
        </div>
        <div class="p-6">
            @if ($facilities->isEmpty())
                <p class="text-sm text-slate-500">ไม่มีข้อมูล facility</p>
            @else
                <div class="space-y-3">
                    @foreach ($facilities as $fi => $facility)
                        @php
                            $existingItem = $facilityItems->firstWhere('facility_id', $facility->id);
                        @endphp
                        <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <input type="hidden" name="facility_items[{{ $fi }}][facility_id]" value="{{ $facility->id }}">
                            <div class="col-span-12 sm:col-span-4 flex items-center gap-2 pt-1">
                                <span class="text-sm font-medium text-slate-700">{{ $facility->name }}</span>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-600">Value</label>
                                <input type="text" name="facility_items[{{ $fi }}][value]"
                                    value="{{ old("facility_items.$fi.value", $existingItem?->value) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                    placeholder="เช่น มี, ฟรี, 10 บาท">
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-600">Note</label>
                                <input type="text" name="facility_items[{{ $fi }}][note]"
                                    value="{{ old("facility_items.$fi.note", $existingItem?->note) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                                    placeholder="หมายเหตุ">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Section: Highlights ──────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="repeaterManager('highlights', @json($jsHighlights))">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Highlights</h2>
                <p class="mt-0.5 text-xs text-slate-500">จุดเด่นของวัด</p>
            </div>
            <button type="button" @click="addRow({title:'',description:'',sort_order:rows.length})" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Highlight
            </button>
        </div>
        <div class="p-6 space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <div class="col-span-12 sm:col-span-4">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Title <span class="text-rose-400">*</span></label>
                        <input type="text" :name="`highlights[${index}][title]`" x-model="row.title"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="เช่น พระพุทธรูปสำคัญ">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Description</label>
                        <input type="text" :name="`highlights[${index}][description]`" x-model="row.description"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="รายละเอียดเพิ่มเติม">
                    </div>
                    <div class="col-span-8 sm:col-span-1">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Order</label>
                        <input type="number" :name="`highlights[${index}][sort_order]`" x-model="row.sort_order"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900" min="0">
                    </div>
                    <div class="col-span-4 sm:col-span-1 flex items-end justify-end pb-1">
                        <button type="button" @click="removeRow(index)"
                            class="rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">✕</button>
                    </div>
                </div>
            </template>
            <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล</p>
        </div>
    </div>

    {{-- ── Section: Visit Rules ─────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="repeaterManager('visit_rules', @json($jsVisitRules))">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Visit Rules</h2>
                <p class="mt-0.5 text-xs text-slate-500">กฎระเบียบการเข้าชม</p>
            </div>
            <button type="button" @click="addRow({rule_text:'',sort_order:rows.length})" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Rule
            </button>
        </div>
        <div class="p-6 space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <div class="flex-1">
                        <input type="text" :name="`visit_rules[${index}][rule_text]`" x-model="row.rule_text"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="เช่น แต่งกายสุภาพ ไม่สวมกางเกงขาสั้น">
                    </div>
                    <div class="w-20">
                        <input type="number" :name="`visit_rules[${index}][sort_order]`" x-model="row.sort_order"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" min="0">
                    </div>
                    <button type="button" @click="removeRow(index)"
                        class="mt-0.5 rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">✕</button>
                </div>
            </template>
            <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล</p>
        </div>
    </div>

    {{-- ── Section: Travel Infos ────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="travelInfosManager()">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Travel Information</h2>
                <p class="mt-0.5 text-xs text-slate-500">ข้อมูลการเดินทาง</p>
            </div>
            <button type="button" @click="addRow()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Travel Info
            </button>
        </div>
        <div class="p-6 space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <div class="col-span-12 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Type <span class="text-rose-400">*</span></label>
                        <input type="text" :name="`travel_infos[${index}][travel_type]`" x-model="row.travel_type"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="เช่น BTS, รถยนต์">
                    </div>
                    <div class="col-span-12 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Start Place</label>
                        <input type="text" :name="`travel_infos[${index}][start_place]`" x-model="row.start_place"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="จากจุดเริ่มต้น">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Distance (km)</label>
                        <input type="number" :name="`travel_infos[${index}][distance_km]`" x-model="row.distance_km"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" step="0.1" min="0">
                    </div>
                    <div class="col-span-6 sm:col-span-1">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Duration (min)</label>
                        <input type="number" :name="`travel_infos[${index}][duration_minutes]`" x-model="row.duration_minutes"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" min="0">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Cost Estimate</label>
                        <input type="text" :name="`travel_infos[${index}][cost_estimate]`" x-model="row.cost_estimate"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="เช่น 30-50 บาท">
                    </div>
                    <div class="col-span-12 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Note</label>
                        <input type="text" :name="`travel_infos[${index}][note]`" x-model="row.note"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="หมายเหตุ">
                    </div>
                    <div class="col-span-6 flex items-end gap-3 sm:col-span-1 pb-1">
                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-600">
                            <input type="checkbox" :name="`travel_infos[${index}][is_active]`" value="1" x-model="row.is_active"
                                class="h-3.5 w-3.5 rounded border-slate-300">
                            Active
                        </label>
                    </div>
                    <div class="col-span-6 flex items-end justify-end pb-1">
                        <button type="button" @click="removeRow(index)"
                            class="rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">✕</button>
                    </div>
                </div>
            </template>
            <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล</p>
        </div>
    </div>

    {{-- ── Section: Nearby Places ───────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white" x-data="nearbyPlacesManager()">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Nearby Places</h2>
                <p class="mt-0.5 text-xs text-slate-500">วัดใกล้เคียง</p>
            </div>
            <button type="button" @click="addRow()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                + Add Nearby Place
            </button>
        </div>
        <div class="p-6 space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="grid grid-cols-12 items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Temple <span class="text-rose-400">*</span></label>
                        <select :name="`nearby_places[${index}][nearby_temple_id]`" x-model="row.nearby_temple_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900">
                            <option value="">— เลือกวัด —</option>
                            @foreach ($nearbyTemples as $nt)
                                <option value="{{ $nt->id }}">{{ $nt->content?->title ?? "Temple #$nt->id" }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Relation Type</label>
                        <input type="text" :name="`nearby_places[${index}][relation_type]`" x-model="row.relation_type"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="เช่น nearby, same_complex">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Distance (km)</label>
                        <input type="number" :name="`nearby_places[${index}][distance_km]`" x-model="row.distance_km"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" step="0.1" min="0">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Duration (min)</label>
                        <input type="number" :name="`nearby_places[${index}][duration_minutes]`" x-model="row.duration_minutes"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" min="0">
                    </div>
                    <div class="col-span-6 sm:col-span-1">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Score</label>
                        <input type="number" :name="`nearby_places[${index}][score]`" x-model="row.score"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" step="0.1">
                    </div>
                    <div class="col-span-6 sm:col-span-1">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Order</label>
                        <input type="number" :name="`nearby_places[${index}][sort_order]`" x-model="row.sort_order"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-slate-900"
                            placeholder="0" min="0">
                    </div>
                    <div class="col-span-12 sm:col-span-1 flex items-end justify-end pb-1">
                        <button type="button" @click="removeRow(index)"
                            class="rounded-lg border border-rose-200 px-2 py-1.5 text-xs text-rose-600 hover:bg-rose-50">✕</button>
                    </div>
                </div>
            </template>
            <p x-show="rows.length === 0" class="text-sm text-slate-400">ยังไม่มีข้อมูล</p>
        </div>
    </div>

</div>

{{-- ── Alpine.js Data Managers ─────────────────────────────── --}}
<script>
    // Generic repeater for simple sections
    function repeaterManager(prefix, initialRows = []) {
        return {
            rows: initialRows.length ? initialRows : [],
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
            rows: existing.length ? existing : [],
            addRow() {
                this.rows.push({ day_of_week: 1, open_time: '08:00', close_time: '17:00', is_closed: false, note: '' });
            },
            removeRow(index) { this.rows.splice(index, 1); },
        };
    }

    function feesManager() {
        const existing = @json($jsFees);

        return {
            rows: existing.length ? existing : [],
            addRow() {
                this.rows.push({ fee_type: '', label: '', amount: '', currency: 'THB', note: '', is_active: true, sort_order: this.rows.length });
            },
            removeRow(index) { this.rows.splice(index, 1); },
        };
    }

    function travelInfosManager() {
        const existing = @json($jsTravelInfos);

        return {
            rows: existing.length ? existing : [],
            addRow() {
                this.rows.push({ travel_type: '', start_place: '', distance_km: '', duration_minutes: '', cost_estimate: '', note: '', is_active: true, sort_order: this.rows.length });
            },
            removeRow(index) { this.rows.splice(index, 1); },
        };
    }

    function nearbyPlacesManager() {
        const existing = @json($jsNearbyPlaces);

        return {
            rows: existing.length ? existing : [],
            addRow() {
                this.rows.push({ nearby_temple_id: '', relation_type: '', distance_km: '', duration_minutes: '', score: '', sort_order: this.rows.length });
            },
            removeRow(index) { this.rows.splice(index, 1); },
        };
    }
</script>