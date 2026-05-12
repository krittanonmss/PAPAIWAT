@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $imageData = $section->image_data ?? [];
    $imageUrl = $section->image_url ?? null;
    $background = $settings['background'] ?? 'dark';
    $imageOpacityPercent = max(10, min(100, (int) ($settings['image_opacity'] ?? 100)));
    $imageOpacity = $imageOpacityPercent / 100;
    $imageFit = ($settings['image_fit'] ?? 'contain') === 'cover' ? 'cover' : 'contain';
    $rawImagePosition = $settings['image_position'] ?? 'center';
    $imagePosition = in_array($rawImagePosition, ['center', 'top', 'bottom', 'left', 'right'], true)
        ? $rawImagePosition
        : 'center';
    $showSearchBox = (bool) ($settings['show_search_box'] ?? false);
    $sectionClass = match ($background) {
        'soft' => 'bg-slate-900',
        'plain' => 'bg-slate-950',
        default => 'bg-slate-950',
    };
@endphp

<section class="{{ $sectionClass }} relative overflow-hidden">
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

    <div class="relative mx-auto flex min-h-[580px] max-w-6xl items-center px-4 py-20 text-center md:min-h-[820px] xl:min-h-[900px]">
        <div class="mx-auto max-w-3xl">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="mt-4 text-4xl font-bold leading-tight text-white md:text-6xl">{{ $content['title'] ?? '' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-300">{{ $content['subtitle'] }}</p>
            @endif
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if(!empty($content['primary_label']) && !empty($content['primary_url']))
                    <a href="{{ $content['primary_url'] }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500">{{ $content['primary_label'] }}</a>
                @endif
                @if(!empty($content['secondary_label']) && !empty($content['secondary_url']))
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
                            placeholder="พิมพ์สิ่งที่อยากค้นหา..."
                            class="h-full min-w-0 flex-1 bg-transparent px-0 text-sm text-white placeholder:text-white/65 focus:outline-none"
                        >
                        <button type="submit" class="ml-3 shrink-0 text-sm font-normal text-white transition hover:opacity-75">ค้นหา</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
