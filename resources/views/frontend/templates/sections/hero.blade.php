@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $imageData = $section->image_data ?? [];
    $imageUrl = $section->image_url ?? null;
    $isBanner = ($section->component_key ?? 'hero') === 'banner';
    $bannerHeight = (int) ($settings['banner_height'] ?? 540);
    $bannerHeight = in_array($bannerHeight, [540, 720], true) ? $bannerHeight : 540;
    $sectionSizeClass = $isBanner ? 'min-h-[260px]' : 'aspect-[16/9] min-h-[420px]';
    $sectionSizeStyle = $isBanner
        ? 'aspect-ratio: 1920 / ' . $bannerHeight . ';'
        : '';
    $contentPaddingClass = $isBanner ? 'px-4 py-12 md:py-16' : 'px-4 py-16 md:py-20';
    $contentMaxWidthClass = $isBanner ? 'max-w-4xl' : 'max-w-3xl';
    $headingClass = $isBanner ? 'mt-3 text-3xl font-bold leading-tight text-white md:text-5xl' : 'mt-4 text-4xl font-bold leading-tight text-white md:text-6xl';
    $imageOpacityPercent = max(10, min(100, (int) ($settings['image_opacity'] ?? 100)));
    $imageOpacity = $imageOpacityPercent / 100;
    $imageFit = ($settings['image_fit'] ?? 'contain') === 'cover' ? 'cover' : 'contain';
    $rawImagePosition = $settings['image_position'] ?? 'center';
    $imagePosition = in_array($rawImagePosition, ['center', 'top', 'bottom', 'left', 'right'], true)
        ? $rawImagePosition
        : 'center';
    $showSearchBox = (bool) ($settings['show_search_box'] ?? false);
    $showSummaryStats = (bool) ($settings['show_summary_stats'] ?? false);
    $summaryStats = $section->summary_stats ?? [];
    $showPrimaryButton = (bool) ($content['primary_enabled'] ?? true);
    $showSecondaryButton = (bool) ($content['secondary_enabled'] ?? true);
    $searchPlaceholder = trim((string) ($content['search_placeholder'] ?? '')) ?: 'พิมพ์สิ่งที่อยากค้นหา...';
    $searchButtonLabel = trim((string) ($content['search_button_label'] ?? '')) ?: 'ค้นหา';
    $templeStatLabel = trim((string) ($content['temple_stat_label'] ?? '')) ?: 'วัดทั้งหมด';
    $articleStatLabel = trim((string) ($content['article_stat_label'] ?? '')) ?: 'บทความทั้งหมด';
    $viewStatLabel = trim((string) ($content['view_stat_label'] ?? '')) ?: 'ยอดผู้เข้าชม';
@endphp
<section class="relative overflow-hidden {{ $sectionSizeClass }}" style="{{ $sectionSizeStyle }} @include('frontend.templates.sections._background')">
    @if($imageUrl)
        <div class="absolute inset-0 bg-slate-950"></div>
        <img
            src="{{ $imageUrl }}"
            @if(!empty($imageData['srcset'])) srcset="{{ $imageData['srcset'] }}" @endif
            @if(!empty($imageData['sizes'])) sizes="{{ $imageData['sizes'] }}" @endif
            alt="{{ $content['title'] ?? 'Hero image' }}"
            class="absolute inset-0 h-full w-full {{ $imageFit === 'cover' ? 'object-cover' : 'object-contain' }}"
            style="opacity: {{ $imageOpacity }}; object-position: {{ $imagePosition }};"
        >
        @if($imageOpacityPercent < 100)
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-slate-950/75 to-slate-950"></div>
        @endif
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/45 to-slate-950"></div>
    @endif

    <div class="relative mx-auto flex h-full max-w-7xl items-center {{ $contentPaddingClass }} text-center">
        <div class="mx-auto {{ $contentMaxWidthClass }}">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="{{ $headingClass }}">{{ $content['title'] ?? '' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-300">{{ $content['subtitle'] }}</p>
            @endif
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
                    <a href="{{ $content['primary_url'] }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500">{{ $content['primary_label'] }}</a>
                @endif
                @if($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']))
                    <a href="{{ $content['secondary_url'] }}" class="rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">{{ $content['secondary_label'] }}</a>
                @endif
            </div>
            @if($showSearchBox)
                <form action="{{ route('search') }}" method="GET" class="mx-auto mt-8 w-full max-w-[900px] text-left">
                    <label class="sr-only" for="hero-search-{{ $section->id }}">ค้นหาทุกอย่าง</label>
                    <div class="relative flex h-10 items-center overflow-hidden rounded-full border border-white/12 bg-[rgba(40,40,40,0.42)] px-4 shadow-[0_14px_34px_rgba(0,0,0,0.28),inset_0_1px_0_rgba(255,255,255,0.16),inset_0_-1px_0_rgba(255,255,255,0.05)] backdrop-blur-xl">
                        <div class="pointer-events-none absolute inset-x-5 top-px h-px bg-gradient-to-r from-transparent via-white/22 to-transparent"></div>
                        <input
                            id="hero-search-{{ $section->id }}"
                            type="search"
                            name="q"
                            placeholder="{{ $searchPlaceholder }}"
                            class="h-full min-w-0 flex-1 bg-transparent px-0 text-sm text-white placeholder:text-white/65 focus:outline-none"
                        >
                        <button type="submit" class="ml-3 shrink-0 text-sm font-normal text-white transition hover:opacity-75">{{ $searchButtonLabel }}</button>
                    </div>
                </form>
            @endif
            @if($showSummaryStats)
                <div class="mx-auto mt-8 grid max-w-3xl gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">{{ $templeStatLabel }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format((int) ($summaryStats['temples'] ?? 0)) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">{{ $articleStatLabel }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format((int) ($summaryStats['articles'] ?? 0)) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">{{ $viewStatLabel }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format((int) ($summaryStats['views'] ?? 0)) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
