@php
    $content = $section->content_data ?? [];
@endphp

<section class="bg-slate-950 px-4 py-16">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[1fr_420px]">
        <div>
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? 'ติดต่อเรา' }}</h2>
            @if(!empty($content['address']))
                <p class="mt-5 whitespace-pre-line text-base leading-8 text-slate-300">{{ $content['address'] }}</p>
            @endif
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6">
            <div class="space-y-4 text-sm text-slate-300">
                @if(!empty($content['phone']))
                    <p><span class="text-slate-500">โทร:</span> <a href="tel:{{ $content['phone'] }}" class="hover:text-white">{{ $content['phone'] }}</a></p>
                @endif
                @if(!empty($content['email']))
                    <p><span class="text-slate-500">อีเมล:</span> <a href="mailto:{{ $content['email'] }}" class="hover:text-white">{{ $content['email'] }}</a></p>
                @endif
                @if(!empty($content['map_url']))
                    <a href="{{ $content['map_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">เปิดแผนที่</a>
                @endif
            </div>
        </div>
    </div>
</section>
