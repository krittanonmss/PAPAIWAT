@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $imageUrl = $section->image_url ?? null;
    $background = $settings['background'] ?? 'dark';
    $sectionClass = match ($background) {
        'soft' => 'bg-slate-900',
        'plain' => 'bg-slate-950',
        default => 'bg-slate-950',
    };
@endphp

<section class="{{ $sectionClass }} relative overflow-hidden">
    @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $content['title'] ?? 'Hero image' }}" class="absolute inset-0 h-full w-full object-cover opacity-35">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-slate-950/75 to-slate-950"></div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/45 to-slate-950"></div>
    @endif

    <div class="relative mx-auto flex min-h-[450px] max-w-6xl items-center px-4 py-16 text-center md:min-h-[480px]">
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
        </div>
    </div>
</section>
