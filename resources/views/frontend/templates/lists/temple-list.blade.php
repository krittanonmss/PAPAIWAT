@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'รายการวัด')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'รายการวัด')

@section('content')
    @php
        $items = $items ?? collect();
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8">
            <p class="text-sm text-slate-400">
                พบ <span class="font-semibold text-blue-300">{{ number_format($items->count()) }}</span> วัด
            </p>

            <h1 class="mt-3 text-3xl font-bold text-white">
                {{ $page->title ?? 'รายการวัด' }}
            </h1>

            @if ($page->excerpt)
                <p class="mt-3 text-slate-400">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>

        @if ($items->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($items as $temple)
                    @php
                        $content = $temple->content;
                        $address = $temple->address;
                        $stat = $temple->stat;

                        $cover = $content?->mediaUsages?->firstWhere('role_key', 'cover');
                        $imageUrl = $cover?->media?->path
                            ? (filter_var($cover->media->path, FILTER_VALIDATE_URL)
                                ? $cover->media->path
                                : \Illuminate\Support\Facades\Storage::url($cover->media->path))
                            : null;

                        $category = $content?->categories?->first();

                        $openingHour = $temple->openingHours?->firstWhere('is_closed', false);

                        $openTime = $openingHour?->open_time
                            ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i')
                            : null;

                        $closeTime = $openingHour?->close_time
                            ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i')
                            : null;

                        $fee = $temple->fees?->firstWhere('is_active', true);
                    @endphp

                    <a
                        href="{{ route('temples.show', $temple) }}"
                        class="group overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-white/20"
                    >
                        <div class="relative h-72 overflow-hidden bg-slate-800">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $content?->title ?? 'Temple image' }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">
                                    No Image
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/10 to-transparent"></div>

                            @if ($temple->temple_type)
                                <span class="absolute left-4 top-4 rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-slate-950">
                                    {{ $temple->temple_type }}
                                </span>
                            @endif

                            @if ($category)
                                <span class="absolute bottom-4 left-4 rounded-xl bg-slate-950/55 px-3 py-1.5 text-xs text-white backdrop-blur">
                                    {{ $category->name }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-4 p-6">
                            <div>
                                <h2 class="line-clamp-1 text-2xl font-semibold text-white">
                                    {{ $content?->title ?? '-' }}
                                </h2>

                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">
                                    {{ $content?->excerpt ?? '-' }}
                                </p>
                            </div>

                            <div class="space-y-2 text-sm text-slate-400">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500">ที่ตั้ง</span>
                                    <span>{{ $address?->province ?? '-' }}</span>
                                </div>

                                @if ($temple->sect)
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500">นิกาย</span>
                                        <span>{{ $temple->sect }}</span>
                                    </div>
                                @endif

                                @if ($openTime || $closeTime)
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500">เวลา</span>
                                        <span>{{ $openTime ?? '--:--' }} - {{ $closeTime ?? '--:--' }}</span>
                                    </div>
                                @endif

                                @if ($fee)
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500">ค่าเข้า</span>
                                        <span>
                                            {{ $fee->amount !== null ? number_format($fee->amount, 0) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            @if ($stat)
                                <div class="grid grid-cols-3 gap-3 border-t border-white/10 pt-4 text-center">
                                    <div>
                                        <p class="text-xs text-slate-500">เข้าชม</p>
                                        <p class="mt-1 text-sm font-semibold text-white">
                                            {{ number_format(data_get($stat, 'view_count', 0)) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-slate-500">รีวิว</p>
                                        <p class="mt-1 text-sm font-semibold text-white">
                                            {{ number_format(data_get($stat, 'review_count', 0)) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-slate-500">ถูกใจ</p>
                                        <p class="mt-1 text-sm font-semibold text-white">
                                            {{ number_format(data_get($stat, 'favorite_count', 0)) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                ยังไม่มีข้อมูลวัด
            </div>
        @endif
    </section>
@endsection