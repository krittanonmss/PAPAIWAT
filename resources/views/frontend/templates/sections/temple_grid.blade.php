@php
    $content = $section->content_data ?? [];
    $items = collect($section->items ?? []);
    $showAllButton = (bool) ($content['all_button_enabled'] ?? true);
    $allButtonLabel = trim((string) ($content['all_button_label'] ?? '')) ?: 'ดูทั้งหมด';
    $allButtonUrl = trim((string) ($section->all_button_url ?? '')) ?: url('/temple-list');
    $provinceFallback = trim((string) ($content['province_fallback'] ?? '')) ?: 'ไม่ระบุจังหวัด';
    $emptyExcerpt = trim((string) ($content['empty_excerpt'] ?? '')) ?: 'ยังไม่มีคำโปรย';
    $emptyImageText = trim((string) ($content['empty_image_text'] ?? '')) ?: 'No image';
    $emptyListText = trim((string) ($content['empty_text'] ?? '')) ?: 'ยังไม่มีวัดสำหรับแสดงผล';
    $sliderThreshold = max(1, min((int) (($section->settings_data ?? [])['slider_threshold'] ?? 4), 12));
    $useSlider = $items->count() > $sliderThreshold;
@endphp
<section class="px-4 py-16" style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl">
        <div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if(!empty($content['eyebrow']))
                    <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? 'วัดแนะนำ' }}</h2>
                @if(!empty($content['subtitle']))
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
                @endif
            </div>
            @if($showAllButton)
                <a href="{{ $allButtonUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">{{ $allButtonLabel }}</a>
            @endif
        </div>

        @if($items->isEmpty())
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">{{ $emptyListText }}</div>
        @elseif($useSlider)
            <div class="relative" data-section-slider>
                <div
                    class="flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth pb-4 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    data-section-slider-track
                >
                    @foreach($items as $temple)
                        @php
                            $templeContent = $temple->relationLoaded('content') ? $temple->content : null;
                            $mediaUsages = ($templeContent && $templeContent->relationLoaded('mediaUsages')) ? $templeContent->mediaUsages : collect();
                            $cover = $mediaUsages->firstWhere('role_key', 'cover');
                            $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                            $path = $coverMedia?->path;
                            $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                            $address = $temple->relationLoaded('address') ? $temple->address : null;
                        @endphp
                        <article class="group flex-[0_0_min(82vw,22rem)] snap-start overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40 sm:flex-[0_0_20rem] lg:flex-[0_0_calc((100%-3rem)/3)] xl:flex-[0_0_calc((100%-4.5rem)/4)]">
                            <a href="{{ route('temples.show', $temple) }}" class="flex h-full w-full flex-col">
                                <div class="h-52 bg-slate-900 sm:h-56">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $templeContent?->title ?? 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-slate-500">{{ $emptyImageText }}</div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <p class="text-xs text-blue-300">{{ $address?->province ?? $provinceFallback }}</p>
                                    <h3 class="mt-2 line-clamp-2 text-xl font-semibold text-white">{{ $templeContent?->title ?? '-' }}</h3>
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $templeContent?->excerpt ?? $emptyExcerpt }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="pointer-events-none absolute inset-y-0 left-0 right-0 hidden items-center justify-between px-2 sm:flex">
                    <button
                        type="button"
                        class="pointer-events-auto inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-slate-950/75 text-2xl text-white shadow-lg shadow-slate-950/40 backdrop-blur transition hover:bg-slate-900"
                        aria-label="เลื่อนรายการวัดไปทางซ้าย"
                        data-section-slider-prev
                    >‹</button>
                    <button
                        type="button"
                        class="pointer-events-auto inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-slate-950/75 text-2xl text-white shadow-lg shadow-slate-950/40 backdrop-blur transition hover:bg-slate-900"
                        aria-label="เลื่อนรายการวัดไปทางขวา"
                        data-section-slider-next
                    >›</button>
                </div>
                <div class="mt-4 flex justify-between gap-2 sm:hidden">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/[0.06] text-xl text-white transition hover:bg-white/10"
                        aria-label="เลื่อนรายการวัดไปทางซ้าย"
                        data-section-slider-prev
                    >‹</button>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/[0.06] text-xl text-white transition hover:bg-white/10"
                        aria-label="เลื่อนรายการวัดไปทางขวา"
                        data-section-slider-next
                    >›</button>
                </div>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach($items as $temple)
                @php
                    $templeContent = $temple->relationLoaded('content') ? $temple->content : null;
                    $mediaUsages = ($templeContent && $templeContent->relationLoaded('mediaUsages')) ? $templeContent->mediaUsages : collect();
                    $cover = $mediaUsages->firstWhere('role_key', 'cover');
                    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                    $path = $coverMedia?->path;
                    $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                    $address = $temple->relationLoaded('address') ? $temple->address : null;
                @endphp
                <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                    <a href="{{ route('temples.show', $temple) }}">
                        <div class="h-56 bg-slate-900">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $templeContent?->title ?? 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">{{ $emptyImageText }}</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-blue-300">{{ $address?->province ?? $provinceFallback }}</p>
                            <h3 class="mt-2 line-clamp-2 text-xl font-semibold text-white">{{ $templeContent?->title ?? '-' }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $templeContent?->excerpt ?? $emptyExcerpt }}</p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
