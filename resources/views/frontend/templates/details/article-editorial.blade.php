@extends('frontend.layouts.app')

@php
    $articleContent = $articleContent ?? $content ?? null;
    $article = $article ?? (($articleContent && $articleContent->relationLoaded('article')) ? $articleContent->article : null);
    $categories = ($articleContent && $articleContent->relationLoaded('categories')) ? $articleContent->categories : collect();
    $category = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
    $tags = ($article && $article->relationLoaded('tags')) ? $article->tags : collect();
    $body = $article?->body ?? '';
    $bodyFormat = $article?->body_format ?? 'markdown';
@endphp

@section('title', $articleContent?->meta_title ?? $articleContent?->title ?? 'Article Detail')
@section('meta_description', $articleContent?->meta_description ?? $articleContent?->excerpt ?? 'Article Detail')

@section('content')
    <main class="bg-zinc-50 text-zinc-950">
        <section class="border-b border-zinc-200 bg-white">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 lg:grid-cols-[minmax(0,1fr)_280px]">
                <article>
                    @if ($category)
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ $category->name }}</p>
                    @endif

                    <h1 class="mt-4 max-w-4xl text-4xl font-semibold leading-tight text-zinc-950 md:text-6xl">
                        {{ $articleContent?->title ?? 'รายละเอียดบทความ' }}
                    </h1>

                    <p class="mt-5 max-w-3xl text-lg leading-8 text-zinc-600">
                        {{ $articleContent?->excerpt ?? $article?->excerpt_en ?? 'ยังไม่มีคำโปรย' }}
                    </p>
                </article>

                <aside class="border-t border-zinc-200 pt-6 text-sm text-zinc-600 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-2">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs uppercase text-zinc-400">Author</dt>
                            <dd class="mt-1 font-medium text-zinc-900">{{ $article?->author_name ?: 'PAPAIWAT Editorial' }}</dd>
                        </div>
                        @if ($article?->reading_time_minutes)
                            <div>
                                <dt class="text-xs uppercase text-zinc-400">Reading time</dt>
                                <dd class="mt-1 font-medium text-zinc-900">{{ number_format($article->reading_time_minutes) }} นาที</dd>
                            </div>
                        @endif
                        @if ($articleContent?->published_at)
                            <div>
                                <dt class="text-xs uppercase text-zinc-400">Published</dt>
                                <dd class="mt-1 font-medium text-zinc-900">{{ $articleContent->published_at->format('d M Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </aside>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-12">
            @if ($articleContent?->description)
                <p class="mb-8 border-l-4 border-emerald-600 pl-5 text-base leading-8 text-zinc-600">
                    {!! nl2br(e($articleContent->description)) !!}
                </p>
            @endif

            <div class="max-w-none text-lg leading-9 text-zinc-700
                [&_a]:font-medium [&_a]:text-emerald-700
                [&_blockquote]:border-l-4 [&_blockquote]:border-zinc-300 [&_blockquote]:pl-5 [&_blockquote]:text-zinc-500
                [&_h2]:mb-4 [&_h2]:mt-10 [&_h2]:text-3xl [&_h2]:font-semibold [&_h2]:text-zinc-950
                [&_h3]:mb-3 [&_h3]:mt-8 [&_h3]:text-2xl [&_h3]:font-semibold [&_h3]:text-zinc-950
                [&_li]:mb-2
                [&_ol]:mb-7 [&_ol]:list-decimal [&_ol]:pl-6
                [&_p]:mb-7
                [&_strong]:text-zinc-950
                [&_ul]:mb-7 [&_ul]:list-disc [&_ul]:pl-6"
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
                    <p class="text-zinc-500">ยังไม่มีเนื้อหาบทความ</p>
                @endif
            </div>

            @if ($tags->isNotEmpty())
                <div class="mt-10 flex flex-wrap gap-2 border-t border-zinc-200 pt-6">
                    @foreach ($tags as $tag)
                        <span class="rounded-full bg-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-700">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection
