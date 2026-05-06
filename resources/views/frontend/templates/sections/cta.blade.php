@php
    $content = $section->content_data ?? [];
@endphp

<section class="bg-slate-950 px-4 py-14">
    <div class="mx-auto max-w-5xl rounded-3xl border border-blue-300/20 bg-blue-500/10 p-8 text-center shadow-xl shadow-blue-950/20">
        @if(!empty($content['eyebrow']))
            <p class="text-sm font-semibold text-blue-200">{{ $content['eyebrow'] }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? '' }}</h2>
        @if(!empty($content['body']))
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-300">{{ $content['body'] }}</p>
        @endif
        @if(!empty($content['primary_label']) && !empty($content['primary_url']))
            <div class="mt-6">
                <a href="{{ $content['primary_url'] }}" class="inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">{{ $content['primary_label'] }}</a>
            </div>
        @endif
    </div>
</section>
