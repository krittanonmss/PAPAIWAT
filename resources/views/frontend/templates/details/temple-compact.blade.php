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
@endphp

@section('title', $content?->meta_title ?? $content?->title ?? 'Temple Detail')
@section('meta_description', $content?->meta_description ?? $content?->excerpt ?? 'Temple Detail')

@section('content')
    <main class="bg-stone-50 text-stone-950">
        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 lg:grid-cols-[420px_minmax(0,1fr)]">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                    @if ($coverUrl)
                        <img
                            src="{{ $coverUrl }}"
                            alt="{{ $coverUsage?->media?->alt_text ?: $content?->title ?: 'Temple image' }}"
                            class="aspect-[4/3] w-full object-cover"
                        >
                    @else
                        <div class="aspect-[4/3] w-full bg-gradient-to-br from-amber-100 via-stone-100 to-emerald-100"></div>
                    @endif

                    <div class="space-y-4 p-5">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-white">
                                {{ $primaryCategory?->name ?? $temple?->temple_type ?? 'Temple' }}
                            </span>
                            @if ($address?->province)
                                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700">{{ $address->province }}</span>
                            @endif
                        </div>

                        <dl class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-stone-100 p-3">
                                <dt class="text-xs text-stone-500">Rating</dt>
                                <dd class="mt-1 font-semibold">{{ data_get($stat, 'average_rating') ? number_format((float) data_get($stat, 'average_rating'), 1) : '-' }}</dd>
                            </div>
                            <div class="rounded-xl bg-stone-100 p-3">
                                <dt class="text-xs text-stone-500">Score</dt>
                                <dd class="mt-1 font-semibold">{{ data_get($stat, 'score') ? number_format((float) data_get($stat, 'score'), 0) : '-' }}</dd>
                            </div>
                        </dl>

                        @if ($address?->google_maps_url)
                            <a
                                href="{{ $address->google_maps_url }}"
                                target="_blank"
                                rel="noopener"
                                class="block rounded-xl bg-emerald-700 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-emerald-600"
                            >
                                เปิดแผนที่
                            </a>
                        @endif
                    </div>
                </div>
            </aside>

            <article class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-700">Temple Guide</p>
                <h1 class="mt-4 text-4xl font-semibold leading-tight md:text-6xl">
                    {{ $content?->title ?? 'รายละเอียดวัด' }}
                </h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-stone-600">
                    {{ $content?->excerpt ?? $content?->description ?? 'ยังไม่มีคำอธิบายสั้น' }}
                </p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <p class="text-xs text-stone-500">ประเภทวัด</p>
                        <p class="mt-2 font-semibold">{{ $temple?->temple_type ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <p class="text-xs text-stone-500">นิกาย</p>
                        <p class="mt-2 font-semibold">{{ $temple?->sect ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-5">
                        <p class="text-xs text-stone-500">ปีที่ก่อตั้ง</p>
                        <p class="mt-2 font-semibold">{{ $temple?->founded_year ?: '-' }}</p>
                    </div>
                </div>

                <section class="mt-8 rounded-2xl border border-stone-200 bg-white p-6">
                    <h2 class="text-2xl font-semibold">ประวัติและข้อมูลสำคัญ</h2>
                    <div class="mt-4 space-y-5 text-base leading-8 text-stone-600">
                        @if ($content?->description)
                            <p>{!! nl2br(e($content->description)) !!}</p>
                        @endif
                        @if ($temple?->history)
                            <p>{!! nl2br(e($temple->history)) !!}</p>
                        @endif
                        @if ($temple?->dress_code)
                            <p><span class="font-semibold text-stone-950">การแต่งกาย:</span> {!! nl2br(e($temple->dress_code)) !!}</p>
                        @endif
                    </div>
                </section>

                @if ($temple?->highlights?->isNotEmpty())
                    <section class="mt-8">
                        <h2 class="text-2xl font-semibold">จุดเด่น</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            @foreach ($temple->highlights as $highlight)
                                <div class="rounded-2xl border border-stone-200 bg-white p-5">
                                    <h3 class="font-semibold">{{ $highlight->title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-stone-600">{{ $highlight->description ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>
        </section>
    </main>
@endsection
