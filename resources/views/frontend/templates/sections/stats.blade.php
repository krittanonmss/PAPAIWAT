@php
    $content = $section->content_data ?? [];
    $items = collect(preg_split("/\r\n|\n|\r/", trim($content['stats_text'] ?? '')))
        ->map(function ($line) {
            [$value, $label] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
            return ['value' => $value, 'label' => $label];
        })
        ->filter(fn ($item) => $item['value'] !== '');
    $emptyText = trim((string) ($content['empty_text'] ?? '')) ?: 'ยังไม่มีตัวเลขสำคัญ';
@endphp
<section class="px-4 py-14" style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl">
        @if(!empty($content['title']) || !empty($content['subtitle']))
            <div class="mb-8 max-w-3xl">
                @if(!empty($content['eyebrow']))
                    <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? '' }}</h2>
                @if(!empty($content['subtitle']))
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
                @endif
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($items as $item)
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6">
                    <p class="text-4xl font-bold text-white">{{ $item['value'] }}</p>
                    @if($item['label'])
                        <p class="mt-2 text-sm leading-6 text-slate-400">{{ $item['label'] }}</p>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 sm:col-span-2 lg:col-span-4">{{ $emptyText }}</div>
            @endforelse
        </div>
    </div>
</section>
