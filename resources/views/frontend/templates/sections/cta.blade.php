@php
    $content = $section->content_data ?? [];
    $showPrimaryButton = (bool) ($content['primary_enabled'] ?? true);
    $showSecondaryButton = (bool) ($content['secondary_enabled'] ?? true);
@endphp
<section class="px-4 py-14" style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl rounded-3xl border border-blue-300/20 bg-blue-500/10 p-8 text-center shadow-xl shadow-blue-950/20">
        @if(!empty($content['eyebrow']))
            <p class="text-sm font-semibold text-blue-200">{{ $content['eyebrow'] }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? '' }}</h2>
        @if(!empty($content['body']))
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-300">{{ $content['body'] }}</p>
        @endif
        @if(($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url'])) || ($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url'])))
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                @if($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
                <a href="{{ $content['primary_url'] }}" class="inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">{{ $content['primary_label'] }}</a>
                @endif
                @if($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']))
                    <a href="{{ $content['secondary_url'] }}" class="inline-flex rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">{{ $content['secondary_label'] }}</a>
                @endif
            </div>
        @endif
    </div>
</section>
