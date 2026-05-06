@php
    $content = $section->content_data ?? [];
@endphp

<section class="bg-slate-950 px-4 py-16">
    <div class="mx-auto max-w-4xl">
        @if(!empty($content['eyebrow']))
            <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
        @endif
        @if(!empty($content['title']))
            <h2 class="mt-2 text-3xl font-bold text-white md:text-4xl">{{ $content['title'] }}</h2>
        @endif
        @if(!empty($content['subtitle']))
            <p class="mt-4 text-base leading-7 text-slate-400">{{ $content['subtitle'] }}</p>
        @endif
        @if(!empty($content['body']))
            <div class="mt-8 space-y-4 text-base leading-8 text-slate-300">
                @foreach(preg_split("/\r\n|\n|\r/", trim($content['body'])) as $paragraph)
                    @if(trim($paragraph) !== '')
                        <p>{{ $paragraph }}</p>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
