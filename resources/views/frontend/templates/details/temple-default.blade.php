@extends('frontend.layouts.app')

@php
    $content = $temple->content;
    $address = $temple->address;
    $stat = $temple->stat;

    $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
    $coverUrl = $coverUsage?->media?->path
        ? (filter_var($coverUsage->media->path, FILTER_VALIDATE_URL)
            ? $coverUsage->media->path
            : \Illuminate\Support\Facades\Storage::url($coverUsage->media->path))
        : null;

    $galleryUsages = $content?->mediaUsages?->where('role_key', 'gallery') ?? collect();

    $days = [
        0 => 'อาทิตย์',
        1 => 'จันทร์',
        2 => 'อังคาร',
        3 => 'พุธ',
        4 => 'พฤหัสบดี',
        5 => 'ศุกร์',
        6 => 'เสาร์',
    ];
@endphp

@section('title', $content?->meta_title ?? $content?->title ?? 'Temple Detail')
@section('meta_description', $content?->meta_description ?? $content?->excerpt ?? 'Temple Detail')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/70 to-slate-950"></div>

        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt="{{ $content?->title ?? 'Temple image' }}"
                class="h-[520px] w-full object-cover"
            >
        @else
            <div class="h-[420px] w-full bg-slate-900"></div>
        @endif

        <div class="absolute inset-x-0 bottom-0">
            <div class="mx-auto max-w-6xl px-4 pb-12">
                <p class="text-sm font-medium text-blue-300">
                    {{ $address?->province ?? 'Temple Detail' }}
                </p>

                <h1 class="mt-3 text-4xl font-bold text-white md:text-6xl">
                    {{ $content?->title ?? '-' }}
                </h1>

                @if ($content?->excerpt)
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300">
                        {{ $content->excerpt }}
                    </p>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($temple->temple_type)
                        <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-slate-200">
                            {{ $temple->temple_type }}
                        </span>
                    @endif

                    @if ($temple->sect)
                        <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-slate-200">
                            {{ $temple->sect }}
                        </span>
                    @endif

                    @if ($temple->architecture_style)
                        <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-slate-200">
                            {{ $temple->architecture_style }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-xl font-semibold text-white">รายละเอียด</h2>

                    <div class="mt-5 space-y-5 text-sm leading-7 text-slate-300">
                        @if ($content?->description)
                            <p>{!! nl2br(e($content->description)) !!}</p>
                        @endif

                        @if ($temple->history)
                            <div>
                                <h3 class="mb-2 font-semibold text-white">ประวัติ</h3>
                                <p>{!! nl2br(e($temple->history)) !!}</p>
                            </div>
                        @endif

                        @if ($temple->dress_code)
                            <div>
                                <h3 class="mb-2 font-semibold text-white">การแต่งกาย</h3>
                                <p>{!! nl2br(e($temple->dress_code)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($temple->highlights->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-xl font-semibold text-white">จุดเด่น</h2>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->highlights as $highlight)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <h3 class="font-medium text-white">{{ $highlight->title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-400">
                                        {{ $highlight->description ?: '-' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($galleryUsages->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-xl font-semibold text-white">รูปภาพ</h2>

                        <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($galleryUsages as $usage)
                                @php
                                    $galleryUrl = $usage->media?->path
                                        ? (filter_var($usage->media->path, FILTER_VALIDATE_URL)
                                            ? $usage->media->path
                                            : \Illuminate\Support\Facades\Storage::url($usage->media->path))
                                        : null;
                                @endphp

                                @if ($galleryUrl)
                                    <img
                                        src="{{ $galleryUrl }}"
                                        alt="{{ $usage->media?->alt_text ?: $usage->media?->title ?: 'Temple gallery image' }}"
                                        class="aspect-square rounded-2xl border border-white/10 object-cover"
                                        loading="lazy"
                                    >
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($temple->visitRules->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-xl font-semibold text-white">กฎการเข้าชม</h2>

                        <ul class="mt-5 space-y-3">
                            @foreach ($temple->visitRules as $rule)
                                <li class="flex gap-3 text-sm leading-6 text-slate-300">
                                    <span class="text-blue-300">•</span>
                                    <span>{{ $rule->rule_text }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($temple->travelInfos->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-xl font-semibold text-white">การเดินทาง</h2>

                        <div class="mt-5 space-y-4">
                            @foreach ($temple->travelInfos as $travelInfo)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <h3 class="font-medium text-white">
                                        {{ $travelInfo->travel_type ?? 'การเดินทาง' }}
                                    </h3>

                                    <div class="mt-2 space-y-1 text-sm leading-6 text-slate-400">
                                        @if ($travelInfo->start_place)
                                            <p>เริ่มต้น: {{ $travelInfo->start_place }}</p>
                                        @endif

                                        @if ($travelInfo->distance_km)
                                            <p>ระยะทาง: {{ number_format($travelInfo->distance_km, 1) }} กม.</p>
                                        @endif

                                        @if ($travelInfo->duration_minutes)
                                            <p>เวลาเดินทางประมาณ: {{ number_format($travelInfo->duration_minutes) }} นาที</p>
                                        @endif

                                        @if ($travelInfo->cost_estimate)
                                            <p>ค่าใช้จ่ายประมาณ: {{ $travelInfo->cost_estimate }}</p>
                                        @endif

                                        @if ($travelInfo->note)
                                            <p>{{ $travelInfo->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <h2 class="text-lg font-semibold text-white">ข้อมูลวัด</h2>

                    <div class="mt-5 divide-y divide-white/10 text-sm">
                        <div class="py-3">
                            <p class="text-xs text-slate-500">จังหวัด</p>
                            <p class="mt-1 text-slate-300">{{ $address?->province ?? '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">เขต / อำเภอ</p>
                            <p class="mt-1 text-slate-300">{{ $address?->district ?? '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">แขวง / ตำบล</p>
                            <p class="mt-1 text-slate-300">{{ $address?->subdistrict ?? '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">ประเภทวัด</p>
                            <p class="mt-1 text-slate-300">{{ $temple->temple_type ?: '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">นิกาย</p>
                            <p class="mt-1 text-slate-300">{{ $temple->sect ?: '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">สถาปัตยกรรม</p>
                            <p class="mt-1 text-slate-300">{{ $temple->architecture_style ?: '-' }}</p>
                        </div>

                        <div class="py-3">
                            <p class="text-xs text-slate-500">ปีที่ก่อตั้ง</p>
                            <p class="mt-1 text-slate-300">{{ $temple->founded_year ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                @if ($temple->openingHours->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold text-white">เวลาเปิด-ปิด</h2>

                        <div class="mt-5 space-y-3">
                            @foreach ($temple->openingHours as $hour)
                                @php
                                    $openTime = $hour->open_time
                                        ? \Carbon\Carbon::parse($hour->open_time)->format('H:i')
                                        : null;

                                    $closeTime = $hour->close_time
                                        ? \Carbon\Carbon::parse($hour->close_time)->format('H:i')
                                        : null;
                                @endphp

                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm">
                                    <span class="text-slate-400">
                                        {{ $days[$hour->day_of_week] ?? '-' }}
                                    </span>

                                    @if ($hour->is_closed)
                                        <span class="font-medium text-rose-300">ปิด</span>
                                    @else
                                        <span class="font-medium text-slate-100">
                                            {{ $openTime ?? '--:--' }} - {{ $closeTime ?? '--:--' }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($temple->fees->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold text-white">ค่าธรรมเนียม</h2>

                        <div class="mt-5 space-y-3">
                            @foreach ($temple->fees as $fee)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-sm font-medium text-white">
                                        {{ $fee->label ?: '-' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ $fee->amount !== null ? number_format($fee->amount, 0) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($temple->facilityItems->isNotEmpty())
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold text-white">สิ่งอำนวยความสะดวก</h2>

                        <div class="mt-5 space-y-3">
                            @foreach ($temple->facilityItems as $item)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <p class="text-sm font-medium text-white">
                                        {{ $item->facility?->name ?? '-' }}
                                    </p>

                                    @if ($item->note)
                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $item->note }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($stat)
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold text-white">สถิติ</h2>

                        <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3">
                                <p class="text-xs text-slate-500">เข้าชม</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($stat, 'view_count', 0)) }}</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3">
                                <p class="text-xs text-slate-500">รีวิว</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($stat, 'review_count', 0)) }}</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3">
                                <p class="text-xs text-slate-500">ถูกใจ</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($stat, 'favorite_count', 0)) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($address?->google_maps_url)
                    <a
                        href="{{ $address->google_maps_url }}"
                        target="_blank"
                        rel="noopener"
                        class="block rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-center text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                    >
                        เปิดใน Google Maps
                    </a>
                @endif
            </aside>
        </div>
    </section>
@endsection