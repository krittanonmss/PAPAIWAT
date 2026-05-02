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
    {{-- Hero --}}
    <section class="relative">
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt="{{ $content?->title }}"
                class="h-[420px] w-full object-cover"
            >
        @else
            <div class="h-[320px] w-full bg-slate-200"></div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-white via-white/70 to-transparent"></div>

        <div class="absolute bottom-0 w-full">
            <div class="mx-auto max-w-6xl px-4 pb-8">
                <p class="text-sm text-slate-500">
                    {{ $address?->province ?? '-' }}
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900 md:text-5xl">
                    {{ $content?->title ?? '-' }}
                </h1>

                @if ($content?->excerpt)
                    <p class="mt-3 max-w-2xl text-sm text-slate-600">
                        {{ $content->excerpt }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="mx-auto max-w-6xl px-4 py-10">
        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main --}}
            <div class="space-y-8 lg:col-span-2">

                {{-- Description --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">รายละเอียด</h2>

                    <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                        @if ($content?->description)
                            <p>{!! nl2br(e($content->description)) !!}</p>
                        @endif

                        @if ($temple->history)
                            <div>
                                <h3 class="font-medium text-slate-900">ประวัติ</h3>
                                <p>{!! nl2br(e($temple->history)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Highlights --}}
                @if ($temple->highlights->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">จุดเด่น</h2>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->highlights as $highlight)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <h3 class="text-sm font-medium text-slate-900">
                                        {{ $highlight->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $highlight->description }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Gallery --}}
                @if ($galleryUsages->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">รูปภาพ</h2>

                        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                            @foreach ($galleryUsages as $usage)
                                @php
                                    $url = $usage->media?->path
                                        ? \Illuminate\Support\Facades\Storage::url($usage->media->path)
                                        : null;
                                @endphp

                                @if ($url)
                                    <img
                                        src="{{ $url }}"
                                        class="aspect-square rounded-lg object-cover"
                                        loading="lazy"
                                    >
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6">

                {{-- Info --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">ข้อมูลวัด</h2>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div>จังหวัด: {{ $address?->province ?? '-' }}</div>
                        <div>อำเภอ: {{ $address?->district ?? '-' }}</div>
                        <div>ประเภท: {{ $temple->temple_type ?? '-' }}</div>
                        <div>นิกาย: {{ $temple->sect ?? '-' }}</div>
                    </div>
                </div>

                {{-- Opening --}}
                @if ($temple->openingHours->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-slate-900">เวลาเปิด-ปิด</h2>

                        <div class="mt-4 space-y-2 text-sm">
                            @foreach ($temple->openingHours as $hour)
                                @php
                                    $open = $hour->open_time ? \Carbon\Carbon::parse($hour->open_time)->format('H:i') : '--:--';
                                    $close = $hour->close_time ? \Carbon\Carbon::parse($hour->close_time)->format('H:i') : '--:--';
                                @endphp

                                <div class="flex justify-between">
                                    <span class="text-slate-500">{{ $days[$hour->day_of_week] }}</span>
                                    <span class="text-slate-900">
                                        {{ $hour->is_closed ? 'ปิด' : "$open - $close" }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Map --}}
                @if ($address?->google_maps_url)
                    <a
                        href="{{ $address->google_maps_url }}"
                        target="_blank"
                        class="block rounded-xl bg-blue-600 px-4 py-3 text-center text-sm font-medium text-white hover:bg-blue-700"
                    >
                        เปิด Google Maps
                    </a>
                @endif

            </aside>
        </div>
    </section>
@endsection