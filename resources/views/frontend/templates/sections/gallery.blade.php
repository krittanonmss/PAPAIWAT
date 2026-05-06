@php
    $content = $section->content_data ?? [];
    $items = collect(preg_split("/\r\n|\n|\r/", trim($content['gallery_text'] ?? '')))
        ->map(function ($line) {
            [$url, $caption] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
            return ['url' => $url, 'caption' => $caption];
        })
        ->filter(fn ($item) => $item['url'] !== '');
@endphp

<section class="bg-slate-950 px-4 py-16">
    <div class="mx-auto max-w-7xl">
        @if(!empty($content['eyebrow']))
            <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? 'แกลเลอรี' }}</h2>
        @if(!empty($content['subtitle']))
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
        @endif

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($items as $item)
                <figure class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
                    <img src="{{ $item['url'] }}" alt="{{ $item['caption'] ?: ($content['title'] ?? 'Gallery image') }}" class="aspect-[4/3] w-full object-cover" loading="lazy">
                    @if($item['caption'])
                        <figcaption class="px-4 py-3 text-sm text-slate-400">{{ $item['caption'] }}</figcaption>
                    @endif
                </figure>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 sm:col-span-2 lg:col-span-3">ยังไม่มีรูปในแกลเลอรี</div>
            @endforelse
        </div>
    </div>
</section>
