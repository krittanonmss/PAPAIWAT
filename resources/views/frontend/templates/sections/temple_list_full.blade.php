@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $items = $section->items ?? collect();
    $filters = $section->filters ?? [];
    $totalItems = method_exists($items, 'total') ? $items->total() : collect($items)->count();
    $activeSearch = request('search');
    $activeProvince = request('province');
    $activeCategory = request('category');
    $activeSort = request('sort');
    $categories = collect($filters['categories'] ?? []);
    $provinces = collect($filters['provinces'] ?? []);
    $hasActiveFilters = $activeSearch || $activeProvince || $activeCategory || $activeSort;
    $searchPlaceholder = trim((string) ($content['search_placeholder'] ?? '')) ?: 'ค้นหาวัด, จังหวัด, หรือสิ่งที่คุณต้องการ...';
    $provinceAllLabel = trim((string) ($content['province_all_label'] ?? '')) ?: 'ทุกจังหวัด';
    $categoryAllLabel = trim((string) ($content['category_all_label'] ?? '')) ?: 'ทุกหมวดหมู่';
    $sortDefaultLabel = trim((string) ($content['sort_default_label'] ?? '')) ?: 'เรียงตามระบบ';
    $popularOptionLabel = trim((string) ($content['popular_option_label'] ?? '')) ?: 'คะแนนสูงสุด';
    $ratingOptionLabel = trim((string) ($content['rating_option_label'] ?? '')) ?: 'รีวิวดีที่สุด';
    $latestOptionLabel = trim((string) ($content['latest_option_label'] ?? '')) ?: 'ล่าสุด';
    $totalLabel = trim((string) ($content['total_label'] ?? '')) ?: 'ทั้งหมด';
    $totalSuffix = trim((string) ($content['total_suffix'] ?? '')) ?: 'วัด';
    $clearLabel = trim((string) ($content['clear_label'] ?? '')) ?: 'ล้างตัวกรอง';
    $submitLabel = trim((string) ($content['submit_label'] ?? '')) ?: 'ค้นหา';
    $provinceFallback = trim((string) ($content['province_fallback'] ?? '')) ?: 'ไม่ระบุจังหวัด';
    $emptyExcerpt = trim((string) ($content['empty_excerpt'] ?? '')) ?: 'ยังไม่มีคำโปรย';
    $emptyImageText = trim((string) ($content['empty_image_text'] ?? '')) ?: 'No Image';
    $emptyListText = trim((string) ($content['empty_text'] ?? '')) ?: 'ยังไม่มีวัด';
    $listColumns = max(1, min((int) ($settings['list_columns'] ?? 4), 6));
    $gridColumnClass = [
        1 => 'grid-cols-1',
        2 => 'md:grid-cols-2',
        3 => 'md:grid-cols-2 xl:grid-cols-3',
        4 => 'md:grid-cols-2 xl:grid-cols-4',
        5 => 'md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5',
        6 => 'md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6',
    ][$listColumns] ?? 'md:grid-cols-2 xl:grid-cols-4';
    $emptyColumnClass = [
        1 => '',
        2 => 'md:col-span-2',
        3 => 'md:col-span-2 xl:col-span-3',
        4 => 'md:col-span-2 xl:col-span-4',
        5 => 'md:col-span-2 lg:col-span-3 xl:col-span-5',
        6 => 'md:col-span-2 lg:col-span-3 xl:col-span-6',
    ][$listColumns] ?? 'md:col-span-2 xl:col-span-4';
    $sectionFilterId = 'section-filter-' . ($section->id ?: 'preview');
@endphp
<section id="{{ $sectionFilterId }}" class="px-4 py-16 text-white" data-section-filter-root style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 text-center">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="mt-3 text-4xl font-bold md:text-6xl">{{ $content['title'] ?? 'รวมวัดทั่วไทย' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-400">{{ $content['subtitle'] }}</p>
            @endif
        </div>

        <form action="{{ url()->current() }}#{{ $sectionFilterId }}" method="GET" class="mb-8 rounded-3xl border border-white/10 bg-slate-900/75 p-5 shadow-2xl shadow-slate-950/50 backdrop-blur-xl" data-section-filter-form>
            <input type="search" name="search" value="{{ $activeSearch }}" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-2xl border border-white/10 bg-white/10 py-4 px-5 text-sm text-white placeholder:text-slate-400 outline-none transition focus:border-blue-400/50">

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <select name="province" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">{{ $provinceAllLabel }}</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province }}" @selected($activeProvince === $province)>{{ $province }}</option>
                    @endforeach
                </select>

                <select name="category" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">{{ $categoryAllLabel }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug ?? $category->id }}" @selected((string) $activeCategory === (string) ($category->slug ?? $category->id))>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="sort" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">{{ $sortDefaultLabel }}</option>
                    <option value="popular" @selected($activeSort === 'popular')>{{ $popularOptionLabel }}</option>
                    <option value="rating" @selected($activeSort === 'rating')>{{ $ratingOptionLabel }}</option>
                    <option value="latest" @selected($activeSort === 'latest')>{{ $latestOptionLabel }}</option>
                </select>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs text-blue-200">{{ $totalLabel }} {{ number_format($totalItems) }} {{ $totalSuffix }}</span>
                <div class="flex gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ url()->current() }}#{{ $sectionFilterId }}" class="rounded-2xl border border-blue-300/20 bg-blue-300/10 px-4 py-2.5 text-sm font-medium text-blue-100" data-section-filter-link>{{ $clearLabel }}</a>
                    @endif
                    <button class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">{{ $submitLabel }}</button>
                </div>
            </div>
        </form>

        <div class="grid gap-7 {{ $gridColumnClass }}">
            @forelse ($items as $temple)
                @php
                    $templeContent = $temple->relationLoaded('content') ? $temple->content : null;
                    $address = $temple->relationLoaded('address') ? $temple->address : null;
                    $mediaUsages = ($templeContent && $templeContent->relationLoaded('mediaUsages')) ? $templeContent->mediaUsages : collect();
                    $cover = $mediaUsages->firstWhere('role_key', 'cover');
                    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                    $path = $coverMedia?->path;
                    $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                    $stat = $temple->relationLoaded('stat') ? $temple->stat : null;
                    $templeCategories = ($templeContent && $templeContent->relationLoaded('categories')) ? $templeContent->categories : collect();
                    $primaryCategory = $templeCategories->first();
                    $locationParts = collect([$address?->district, $address?->province])->filter()->values();
                    $locationText = $locationParts->isNotEmpty() ? $locationParts->implode(', ') : $provinceFallback;
                    $highlight = ($temple->relationLoaded('highlights') ? $temple->highlights : collect())->first();
                    $fee = ($temple->relationLoaded('fees') ? $temple->fees : collect())->first();
                    $openingHour = ($temple->relationLoaded('openingHours') ? $temple->openingHours : collect())
                        ->first(fn ($hour) => ! $hour->is_closed && ($hour->open_time || $hour->close_time));
                    $recommendedTime = trim(($temple->recommended_visit_start_time ?? '') . (($temple->recommended_visit_start_time && $temple->recommended_visit_end_time) ? ' - ' : '') . ($temple->recommended_visit_end_time ?? ''));
                    $feeText = $fee
                        ? ($fee->amount !== null ? number_format((float) $fee->amount, 0) . ' ' . ($fee->currency ?: 'บาท') : ($fee->label ?: $fee->fee_type))
                        : null;
                @endphp
                <article class="group flex h-full overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                    <a href="{{ route('temples.show', $temple) }}" class="flex h-full w-full flex-col">
                        <div class="relative h-56 overflow-hidden bg-slate-900">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $templeContent?->title ?? 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">{{ $emptyImageText }}</div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/90 to-transparent"></div>
                            <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                @if($primaryCategory)
                                    <span class="rounded-full border border-white/20 bg-slate-950/70 px-3 py-1 text-xs font-medium text-white backdrop-blur">{{ $primaryCategory->name }}</span>
                                @endif
                                @if($templeContent?->is_featured)
                                    <span class="rounded-full border border-amber-500/30 bg-slate-950/85 px-3 py-1 text-xs font-medium text-amber-300 backdrop-blur">แนะนำ</span>
                                @endif
                            </div>
                            @if($stat?->average_rating)
                                <div class="absolute bottom-4 right-4 inline-flex items-center gap-1.5 rounded-full border border-yellow-300/25 bg-slate-950/80 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                                    <span>{{ number_format((float) $stat->average_rating, 1) }}</span>
                                    <span class="text-sm leading-none text-yellow-300">★</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-xs font-medium text-blue-300">{{ $locationText }}</p>
                            <h2 class="mt-2 line-clamp-2 text-xl font-semibold leading-snug">{{ $templeContent?->title ?? '-' }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $templeContent?->excerpt ?? $emptyExcerpt }}</p>

                            <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-300">
                                @if($temple->temple_type)
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1">{{ $temple->temple_type }}</span>
                                @endif
                                @if($temple->sect)
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1">{{ $temple->sect }}</span>
                                @endif
                                @if($openingHour)
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1">{{ $openingHour->open_time }} - {{ $openingHour->close_time }}</span>
                                @elseif($recommendedTime !== '')
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1">แนะนำ {{ $recommendedTime }}</span>
                                @endif
                                @if($feeText)
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1">ค่าเข้า {{ $feeText }}</span>
                                @endif
                            </div>

                            @if($highlight)
                                <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/35 p-3">
                                    <p class="line-clamp-1 text-xs font-semibold text-white">{{ $highlight->title }}</p>
                                    @if($highlight->description)
                                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-400">{{ $highlight->description }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto pt-5">
                                <div class="grid grid-cols-3 gap-2 border-t border-white/10 pt-4 text-center">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ number_format((int) ($stat?->view_count ?? 0)) }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">views</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ number_format((int) ($stat?->review_count ?? 0)) }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">reviews</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ number_format((int) ($stat?->favorite_count ?? 0)) }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">saved</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 {{ $emptyColumnClass }}">{{ $emptyListText }}</div>
            @endforelse
        </div>

        @if (method_exists($items, 'links') && $items->hasPages())
            <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30" data-section-filter-pagination>
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
