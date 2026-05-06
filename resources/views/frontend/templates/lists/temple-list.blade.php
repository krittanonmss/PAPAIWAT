@extends('frontend.layouts.app')

@section('content')
    @php
        $sections = collect($sections ?? []);
        $items = $items ?? collect();
        $totalItems = method_exists($items, 'total') ? $items->total() : $items->count();
        $activeSearch = request('search');
        $activeProvince = request('province');
        $activeCategory = request('category');
        $activeTempleType = request('temple_type');
        $activeSort = request('sort');
        $categories = collect($filters['categories'] ?? [])
            ->whenEmpty(fn () => $items
                ->flatMap(fn ($temple) => $temple->content?->categories ?? collect())
                ->filter()
                ->unique('id')
                ->values());
        $provinces = collect($filters['provinces'] ?? [])
            ->whenEmpty(fn () => $items->map(fn ($temple) => $temple->address?->province)->filter()->unique()->values());
    @endphp

    @if($sections->isNotEmpty())
        <main class="bg-slate-950 text-white">
            @foreach($sections as $section)
                @include('frontend.templates.sections._renderer', ['section' => $section])
            @endforeach
        </main>
    @else
    <section class="relative overflow-hidden bg-slate-950 pb-16 text-white">
        {{-- Hero --}}
        <div class="relative overflow-hidden border-b border-white/10 bg-gradient-to-b from-indigo-950/70 via-slate-950 to-slate-950">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.18),transparent_35%)]"></div>

            <div class="relative mx-auto flex min-h-[420px] max-w-7xl flex-col items-center justify-center px-4 py-24 text-center">
                <p class="text-sm font-semibold tracking-wide text-blue-300">
                    PAPAIWAT Temples
                </p>

                <h1 class="mt-6 text-6xl font-bold tracking-tight text-white md:text-7xl">
                    {{ $page->title ?? 'Temple' }}
                </h1>

                <p class="mt-8 max-w-2xl text-lg font-medium leading-8 text-slate-300">
                    {{ $page->excerpt ?? 'ค้นพบวัด วัฒนธรรม และสถานที่ศักดิ์สิทธิ์ทั่วประเทศไทย' }}
                </p>
            </div>
        </div>

        {{-- Search / Filter Panel --}}
        <div class="relative mx-auto -mt-14 max-w-7xl px-4">
            <form method="GET" class="w-full rounded-3xl border border-white/10 bg-slate-800/80 p-5 text-left shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
                <div class="relative">
                    <span class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                    </span>

                    <input
                        type="search"
                        name="search"
                        value="{{ $activeSearch }}"
                        placeholder="ค้นหาวัด, จังหวัด, หรือสิ่งที่คุณต้องการ..."
                        class="w-full rounded-2xl border border-white/10 bg-white/10 py-4 pl-14 pr-5 text-sm text-white placeholder:text-slate-400 outline-none transition focus:border-blue-400/50 focus:bg-white/[0.13] focus:ring-4 focus:ring-blue-500/10"
                    >
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ url()->current() }}" class="rounded-full {{ !$activeCategory && !$activeTempleType && !$activeProvince && !$activeSearch ? 'bg-white text-slate-950' : 'bg-white/10 text-slate-300 hover:bg-white/15 hover:text-white' }} px-4 py-2 text-xs font-semibold transition">
                        ทั้งหมด
                    </a>

                    @foreach ($categories->take(8) as $categoryItem)
                        <button
                            type="submit"
                            name="category"
                            value="{{ $categoryItem->slug }}"
                            class="rounded-full {{ $activeCategory === $categoryItem->slug || (string) $activeCategory === (string) $categoryItem->id ? 'bg-white text-slate-950' : 'bg-white/10 text-slate-300 hover:bg-white/15 hover:text-white' }} px-4 py-2 text-xs font-semibold transition"
                        >
                            {{ $categoryItem->name }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="grid w-full gap-3 sm:grid-cols-3">
                        <select
                            name="province"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm font-medium text-slate-200 outline-none transition focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/10"
                        >
                            <option value="">ทุกจังหวัด</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}" @selected($activeProvince === $province)>{{ $province }}</option>
                            @endforeach
                        </select>

                        <select
                            name="category"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm font-medium text-slate-200 outline-none transition focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/10"
                        >
                            <option value="">ทุกหมวดหมู่</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->slug ?? $category->id }}"
                                    @selected((string) request('category') === (string) ($category->slug ?? $category->id))
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <select
                            name="sort"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm font-medium text-slate-200 outline-none transition focus:border-blue-400/50 focus:ring-4 focus:ring-blue-500/10"
                        >
                            <option value="">เรียงตามระบบ</option>
                            <option value="popular" @selected($activeSort === 'popular')>คะแนนสูงสุด</option>
                            <option value="rating" @selected($activeSort === 'rating')>รีวิวดีที่สุด</option>
                            <option value="latest" @selected($activeSort === 'latest')>ล่าสุด</option>
                        </select>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500"
                        >
                            ค้นหา
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="mx-auto mt-8 max-w-7xl px-4">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm text-slate-400">
                        พบ <span class="font-semibold text-blue-300">{{ number_format($totalItems) }}</span> วัด
                        @if (method_exists($items, 'currentPage') && $items->lastPage() > 1)
                            <span class="text-slate-500"> · หน้า {{ number_format($items->currentPage()) }} / {{ number_format($items->lastPage()) }}</span>
                        @endif
                    </p>
                    @if ($activeSearch || $activeProvince || $activeCategory || $activeTempleType)
                        <p class="mt-1 text-xs text-slate-500">
                            กำลังกรองข้อมูลจากฐานข้อมูลตามเงื่อนไขที่เลือก
                        </p>
                    @endif
                </div>

                @if ($activeSearch || $activeProvince || $activeCategory || $activeTempleType || $activeSort)
                    <a href="{{ url()->current() }}" class="text-sm font-medium text-blue-300 transition hover:text-blue-200">
                        ล้างตัวกรอง
                    </a>
                @endif
            </div>

            @if ($items->isNotEmpty())
                <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($items as $temple)
                        @php
                            $content = $temple->content;
                            $address = $temple->address;
                            $stat = $temple->stat;
                            $openingHour = $temple->openingHours?->firstWhere('is_closed', false);
                            $fee = $temple->fees?->firstWhere('is_active', true) ?? $temple->fees?->first();
                            $highlight = $temple->highlights?->first();
                            $facilityItems = $temple->facilityItems?->take(3) ?? collect();
                            $travelInfo = $temple->travelInfos?->first();

                            $cover = $content?->mediaUsages?->firstWhere('role_key', 'cover');
                            $imageUrl = $cover?->media?->path
                                ? (filter_var($cover->media->path, FILTER_VALIDATE_URL)
                                    ? $cover->media->path
                                    : \Illuminate\Support\Facades\Storage::url($cover->media->path))
                                : null;

                            $category = $content?->categories?->first();

                            $typeLabel = $temple->temple_type ?: 'วัดไทย';
                            $rating = data_get($stat, 'average_rating');
                            $reviewCount = data_get($stat, 'review_count', 0);
                            $favoriteCount = data_get($stat, 'favorite_count', 0);
                            $score = data_get($stat, 'score', 0);
                            $openTime = $openingHour?->open_time
                                ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i')
                                : null;
                            $closeTime = $openingHour?->close_time
                                ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i')
                                : null;
                            $feeText = $fee
                                ? ($fee->amount !== null
                                    ? number_format($fee->amount, 0) . ' ' . ($fee->currency ?: 'THB')
                                    : 'ฟรี')
                                : null;
                            $recommendedStart = $temple->recommended_visit_start_time
                                ? \Carbon\Carbon::parse($temple->recommended_visit_start_time)->format('H:i')
                                : null;
                            $recommendedEnd = $temple->recommended_visit_end_time
                                ? \Carbon\Carbon::parse($temple->recommended_visit_end_time)->format('H:i')
                                : null;
                            $detailHref = route('temples.show', $temple);
                        @endphp

                        <a
                            href="{{ $detailHref }}"
                            class="group overflow-hidden rounded-3xl border border-white/10 bg-slate-800/80 shadow-xl shadow-slate-950/30 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-blue-400/30 hover:shadow-2xl hover:shadow-blue-950/20"
                        >
                            <div class="relative h-72 overflow-hidden bg-slate-800">
                                @if ($imageUrl)
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $content?->title ?? 'Temple image' }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                    >
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 text-sm text-slate-500">
                                        No Image
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent"></div>

                                <span class="absolute left-4 top-4 rounded-full bg-amber-400 px-3 py-1.5 text-xs font-bold text-slate-950 shadow-lg">
                                    {{ $typeLabel }}
                                </span>

                                <span class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition group-hover:bg-white/20">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.015-4.5-4.5-4.5-1.74 0-3.25.99-4 2.438A4.49 4.49 0 0 0 8.5 3.75C6.015 3.75 4 5.765 4 8.25c0 7.22 8.5 12 8.5 12s8.5-4.78 8.5-12Z" />
                                    </svg>
                                </span>

                                @if ($category)
                                    <span class="absolute bottom-4 left-4 rounded-xl bg-slate-950/55 px-3 py-1.5 text-xs font-semibold text-white shadow-lg backdrop-blur">
                                        {{ $category->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-4 p-6">
                                <div>
                                    <h2 class="line-clamp-1 text-2xl font-medium text-white">
                                        {{ $content?->title ?? '-' }}
                                    </h2>

                                    <p class="mt-2 line-clamp-1 text-sm text-slate-400">
                                        {{ $content?->excerpt ?: ($content?->description ? \Illuminate\Support\Str::limit(trim(strip_tags($content->description)), 120) : '-') }}
                                    </p>
                                </div>

                                <div class="grid gap-2 text-sm text-slate-400">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.438 7-11a7 7 0 1 0-14 0c0 6.562 7 11 7 11Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5h.01" />
                                        </svg>
                                        <span class="line-clamp-1">{{ $address?->address_line ?: collect([$address?->subdistrict, $address?->district, $address?->province])->filter()->join(', ') ?: '-' }}</span>
                                    </div>

                                    @if ($temple->sect)
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-500">นิกาย</span>
                                            <span>{{ $temple->sect }}</span>
                                        </div>
                                    @endif

                                    @if ($temple->architecture_style)
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-500">สถาปัตยกรรม</span>
                                            <span>{{ $temple->architecture_style }}</span>
                                        </div>
                                    @endif

                                    @if ($openTime || $closeTime)
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-500">เวลา</span>
                                            <span>{{ $openTime ?? '--:--' }} - {{ $closeTime ?? '--:--' }}</span>
                                        </div>
                                    @endif

                                    @if ($recommendedStart || $recommendedEnd)
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-500">ช่วงแนะนำ</span>
                                            <span>{{ $recommendedStart ?? '--:--' }} - {{ $recommendedEnd ?? '--:--' }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-400">
                                    @if ($rating)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.7 5.47 6.03.88-4.36 4.25 1.03 6-5.4-2.84-5.4 2.84 1.03-6-4.36-4.25 6.03-.88L12 3Z" />
                                            </svg>
                                            <span class="font-semibold text-slate-200">{{ number_format((float) $rating, 1) }}</span>
                                            <span>({{ number_format($reviewCount) }})</span>
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span>คะแนน {{ number_format((float) $score, 0) }}</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-3 border-t border-white/10 pt-4 text-center">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">ก่อตั้ง</p>
                                        <p class="mt-1 text-base font-bold text-slate-200">{{ $temple->founded_year ?: '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium text-slate-500">ค่าเข้า</p>
                                        <p class="mt-1 text-base font-bold text-slate-200">{{ $feeText ?: '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium text-slate-500">ถูกใจ</p>
                                        <p class="mt-1 text-base font-bold text-slate-200">{{ number_format($favoriteCount) }}</p>
                                    </div>
                                </div>

                                @if ($highlight)
                                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3">
                                        <p class="line-clamp-1 text-sm font-medium text-slate-200">{{ $highlight->title ?? $highlight->name ?? 'ไฮไลต์' }}</p>
                                        @if ($highlight->description ?? null)
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit(trim(strip_tags($highlight->description)), 120) }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if ($facilityItems->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($facilityItems as $item)
                                            <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-300">
                                                {{ $item->facility?->name ?? $item->value ?? 'สิ่งอำนวยความสะดวก' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($travelInfo)
                                    <div class="rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3 text-xs leading-5 text-slate-400">
                                        <span class="font-medium text-slate-300">{{ $travelInfo->travel_type }}</span>
                                        @if ($travelInfo->start_place)
                                            <span> จาก {{ $travelInfo->start_place }}</span>
                                        @endif
                                        @if ($travelInfo->distance_km)
                                            <span> · {{ number_format((float) $travelInfo->distance_km, 1) }} กม.</span>
                                        @endif
                                        @if ($travelInfo->duration_minutes)
                                            <span> · {{ number_format($travelInfo->duration_minutes) }} นาที</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                @if (method_exists($items, 'links') && $items->hasPages())
                    <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                        {{ $items->links() }}
                    </div>
                @endif
            @else
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-10 text-center text-sm text-slate-400 shadow-xl shadow-slate-950/30 backdrop-blur">
                    ยังไม่มีข้อมูลวัด
                </div>
            @endif
        </div>
    </section>
    @endif
@endsection
