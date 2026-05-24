{{-- _form.blade.php --}}
{{-- Variables expected: $temple, $statusOptions, $categories, $mediaItems, $facilities, $nearbyTemples --}}

@php
    $content = $temple?->content;
    $address = $temple?->address;
    $adminPreferences = app(\App\Services\Admin\AdminPreferenceService::class)->forAdmin(auth('admin')->user());
    $autosaveDrafts = (bool) ($adminPreferences['editor.autosave_drafts'] ?? true);
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
            'facility_name' => $item->facility?->name ?? '',
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
            'nearby_temple_title' => $n['nearby_temple_title'] ?? '',
            'relation_type'    => $n['relation_type'] ?? '',
            'distance_km'      => $n['distance_km'] ?? '',
            'duration_minutes' => $n['duration_minutes'] ?? '',
            'score'            => $n['score'] ?? '',
            'sort_order'       => $n['sort_order'] ?? 0,
        ];
    })->values() : $nearbyPlaces->map(function ($n) {
        return [
            'nearby_temple_id' => $n->nearby_temple_id,
            'nearby_temple_title' => $n->nearbyTemple?->content?->title ?? '',
            'relation_type'    => $n->relation_type ?? '',
            'distance_km'      => $n->distance_km,
            'duration_minutes' => $n->duration_minutes,
            'score'            => $n->score,
            'sort_order'       => $n->sort_order ?? 0,
        ];
    })->values();
@endphp

<div class="space-y-6 text-white">
    @include('admin.content.temples.partials.form._assets')

    <input type="hidden" name="content_id" value="{{ $content?->id }}">
    <input type="hidden" name="temple_id" value="{{ $temple?->id }}">

    @include('admin.content.temples.partials.form._basic_info')
    @include('admin.content.temples.partials.form._temple_details')

    <div class="grid grid-cols-1 gap-6">
        @include('admin.content.temples.partials.form._seo')
        @include('admin.content.temples.partials.form._categories')
    </div>

    @include('admin.content.temples.partials.form._media')
    @include('admin.content.temples.partials.form._address')
    @include('admin.content.temples.partials.form._opening_hours')
    @include('admin.content.temples.partials.form._fees')
    @include('admin.content.temples.partials.form._facilities')
    @include('admin.content.temples.partials.form._highlights')
    @include('admin.content.temples.partials.form._visit_rules')
    @include('admin.content.temples.partials.form._travel_infos')
    @include('admin.content.temples.partials.form._nearby_places')
    @include('admin.content.temples.partials.form._publishing')
</div>

@if ($autosaveDrafts)
    @include('admin.content.temples.partials.form._draft_script')
@endif
