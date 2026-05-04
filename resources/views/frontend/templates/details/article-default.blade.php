@extends('frontend.layouts.app')

@section('title', ($articleContent ?? null)?->meta_title ?? ($articleContent ?? null)?->title ?? 'Article Detail')
@section('meta_description', ($articleContent ?? null)?->meta_description ?? ($articleContent ?? null)?->excerpt ?? 'Article Detail')

@section('content')
@php
    $articleContent = $articleContent ?? $content ?? null;
    $article = $article ?? (($articleContent && $articleContent->relationLoaded('article')) ? $articleContent->article : null);

    $mediaUsages = ($articleContent && $articleContent->relationLoaded('mediaUsages'))
        ? $articleContent->mediaUsages
        : collect();

    $categories = ($articleContent && $articleContent->relationLoaded('categories'))
        ? $articleContent->categories
        : collect();

    $tags = ($article && $article->relationLoaded('tags'))
        ? $article->tags
        : collect();

    $cover = $mediaUsages->firstWhere('role_key', 'cover');
    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
    $coverPath = $coverMedia?->path;

    $coverUrl = $coverPath
        ? (filter_var($coverPath, FILTER_VALIDATE_URL)
            ? $coverPath
            : \Illuminate\Support\Facades\Storage::url($coverPath))
        : null;

    $category = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();

    $bodyFormat = $article?->body_format ?? 'markdown';
    $body = $article?->body ?? '';

    $publishedAt = $articleContent?->published_at;
    $authorName = $article?->author_name ?: 'PAPAIWAT Editorial';
    $readingTime = $article?->reading_time_minutes;
@endphp

<main class="relative min-h-screen overflow-hidden bg-slate-950 px-4 py-8 text-white">
    {{-- Background --}}
    <div class="fixed inset-0">
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt=""
                class="h-full w-full scale-110 object-cover opacity-25 blur-md"
            >
        @else
            <div class="h-full w-full bg-gradient-to-br from-slate-950 via-indigo-950/70 to-slate-900"></div>
        @endif

        <div class="absolute inset-0 bg-slate-950/80"></div>
    </div>

    {{-- Article Card --}}
    <article class="relative mx-auto max-w-4xl overflow-hidden rounded-[1.75rem] border border-white/10 bg-slate-800/90 shadow-2xl shadow-slate-950/70 backdrop-blur-xl">
        {{-- Cover --}}
        <section class="relative h-[420px] overflow-hidden">
            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $coverMedia?->alt_text ?: $articleContent?->title ?: 'Article cover' }}"
                    class="h-full w-full object-cover"
                >
            @else
                <div class="h-full w-full bg-gradient-to-br from-slate-800 via-indigo-950/50 to-slate-950"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-900/45 to-transparent"></div>

            <div class="absolute inset-x-0 bottom-0 px-7 pb-8">
                @if ($category)
                    <span class="inline-flex rounded-full bg-emerald-500 px-3 py-1 text-xs font-semibold text-white">
                        {{ $category->name }}
                    </span>
                @endif

                <h1 class="mt-5 max-w-3xl text-3xl font-light leading-tight text-white md:text-4xl">
                    {{ $articleContent?->title ?? 'รายละเอียดบทความ' }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    {{ $articleContent?->excerpt ?? $article?->excerpt_en ?? 'ยังไม่มีคำโปรย' }}
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-6 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                        {{ $authorName }}
                    </span>

                    @if ($readingTime)
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ number_format($readingTime) }} นาที
                        </span>
                    @endif

                    @if ($article?->lesson_count ?? false)
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v12m0-12c-2.25-1.5-5.25-1.5-7.5 0v12c2.25-1.5 5.25-1.5 7.5 0m0-12c2.25-1.5 5.25-1.5 7.5 0v12c-2.25-1.5-5.25-1.5-7.5 0" />
                            </svg>
                            {{ number_format($article->lesson_count) }} บทเรียน
                        </span>
                    @endif

                    @if ($publishedAt)
                        <span class="inline-flex items-center gap-2">
                            {{ $publishedAt->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Content --}}
        <section class="px-7 py-8 md:px-8 md:py-10">
            @if ($articleContent?->description)
                <p class="mb-8 text-base leading-8 text-slate-300">
                    {!! nl2br(e($articleContent->description)) !!}
                </p>
            @endif

            <div class="max-w-none text-base leading-8 text-slate-300
                [&_a]:text-blue-300
                [&_blockquote]:border-l-4 [&_blockquote]:border-blue-300/40 [&_blockquote]:pl-4 [&_blockquote]:text-slate-400
                [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-white
                [&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-white
                [&_li]:mb-1
                [&_ol]:mb-6 [&_ol]:list-decimal [&_ol]:pl-5
                [&_p]:mb-6
                [&_strong]:text-white
                [&_ul]:mb-6 [&_ul]:list-disc [&_ul]:pl-5"
            >
                @if ($body)
                    @if ($bodyFormat === 'markdown')
                        {!! \Illuminate\Support\Str::markdown($body) !!}
                    @elseif ($bodyFormat === 'html')
                        {!! $body !!}
                    @else
                        {!! nl2br(e($body)) !!}
                    @endif
                @else
                    <p class="text-slate-500">ยังไม่มีเนื้อหาบทความ</p>
                @endif
            </div>

            @if ($tags->isNotEmpty())
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs text-slate-300">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.015-4.5-4.5-4.5-1.74 0-3.25.99-4 2.438A4.49 4.49 0 0 0 8.5 3.75C6.015 3.75 4 5.765 4 8.25c0 7.22 8.5 12 8.5 12s8.5-4.78 8.5-12Z" />
                    </svg>
                    ถูกใจ
                </button>

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12A1.25 1.25 0 0 1 19.25 5v15.25L12 16.5l-7.25 3.75V5A1.25 1.25 0 0 1 6 3.75Z" />
                    </svg>
                    บันทึก
                </button>

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 16.5 6.75M7.5 12l9 5.25M7.5 12a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm13.5-6.75a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm0 13.5a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    แชร์
                </button>
            </div>
        </section>
    </article>
</main>
@endsection
