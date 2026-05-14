@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $imageData = $section->image_data ?? [];
    $imageUrl = $section->image_url ?? null;
    $imageFirst = ($settings['layout'] ?? 'image_right') === 'image_left';
    $imageOpacity = max(10, min(100, (int) ($settings['image_opacity'] ?? 100))) / 100;
    $imageFit = ($settings['image_fit'] ?? 'contain') === 'cover' ? 'cover' : 'contain';
    $rawImagePosition = $settings['image_position'] ?? 'center';
    $imagePosition = in_array($rawImagePosition, ['center', 'top', 'bottom', 'left', 'right'], true)
        ? $rawImagePosition
        : 'center';
    $showPrimaryButton = (bool) ($content['primary_enabled'] ?? true);
    $showSecondaryButton = (bool) ($content['secondary_enabled'] ?? true);
    $emptyImageText = trim((string) ($content['empty_image_text'] ?? '')) ?: 'No image';
@endphp
<section class="px-4 py-16" style="@include('frontend.templates.sections._background')">
    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2">
        <div class="{{ $imageFirst ? 'lg:order-2' : '' }}">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h2 class="mt-2 text-3xl font-bold leading-tight text-white md:text-4xl">{{ $content['title'] ?? '' }}</h2>
            @if(!empty($content['subtitle']))
                <p class="mt-4 text-base leading-7 text-slate-400">{{ $content['subtitle'] }}</p>
            @endif
            @if(!empty($content['body']))
                <div class="mt-6 space-y-4 text-sm leading-7 text-slate-300">
                    @foreach(preg_split("/\r\n|\n|\r/", trim($content['body'])) as $paragraph)
                        @if(trim($paragraph) !== '')
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                </div>
            @endif
            @if(($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url'])) || ($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url'])))
                <div class="mt-7 flex flex-wrap gap-3">
                    @if($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
                        <a href="{{ $content['primary_url'] }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">{{ $content['primary_label'] }}</a>
                    @endif
                    @if($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']))
                        <a href="{{ $content['secondary_url'] }}" class="rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">{{ $content['secondary_label'] }}</a>
                    @endif
                </div>
            @endif
        </div>

        <div class="{{ $imageFirst ? 'lg:order-1' : '' }}">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30">
                @if($imageUrl)
                    <img
                        src="{{ $imageUrl }}"
                        @if(!empty($imageData['srcset'])) srcset="{{ $imageData['srcset'] }}" @endif
                        @if(!empty($imageData['sizes'])) sizes="{{ $imageData['sizes'] }}" @endif
                        alt="{{ $content['title'] ?? 'Section image' }}"
                        class="aspect-[4/3] w-full {{ $imageFit === 'cover' ? 'object-cover' : 'object-contain' }}"
                        style="opacity: {{ $imageOpacity }}; object-position: {{ $imagePosition }};"
                    >
                @else
                    <div class="flex aspect-[4/3] items-center justify-center text-sm text-slate-500">{{ $emptyImageText }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
