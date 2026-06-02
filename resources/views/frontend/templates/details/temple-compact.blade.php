@extends('frontend.layouts.app')

@php
    $content = $temple?->content;
    $address = $temple?->address;
    $stat = $temple?->stat;
    $categories = $content?->categories ?? collect();
    $primaryCategory = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
    $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
    $coverPath = $coverUsage?->media?->path;
    $coverUrl = $coverPath ? (filter_var($coverPath, FILTER_VALIDATE_URL) ? $coverPath : \Illuminate\Support\Facades\Storage::url($coverPath)) : null;
    $galleryUsages = $content?->mediaUsages?->where('role_key', 'gallery') ?? collect();

    $averageRating = (float) data_get($stat, 'average_rating', 0);
    $reviewCount = (int) data_get($stat, 'review_count', 0);
    $favoriteCount = (int) data_get($stat, 'favorite_count', 0);
    $shareCount = (int) data_get($stat, 'share_count', 0);
    $score = (float) data_get($stat, 'score', 0);
    $approvedReviews = $approvedReviews ?? collect();
    $visitorPendingReviews = $visitorPendingReviews ?? collect();

    $recommendedStart = $temple?->recommended_visit_start_time
        ? substr((string) $temple->recommended_visit_start_time, 0, 5)
        : null;
    $recommendedEnd = $temple?->recommended_visit_end_time
        ? substr((string) $temple->recommended_visit_end_time, 0, 5)
        : null;

    $days = [
        0 => 'อาทิตย์',
        1 => 'จันทร์',
        2 => 'อังคาร',
        3 => 'พุธ',
        4 => 'พฤหัสบดี',
        5 => 'ศุกร์',
        6 => 'เสาร์',
    ];

    $infoItems = [
        'ประเภทวัด' => $temple?->temple_type,
        'นิกาย' => $temple?->sect,
        'สถาปัตยกรรม' => $temple?->architecture_style,
        'ปีที่ก่อตั้ง' => $temple?->founded_year,
        'จังหวัด' => $address?->province,
        'เขต / อำเภอ' => $address?->district,
        'แขวง / ตำบล' => $address?->subdistrict,
        'รหัสไปรษณีย์' => $address?->postal_code,
    ];

    $renderRichText = function (?string $value) {
        if (! $value) {
            return '';
        }

        return $value === strip_tags($value)
            ? nl2br(e($value))
            : $value;
    };

    $summary = $content?->excerpt
        ?: ($content?->description ? \Illuminate\Support\Str::limit(trim(strip_tags($content->description)), 180) : null);
    $favoritePayload = $temple?->id ? [
        'type' => 'temple',
        'id' => $temple->id,
        'title' => $content?->title,
        'url' => route('temples.show', $temple),
        'excerpt' => $summary,
        'image' => $coverUrl,
    ] : [];
@endphp

@section('title', $content?->meta_title ?? $content?->title ?? 'Temple Detail')
@section('meta_description', $content?->meta_description ?? $content?->excerpt ?? 'Temple Detail')

@section('content')
    @include('frontend.templates.details.partials._rich_content_styles')

    <main class="bg-stone-50 text-stone-950">
        @if (session('success'))
            <div class="mx-auto max-w-7xl px-4 pt-5">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <section class="border-b border-stone-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end">
                <article class="min-w-0">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-emerald-700 px-3 py-1 text-xs font-semibold text-white">
                            {{ $primaryCategory?->name ?? $temple?->temple_type ?? 'Temple' }}
                        </span>
                        @if ($address?->province)
                            <span class="rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-medium text-stone-700">{{ $address->province }}</span>
                        @endif
                        @if ($score > 0)
                            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">คะแนน {{ number_format($score, 0) }}</span>
                        @endif
                    </div>

                    <h1 class="mt-5 max-w-4xl text-4xl font-semibold leading-tight text-stone-950 md:text-6xl">
                        {{ $content?->title ?? 'รายละเอียดวัด' }}
                    </h1>

                    <p class="mt-5 max-w-3xl text-base leading-8 text-stone-600 md:text-lg">
                        {{ $summary ?? 'ยังไม่มีคำอธิบายสั้น' }}
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                            <p class="text-xs text-stone-500">ประเภท</p>
                            <p class="mt-1 font-semibold">{{ $temple?->temple_type ?: '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                            <p class="text-xs text-stone-500">รีวิว</p>
                            <p class="mt-1 font-semibold">{{ $averageRating > 0 ? number_format($averageRating, 1).' / 5' : '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                            <p class="text-xs text-stone-500">จำนวนรีวิว</p>
                            <p class="mt-1 font-semibold">{{ number_format($reviewCount) }}</p>
                        </div>
                    </div>
                </article>

                <div class="overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                    @if ($coverUrl)
                        <a href="{{ $coverUrl }}" target="_blank" rel="noopener" class="block">
                            <img
                                src="{{ $coverUrl }}"
                                alt="{{ $coverUsage?->media?->alt_text ?: $content?->title ?: 'Temple image' }}"
                                class="aspect-[4/3] w-full object-cover"
                            >
                        </a>
                    @else
                        <div class="aspect-[4/3] w-full bg-gradient-to-br from-emerald-100 via-stone-100 to-amber-100"></div>
                    @endif
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-6 px-4 py-8 lg:grid-cols-[340px_minmax(0,1fr)]">
            <aside class="order-1 flex flex-col gap-4 lg:sticky lg:top-24 lg:self-start">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-2">
                        @if ($temple?->id)
                            <button
                                type="button"
                                data-local-favorite-toggle
                                data-favorite='@json($favoritePayload)'
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-stone-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-800"
                            >
                                <svg data-favorite-icon class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.015-4.5-4.5-4.5-1.74 0-3.25.99-4 2.438A4.49 4.49 0 0 0 8.5 3.75C6.015 3.75 4 5.765 4 8.25c0 7.22 8.5 12 8.5 12s8.5-4.78 8.5-12Z" />
                                </svg>
                                <span data-favorite-unsaved>เพิ่มในรายการโปรด</span>
                                <span data-favorite-saved class="hidden">อยู่ในรายการโปรดแล้ว</span>
                                <span class="rounded-full bg-white/15 px-2 py-0.5 text-xs font-semibold" data-favorite-count="temple:{{ $temple->id }}">{{ number_format($favoriteCount) }}</span>
                                <span class="text-xs opacity-80">คนกด</span>
                            </button>
                        @endif

                        <button
                            type="button"
                            data-share-button
                            data-share-type="temple"
                            data-share-id="{{ $temple?->id }}"
                            data-share-title="{{ $content?->title ?? 'PAPAIWAT' }}"
                            data-share-text="{{ $summary ?? $content?->title ?? 'PAPAIWAT' }}"
                            data-share-url="{{ url()->current() }}"
                            data-share-default-label="แชร์"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-900 transition hover:bg-stone-50"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 16.5 6.75M7.5 12l9 5.25M7.5 12a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm13.5-6.75a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm0 13.5a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            <span data-share-label>แชร์</span>
                            <span class="text-xs opacity-75" data-share-count="temple:{{ $temple?->id }}">{{ number_format($shareCount) }}</span>
                        </button>
                    </div>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-semibold">ที่ตั้ง</h2>
                    <div class="mt-4 space-y-3 text-sm leading-6 text-stone-600">
                        <p><span class="text-stone-500">ที่อยู่:</span> {{ $address?->address_line ?? '-' }}</p>
                        @if ($address?->latitude && $address?->longitude)
                            <p><span class="text-stone-500">พิกัด:</span> {{ $address->latitude }}, {{ $address->longitude }}</p>
                        @endif
                        @if ($address?->google_place_id)
                            <p><span class="text-stone-500">Google Place ID:</span> <span class="break-all">{{ $address->google_place_id }}</span></p>
                        @endif
                        @if ($address?->google_maps_url)
                            <a
                                href="{{ $address->google_maps_url }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-600"
                            >
                                เปิดแผนที่
                            </a>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-semibold">เวลาเปิด-ปิด</h2>
                    <div class="mt-4 divide-y divide-stone-200 text-sm">
                        @forelse ($temple?->openingHours ?? collect() as $hour)
                            @php
                                $openTime = $hour->open_time ? substr((string) $hour->open_time, 0, 5) : null;
                                $closeTime = $hour->close_time ? substr((string) $hour->close_time, 0, 5) : null;
                            @endphp
                            <div class="py-2">
                                <div class="flex justify-between gap-4 text-stone-700">
                                    <span>{{ $days[$hour->day_of_week] ?? '-' }}</span>
                                    <span>{{ $hour->is_closed ? 'ปิด' : (($openTime ?? '--:--').' - '.($closeTime ?? '--:--')) }}</span>
                                </div>
                                @if ($hour->note)
                                    <p class="mt-1 text-xs text-stone-500">{{ $hour->note }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">ยังไม่มีข้อมูลเวลาเปิดทำการ</p>
                        @endforelse
                    </div>
                </section>

                @if ($temple?->fees?->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-semibold">ค่าธรรมเนียม</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($temple->fees as $fee)
                                <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
                                    <p class="text-sm font-semibold">{{ $fee->label }}</p>
                                    <p class="mt-1 text-sm text-stone-600">
                                        {{ $fee->amount !== null ? number_format((float) $fee->amount, 0).' '.($fee->currency ?: 'THB') : 'ฟรี' }}
                                        @if ($fee->fee_type)
                                            · {{ $fee->fee_type }}
                                        @endif
                                    </p>
                                    @if ($fee->note)
                                        <p class="mt-1 text-xs text-stone-500">{{ $fee->note }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-semibold">สิ่งอำนวยความสะดวก</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($temple?->facilityItems ?? collect() as $item)
                            <span class="rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs text-stone-700">
                                {{ $item->facility?->name ?? $item->value ?? 'Facility' }}
                                @if ($item->note)
                                    · {{ $item->note }}
                                @endif
                            </span>
                        @empty
                            <span class="text-sm text-stone-500">ยังไม่มีข้อมูล</span>
                        @endforelse
                    </div>
                </section>
            </aside>

            <div class="order-2 flex flex-col gap-6">
                @if ($temple?->highlights?->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="text-2xl font-semibold">จุดเด่น</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->highlights as $highlight)
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
                                    <h3 class="font-semibold">{{ $highlight->title }}</h3>
                                    <div class="temple-rich-content temple-rich-content-light mt-2 text-sm leading-6 text-stone-600">
                                        {!! $highlight->description ? $renderRichText($highlight->description) : '-' !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($temple?->visitRules?->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="text-2xl font-semibold">กฎการเข้าชม</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach ($temple->visitRules as $rule)
                                <li class="flex gap-3 text-sm leading-6 text-stone-600">
                                    <span class="text-emerald-700">•</span>
                                    <div class="temple-rich-content temple-rich-content-light min-w-0 flex-1">
                                        {!! $renderRichText($rule->rule_text) !!}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold">แกลเลอรีรูปภาพ</h2>
                    @if ($galleryUsages->isNotEmpty())
                        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($galleryUsages as $usage)
                                @php
                                    $path = $usage->media?->path;
                                    $url = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                                @endphp

                                @if ($url)
                                    <figure class="group overflow-hidden rounded-2xl border border-stone-200 bg-stone-50">
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="relative block">
                                            <img
                                                src="{{ $url }}"
                                                alt="{{ $usage->media?->alt_text ?: $usage->media?->title ?: $content?->title ?: 'Temple gallery image' }}"
                                                class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105"
                                                loading="lazy"
                                            >
                                            <span class="pointer-events-none absolute inset-x-3 bottom-3 rounded-full bg-stone-950/75 px-3 py-1.5 text-center text-xs font-medium text-white opacity-0 backdrop-blur transition group-hover:opacity-100">
                                                ดูรูป
                                            </span>
                                        </a>
                                        @if ($usage->media?->caption)
                                            <figcaption class="p-3 text-xs text-stone-500">{{ $usage->media->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-sm text-stone-500">ยังไม่มีรูปภาพ</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold">ประวัติและข้อมูลวัด</h2>
                    <div class="temple-rich-content temple-rich-content-light mt-4 space-y-5 text-base leading-8 text-stone-600">
                        @if ($content?->description)
                            <div>{!! $renderRichText($content->description) !!}</div>
                        @endif
                        @if ($temple?->history)
                            <div>
                                <h3 class="mb-2 font-semibold text-stone-950">ประวัติ</h3>
                                <div>{!! $renderRichText($temple->history) !!}</div>
                            </div>
                        @endif
                        @if ($temple?->dress_code)
                            <p><span class="font-semibold text-stone-950">การแต่งกาย:</span> {!! nl2br(e($temple->dress_code)) !!}</p>
                        @endif
                        @if (! $content?->description && ! $temple?->history && ! $temple?->dress_code)
                            <p class="text-stone-500">ยังไม่มีรายละเอียด</p>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold">ข้อมูลวัด</h2>
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach ($infoItems as $label => $value)
                            <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
                                <p class="text-xs text-stone-500">{{ $label }}</p>
                                <p class="mt-1 break-words text-sm font-medium text-stone-800">{{ $value ?: '-' }}</p>
                            </div>
                        @endforeach
                        @if ($recommendedStart || $recommendedEnd)
                            <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
                                <p class="text-xs text-stone-500">ช่วงเวลาแนะนำ</p>
                                <p class="mt-1 text-sm font-medium text-stone-800">{{ $recommendedStart ?? '--:--' }} - {{ $recommendedEnd ?? '--:--' }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                @if ($temple?->travelInfos?->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="text-2xl font-semibold">การเดินทาง</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->travelInfos as $travelInfo)
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
                                    <h3 class="font-semibold">{{ $travelInfo->travel_type ?? 'การเดินทาง' }}</h3>
                                    <div class="mt-2 space-y-1 text-sm leading-6 text-stone-600">
                                        @if ($travelInfo->start_place)
                                            <p>เริ่มต้น: {{ $travelInfo->start_place }}</p>
                                        @endif
                                        @if ($travelInfo->distance_km)
                                            <p>ระยะทาง: {{ number_format((float) $travelInfo->distance_km, 1) }} กม.</p>
                                        @endif
                                        @if ($travelInfo->duration_minutes)
                                            <p>เวลาเดินทางประมาณ: {{ number_format($travelInfo->duration_minutes) }} นาที</p>
                                        @endif
                                        @if ($travelInfo->cost_estimate)
                                            <p>ค่าใช้จ่ายประมาณ: {{ number_format((float) $travelInfo->cost_estimate, 0) }} บาท</p>
                                        @endif
                                        @if ($travelInfo->note)
                                            <p>{{ $travelInfo->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @include('frontend.templates.details.partials._nearby_recommendations', [
                    'theme' => 'light',
                ])

                @if ($temple?->nearbyPlaces?->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase text-emerald-700">Nearby Temples</p>
                                <h2 class="mt-2 text-2xl font-semibold">วัดใกล้เคียง</h2>
                            </div>
                            <span class="rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs text-stone-600">
                                {{ number_format($temple->nearbyPlaces->count()) }} วัด
                            </span>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($temple->nearbyPlaces as $nearby)
                                @php
                                    $nearbyTemple = $nearby->nearbyTemple;
                                    $nearbyContent = $nearbyTemple?->content;
                                    $nearbyCoverUsage = $nearbyContent?->mediaUsages?->firstWhere('role_key', 'cover');
                                    $nearbyCoverPath = $nearbyCoverUsage?->media?->path;
                                    $nearbyCoverUrl = $nearbyCoverPath
                                        ? (filter_var($nearbyCoverPath, FILTER_VALIDATE_URL) ? $nearbyCoverPath : \Illuminate\Support\Facades\Storage::url($nearbyCoverPath))
                                        : null;
                                    $nearbySummary = $nearbyContent?->excerpt
                                        ?: ($nearbyContent?->description ? \Illuminate\Support\Str::limit(trim(strip_tags($nearbyContent->description)), 80) : null);
                                @endphp
                                <a href="{{ $nearbyTemple ? route('temples.show', $nearbyTemple) : '#' }}" class="group flex min-h-full flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                                    <div class="h-40 overflow-hidden bg-stone-100">
                                        @if ($nearbyCoverUrl)
                                            <img
                                                src="{{ $nearbyCoverUrl }}"
                                                alt="{{ $nearbyCoverUsage?->media?->alt_text ?: $nearbyContent?->title ?: 'วัดใกล้เคียง' }}"
                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-stone-100 text-xs font-medium text-stone-400">
                                                ไม่มีรูปภาพ
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex min-h-[10rem] flex-1 flex-col p-4">
                                        <h3 class="line-clamp-2 font-semibold leading-6">{{ $nearbyContent?->title ?? 'วัดใกล้เคียง' }}</h3>
                                        @if ($nearbySummary)
                                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-600">{{ $nearbySummary }}</p>
                                        @endif
                                        <div class="mt-3 flex flex-wrap gap-1.5 text-xs">
                                            <span class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-stone-600">
                                                {{ $nearby->relation_type ?: 'สถานที่ใกล้เคียง' }}
                                            </span>
                                            @if ($nearby->distance_km)
                                                <span class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-stone-600">
                                                    {{ number_format((float) $nearby->distance_km, 1) }} กม.
                                                </span>
                                            @endif
                                            @if ($nearby->duration_minutes)
                                                <span class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-stone-600">
                                                    {{ number_format($nearby->duration_minutes) }} นาที
                                                </span>
                                            @endif
                                        </div>
                                        <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-sm font-semibold text-emerald-700 group-hover:text-emerald-600">
                                            ดูรายละเอียด
                                            <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold">รีวิวจากผู้เยี่ยมชม</h2>
                            <p class="mt-1 text-sm text-stone-500">ให้คะแนนประสบการณ์จริงและเล่าข้อมูลที่เป็นประโยชน์กับคนถัดไป</p>
                        </div>
                        <div class="text-sm text-stone-500">
                            {{ number_format($reviewCount) }} รีวิว
                            @if ($averageRating > 0)
                                · {{ number_format($averageRating, 1) }} / 5
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @forelse ($approvedReviews as $review)
                            <article class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold">{{ $review->display_name ?: 'ผู้เยี่ยมชม' }}</p>
                                    <p class="text-sm font-semibold text-amber-700">{{ number_format($review->rating) }} / 5</p>
                                </div>
                                @if ($review->comment)
                                    <p class="mt-3 text-sm leading-6 text-stone-600">{{ $review->comment }}</p>
                                @endif
                                <form method="POST" action="{{ route('reviews.report', $review) }}" class="mt-3 text-right">
                                    @csrf
                                    <input type="hidden" name="reason" value="ไม่เหมาะสม">
                                    <button type="submit" class="text-xs text-stone-500 transition hover:text-red-600">รายงาน</button>
                                </form>
                            </article>
                        @empty
                            <p class="text-sm text-stone-500">ยังไม่มีรีวิวที่เผยแพร่</p>
                        @endforelse

                        @foreach ($visitorPendingReviews as $review)
                            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-amber-950">{{ $review->display_name ?: 'คุณ' }}</p>
                                    <span class="rounded-full border border-amber-200 px-2 py-0.5 text-xs text-amber-700">รอตรวจสอบ</span>
                                </div>
                                <p class="mt-2 text-sm font-semibold text-amber-700">{{ number_format($review->rating) }} / 5</p>
                                @if ($review->comment)
                                    <p class="mt-3 text-sm leading-6 text-amber-950/80">{{ $review->comment }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('temples.reviews.store', $temple) }}" class="mt-6 space-y-4 rounded-2xl border border-stone-200 bg-stone-50 p-5" data-review-rating-form>
                        @csrf
                        <input type="hidden" name="rating" value="{{ (int) old('rating', 0) }}" required data-review-rating-input>

                        <div>
                            <p class="text-sm font-semibold">เขียนรีวิว</p>
                            <p class="mt-1 text-xs text-stone-500">รีวิวจะถูกตรวจสอบก่อนเผยแพร่</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-stone-700">ให้คะแนน</p>
                            <div class="mt-3 flex items-center gap-2">
                                @for ($star = 1; $star <= 5; $star++)
                                    <button
                                        type="button"
                                        data-review-rating-star="{{ $star }}"
                                        class="inline-flex h-10 w-10 items-center justify-center text-stone-400 transition hover:scale-105 hover:text-amber-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/60"
                                        aria-pressed="false"
                                        aria-label="{{ $star }} ดาว"
                                    >
                                        <svg
                                            class="h-8 w-8 fill-transparent transition"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.48 3.5 2.52 5.11 5.64.82-4.08 3.98.96 5.62-5.04-2.65-5.04 2.65.96-5.62-4.08-3.98 5.64-.82 2.52-5.11Z" />
                                        </svg>
                                    </button>
                                @endfor

                                <span class="ml-2 text-sm text-stone-500" data-review-rating-label>{{ old('rating') ? old('rating').' / 5' : 'ยังไม่ได้ให้คะแนน' }}</span>
                            </div>
                            @error('rating')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <input
                            type="text"
                            name="display_name"
                            value="{{ old('display_name') }}"
                            placeholder="ชื่อที่ต้องการแสดง"
                            class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-950 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none"
                        >
                        <textarea
                            name="comment"
                            rows="4"
                            placeholder="เขียนรีวิว"
                            class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-950 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none"
                        >{{ old('comment') }}</textarea>
                        <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">
                            ส่งรีวิว
                        </button>
                    </form>
                </section>
            </div>
        </section>
    </main>
    @include('frontend.partials.local_favorites_script')
    @include('frontend.partials.share_script')
    @include('frontend.partials.review_rating_script')
@endsection
