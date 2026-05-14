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
    $favoriteCount = (int) data_get($stat, 'favorite_count', 0);
    $shareCount = (int) data_get($stat, 'share_count', 0);

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
        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 lg:grid-cols-[420px_minmax(0,1fr)]">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                    @if ($coverUrl)
                        <a href="{{ $coverUrl }}" target="_blank" rel="noopener" class="block">
                            <img
                                src="{{ $coverUrl }}"
                                alt="{{ $coverUsage?->media?->alt_text ?: $content?->title ?: 'Temple image' }}"
                                class="aspect-[4/3] w-full object-cover"
                            >
                        </a>
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

                        <div class="grid gap-2">
                            @if ($temple?->id)
                                <button
                                    type="button"
                                    data-local-favorite-toggle
                                    data-favorite='@json($favoritePayload)'
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-800"
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
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-900 transition hover:bg-stone-100"
                            >
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 16.5 6.75M7.5 12l9 5.25M7.5 12a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm13.5-6.75a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm0 13.5a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                                <span data-share-label>แชร์</span>
                                <span class="text-xs opacity-75" data-share-count="temple:{{ $temple?->id }}">{{ number_format($shareCount) }}</span>
                            </button>
                        </div>

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
                    {{ $summary ?? 'ยังไม่มีคำอธิบายสั้น' }}
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
                    <div class="temple-rich-content temple-rich-content-light mt-4 space-y-5 text-base leading-8 text-stone-600">
                        @if ($content?->description)
                            <div>{!! $renderRichText($content->description) !!}</div>
                        @endif
                        @if ($temple?->history)
                            <div>{!! $renderRichText($temple->history) !!}</div>
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
                                    <div class="temple-rich-content temple-rich-content-light mt-2 text-sm leading-6 text-stone-600">
                                        {!! $highlight->description ? $renderRichText($highlight->description) : '-' !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="mt-8 rounded-2xl border border-stone-200 bg-white p-6">
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
            </article>
        </section>
    </main>
    @include('frontend.partials.local_favorites_script')
    @include('frontend.partials.share_script')
@endsection
