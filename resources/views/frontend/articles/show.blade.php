<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $articleContent->meta_title ?? $articleContent->title }}</title>
    <meta name="description" content="{{ $articleContent->meta_description ?? $articleContent->excerpt }}">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @php
        $cover = $articleContent->mediaUsages->firstWhere('role_key', 'cover');
        $coverMedia = $cover?->media;

        $imageUrl = $coverMedia?->path
            ? \Illuminate\Support\Facades\Storage::url($coverMedia->path)
            : null;

        $primaryCategory = $articleContent->categories->firstWhere('pivot.is_primary', true)
            ?? $articleContent->categories->first();

        $publishedDate = $articleContent->published_at
            ? $articleContent->published_at->format('d/m/Y')
            : null;

        $bodyFormat = $article?->body_format ?? 'markdown';
        $body = $article?->body ?? '';
    @endphp

    <main class="relative min-h-screen overflow-hidden">
        {{-- Background --}}
        @if ($imageUrl)
            <div class="fixed inset-0 -z-20">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $coverMedia?->alt_text ?: $articleContent->title }}"
                    class="h-full w-full scale-105 object-cover opacity-40 blur-sm"
                >
            </div>
        @endif

        <div class="fixed inset-0 -z-10 bg-slate-950/80"></div>

        {{-- Article Container --}}
        <article class="mx-auto max-w-5xl px-4 py-10 md:py-16">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 shadow-2xl shadow-slate-950/60 backdrop-blur">

                {{-- Hero --}}
                <header class="relative overflow-hidden px-6 py-10 md:px-10 md:py-16">
                    @if ($imageUrl)
                        <div class="absolute inset-0">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $coverMedia?->alt_text ?: $articleContent->title }}"
                                class="h-full w-full object-cover opacity-35"
                            >
                            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-900/75 to-slate-900"></div>
                        </div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950"></div>
                    @endif

                    <div class="relative max-w-4xl">
                        @if ($primaryCategory)
                            <span class="inline-flex rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-slate-950">
                                {{ $primaryCategory->name }}
                            </span>
                        @endif

                        <h1 class="mt-6 text-3xl font-semibold leading-tight text-white md:text-5xl">
                            {{ $articleContent->title }}
                        </h1>

                        @if ($articleContent->excerpt)
                            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                                {{ $articleContent->excerpt }}
                            </p>
                        @endif

                        <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-300">
                            @if ($article?->author_name)
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-400">ผู้เขียน: </span>
                                    {{ $article->author_name }}
                                </span>
                            @endif

                            @if ($article?->reading_time_minutes)
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-400">เวลาอ่าน: </span>
                                    {{ $article->reading_time_minutes }} นาที
                                </span>
                            @endif

                            @if ($publishedDate)
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-400">วันที่เผยแพร่: </span>
                                    {{ $publishedDate }}
                                </span>
                            @endif
                        </div>
                    </div>
                </header>

                {{-- Body --}}
                <div class="px-6 pb-10 md:px-10 md:pb-14">
                    <div class="border-t border-white/10 pt-8">
                        <div class="max-w-none text-base leading-8 text-slate-300
                            [&_h1]:mb-4 [&_h1]:text-3xl [&_h1]:font-bold [&_h1]:text-white
                            [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-white
                            [&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-white
                            [&_p]:mb-5
                            [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pl-6
                            [&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:space-y-2 [&_ol]:pl-6
                            [&_li]:text-slate-300
                            [&_strong]:text-white
                            [&_a]:text-blue-300 [&_a:hover]:text-blue-200
                            [&_blockquote]:mb-5 [&_blockquote]:border-l-4 [&_blockquote]:border-amber-400/60 [&_blockquote]:bg-white/[0.04] [&_blockquote]:py-3 [&_blockquote]:pl-4 [&_blockquote]:pr-4 [&_blockquote]:text-slate-300
                            [&_code]:rounded [&_code]:bg-slate-950/70 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:text-sm [&_code]:text-amber-200
                            [&_pre]:mb-5 [&_pre]:overflow-x-auto [&_pre]:rounded-2xl [&_pre]:border [&_pre]:border-white/10 [&_pre]:bg-slate-950/70 [&_pre]:p-4
                            [&_pre_code]:bg-transparent [&_pre_code]:p-0
                        ">
                            @if ($bodyFormat === 'markdown')
                                {!! \Illuminate\Support\Str::markdown($body) !!}
                            @elseif ($bodyFormat === 'html')
                                {!! $body !!}
                            @elseif ($bodyFormat === 'editorjs')
                                @php
                                    $editorData = json_decode($body, true);
                                    $blocks = is_array($editorData) ? ($editorData['blocks'] ?? []) : [];
                                @endphp

                                @if (!empty($blocks))
                                    @foreach ($blocks as $block)
                                        @php
                                            $type = $block['type'] ?? null;
                                            $data = $block['data'] ?? [];
                                        @endphp

                                        @if ($type === 'header')
                                            @php
                                                $level = (int) ($data['level'] ?? 2);
                                                $text = $data['text'] ?? '';
                                            @endphp

                                            @if ($level === 1)
                                                <h1>{!! $text !!}</h1>
                                            @elseif ($level === 3)
                                                <h3>{!! $text !!}</h3>
                                            @else
                                                <h2>{!! $text !!}</h2>
                                            @endif
                                        @elseif ($type === 'paragraph')
                                            <p>{!! $data['text'] ?? '' !!}</p>
                                        @elseif ($type === 'list')
                                            @php
                                                $style = $data['style'] ?? 'unordered';
                                                $items = $data['items'] ?? [];
                                            @endphp

                                            @if ($style === 'ordered')
                                                <ol>
                                                    @foreach ($items as $item)
                                                        <li>{!! is_array($item) ? ($item['content'] ?? '') : $item !!}</li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <ul>
                                                    @foreach ($items as $item)
                                                        <li>{!! is_array($item) ? ($item['content'] ?? '') : $item !!}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @elseif ($type === 'quote')
                                            <blockquote>
                                                {!! $data['text'] ?? '' !!}
                                                @if (!empty($data['caption']))
                                                    <footer class="mt-2 text-sm text-slate-500">
                                                        {{ $data['caption'] }}
                                                    </footer>
                                                @endif
                                            </blockquote>
                                        @elseif ($type === 'image')
                                            @php
                                                $file = $data['file'] ?? [];
                                                $url = $file['url'] ?? ($data['url'] ?? null);
                                            @endphp

                                            @if ($url)
                                                <figure class="mb-6 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/50">
                                                    <img
                                                        src="{{ $url }}"
                                                        alt="{{ strip_tags($data['caption'] ?? $articleContent->title) }}"
                                                        class="w-full object-cover"
                                                        loading="lazy"
                                                    >

                                                    @if (!empty($data['caption']))
                                                        <figcaption class="px-4 py-3 text-sm text-slate-400">
                                                            {!! $data['caption'] !!}
                                                        </figcaption>
                                                    @endif
                                                </figure>
                                            @endif
                                        @elseif ($type === 'delimiter')
                                            <hr class="my-8 border-white/10">
                                        @elseif ($type === 'code')
                                            <pre><code>{{ $data['code'] ?? '' }}</code></pre>
                                        @else
                                            @if (!empty($data['text']))
                                                <p>{!! $data['text'] !!}</p>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <pre class="overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-300">{{ $body }}</pre>
                                @endif
                            @else
                                {!! nl2br(e($body)) !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Back --}}
            <div class="mt-6">
                <a
                    href="{{ url('/articles') }}"
                    class="inline-flex items-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    ← กลับไปหน้าบทความ
                </a>
            </div>
        </article>
    </main>
</body>
</html>