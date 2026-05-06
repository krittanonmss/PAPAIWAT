@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $imageUrl = $section->image_url ?? null;
    $imageFirst = ($settings['layout'] ?? 'image_right') === 'image_left';
@endphp

<section class="bg-slate-950 px-4 py-16">
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
        </div>

        <div class="{{ $imageFirst ? 'lg:order-1' : '' }}">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $content['title'] ?? 'Section image' }}" class="aspect-[4/3] w-full object-cover">
                @else
                    <div class="flex aspect-[4/3] items-center justify-center text-sm text-slate-500">No image</div>
                @endif
            </div>
        </div>
    </div>
</section>
