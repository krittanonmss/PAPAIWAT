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
    $score = (float) data_get($stat, 'score', 0);

    $recommendedStart = $temple?->recommended_visit_start_time
        ? \Carbon\Carbon::parse($temple->recommended_visit_start_time)->format('H:i')
        : null;
    $recommendedEnd = $temple?->recommended_visit_end_time
        ? \Carbon\Carbon::parse($temple->recommended_visit_end_time)->format('H:i')
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
@endphp

@section('title', $content?->meta_title ?? $content?->title ?? 'Temple Detail')
@section('meta_description', $content?->meta_description ?? $content?->excerpt ?? 'Temple Detail')

@section('content')
    @include('frontend.templates.details.partials._rich_content_styles')

    <main class="bg-slate-950 text-white">
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/10 via-slate-950/75 to-slate-950"></div>

            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $coverUsage?->media?->alt_text ?: $content?->title ?: 'Temple image' }}"
                    class="h-[560px] w-full object-cover"
                >
            @else
                <div class="h-[480px] w-full bg-gradient-to-br from-slate-900 via-blue-950/50 to-slate-950"></div>
            @endif

            <div class="absolute inset-x-0 bottom-0">
                <div class="mx-auto max-w-6xl px-4 pb-10">
                    <article class="rounded-3xl border border-white/10 bg-slate-950/60 p-6 shadow-2xl shadow-slate-950/50 backdrop-blur-xl md:p-8">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-slate-950">
                                {{ $primaryCategory?->name ?? $temple?->temple_type ?? 'Temple' }}
                            </span>
                            @if ($address?->province)
                                <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-xs text-slate-200">{{ $address->province }}</span>
                            @endif
                            @if ($score > 0)
                                <span class="rounded-full border border-blue-300/30 bg-blue-500/10 px-3 py-1 text-xs text-blue-200">คะแนน {{ number_format($score, 0) }}</span>
                            @endif
                        </div>

                        <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                            {{ $content?->title ?? 'รายละเอียดวัด' }}
                        </h1>

                        <p class="mt-5 max-w-3xl text-base leading-7 text-slate-300">
                            {{ $summary ?? 'ยังไม่มีคำอธิบายสั้น' }}
                        </p>

                        <div class="mt-6 grid gap-3 text-sm text-slate-300 md:grid-cols-4">
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs text-slate-500">ประเภท</p>
                                <p class="mt-1 font-medium">{{ $temple?->temple_type ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs text-slate-500">รีวิว</p>
                                <p class="mt-1 font-medium">{{ $averageRating > 0 ? number_format($averageRating, 1) . ' / 5' : '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs text-slate-500">จำนวนรีวิว</p>
                                <p class="mt-1 font-medium">{{ number_format($reviewCount) }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs text-slate-500">ถูกใจ</p>
                                <p class="mt-1 font-medium">{{ number_format($favoriteCount) }}</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-6xl gap-6 px-4 py-10 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <article class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur md:p-8">
                    <h2 class="text-2xl font-semibold">ประวัติและข้อมูลวัด</h2>
                    <div class="temple-rich-content temple-rich-content-dark mt-5 space-y-5 text-sm leading-7 text-slate-300">
                        @if ($content?->description)
                            <div>{!! $renderRichText($content->description) !!}</div>
                        @endif

                        @if ($temple?->history)
                            <div>
                                <h3 class="mb-2 font-semibold text-white">ประวัติ</h3>
                                <div>{!! $renderRichText($temple->history) !!}</div>
                            </div>
                        @endif

                        @if ($temple?->dress_code)
                            <div>
                                <h3 class="mb-2 font-semibold text-white">การแต่งกาย</h3>
                                <p>{!! nl2br(e($temple->dress_code)) !!}</p>
                            </div>
                        @endif

                        @if (! $content?->description && ! $temple?->history && ! $temple?->dress_code)
                            <p class="text-slate-500">ยังไม่มีรายละเอียด</p>
                        @endif
                    </div>
                </article>

                @if ($temple?->highlights?->isNotEmpty())
                    <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-2xl font-semibold">จุดเด่น</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->highlights as $highlight)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                    <h3 class="font-medium">{{ $highlight->title }}</h3>
                                    <div class="temple-rich-content temple-rich-content-dark mt-2 text-sm leading-6 text-slate-400">
                                        {!! $highlight->description ? $renderRichText($highlight->description) : '-' !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-2xl font-semibold">แกลเลอรีรูปภาพ</h2>
                    @if ($galleryUsages->isNotEmpty())
                        <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($galleryUsages as $usage)
                                @php
                                    $path = $usage->media?->path;
                                    $url = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                                @endphp

                                @if ($url)
                                    <figure class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/45">
                                        <img
                                            src="{{ $url }}"
                                            alt="{{ $usage->media?->alt_text ?: $usage->media?->title ?: $content?->title ?: 'Temple gallery image' }}"
                                            class="aspect-square w-full object-cover"
                                            loading="lazy"
                                        >
                                        @if ($usage->media?->caption)
                                            <figcaption class="p-3 text-xs text-slate-400">{{ $usage->media->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="mt-5 text-sm text-slate-500">ยังไม่มีรูปภาพ</p>
                    @endif
                </section>

                @if ($temple?->visitRules?->isNotEmpty())
                    <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-2xl font-semibold">กฎการเข้าชม</h2>
                        <ul class="mt-5 space-y-3">
                            @foreach ($temple->visitRules as $rule)
                                <li class="flex gap-3 text-sm leading-6 text-slate-300">
                                    <span class="text-blue-300">•</span>
                                    <div class="temple-rich-content temple-rich-content-dark min-w-0 flex-1">
                                        {!! $renderRichText($rule->rule_text) !!}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($temple?->travelInfos?->isNotEmpty())
                    <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-2xl font-semibold">การเดินทาง</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->travelInfos as $travelInfo)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                    <h3 class="font-medium text-white">{{ $travelInfo->travel_type ?? 'การเดินทาง' }}</h3>
                                    <div class="mt-2 space-y-1 text-sm leading-6 text-slate-400">
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

                @if ($temple?->nearbyPlaces?->isNotEmpty())
                    <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-2xl font-semibold">วัดใกล้เคียง</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->nearbyPlaces as $nearby)
                                @php
                                    $nearbyTemple = $nearby->nearbyTemple;
                                    $nearbyContent = $nearbyTemple?->content;
                                @endphp
                                <a href="{{ $nearbyTemple ? route('temples.show', $nearbyTemple) : '#' }}" class="rounded-2xl border border-white/10 bg-slate-950/45 p-4 transition hover:border-blue-300/40">
                                    <h3 class="font-medium text-white">{{ $nearbyContent?->title ?? 'วัดใกล้เคียง' }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-400">
                                        {{ $nearby->relation_type ?: 'สถานที่ใกล้เคียง' }}
                                        @if ($nearby->distance_km)
                                            · {{ number_format((float) $nearby->distance_km, 1) }} กม.
                                        @endif
                                        @if ($nearby->duration_minutes)
                                            · {{ number_format($nearby->duration_minutes) }} นาที
                                        @endif
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-lg font-semibold">ข้อมูลวัด</h2>
                    <div class="mt-5 divide-y divide-white/10 text-sm">
                        @foreach ($infoItems as $label => $value)
                            <div class="py-3">
                                <p class="text-xs text-slate-500">{{ $label }}</p>
                                <p class="mt-1 break-words text-slate-300">{{ $value ?: '-' }}</p>
                            </div>
                        @endforeach
                        @if ($recommendedStart || $recommendedEnd)
                            <div class="py-3">
                                <p class="text-xs text-slate-500">ช่วงเวลาแนะนำ</p>
                                <p class="mt-1 text-slate-300">{{ $recommendedStart ?? '--:--' }} - {{ $recommendedEnd ?? '--:--' }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-lg font-semibold">ที่ตั้ง</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <p><span class="text-slate-500">ที่อยู่:</span> {{ $address?->address_line ?? '-' }}</p>
                        @if ($address?->latitude && $address?->longitude)
                            <p><span class="text-slate-500">พิกัด:</span> {{ $address->latitude }}, {{ $address->longitude }}</p>
                        @endif
                        @if ($address?->google_place_id)
                            <p><span class="text-slate-500">Google Place ID:</span> <span class="break-all">{{ $address->google_place_id }}</span></p>
                        @endif
                        @if ($address?->google_maps_url)
                            <a
                                href="{{ $address->google_maps_url }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500"
                            >
                                เปิดแผนที่
                            </a>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-lg font-semibold">เวลาเปิด-ปิด</h2>
                    <div class="mt-4 divide-y divide-white/10 text-sm">
                        @forelse ($temple?->openingHours ?? collect() as $hour)
                            @php
                                $openTime = $hour->open_time ? \Carbon\Carbon::parse($hour->open_time)->format('H:i') : null;
                                $closeTime = $hour->close_time ? \Carbon\Carbon::parse($hour->close_time)->format('H:i') : null;
                            @endphp
                            <div class="flex justify-between gap-4 py-2 text-slate-300">
                                <span>{{ $days[$hour->day_of_week] ?? '-' }}</span>
                                <span>{{ $hour->is_closed ? 'ปิด' : (($openTime ?? '--:--') . ' - ' . ($closeTime ?? '--:--')) }}</span>
                            </div>
                            @if ($hour->note)
                                <p class="pb-2 text-xs text-slate-500">{{ $hour->note }}</p>
                            @endif
                        @empty
                            <p class="text-sm text-slate-500">ยังไม่มีข้อมูลเวลาเปิดทำการ</p>
                        @endforelse
                    </div>
                </section>

                @if ($temple?->fees?->isNotEmpty())
                    <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold">ค่าธรรมเนียม</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($temple->fees as $fee)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                                    <p class="text-sm font-medium text-white">{{ $fee->label }}</p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ $fee->amount !== null ? number_format((float) $fee->amount, 0) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                        @if ($fee->fee_type)
                                            · {{ $fee->fee_type }}
                                        @endif
                                    </p>
                                    @if ($fee->note)
                                        <p class="mt-1 text-xs text-slate-500">{{ $fee->note }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-lg font-semibold">สิ่งอำนวยความสะดวก</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($temple?->facilityItems ?? collect() as $item)
                            <span class="rounded-full border border-white/10 bg-slate-950/50 px-3 py-1 text-xs text-slate-300">
                                {{ $item->facility?->name ?? $item->value ?? 'Facility' }}
                                @if ($item->note)
                                    · {{ $item->note }}
                                @endif
                            </span>
                        @empty
                            <span class="text-sm text-slate-500">ยังไม่มีข้อมูล</span>
                        @endforelse
                    </div>
                </section>
            </aside>
        </section>
    </main>
@endsection
